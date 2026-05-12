<?php
namespace Pes\Database\Handler\Mini;

class HandlerMini extends \PDO implements HandlerMiniInterface {

    /**
     * @var DsnInterface
     */
    private $dsn;

    private $connection;


    public function __construct(DsnInterface $dsn, $user, $password, ?array $options = null) {
        $this->dsn = $dsn;
        $this->connection = \PDO::connect($dsn->getDsnString(), $user, $password, $options);
    }

    /**
     * Metoda mění adapter na kombinaci adapteru a wrapperu. Pro metody implementované v této třídě se objekt chová jako adapter,
     * volá se implementovaná metoda třídy. Pro neimplementované metody se volá metoda "obaleného" objektu, v tomto případě tedy metoda PDO.
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call(string$method, array $arguments )
    {
        return \call_user_func_array([$this->connection, $method], $arguments);
    }

   /**
    *
    * @return DsnInterface
    */
    public function getDsn() {
        return $this->dsn;
    }

}
