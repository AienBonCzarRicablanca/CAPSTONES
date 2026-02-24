# Quick Start: Reading Assessment with Whisper (No Training Needed!)

## 🎯 TL;DR - Get Started in 1 Hour

**Good News:** You don't need to train a model from scratch! 
Use OpenAI's pre-trained Whisper model which already supports Filipino + English.

## ⚡ Prerequisites

```bash
# Python 3.8+ required
# Check version:
python --version

# Install required packages
pip install openai-whisper
pip install flask
pip install librosa
pip install jiwer  # For Word Error Rate calculation
```

## 📁 Project Structure

```
CAPSTONE/
├── ml_service/
│   ├── app.py              # Flask API
│   ├── assessment.py       # Reading assessment logic
│   ├── requirements.txt    # Python dependencies
│   └── models/            # Downloaded Whisper models
├── public/
│   └── recordings/        # Student recordings
└── storage/
    └── reading_passages/  # Texts students will read
```

## 🚀 Step 1: Create Python Service

Create `ml_service/assessment.py`:

```python
"""
Reading Assessment Module
Analyzes student reading recordings
"""

import whisper
import librosa
import numpy as np
from jiwer import wer
import re

class ReadingAssessment:
    def __init__(self):
        """Initialize with Whisper model"""
        print("Loading Whisper model... (this may take a few minutes)")
        # Use 'medium' for balance of speed and accuracy
        # Options: tiny, base, small, medium, large
        self.model = whisper.load_model("medium")
        print("✓ Whisper model loaded!")
    
    def assess(self, audio_path, expected_text, language='filipino'):
        """
        Main assessment function
        
        Args:
            audio_path: Path to student's recording
            expected_text: What student should have read
            language: 'filipino' or 'english'
        
        Returns:
            dict with assessment results
        """
        # 1. Transcribe audio
        transcription = self.transcribe(audio_path, language)
        
        # 2. Calculate accuracy
        accuracy = self.calculate_accuracy(transcription, expected_text)
        
        # 3. Calculate WPM (Words Per Minute)
        wpm = self.calculate_wpm(audio_path, expected_text)
        
        # 4. Analyze fluency
        fluency_score = self.analyze_fluency(audio_path)
        
        # 5. Identify errors
        errors = self.identify_errors(transcription, expected_text)
        
        # 6. Calculate overall grade
        grade = self.calculate_grade(accuracy, wpm, fluency_score)
        
        return {
            'transcription': transcription,
            'expected_text': expected_text,
            'accuracy': round(accuracy, 2),
            'wpm': round(wpm, 2),
            'fluency_score': round(fluency_score, 2),
            'grade': grade,
            'errors': errors,
            'recommendations': self.get_recommendations(accuracy, wpm, fluency_score)
        }
    
    def transcribe(self, audio_path, language):
        """Transcribe audio to text using Whisper"""
        lang_code = 'tl' if language == 'filipino' else 'en'
        
        result = self.model.transcribe(
            audio_path,
            language=lang_code,
            task='transcribe',
            fp16=False  # Use CPU
        )
        
        return result['text'].strip()
    
    def calculate_accuracy(self, transcription, expected):
        """Calculate word accuracy percentage"""
        # Clean texts
        trans_clean = self.clean_text(transcription)
        expected_clean = self.clean_text(expected)
        
        # Calculate Word Error Rate
        error_rate = wer(expected_clean, trans_clean)
        
        # Convert to accuracy (100% - error%)
        accuracy = max(0, (1 - error_rate) * 100)
        
        return accuracy
    
    def calculate_wpm(self, audio_path, expected_text):
        """Calculate Words Per Minute"""
        # Get audio duration
        y, sr = librosa.load(audio_path, sr=None)
        duration_seconds = librosa.get_duration(y=y, sr=sr)
        
        # Count words
        word_count = len(expected_text.split())
        
        # Calculate WPM
        if duration_seconds > 0:
            wpm = (word_count / duration_seconds) * 60
        else:
            wpm = 0
        
        return wpm
    
    def analyze_fluency(self, audio_path):
        """
        Analyze reading fluency based on audio features
        Checks for: proper pacing, minimal pauses, consistent rhythm
        """
        try:
            # Load audio
            y, sr = librosa.load(audio_path, sr=22050)
            
            # Detect pauses (silence)
            intervals = librosa.effects.split(y, top_db=30)
            
            # Calculate pause statistics
            total_duration = len(y) / sr
            speaking_duration = sum((end - start) / sr for start, end in intervals)
            pause_ratio = 1 - (speaking_duration / total_duration)
            
            # Calculate tempo (rhythm consistency)
            tempo, _ = librosa.beat.beat_track(y=y, sr=sr)
            
            # Score fluency (0-5 scale)
            # Less pauses = better fluency
            pause_score = max(0, 5 - (pause_ratio * 10))
            
            # Normalize tempo score
            ideal_tempo = 100  # beats per minute for reading
            tempo_score = 5 - min(5, abs(tempo - ideal_tempo) / 20)
            
            # Average the scores
            fluency_score = (pause_score + tempo_score) / 2
            
            return fluency_score
            
        except Exception as e:
            print(f"Fluency analysis error: {e}")
            return 3.0  # Default to average
    
    def identify_errors(self, transcription, expected):
        """Identify specific reading errors"""
        trans_words = self.clean_text(transcription).split()
        expected_words = self.clean_text(expected).split()
        
        errors = []
        
        # Simple word-by-word comparison
        max_len = max(len(trans_words), len(expected_words))
        
        for i in range(max_len):
            expected_word = expected_words[i] if i < len(expected_words) else None
            trans_word = trans_words[i] if i < len(trans_words) else None
            
            if expected_word != trans_word:
                if expected_word and not trans_word:
                    errors.append({
                        'type': 'omission',
                        'expected': expected_word,
                        'position': i
                    })
                elif trans_word and not expected_word:
                    errors.append({
                        'type': 'addition',
                        'word': trans_word,
                        'position': i
                    })
                else:
                    errors.append({
                        'type': 'substitution',
                        'expected': expected_word,
                        'said': trans_word,
                        'position': i
                    })
        
        return errors
    
    def calculate_grade(self, accuracy, wpm, fluency):
        """Calculate overall grade (Excellent/Good/Fair/Needs Practice)"""
        # Weighted score
        score = (accuracy * 0.5) + (min(wpm/100, 1) * 100 * 0.3) + (fluency * 20 * 0.2)
        
        if score >= 85:
            return 'Excellent'
        elif score >= 70:
            return 'Good'
        elif score >= 50:
            return 'Fair'
        else:
            return 'Needs Practice'
    
    def get_recommendations(self, accuracy, wpm, fluency):
        """Generate personalized recommendations"""
        recommendations = []
        
        if accuracy < 70:
            recommendations.append("Practice pronunciation of difficult words")
        
        if wpm < 60:
            recommendations.append("Try to read a bit faster - aim for 60-100 words per minute")
        elif wpm > 150:
            recommendations.append("Slow down a little for better clarity")
        
        if fluency < 3:
            recommendations.append("Practice reading smoothly without long pauses")
        
        if not recommendations:
            recommendations.append("Great job! Keep practicing regularly!")
        
        return recommendations
    
    @staticmethod
    def clean_text(text):
        """Clean and normalize text for comparison"""
        # Convert to lowercase
        text = text.lower()
        # Remove punctuation
        text = re.sub(r'[^\w\s]', '', text)
        # Remove extra whitespace
        text = ' '.join(text.split())
        return text

# Test if run directly
if __name__ == "__main__":
    assessor = ReadingAssessment()
    
    # Test with sample
    result = assessor.assess(
        audio_path="test_recording.wav",
        expected_text="May isang masayang ibon na kumakanta araw-araw.",
        language="filipino"
    )
    
    print("Assessment Results:")
    print(f"Accuracy: {result['accuracy']}%")
    print(f"WPM: {result['wpm']}")
    print(f"Grade: {result['grade']}")
```

Create `ml_service/app.py` (Flask API):

```python
"""
Flask API for Reading Assessment
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
from assessment import ReadingAssessment
import os
from werkzeug.utils import secure_filename
import uuid

app = Flask(__name__)
CORS(app)  # Allow requests from Laravel

# Initialize assessment engine
assessor = ReadingAssessment()

# Upload configuration
UPLOAD_FOLDER = '../public/recordings'
ALLOWED_EXTENSIONS = {'wav', 'mp3', 'm4a', 'ogg', 'webm'}

os.makedirs(UPLOAD_FOLDER, exist_ok=True)

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'service': 'Reading Assessment API',
        'version': '1.0'
    })

@app.route('/assess', methods=['POST'])
def assess_reading():
    """
    Main assessment endpoint
    
    Expected form data:
    - audio: audio file
    - expected_text: text that should be read
    - language: 'filipino' or 'english'
    """
    try:
        # Validate request
        if 'audio' not in request.files:
            return jsonify({'error': 'No audio file provided'}), 400
        
        audio_file = request.files['audio']
        if audio_file.filename == '':
            return jsonify({'error': 'No file selected'}), 400
        
        if not allowed_file(audio_file.filename):
            return jsonify({'error': 'Invalid file type'}), 400
        
        # Get parameters
        expected_text = request.form.get('expected_text', '')
        language = request.form.get('language', 'filipino')
        
        if not expected_text:
            return jsonify({'error': 'Expected text is required'}), 400
        
        # Save uploaded file
        filename = f"{uuid.uuid4()}_{secure_filename(audio_file.filename)}"
        filepath = os.path.join(UPLOAD_FOLDER, filename)
        audio_file.save(filepath)
        
        # Run assessment
        result = assessor.assess(filepath, expected_text, language)
        
        # Add filename to result
        result['audio_filename'] = filename
        
        return jsonify({
            'success': True,
            'assessment': result
        })
        
    except Exception as e:
        return jsonify({
            'error': str(e)
        }), 500

@app.route('/transcribe', methods=['POST'])
def transcribe_only():
    """
    Transcribe audio without assessment
    Useful for testing
    """
    try:
        if 'audio' not in request.files:
            return jsonify({'error': 'No audio file provided'}), 400
        
        audio_file = request.files['audio']
        language = request.form.get('language', 'filipino')
        
        # Save file
        filename = f"{uuid.uuid4()}_{secure_filename(audio_file.filename)}"
        filepath = os.path.join(UPLOAD_FOLDER, filename)
        audio_file.save(filepath)
        
        # Transcribe
        transcription = assessor.transcribe(filepath, language)
        
        return jsonify({
            'success': True,
            'transcription': transcription
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    print("Starting Reading Assessment API...")
    print("Listening on http://localhost:5000")
    app.run(host='0.0.0.0', port=5000, debug=True)
```

Create `ml_service/requirements.txt`:

```txt
flask==3.0.0
flask-cors==4.0.0
openai-whisper==20231117
librosa==0.10.1
jiwer==3.0.3
numpy==1.24.3
soundfile==0.12.1
```

## 🔧 Step 2: Install & Test

```bash
# Navigate to ml_service directory
cd CAPSTONE/ml_service

# Install dependencies (takes 5-10 minutes)
pip install -r requirements.txt

# Test the API
python app.py
```

You should see:
```
Loading Whisper model...
✓ Whisper model loaded!
Starting Reading Assessment API...
Listening on http://localhost:5000
```

## 🧪 Step 3: Test with cURL

```bash
# Test health check
curl http://localhost:5000/health

# Test transcription (create test_recording.wav first)
curl -X POST http://localhost:5000/transcribe \
  -F "audio=@test_recording.wav" \
  -F "language=filipino"

# Test full assessment
curl -X POST http://localhost:5000/assess \
  -F "audio=@test_recording.wav" \
  -F "expected_text=May isang masayang ibon" \
  -F "language=filipino"
```

## 📝 Step 4: Integrate with Laravel

See next file: `LARAVEL_INTEGRATION_GUIDE.md`

## 💾 Model Downloads

On first run, Whisper will download the model (~1.5GB for medium):
- Stored in: `~/.cache/whisper/`
- Only downloads once
- Reusable across projects

## 🎯 Model Size Options

| Model | Size | Speed | Accuracy | Recommended For |
|-------|------|-------|----------|----------------|
| tiny | 39M | Fast | Low | Quick testing |
| base | 74M | Fast | Fair | Demos |
| small | 244M | Medium | Good | Development |
| **medium** | 769M | Slow | **Very Good** | **Production** ✅ |
| large | 1550M | Very Slow | Best | High accuracy needs |

**Recommendation:** Use `medium` for production Filipino/English assessment.

## ✅ Success Indicators

When working correctly, you'll see:
```json
{
  "success": true,
  "assessment": {
    "transcription": "may isang masayang ibon",
    "expected_text": "May isang masayang ibon.",
    "accuracy": 95.5,
    "wpm": 78.3,
    "fluency_score": 4.2,
    "grade": "Excellent",
    "errors": [],
    "recommendations": ["Great job! Keep practicing regularly!"]
  }
}
```

## 🐛 Troubleshooting

**Error:** `No module named 'whisper'`
```bash
pip install openai-whisper
```

**Error:** `CUDA not available`
- This is OK! Whisper runs on CPU
- Just slower (3-10 seconds per recording)

**Error:** `Could not load audio`
```bash
pip install soundfile ffmpeg-python
```

**Slow transcription?**
- Use smaller model (`tiny` or `base`)
- Or use GPU if available

## 🚀 Next Steps

1. ✅ Python service running
2. ✅ Can transcribe audio
3. ✅ Can assess reading
4. ➡️ **Next:** Integrate with Laravel (see `LARAVEL_INTEGRATION_GUIDE.md`)

**Time to complete:** 30-60 minutes
**No training required!** 🎉
