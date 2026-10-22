<?php
namespace Pes\Database\Handler\Mini;

/**
 * Description of HandlerFactory
 *
 * @author pes2704
 */
class HandlerMiniFactory implements HandlerMiniFactoryInterface {

    private $dsn;
    private $user;
    private $password;

    public function __construct(DsnInterface $dsn, string $user, string$password) {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
    }
    /**
     *
     * @return HandlerMini
     */
    public function get(): ?HandlerMini {
    try {
        // connect to database
            return new HandlerMini($this->dsn, $this->user, $this->password,
                    [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Nepodařilo se vytvořit db handler HandlerMini. '.$e->getMessage());
        }
    }


}
