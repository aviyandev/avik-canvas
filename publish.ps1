# Simple auto-publish script for Avik packages
# Repo name = current directory name
# Version = fixed v1.0.0

$GitHubUser = "aviyandev"
$Version = "v1.0.0"

# Get repo name from current directory
$RepoName = Split-Path -Leaf (Get-Location)

Write-Host "🚀 Publishing $RepoName ($Version)" -ForegroundColor Cyan

# Init git if not exists
if (-not (Test-Path ".git")) {
    git init
    git branch -M main
}

# Add remote if missing
$remotes = git remote 2>$null
if ($remotes -notcontains "origin") {
    git remote add origin "https://github.com/$GitHubUser/$RepoName.git"
}

# Commit (safe if nothing to commit)
git add .
git commit -m "Initial release: $RepoName" 2>$null

# Pull remote changes (LICENSE, README)
git pull origin main --allow-unrelated-histories 2>$null

# Push code
git push -u origin main

# Tag (fixed version)
git tag $Version 2>$null
git push origin $Version 2>$null

Write-Host "✅ Done: $RepoName published successfully" -ForegroundColor Green
