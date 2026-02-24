# Dataset Download & Preparation Guide

## 🎯 Goal
Download and prepare datasets for Filipino + English reading assessment

## 📥 Step 1: Download Common Voice (Filipino)

### Method 1: Direct Download (Easiest)
```bash
# 1. Go to: https://commonvoice.mozilla.org/tl/datasets
# 2. Scroll down to "Download Dataset"
# 3. Select "Common Voice Corpus 15.0" (latest)
# 4. Click "Download" (requires Mozilla account - FREE)
# 5. File size: ~2GB (Filipino only)
```

### Method 2: Using Python Script
```python
# download_common_voice.py
import urllib.request
import tarfile
import os

def download_file(url, filename):
    print(f"Downloading {filename}...")
    urllib.request.urlretrieve(url, filename, reporthook=progress_hook)
    print(f"\n✓ Downloaded {filename}")

def progress_hook(count, block_size, total_size):
    percent = int(count * block_size * 100 / total_size)
    print(f"\rProgress: {percent}%", end='')

def extract_tar(filename, extract_to):
    print(f"Extracting {filename}...")
    with tarfile.open(filename, "r:gz") as tar:
        tar.extractall(extract_to)
    print(f"✓ Extracted to {extract_to}")

# Download Filipino dataset
os.makedirs('datasets/filipino/common_voice', exist_ok=True)

# NOTE: You need to get the actual download URL from Common Voice website
# after agreeing to terms and logging in
print("Please visit: https://commonvoice.mozilla.org/tl/datasets")
print("Sign up/login, then copy the download link for Filipino dataset")
download_url = input("Paste download URL here: ")

download_file(download_url, 'cv-corpus-filipino.tar.gz')
extract_tar('cv-corpus-filipino.tar.gz', 'datasets/filipino/common_voice')

print("✓ Filipino dataset ready!")
```

## 📥 Step 2: Download LibriSpeech (English)

```bash
# Create directory
mkdir -p datasets/english/librispeech

# Download dev-clean subset (337MB - good for testing)
cd datasets/english/librispeech
wget https://www.openslr.org/resources/12/dev-clean.tar.gz

# Or download train-clean-100 (6.3GB - for training)
wget https://www.openslr.org/resources/12/train-clean-100.tar.gz

# Extract
tar -xzf dev-clean.tar.gz
tar -xzf train-clean-100.tar.gz

echo "✓ LibriSpeech downloaded!"
```

### Windows PowerShell Version:
```powershell
# Create directory
New-Item -Path "datasets\english\librispeech" -ItemType Directory -Force

# Download using Invoke-WebRequest
$url = "https://www.openslr.org/resources/12/dev-clean.tar.gz"
$output = "datasets\english\librispeech\dev-clean.tar.gz"
Invoke-WebRequest -Uri $url -OutFile $output

# For extraction, use 7-Zip or WinRAR
Write-Host "Download complete! Extract with 7-Zip or WinRAR"
```

## 📥 Step 3: Download Filipino Children's Corpus (Optional)

Since there's no large Filipino children's dataset, create your own:

```python
# record_students.py
import sounddevice as sd
import soundfile as sf
import os
from datetime import datetime

def record_reading(student_name, passage_id, duration=30):
    """Record student reading for specified duration"""
    
    print(f"Recording {student_name} reading passage {passage_id}...")
    print("3... 2... 1... START!")
    
    # Record at 16kHz (standard for speech)
    recording = sd.rec(int(duration * 16000), 
                       samplerate=16000, 
                       channels=1, 
                       dtype='int16')
    sd.wait()
    
    print("✓ Recording complete!")
    
    # Save with metadata in filename
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    filename = f"{student_name}_{passage_id}_{timestamp}.wav"
    output_path = f"datasets/filipino/children/{filename}"
    
    os.makedirs("datasets/filipino/children", exist_ok=True)
    sf.write(output_path, recording, 16000)
    
    print(f"✓ Saved to {output_path}")
    return output_path

# Usage
if __name__ == "__main__":
    # Install: pip install sounddevice soundfile
    
    student = input("Student name: ")
    passage = input("Passage ID (1-10): ")
    
    print("\nStudent will read the passage in 3 seconds...")
    print("Recording will last 30 seconds.")
    input("Press ENTER when ready...")
    
    record_reading(student, passage, duration=30)
```

## 📝 Step 4: Prepare Reading Passages

Create structured reading passages from your library:

```python
# prepare_passages.py
import os
import json

passages = {
    "filipino": {
        "beginner": [
            {
                "id": "fil_beg_001",
                "title": "Ang Masayang Ibon",
                "text": "May isang masayang ibon. Ang ibon ay kumakanta araw-araw. Masaya ang lahat ng hayop.",
                "word_count": 15,
                "expected_wpm": 60  # Slow for beginners
            },
            {
                "id": "fil_beg_002",
                "title": "Si Pedro at ang Aso",
                "text": "Si Pedro ay may aso. Ang aso ay mabait. Mahal ni Pedro ang kanyang aso.",
                "word_count": 15,
                "expected_wpm": 60
            }
        ],
        "intermediate": [
            {
                "id": "fil_int_001",
                "title": "Ang Mahiwagang Puno",
                "text": "Noong unang panahon, may isang mahiwagang puno sa gitna ng nayon. Ang puno ay nagbibigay ng masasarap na prutas. Maraming tao ang pumupunta para kumuha ng prutas.",
                "word_count": 26,
                "expected_wpm": 90
            }
        ]
    },
    "english": {
        "beginner": [
            {
                "id": "eng_beg_001",
                "title": "The Happy Bird",
                "text": "There is a happy bird. The bird sings every day. All the animals are happy.",
                "word_count": 15,
                "expected_wpm": 60
            }
        ]
    }
}

# Save to JSON
os.makedirs("datasets/reading_passages", exist_ok=True)

with open("datasets/reading_passages/passages.json", "w", encoding="utf-8") as f:
    json.dump(passages, f, indent=2, ensure_ascii=False)

print("✓ Reading passages saved!")

# Generate text files for each passage
for lang in passages:
    for level in passages[lang]:
        for passage in passages[lang][level]:
            filepath = f"datasets/reading_passages/{lang}/{level}/{passage['id']}.txt"
            os.makedirs(os.path.dirname(filepath), exist_ok=True)
            
            with open(filepath, "w", encoding="utf-8") as f:
                f.write(passage['text'])
            
            print(f"✓ Created {filepath}")
```

## 📊 Step 5: Organize Dataset Structure

Run this script to create proper directory structure:

```python
# organize_datasets.py
import os
import shutil

def create_structure():
    """Create organized dataset directory structure"""
    
    dirs = [
        # Filipino
        "datasets/filipino/common_voice/audio",
        "datasets/filipino/common_voice/transcripts",
        "datasets/filipino/children/grade1",
        "datasets/filipino/children/grade2",
        "datasets/filipino/children/grade3",
        "datasets/filipino/children/grade4",
        "datasets/filipino/phonetic",
        
        # English
        "datasets/english/librispeech/audio",
        "datasets/english/librispeech/transcripts",
        "datasets/english/children/grade1",
        "datasets/english/children/grade2",
        "datasets/english/children/grade3",
        "datasets/english/children/grade4",
        "datasets/english/phonetic",
        
        # Reading passages
        "datasets/reading_passages/filipino/beginner",
        "datasets/reading_passages/filipino/intermediate",
        "datasets/reading_passages/filipino/advanced",
        "datasets/reading_passages/english/beginner",
        "datasets/reading_passages/english/intermediate",
        "datasets/reading_passages/english/advanced",
        
        # Labels and metadata
        "datasets/labels/filipino",
        "datasets/labels/english",
        
        # Processed (after preprocessing)
        "datasets/processed/filipino",
        "datasets/processed/english",
        
        # Models
        "models/whisper",
        "models/assessment",
        "models/checkpoints"
    ]
    
    for dir_path in dirs:
        os.makedirs(dir_path, exist_ok=True)
        print(f"✓ Created {dir_path}")
    
    # Create README in each directory
    for dir_path in dirs:
        readme_path = os.path.join(dir_path, "README.txt")
        with open(readme_path, "w") as f:
            f.write(f"Directory: {dir_path}\n")
            f.write(f"Purpose: {get_purpose(dir_path)}\n")
        print(f"✓ Created README in {dir_path}")

def get_purpose(dir_path):
    """Get purpose description for directory"""
    if "children" in dir_path:
        return "Children's reading recordings organized by grade level"
    elif "passages" in dir_path:
        return "Reading passage texts for assessment"
    elif "phonetic" in dir_path:
        return "Phonetic dictionaries and pronunciation guides"
    elif "labels" in dir_path:
        return "Manual labels for training data (accuracy, WPM, etc.)"
    elif "processed" in dir_path:
        return "Preprocessed audio files ready for training"
    elif "models" in dir_path:
        return "Trained ML models and checkpoints"
    else:
        return "Dataset files"

if __name__ == "__main__":
    create_structure()
    print("\n✓ Dataset structure created successfully!")
    print("\nNext steps:")
    print("1. Download Common Voice to: datasets/filipino/common_voice/")
    print("2. Download LibriSpeech to: datasets/english/librispeech/")
    print("3. Record students to: datasets/*/children/grade*/")
    print("4. Add reading passages to: datasets/reading_passages/")
```

## 🏷️ Step 6: Create Labels/Annotations

```python
# create_labels.py
import csv
import os

def create_label_template():
    """Create CSV template for labeling recordings"""
    
    headers = [
        'filename',           # recording filename
        'student_id',         # student identifier
        'grade_level',        # 1-4
        'passage_id',         # which passage was read
        'expected_text',      # what they should have read
        'transcription',      # what they actually said (manual or auto)
        'word_accuracy',      # % words correct (0-100)
        'wpm',                # words per minute
        'duration_seconds',   # length of recording
        'fluency_score',      # 1-5 rating
        'error_types',        # substitution, omission, addition
        'notes',              # teacher notes
        'date_recorded'       # when recorded
    ]
    
    # Create template for Filipino
    os.makedirs("datasets/labels/filipino", exist_ok=True)
    with open("datasets/labels/filipino/labels_template.csv", "w", newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerow(headers)
        # Add example row
        writer.writerow([
            'student001_fil_beg_001_20240224.wav',
            'STU001',
            '1',
            'fil_beg_001',
            'May isang masayang ibon.',
            'May isang masaya ibon.',  # Missing 'ng'
            '80',  # 4/5 words correct
            '45',  # slow reading
            '15',  # 15 seconds
            '3',   # average fluency
            'omission',
            'Forgot word "ng"',
            '2024-02-24'
        ])
    
    # Create template for English
    os.makedirs("datasets/labels/english", exist_ok=True)
    with open("datasets/labels/english/labels_template.csv", "w", newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerow(headers)
        writer.writerow([
            'student001_eng_beg_001_20240224.wav',
            'STU001',
            '1',
            'eng_beg_001',
            'There is a happy bird.',
            'There is a happy bird.',
            '100',
            '60',
            '10',
            '4',
            'none',
            'Excellent reading!',
            '2024-02-24'
        ])
    
    print("✓ Label templates created!")
    print("Location: datasets/labels/*/labels_template.csv")
    print("\nInstructions:")
    print("1. Duplicate template for each language")
    print("2. Listen to student recordings")
    print("3. Fill in all columns")
    print("4. Use for training assessment model")

if __name__ == "__main__":
    create_label_template()
```

## ✅ Verification Checklist

After downloading, verify your setup:

```python
# verify_datasets.py
import os

def verify_structure():
    """Verify all required directories and files exist"""
    
    checks = {
        "Filipino Common Voice": "datasets/filipino/common_voice",
        "English LibriSpeech": "datasets/english/librispeech",
        "Reading Passages": "datasets/reading_passages/passages.json",
        "Label Templates": "datasets/labels/filipino/labels_template.csv",
        "Model Directory": "models"
    }
    
    print("Verifying dataset structure...\n")
    
    all_ok = True
    for name, path in checks.items():
        exists = os.path.exists(path)
        status = "✓" if exists else "✗"
        print(f"{status} {name}: {path}")
        if not exists:
            all_ok = False
    
    if all_ok:
        print("\n✓ All datasets ready!")
        print("\nDataset statistics:")
        print_statistics()
    else:
        print("\n✗ Some datasets missing. Please download them.")

def print_statistics():
    """Print dataset statistics"""
    
    # Count files in each directory
    stats = {}
    
    for root, dirs, files in os.walk("datasets"):
        audio_files = [f for f in files if f.endswith(('.wav', '.mp3', '.flac'))]
        if audio_files:
            stats[root] = len(audio_files)
    
    print("\nAudio files found:")
    for path, count in stats.items():
        print(f"  {path}: {count} files")

if __name__ == "__main__":
    verify_structure()
```

## 🚀 Quick Start Commands

Run these in order:

```bash
# 1. Create structure
python organize_datasets.py

# 2. Prepare passages
python prepare_passages.py

# 3. Create label templates
python create_labels.py

# 4. Download datasets (manual - follow instructions above)
# - Common Voice Filipino
# - LibriSpeech English

# 5. Verify everything
python verify_datasets.py

# 6. Start recording students (optional)
python record_students.py
```

## 📦 Download Summary

**What you'll have:**
- Common Voice: ~30 hours Filipino speech (2GB)
- LibriSpeech: ~100 hours English speech (6GB)
- Reading passages: 30+ texts (Filipino + English)
- Label templates: Ready for annotation
- Directory structure: Organized and ready

**Total download size:** ~8GB
**Total storage needed:** ~15GB (with processed files)

**Next:** See `MODEL_TRAINING_TUTORIAL.md` for training instructions!
