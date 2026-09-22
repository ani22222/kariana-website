# -*- coding: utf-8 -*-
"""
Smart AI Assistant for Telegram Bot
Uses Antigravity fast flash_lite engine to generate immediate, warm, intelligent Bengali responses.
"""
import sys
import subprocess
import json
import time
import os

if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

def ask_ai(prompt: str, conv_title: str = "Telegram Assistant") -> str:
    system_instruction = (
        "আপনি একজন বিশ্বস্ত, অত্যন্ত দক্ষ এবং শ্রদ্ধাশীল ব্যক্তিগত এআই সহকারী (Personal AI Assistant)। "
        "ব্যবহারকারী মোবাইল থেকে বিছানায় শুয়ে আরাম করছেন। "
        "তাঁর প্রতিটি বার্তা বা প্রশ্ন গুরুত্বের সাথে শুনুন এবং অত্যন্ত আন্তরিক, শ্রদ্ধাশীল ও সাবলীল বাংলায় সরাসরি উত্তর দিন। "
        "যদি তিনি কোনো ক্ষোভ প্রকাশ করেন বা বলেন উত্তর দেওয়া হয়নি, তাকে আশ্বস্ত করুন এবং স্পষ্ট উত্তর দিন। "
        "উত্তরটি ২-৪ বাক্যের মধ্যে স্পষ্ট এবং বন্ধুত্বপূর্ণ রাখুন। কোনো কৃত্রিম টেমপ্লেট ব্যবহার করবেন না।"
    )
    full_prompt = f"{system_instruction}\n\nব্যবহারকারীর বার্তা: {prompt}"
    
    agentapi_bat = r"C:\Users\UseR\.gemini\antigravity\bin\agentapi.bat"
    brain_dir = r"C:\Users\UseR\.gemini\antigravity\brain"
    
    cmd = [agentapi_bat, "new-conversation", "--model=flash_lite", f"--title={conv_title}", full_prompt]
    try:
        res = subprocess.run(cmd, capture_output=True, encoding="utf-8", timeout=12)
        if res.returncode != 0:
            return ""
        data = json.loads(res.stdout)
        conv_id = data.get("response", {}).get("newConversation", {}).get("conversationId", "")
        if not conv_id:
            return ""
        
        transcript_file = os.path.join(brain_dir, conv_id, ".system_generated", "logs", "transcript.jsonl")
        for _ in range(16):  # poll up to 8 seconds (0.5s intervals)
            time.sleep(0.5)
            if os.path.exists(transcript_file):
                with open(transcript_file, "r", encoding="utf-8", errors="ignore") as f:
                    for line in f:
                        line = line.strip()
                        if not line:
                            continue
                        try:
                            j = json.loads(line)
                            if j.get("source") == "MODEL" and j.get("content"):
                                return j["content"].strip()
                        except:
                            continue
    except Exception as e:
        return ""
    return ""

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
        if reply:
            print(reply)
        else:
            print("আমি আপনার বার্তাটি পেয়েছি এবং সবসময় আপনার সহায়তায় প্রস্তুত আছি।")
    else:
        print("No prompt provided.")
