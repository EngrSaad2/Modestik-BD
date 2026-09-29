# PowerShell script to deploy local changes to remote server.

param (
    [string]$IdentityFile = "C:\Users\Admin\.ssh\modestik_nopw",
    [string]$RemoteUser = "modestik",
    [string]$RemoteHost = "209.42.27.61",
    [string]$RemotePath = "public_html",
    [switch]$ForceAll
)

$ErrorActionPreference = "Stop"

# Local root directory (3 levels up from script location)
$LocalRoot = [System.IO.Path]::GetFullPath("$PSScriptRoot\..\..\..")
Write-Host "Local Root: $LocalRoot"

# Exclude patterns (regex)
$ExcludePatterns = @(
    "\\vendor\\",
    "\\node_modules\\",
    "\\.git\\",
    "\\.agents\\",
    "\\storage\\logs\\",
    "\\storage\\framework\\",
    "\\storage\\debugbar\\",
    "\\.env$",
    "\\.env\.",
    "\\.last_deploy$",
    "\\tests\\",
    "\\.editorconfig$",
    "\\.gitattributes$",
    "\\.gitignore$",
    "\\composer\\.lock$",
    "\\package-lock\\.json$"
)

# Determine last deploy time
$LastDeployFile = "$PSScriptRoot\.last_deploy"
$LastDeployTime = $null

if (!$ForceAll -and (Test-Path $LastDeployFile)) {
    $LastDeployTime = (Get-Item $LastDeployFile).LastWriteTime
    Write-Host "Checking for files modified since last deploy: $LastDeployTime"
} else {
    Write-Host "Deploying all files (excluding vendor/logs/etc.)."
}

# Find files
$FilesToDeploy = @()
Get-ChildItem -Path $LocalRoot -Recurse -File | ForEach-Object {
    $FilePath = $_.FullName
    
    # Check exclusions
    $Exclude = $false
    foreach ($Pattern in $ExcludePatterns) {
        if ($FilePath -match $Pattern) {
            $Exclude = $true
            break
        }
    }
    
    if (!$Exclude) {
        if ($null -eq $LastDeployTime -or $_.LastWriteTime -gt $LastDeployTime) {
            $FilesToDeploy += $_
        }
    }
}

if ($FilesToDeploy.Count -eq 0) {
    Write-Host "No files to deploy."
    exit 0
}

Write-Host "Found $($FilesToDeploy.Count) file(s) to deploy."

# Loop and upload
foreach ($File in $FilesToDeploy) {
    $RelativePath = $File.FullName.Substring($LocalRoot.Length + 1)
    $RemoteRelativePath = $RelativePath.Replace("\", "/")
    
    if ($RemoteRelativePath.Contains("/")) {
        $RemoteDir = $RemotePath + "/" + $RemoteRelativePath.Substring(0, $RemoteRelativePath.LastIndexOf("/"))
    } else {
        $RemoteDir = $RemotePath
    }
    
    Write-Host "Uploading: $RelativePath -> $RemoteDir/"
    
    # Create remote directory if not exists
    ssh -i $IdentityFile -o StrictHostKeyChecking=accept-new "$RemoteUser@$RemoteHost" "mkdir -p '$RemoteDir'"
    
    # Upload file
    scp -i $IdentityFile -o StrictHostKeyChecking=accept-new $File.FullName "$RemoteUser@$RemoteHost`:$RemoteDir/"
}

# Record timestamp
New-Item -Path $LastDeployFile -ItemType File -Force | Out-Null
Write-Host "Deploy complete!"
