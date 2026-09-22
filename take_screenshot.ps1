Add-Type -AssemblyName System.Windows.Forms
Add-Type -AssemblyName System.Drawing

$screen = [System.Windows.Forms.Screen]::PrimaryScreen
$bounds = $screen.Bounds
$bmp = New-Object System.Drawing.Bitmap $bounds.Width, $bounds.Height
$graphics = [System.Drawing.Graphics]::FromImage($bmp)
$graphics.CopyFromScreen($bounds.Location, [System.Drawing.Point]::Empty, $bounds.Size)

$dest = "c:\xampp\htdocs\Kariana Website\storage\logs\pc_screenshot.png"
if (!(Test-Path "c:\xampp\htdocs\Kariana Website\storage\logs")) {
    New-Item -ItemType Directory -Path "c:\xampp\htdocs\Kariana Website\storage\logs" -Force | Out-Null
}
$bmp.Save($dest, [System.Drawing.Imaging.ImageFormat]::Png)
$graphics.Dispose()
$bmp.Dispose()
Write-Host "Screenshot saved: $dest"
