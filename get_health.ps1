$os = Get-CimInstance Win32_OperatingSystem
$up = (Get-Date) - $os.LastBootUpTime
$rt = [Math]::Round($os.TotalVisibleMemorySize / 1MB, 1)
$rf = [Math]::Round($os.FreePhysicalMemory / 1MB, 1)
$ru = [Math]::Round($rt - $rf, 1)
$d = Get-CimInstance Win32_LogicalDisk -Filter "DeviceID='C:'"
$df = [Math]::Round($d.FreeSpace / 1GB, 1)
$dt = [Math]::Round($d.Size / 1GB, 1)
$c = (Get-CimInstance Win32_Processor | Measure-Object -Property LoadPercentage -Average).Average
Write-Output "$c|$ru|$rt|$df|$dt|$($up.Days)d $($up.Hours)h $($up.Minutes)m"
