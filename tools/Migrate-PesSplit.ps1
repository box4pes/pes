<#
.SYNOPSIS
  Replaces legacy Pes namespaces after the split (master_php8 -> split).

.DESCRIPTION
  String replacements for every namespace rename between branches master_php8 and split.
  Does not change namespaces that only moved into Composer packages (Http, Container, Router,
  Session, Logger, Database, Query, most of View, Application\App, etc.).

  Default is dry run: prints paths that would change; no writes.
  Use -Commit to write files as UTF-8 without BOM.

  Prefix order: longer/more specific paths first (see $map).

  Afterward, still do manually:
  - composer require pes/pes-* (or path/VCS repositories)
  - Bootstrap: procedural includes -> \Pes\Bootstrap\BootstrapEntry::load() or vendor bootstrap path
  - ComparatorInterface: Order type -> iterable $order
  - HtmlDocument::includeDocument() if used
  - Pes\Utils\Exception\InvalidArgumentException and UtilsExceptionInterface were removed (use \InvalidArgumentException or pes-core equivalents)

.PARAMETER Root
  Directory tree to scan (default: current location).

.PARAMETER Commit
  When set, changed files are written to disk.

.PARAMETER ShowMap
  Print namespace prefix map and exit (no scan).

.EXAMPLE
  .\tools\Migrate-PesSplit.ps1 -Root "C:\work\myapp\src"

.EXAMPLE
  .\tools\Migrate-PesSplit.ps1 -Root "C:\work\myapp\src" -Commit

.NOTES
  Map derived from: git diff master_php8..split -U0 -- '*.php' (namespace declarations).
#>
param(
    [Parameter(Mandatory = $false)]
    [string] $Root = (Get-Location).Path,

    [Parameter(Mandatory = $false)]
    [switch] $Commit,

    [Parameter(Mandatory = $false)]
    [switch] $ShowMap
)

$ErrorActionPreference = 'Stop'

$utf8NoBom = New-Object System.Text.UTF8Encoding $false

$extensions = @(
    '.php', '.phtml', '.twig', '.neon', '.xml', '.md', '.json'
)

# master_php8 -> split; longest prefix first
$map = [ordered]@{
    'Pes\Middleware\Exception\' = 'Pes\Application\Middleware\Exception\'
    'Pes\Middleware\'             = 'Pes\Application\Middleware\'
    'Pes\Collection\Normalizer\'  = 'Pes\Core\Collection\Normalizer\'
    'Pes\Collection\'             = 'Pes\Core\Collection\'
    'Pes\Comparator\'             = 'Pes\Core\Comparator\'
    'Pes\Document\'               = 'Pes\View\Document\'
    'Pes\Dom\'                    = 'Pes\View\Dom\'
    'Pes\Slot\'                   = 'Pes\View\Slot\'
    'Pes\Security\'               = 'Pes\Core\Security\'
    'Pes\Text\'                   = 'Pes\Core\Text\'
    'Pes\Type\Exception\'         = 'Pes\Core\Type\Exception\'
    'Pes\Type\'                   = 'Pes\Core\Type\'
    'Pes\Utils\Exception\'        = 'Pes\Core\Directory\Exception\'
    'Pes\Utils\'                  = 'Pes\Core\Directory\'
    'Pes\Validator\Exception\'    = 'Pes\Core\Validator\Exception\'
    'Pes\Validator\'              = 'Pes\Core\Validator\'
    'Pes\Debug\'                  = 'Pes\Core\Debug\'
}

if ($ShowMap) {
    foreach ($key in $map.Keys) {
        Write-Host ("{0} -> {1}" -f $key, $map[$key])
    }
    exit 0
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
Write-Host 'Manual follow-up: composer packages, BootstrapEntry::load(), Comparator (iterable), HtmlDocument::includeDocument(), removed Utils exception types.'
