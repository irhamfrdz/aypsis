$root = "C:\folder_kerjaan\aypsis"
$timestamp = (Get-Date).ToString('yyyyMMddHHmmss')
$trash = Join-Path $root "_trash\removed_tagihan_$timestamp"
New-Item -ItemType Directory -Force -Path $trash | Out-Null

$searchPatterns = @(
    Join-Path $root "scripts"
    Join-Path $root "tests"
    Join-Path $root "database\seeders"
    Join-Path $root "app\Console"
    Join-Path $root "resources\views"
)
$searchStrings = @('tagihan_kontainer_sewa','TagihanKontainerSewa')

Write-Output "Trash destination: $trash"

foreach ($base in $searchPatterns) {
    if (-not (Test-Path $base)) { continue }
    Get-ChildItem -Path $base -Recurse -File -ErrorAction SilentlyContinue | ForEach-Object {
        $file = $_.FullName
        try {
            $content = Get-Content -Raw -ErrorAction Stop $file
        } catch {
            $content = ''
        }
        $match = $false
        foreach ($s in $searchStrings) { if ($content -match [regex]::Escape($s)) { $match = $true; break } }
        if ($match) {
            $rel = $file.Substring($root.Length + 1)
            $destDir = Join-Path $trash (Split-Path $rel -Parent)
            New-Item -ItemType Directory -Force -Path $destDir | Out-Null
            $dest = Join-Path $destDir $_.Name
            if (Test-Path $dest) {
                $dest = $dest + '.' + $timestamp + '.bak'
            }
            Copy-Item -Path $file -Destination $dest -Force
            Remove-Item -Path $file -Force
            Write-Output "MOVED: $rel -> $dest"
        }
    }
}

Write-Output "Done. Please review folder: $trash"
