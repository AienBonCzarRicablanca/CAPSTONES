# Reading Assessment System - ML Implementation Plan

## 🎯 Project Goal
Build a system where students record themselves reading short stories/lessons, and ML analyzes:
- **Reading accuracy** (correct pronunciation)
- **Fluency** (smooth reading, proper pace)
- **Expression** (tone, emphasis)
- **Comprehension** (optional: answer questions after)

## 📊 Datasets Needed

### 1. **Speech Recognition Dataset** (Filipino + English)

#### For Filipino:
**Dataset Sources:**
- **Common Voice by Mozilla** (Filipino)
  - URL: https://commonvoice.mozilla.org/tl/datasets
  - Size: ~30 hours of Filipino speech
  - Format: MP3 + text transcriptions
  - License: CC0 (Public Domain)
  - Contains: Various ages, accents, reading speeds
  
- **Filipino Speech Corpus**
  - URL: https://github.com/bantucarl/tagalog-speech-corpus
  - Smaller dataset but good for fine-tuning
  
- **OpenSLR Filipino**
  - URL: https://www.openslr.org/94/
  - High-quality recordings with transcripts

#### For English:
- **LibriSpeech**
  - URL: https://www.openslr.org/12/
  - Size: 1000 hours of English audiobooks
  - Perfect for children's reading assessment
  
- **Common Voice English**
  - URL: https://commonvoice.mozilla.org/en/datasets
  - Diverse accents and ages

### 2. **Children's Reading Dataset** (Important!)

**Why needed:** Adult speech patterns differ from children's
- Slower pace
- More pronunciation errors
- Different voice characteristics

**Sources:**
- **CSLU: Kids' Speech Corpus**
  - URL: https://catalog.ldc.upenn.edu/LDC2007S18
  - Contains children reading prompts
  - Ages 5-18
  
- **MyST Dataset** (Children Reading)
  - URL: https://groups.inf.ed.ac.uk/myst/
  - Children reading stories
  
- **You can create your own!**
  - Record students (with consent)
  - Build Filipino children's reading dataset
  - This is VALUABLE - no good Filipino kids dataset exists!

### 3. **Phonetic Alignment Dataset**

For pronunciation accuracy:
- **CMU Pronouncing Dictionary** (English)
  - URL: http://www.speech.cs.cmu.edu/cgi-bin/cmudict
  - Maps words to phonemes
  
- **Filipino Phonetic Dictionary**
  - Create your own using IPA (International Phonetic Alphabet)
  - Map Tagalog words to sounds: 
    - "aso" → /ʔa.so/
    - "pusa" → /pu.sa/

### 4. **Reading Fluency Labels**

You'll need to manually label audio samples with:
- **WPM** (Words Per Minute) - normal range for kids: 60-150 WPM
- **Accuracy** (% words read correctly)
- **Prosody score** (expression, 1-5 scale)
- **Error types**: substitutions, omissions, additions

## 🤖 ML Model Architecture

### Recommended Approach: **Hybrid System**

```
Student Recording (WAV/MP3)
         ↓
[1] Speech-to-Text (Transcription)
    → Use: Whisper by OpenAI (supports Filipino!)
         ↓
[2] Forced Alignment
    → Match audio to expected text
    → Detect mispronunciations
         ↓
[3] Feature Extraction
    → WPM calculation
    → Pause detection
    → Pitch/tone analysis
         ↓
[4] ML Classifier
    → Grade: Excellent/Good/Needs Practice
    → Identify specific errors
```

### Pre-trained Models You Can Use:

1. **OpenAI Whisper** (Best for transcription)
   - Supports Filipino (fil) and English
   - Very accurate even with children's voices
   - Free and open source
   - Size: ~3GB (large model recommended)

2. **Wav2Vec 2.0** (Facebook/Meta)
   - Pre-trained on 53 languages including Filipino
   - Good for phoneme recognition
   - Can fine-tune on children's speech

3. **Montreal Forced Aligner** (For alignment)
   - Matches audio to text timing
   - Detects mispronunciations
   - Pre-trained Filipino model available

## 🛠️ Implementation Steps

### Phase 1: Data Collection (Week 1-2)
```bash
1. Download Common Voice Filipino dataset
2. Download LibriSpeech for English
3. Prepare 50-100 short reading passages (Filipino + English)
4. Record 10-20 children reading (different skill levels)
5. Label recordings with accuracy scores
```

### Phase 2: Environment Setup (Week 1)
```bash
# Install Python ML libraries
pip install torch torchaudio
pip install transformers  # For Whisper
pip install librosa soundfile  # Audio processing
pip install praatio  # For forced alignment
pip install scikit-learn pandas numpy

# Install Whisper
pip install openai-whisper

# Install Montreal Forced Aligner
conda install -c conda-forge montreal-forced-aligner
```

### Phase 3: Model Training (Week 2-3)

**Option A: Use Pre-trained (Faster - Recommended)**
```python
# Use Whisper directly
import whisper

model = whisper.load_model("large")
result = model.transcribe("student_recording.mp3", language="tl")
print(result["text"])
```

**Option B: Fine-tune (Better accuracy)**
```python
# Fine-tune Whisper on children's Filipino speech
# Need ~100 hours of labeled audio
# Training time: 2-7 days on GPU
```

### Phase 4: Assessment Algorithm (Week 3-4)

Build scoring system:
```python
def assess_reading(audio_file, expected_text):
    # 1. Transcribe
    transcription = whisper_model.transcribe(audio_file)
    
    # 2. Compare to expected
    accuracy = calculate_word_accuracy(transcription, expected_text)
    
    # 3. Calculate WPM
    duration = get_audio_duration(audio_file)
    wpm = len(expected_text.split()) / (duration / 60)
    
    # 4. Detect pauses/fluency
    fluency_score = analyze_prosody(audio_file)
    
    # 5. Grade
    grade = calculate_grade(accuracy, wpm, fluency_score)
    
    return {
        "accuracy": accuracy,
        "wpm": wpm,
        "fluency": fluency_score,
        "grade": grade,
        "errors": identify_errors(transcription, expected_text)
    }
```

### Phase 5: Laravel Integration (Week 4-5)

Create Python microservice:
```python
# flask_app.py
from flask import Flask, request, jsonify
import whisper
import assess_reading  # Your assessment module

app = Flask(__name__)
model = whisper.load_model("large")

@app.route('/assess', methods=['POST'])
def assess():
    audio_file = request.files['audio']
    expected_text = request.form['text']
    
    result = assess_reading(audio_file, expected_text)
    return jsonify(result)

if __name__ == '__main__':
    app.run(port=5000)
```

Call from Laravel:
```php
// app/Services/ReadingAssessmentService.php
public function assessReading($audioPath, $expectedText)
{
    $response = Http::attach(
        'audio', file_get_contents($audioPath), 'recording.wav'
    )->post('http://localhost:5000/assess', [
        'text' => $expectedText
    ]);
    
    return $response->json();
}
```

## 📂 Dataset Directory Structure

```
datasets/
├── filipino/
│   ├── common_voice/
│   │   ├── audio/
│   │   │   ├── sample001.mp3
│   │   │   └── sample002.mp3
│   │   └── transcripts.csv
│   ├── children/
│   │   ├── grade1/
│   │   ├── grade2/
│   │   └── grade3/
│   └── phonetic_dict_fil.txt
├── english/
│   ├── librispeech/
│   ├── children/
│   └── phonetic_dict_en.txt
└── reading_passages/
    ├── filipino/
    │   ├── beginner/
    │   │   ├── passage001.txt
    │   │   └── passage001_audio.wav
    │   ├── intermediate/
    │   └── advanced/
    └── english/
        ├── beginner/
        ├── intermediate/
        └── advanced/
```

## 🎯 Minimum Viable Dataset

To start training, you need AT MINIMUM:

### For Filipino:
- **100 hours** of Filipino speech (Common Voice provides 30+)
- **50 reading passages** (create yourself from library stories)
- **20 children recordings** per passage (record students)
- **Phonetic dictionary** (500+ common Filipino words)

### For English:
- **LibriSpeech** dataset (download)
- **50 reading passages** (from your library)
- **20 children recordings** (same students, English version)

### Quick Start (Can train in 1 week):
1. Download Common Voice Filipino (30 hours)
2. Download LibriSpeech subset (10 hours)
3. Record 10 students reading 10 passages each = 100 samples
4. Use pre-trained Whisper (no training needed!)
5. Build assessment logic on top

## 💰 Cost Estimate

### Free Option:
- ✅ All datasets: FREE
- ✅ Whisper model: FREE
- ✅ Python libraries: FREE
- ✅ Training: FREE (use CPU, slower)
- **Total: ₱0**

### Fast Option (GPU):
- Google Colab Pro: ₱499/month
- Or rent GPU: ₱500-1000/day
- **Total: ₱500-1000 for initial training**

## 🚀 Quick Start Checklist

**Week 1:**
- [ ] Download Common Voice Filipino dataset
- [ ] Install Python + libraries
- [ ] Install Whisper
- [ ] Test Whisper on sample audio
- [ ] Create 10 reading passages (Filipino + English)

**Week 2:**
- [ ] Record 5 students reading all passages
- [ ] Build simple assessment script
- [ ] Calculate accuracy + WPM
- [ ] Test with real voices

**Week 3:**
- [ ] Create Flask API
- [ ] Integrate with Laravel
- [ ] Build recording interface
- [ ] Test end-to-end

**Week 4:**
- [ ] Add error detection
- [ ] Build teacher dashboard
- [ ] Create student progress reports
- [ ] Polish UI

## 📚 Learning Resources

**Tutorials:**
- Whisper API: https://github.com/openai/whisper
- Speech Recognition Python: https://realpython.com/python-speech-recognition/
- Audio Processing: https://librosa.org/doc/latest/tutorial.html

**Papers:**
- "Automated Reading Assessment" - ETS Research
- "Children's Speech Recognition" - CMU Sphinx Project

## 🎓 Model Training Tutorial

I'll create separate files:
1. `DATASET_DOWNLOAD_GUIDE.md` - Step-by-step download
2. `MODEL_TRAINING_TUTORIAL.md` - Python training scripts
3. `LARAVEL_INTEGRATION_GUIDE.md` - Connect to your app

Ready to proceed? Let me know which phase you want to start with!

## 💡 Pro Tips

1. **Start with pre-trained Whisper** - Don't train from scratch!
2. **Collect your own children's data** - Most valuable asset
3. **Focus on error detection** - More useful than perfect transcription
4. **Use GPU for inference** - But CPU works fine for small scale
5. **Build incrementally** - Start simple, add features gradually

## ❓ Next Steps

Tell me:
1. Do you have access to GPU? (for faster processing)
2. How many students will use this? (determines infrastructure)
3. Want to start with Filipino or English first?
4. Should I create the dataset download scripts now?
