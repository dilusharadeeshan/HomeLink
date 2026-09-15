param([switch]$NoBrowser)
$ErrorActionPreference='Stop'
$projectRoot=$PSScriptRoot
$applicationPath=Join-Path $projectRoot 'homelink'
$phpPath=Join-Path $projectRoot 'tools\php\php.exe'
$runtimePath=Join-Path $projectRoot '.runtime'
$pidPath=Join-Path $runtimePath 'server.pid'
$serverUrl='http://127.0.0.1:8000'
if(!(Test-Path -LiteralPath $phpPath)){throw 'Portable PHP was not found. See homelink/docs/DEPLOYMENT.md.'}
if(!(Test-Path -LiteralPath (Join-Path $applicationPath 'public\build\manifest.json'))){throw 'Frontend build missing. Run npm install and npm run build in homelink.'}
[IO.Directory]::CreateDirectory($runtimePath)|Out-Null
$alreadyRunning=$false
if(Test-Path -LiteralPath $pidPath){
    $savedProcessId=[int][IO.File]::ReadAllText($pidPath)
    $savedProcess=Get-Process -Id $savedProcessId -ErrorAction SilentlyContinue
    if($savedProcess -and $savedProcess.Path -eq $phpPath){$alreadyRunning=$true}
}
if(!$alreadyRunning){
    $listener=Get-NetTCPConnection -LocalPort 8000 -State Listen -ErrorAction SilentlyContinue
    if($listener){throw 'Port 8000 is already in use. Stop that application or configure a different HomeLink port.'}
    $routerPath=Join-Path $applicationPath 'vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php'
    $serverProcess=Start-Process -FilePath $phpPath -ArgumentList @('-S','127.0.0.1:8000',('"' + $routerPath + '"')) -WorkingDirectory (Join-Path $applicationPath 'public') -WindowStyle Hidden -PassThru -RedirectStandardOutput (Join-Path $runtimePath 'server.log') -RedirectStandardError (Join-Path $runtimePath 'server-error.log')
    [IO.File]::WriteAllText($pidPath,[string]$serverProcess.Id)
}
$ready=$false
for($attempt=0;$attempt -lt 20;$attempt++){
    try { $response=Invoke-WebRequest -Uri ($serverUrl+'/up') -TimeoutSec 2; if($response.StatusCode -eq 200){$ready=$true;break} } catch { Start-Sleep -Milliseconds 250 }
}
if(!$ready){throw 'HomeLink did not start. See .runtime/server-error.log.'}
Write-Output ('HomeLink is running at '+$serverUrl)
if(!$NoBrowser){Start-Process $serverUrl}
