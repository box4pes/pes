<?php
namespace Pes\Validator;

use Pes\Validator\Exception\NotValidTypeException;
use Pes\Validator\Exception\TypeNotExistsException;
use Pes\Validator\Exception\TypeNameNotAStringException;

/**
 * Description of IsTypeValidator
 *
 * @author pes2704
 */
class IsObjectTypeValidator implements ValidatorInterface {
    private $type;

    /**
     *
     * @param string $type Jméno typu - jméno interface or class (FQDN - plné kvalifikobané jméno včetně namespace).
     * @throws \InvalidArgumentException
     */
    public function __construct($type) {
        if (is_string($type)) {
            if (interface_exists($type)) {
                $this->type = $type;
            } elseif (class_exists($type)) {
                $this->type = $type;
            } else {
                throw new TypeNotExistsException('Nenalezen zadaný typ (interface nebo class): '.$type);
            }
        } else {
            throw new TypeNameNotAStringException("Jméno typu musí být zadáno jako string.");
        }
    }

    public function validate($param): void {
        if (!($param instanceof $this->type)) {
            throw new NotValidTypeException();
        }
    }
}
