$action = New-ScheduledTaskAction -Execute 'php' -Argument "sync.php" -WorkingDirectory "c:\Users\SERVER SANS\SANS-PROJECT\zk-absensi\local-sync"
$trigger = New-ScheduledTaskTrigger -Once -At (Get-Date).AddMinutes(1) -RepetitionInterval (New-TimeSpan -Minutes 5) -RepetitionDuration (New-TimeSpan -Days 3650)
$settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -DontStopOnIdleEnd -ExecutionTimeLimit (New-TimeSpan -Days 3650)

# Register the task for the current user
Register-ScheduledTask -TaskName "ZKTeco_Sync_Service" -Action $action -Trigger $trigger -Settings $settings -Force
