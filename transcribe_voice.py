import sys
import os
import io
import json
import soundfile as sf
import speech_recognition as sr
import tempfile
import numpy as np

# Force UTF-8 encoding for standard output on Windows
if sys.stdout.encoding != 'utf-8':
    try:
        sys.stdout.reconfigure(encoding='utf-8')
    except AttributeError:
        sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

def transcribe(audio_path):
    if not os.path.exists(audio_path):
        return {"ok": False, "error": "File not found"}

    wav_path = None
    try:
        # 1. Read audio with soundfile
        data, samplerate = sf.read(audio_path)
        
        # If stereo, convert to mono
        if len(data.shape) > 1 and data.shape[1] > 1:
            data = np.mean(data, axis=1)

        # 2. Write as 16kHz mono WAV for speech_recognition
        with tempfile.NamedTemporaryFile(suffix=".wav", delete=False) as tf:
            wav_path = tf.name
            sf.write(wav_path, data, samplerate, subtype='PCM_16')

        # 3. Recognize with SpeechRecognition
        recognizer = sr.Recognizer()
        with sr.AudioFile(wav_path) as source:
            audio_data = recognizer.record(source)

        # Try Bengali first
        text = None
        lang = "bn-BD"
        try:
            text = recognizer.recognize_google(audio_data, language="bn-BD")
        except sr.UnknownValueError:
            # Fallback to English
            try:
                text = recognizer.recognize_google(audio_data, language="en-US")
                lang = "en-US"
            except sr.UnknownValueError:
                return {"ok": False, "error": "Voice could not be recognized"}
        except sr.RequestError as e:
            return {"ok": False, "error": f"API request error: {str(e)}"}

        if not text:
            return {"ok": False, "error": "No speech detected"}

        return {
            "ok": True,
            "text": text,
            "lang": lang
        }

    except Exception as e:
        return {"ok": False, "error": str(e)}
    finally:
        if wav_path and os.path.exists(wav_path):
            try:
                os.remove(wav_path)
            except:
                pass

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"ok": False, "error": "Missing audio file argument"}))
        sys.exit(1)

    result = transcribe(sys.argv[1])
    print(json.dumps(result, ensure_ascii=True))
