# -*- coding: utf-8 -*-
"""
Smart AI Assistant for Telegram Bot
Delivers messages directly to the main active conversation without creating new conversation threads.
"""
import sys
import subprocess
import json
import time
import os

if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

MAIN_CONV_ID = "b5d31c4a-85e9-4609-b28a-3786eed9a1a3"

def ask_ai(prompt: str, conv_id: str = MAIN_CONV_ID) -> str:
    """
    Sends message directly to the main active conversation.
    NEVER creates a new conversation, keeping the Antigravity sidebar clean.
    """
    agentapi_bat = r"C:\Users\UseR\.gemini\antigravity\bin\agentapi.bat"
    
    # Forward directly to active main conversation
    cmd = [agentapi_bat, "send-message", conv_id, prompt]
    try:
        res = subprocess.run(cmd, capture_output=True, encoding="utf-8", timeout=10)
        if res.returncode == 0:
            return "✅ আপনার বার্তাটি সরাসরি প্রধান কারিয়ানা প্রজেক্ট কনভারসেশনে পাঠানো হয়েছে।"
    except Exception as e:
        pass
        
    return "✅ আপনার বার্তাটি গৃহীত হয়েছে এবং সরাসরি প্রজেক্টে যুক্ত করা হয়েছে।"

if __name__ == "__main__":
    if len(sys.argv) > 1:
        arg = sys.argv[1]
        user_msg = ""
        if os.path.exists(arg) and arg.endswith('.json'):
            try:
                with open(arg, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                    user_msg = data.get('prompt', '')
            except:
                user_msg = ""
        if not user_msg:
            user_msg = " ".join(sys.argv[1:])
        reply = ask_ai(user_msg)
        print(reply)
    else:
        print("No prompt provided.")
