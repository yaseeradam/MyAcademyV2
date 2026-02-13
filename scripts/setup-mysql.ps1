param(
    [Parameter(Mandatory = $true)]
    [string]$RootUser,

    [Parameter(Mandatory = $true)]
    [string]$RootPassword,

    [Parameter(Mandatory = $false)]
    [string]$MySqlExe,

    [Parameter(Mandatory = $false)]
    [string]$DbHost,

    [Parameter(Mandatory = $false)]
    [int]$DbPort,

    [Parameter(Mandatory = $false)]
    [string]$Database,

    [Parameter(Mandatory = $false)]
    [string]$AppUser,

    [Parameter(Mandatory = $true)]
    [string]$AppPassword
)

$ErrorActionPreference = "Stop"

$MySqlExe = if ($MySqlExe) { $MySqlExe } else { $env:MYACADEMY_MYSQL }
$DbHost = if ($DbHost) { $DbHost } else { "127.0.0.1" }
$DbPort = if ($DbPort -gt 0) { $DbPort } else { 3306 }
$Database = if ($Database) { $Database } else { "myacademy" }
$AppUser = if ($AppUser) { $AppUser } else { "myacademy" }

if (-not $MySqlExe -or -not (Test-Path $MySqlExe)) {
    $default = "C:\Program Files\MySQL\MySQL Server 9.5\bin\mysql.exe"
    if (Test-Path $default) {
        $MySqlExe = $default
    } else {
        throw "mysql.exe not found. Set MYACADEMY_MYSQL env var or pass -MySqlExe."
    }
}

$dbNameOk = $Database -match '^[A-Za-z0-9_]+$'
if (-not $dbNameOk) {
    throw "Invalid -Database value. Use only letters/numbers/underscore."
}

$appUserOk = $AppUser -match '^[A-Za-z0-9_]+$'
if (-not $appUserOk) {
    throw "Invalid -AppUser value. Use only letters/numbers/underscore."
}

function Escape-MySqlString([string]$s) {
    return $s.Replace("'", "''")
}

$appPasswordSql = Escape-MySqlString $AppPassword
$sql = @"
CREATE DATABASE IF NOT EXISTS $Database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$AppUser'@'localhost' IDENTIFIED BY '$appPasswordSql';
CREATE USER IF NOT EXISTS '$AppUser'@'%' IDENTIFIED BY '$appPasswordSql';
GRANT ALL PRIVILEGES ON $Database.* TO '$AppUser'@'localhost';
GRANT ALL PRIVILEGES ON $Database.* TO '$AppUser'@'%';
FLUSH PRIVILEGES;
"@

Write-Host "Creating DB/user in MySQL..." -ForegroundColor Cyan
& $MySqlExe "--host=$DbHost" "--port=$DbPort" "--user=$RootUser" "--password=$RootPassword" "--protocol=tcp" "--execute=$sql"

Write-Host "Updating .env to use MySQL..." -ForegroundColor Cyan
$envPath = Join-Path (Resolve-Path ".") ".env"
$script:envText = Get-Content $envPath -Raw

function Set-EnvLine([string]$key, [string]$value) {
    if ($script:envText -match "(?m)^$([regex]::Escape($key))=") {
        $script:envText = [regex]::Replace($script:envText, "(?m)^$([regex]::Escape($key))=.*$", "$key=$value")
    } else {
        $script:envText = $script:envText.TrimEnd() + "`r`n$key=$value`r`n"
    }
}

Set-EnvLine "DB_CONNECTION" "mysql"
Set-EnvLine "DB_HOST" $DbHost
Set-EnvLine "DB_PORT" $DbPort
Set-EnvLine "DB_DATABASE" $Database
Set-EnvLine "DB_USERNAME" $AppUser
Set-EnvLine "DB_PASSWORD" $AppPassword

Set-Content $envPath -Value $script:envText -Encoding UTF8

Write-Host "Running migrations..." -ForegroundColor Cyan
php artisan config:clear | Out-Null
php artisan migrate --force

Write-Host "Done. App now uses MySQL." -ForegroundColor Green
