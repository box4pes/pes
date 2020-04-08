<?php
/**
 * Je to nápad z Aury, můžu dát breakpoint ke každému volání PHP funkce. Přidal jsem také uložení návratové hodnoty do lokální proměnné.
 * 
 * Příklad:
 * místo standartního volání session_get_cookie_params(); 
 * volám 
 * (new Pes\Debug\Phpfunc())->session_get_cookie_params(); 
 * a nastavím breakpoint. 
 * 
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Pes\Debug;

/**
 *
 * Intercept calls to PHP functions.
 *
 *
 */
class Phpfunc
{
    /**
     *
     * Magic call to intercept any function pass to it.
     *
     * @param string $func The function to call.
     *
     * @param array $args Arguments passed to the function.
     *
     * @return mixed The result of the function call.
     *
     */
    public function __call($func, $args)
    {
        $ret = call_user_func_array($func, $args);
        return $ret;
    }
}
