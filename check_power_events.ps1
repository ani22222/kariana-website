$events = Get-WinEvent -FilterHashtable @{LogName='System'; Id=41,6008,1074} -MaxEvents 3 -ErrorAction SilentlyContinue
foreach ($e in $events) {
    $type = "Normal"
    if ($e.Id -eq 41 -or $e.Id -eq 6008) {
        $type = "PowerOutage/Crash"
    }
    Write-Output "$($e.Id)|$type|$($e.TimeCreated.ToString('yyyy-MM-dd HH:mm:ss'))"
}
