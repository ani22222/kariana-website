Set WshShell = CreateObject("WScript.Shell")
WshShell.Run "powershell.exe -WindowStyle Hidden -ExecutionPolicy Bypass -File ""c:\xampp\htdocs\Kariana Website\autostart_boot.ps1""", 0, False
