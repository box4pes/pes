<?php

namespace Pes\Bootstrap;

/**
 * Vstupní bod balíčku: načte procedurální {@see bootstrap/Bootstrap.php}.
 */
final class BootstrapEntry
{
    public static function bootstrapDirectory(): string
    {
        return \dirname(__DIR__) . '/bootstrap';
    }

    public static function load(): void
    {
        require_once self::bootstrapDirectory() . '/Bootstrap.php';
    }
}
