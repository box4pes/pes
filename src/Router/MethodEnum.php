<?php
/**
 * Emuluje enum typ DbType.
 *
 * @author pes2704
 */
namespace Pes\Router;

use Pes\Type\Enum;

class MethodEnum extends Enum {
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
    const OPTIONS = 'OPTIONS';
}
