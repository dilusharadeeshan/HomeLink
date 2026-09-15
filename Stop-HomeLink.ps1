$ErrorActionPreference='Stop'
$pidPath=Join-Path $PSScriptRoot '.runtime\server.pid'
$phpPath=Join-Path $PSScriptRoot 'tools\php\php.exe'
if(!(Test-Path -LiteralPath $pidPath)){Write-Output 'No HomeLink server was started by this launcher.';exit}
$savedProcessId=[int][IO.File]::ReadAllText($pidPath)
$serverProcess=Get-Process -Id $savedProcessId -ErrorAction SilentlyContinue
if($serverProcess -and $serverProcess.Path -eq $phpPath){
    $details=Get-CimInstance Win32_Process -Filter ("ProcessId = "+$savedProcessId)
    if($details.CommandLine -notlike '*127.0.0.1:8000*'){throw 'The saved process no longer matches HomeLink; refusing to stop it.'}
    Stop-Process -Id $savedProcessId
    Write-Output 'HomeLink stopped.'
}else{Write-Output 'HomeLink is already stopped.'}
