<#
.SYNOPSIS
  Replaces legacy Pes namespaces after the split (Comparator, Document, Slot, Debug).

.DESCRIPTION
  Default is dry run: prints paths that would change; no writes.
  Use -Commit to write files as UTF-8 without BOM.

  Afterward, still do manually: composer packages, Bootstrap entry path or BootstrapEntry::load(),
  ComparatorInterface (iterable), HtmlDocument::includeDocument().

.PARAMETER Root
  Directory tree to scan (default: current location).

.PARAMETER Commit
  When set, changed files are written to disk.

.EXAMPLE
  .\tools\Migrate-PesSplit.ps1 -Root "C:\work\myapp\src"

.EXAMPLE
  .\tools\Migrate-PesSplit.ps1 -Root "C:\work\myapp\src" -Commit
#>
param(
    [Parameter(Mandatory = $false)]
    [string] $Root = (Get-Location).Path,

    [Parameter(Mandatory = $false)]
    [switch] $Commit
)

$ErrorActionPreference = 'Stop'

$utf8NoBom = New-Object System.Text.UTF8Encoding $false

$extensions = @(
    '.php', '.phtml', '.twig', '.neon', '.xml', '.md', '.json'
)

# Pořadí: nejdřív specifičtější prefixy
$map = [ordered]@{
    'Pes\Comparator\' = 'Pes\Core\Comparator\'
    'Pes\Document\'   = 'Pes\View\Document\'
    'Pes\Slot\'       = 'Pes\View\Slot\'
    'Pes\Debug\'      = 'Pes\Core\Debug\'
}

function Test-ShouldSkipPath {
    param([string] $FullPath)
    $p = $FullPath -replace '\\', '/'
    return $p -match '/(vendor|node_modules|\.git)/'
}

if (-not (Test-Path -LiteralPath $Root -PathType Container)) {
    throw "Root is not a directory or does not exist: $Root"
}

$files = Get-ChildItem -LiteralPath $Root -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object {
        -not (Test-ShouldSkipPath $_.FullName) -and
        ($extensions -contains $_.Extension.ToLowerInvariant())
    } |
    Sort-Object FullName -Unique

$changed = 0
foreach ($f in $files) {
    $text = [System.IO.File]::ReadAllText($f.FullName)
    $orig = $text
    foreach ($key in $map.Keys) {
        $text = $text.Replace([string]$key, [string]$map[$key])
    }
    if ($text -ceq $orig) { continue }

    Write-Host "[changed] $($f.FullName)"
    $changed++
    if ($Commit) {
        [System.IO.File]::WriteAllText($f.FullName, $text, $utf8NoBom)
    }
}

Write-Host ""
Write-Host ("Done. Files with replacements: {0}; wrote to disk: {1}" -f $changed, $Commit.IsPresent)
if (-not $Commit) {
    Write-Host "Tip: re-run with -Commit to save changes."
}
Write-Host 'Manual follow-up: composer require, Bootstrap path or \Pes\Bootstrap\BootstrapEntry::load(), Comparator (iterable), HtmlDocument::includeDocument().'
