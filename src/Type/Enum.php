<?php
namespace Pes\Type;

use Pes\Type\Exception\ValueNotInEnumException;

/**
 * Emuluje typ Enum (obdobně jako SplEnum)
 *
 * SplEnum: This » PECL extension is not bundled with PHP.
 *
 * <p>Použití - definice typu</p>
 * <code>
 * namespace Framework\Db;
 * use Framework\Type\Enum;
 * class DbType extends Enum { *
 *     const MySQL = 'mysql';
 *     const MSSQL = 'mssql';
 * }
 * </code>
 * <p>Vytvoření proměnné</p>
 * <code>
 * try {
 *     $dbType = new DbType();
 *     $msType = $dbType('mssql')   //OK, vrací řetězec 'mssql'
 *     $blaType = $dbType('bla'); // Vyhodí výjimku - za běhu
 * } catch (Pes\Type\Exception\ValueNotInEnumException $notInEnumExc) {
 *     echo $notInEnumExc->getMessage() . PHP_EOL;
 * }
 * </code>
 * <p>Bezpečné vytvoření proměnné (bez rizika vyhozené výjimky) - použití konstanty třídy</p>
 * <code>
 *     $dbType = new DbType();
 *     $msType = $dbType(DbType::MSSQL)   //OK, vrací hodnotu 'mssql'
 * </code>
 * <p>Test "if"</p>
 * <code>
 * //call:
 * $dbType = new DbType();
 * $someObject->someMethod($dbType, DbType::MySQL);
 * //test:
 * function someMethod(DbType $enum, $value) {
 *      if ($value == $enum($value)) {   };
 * }
 * </code>
 * @author pes2704
 */
abstract class Enum {

    private $constants;

    private $externalClassName;

    /**
     * Konstruktor má nepoviný parametr - jméno třídy obsahující definice konstant. To lze požít pro vytvoření typu Enum ze třídy, kterou nelze použít jako potomka
     * abstract class Enum. To může být případ objektu z jiné knihovny apod.
     *
     * @param type $externalClassName
     */
    public function __construct($externalClassName=null) {
        $this->externalClassName = $externalClassName;
    }

    public function __invoke($value) {
        $this->setConstants();
        $key = array_search($value, $this->constants);
        if ($key !== FALSE) {
            return $this->constants[$key];
        } else {
            if (is_scalar($value)) {
                throw new ValueNotInEnumException('Value is not in enum '. get_called_class().'. Value: ' . var_export($value, TRUE).'.');
            } else {
                throw new ValueNotInEnumException('Value is not in enum '. get_called_class().'. Value type: ' . gettype($value));
            }
        }
    }

    /**
     * Kompatibilata s SplEnum
     */
    public function getConstList() {
        $this->setConstants();
        return $this->constants;
    }

    /**
     * Pomocí reflexe získa konstnty třídy a nastaví vlastnost constants (jako asociativní pole)
     */
    private function setConstants() {
        if (!isset($this->constants)) {
            if (isset($this->externalClassName)) {
                $reflexCls = new \ReflectionClass($this->externalClassName);
            } else {
                $reflexCls = new \ReflectionClass(get_called_class());
            }
            $this->constants = $reflexCls->getConstants();
        }
    }
}