<?php
namespace Pes\Validator;

use Pes\Validator\Exception\NotInvocableException;

/**
 * Description of IsInvocableValidator
 * Ověřuje, zda parametr je volatelný jako anonymní funkce. Validní jsou callable proměnné, Closure objekty a objekty,
 * které implementují metodu __invoke().
 *
 *
 * @author pes2704
 */
class IsInvocableValidator implements ValidatorInterface {

    /**
     * Ověřuje, zda parametr je volatelný jako anonymní funkce. Validní jsou callable proměnné, Closure objekty a objekty,
     * které implementují metodu __invoke().
     *
     * Metoda vždy testuje, zda parametr má metodu __invoke(). V PHP jsou anonymní funkce implementovány jako Closure objekty, Closure objekty
     * mají pro kompatibilitu s ostatními "invocable" objekty metodu __invoke a samozřejmě ostatní invocable objekty musí mít metodu __invoke().
     * @param type $param
     * @return type
     */
    public function validate($param) {
        // http://php.net/manual/en/class.closure.php Besides the methods listed here, this class also has an __invoke method.
        // This is for consistency with other classes that implement calling magic, as this method is not used for calling the function.
        if( ! method_exists($param, '__invoke')) {
            throw new NotInvocableException("Object is not invocable");
        }
    }
}
