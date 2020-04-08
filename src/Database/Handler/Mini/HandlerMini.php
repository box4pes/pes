<?php
namespace Pes\Database\Handler\Mini;

class HandlerMini extends \PDO implements HandlerMiniInterface {

    /**
     * @var DsnInterface
     */
    private $dsn;

    public function __construct(DsnInterface $dsn, $user, $password, array $options=NULL) {
        $this->dsn = $dsn;
        parent::__construct($dsn->getDsnString(), $user, $password, $options);
    }

    /**
     * Metoda mění adapter na kombinaci adapteru a wrapperu. Pro metody implementované v této třídě se objekt chová jako adapter,
     * volá se implementovaná metoda třídy. Pro neimplementované metody se volá metoda "obaleného" objektu, v tomto případě tedy metoda PDO.
     * @param type $method
     * @param array $arguments
     * @return type
     */
    public function __call($method, array $arguments )
    {
        return \call_user_func_array(array($this, $method), $arguments);
    }

   /**
    *
    * @return DsnInterface
    */
    public function getDsn() {
        return $this->dsn;
    }

}
