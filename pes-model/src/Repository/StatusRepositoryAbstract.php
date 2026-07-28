<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\Repository;

use Pes\Model\Dao\StatusDao;

use Pes\Model\Entity\EntityInterface;
use Pes\Model\Exception\SessionFinishedException;

use LogicException;

/**
 * StatusRepositoryAbstract má metody pro zápis (aktualizaci) dat v session a destruktor, který zajišťuje automatické uložení (aktualizaci)
 * dat v session při zániku objektu.
 *
 * Po StatusDao::finish() je session uzavřená: get/add/remove/load/flush vyhodí SessionFinishedException.
 * Pro read-only snapshot po finish() použijte isFinished() + getClone().
 *
 * @author pes2704
 */
abstract class StatusRepositoryAbstract {

    /**
     * @var StatusDao
     */
    protected $statusDao;

    private static $loadedFragment = [];   // proměnná společná pro všechny StatusRepository

    protected $entity;

    public function __construct(StatusDao $statusDao) {
        $this->statusDao = $statusDao;
    }

    public function isFinished(): bool {
        return $this->statusDao->isFinished();
    }

    /**
     * Vrátí klon entity držené v paměti repo — bez vazby na session fragment.
     * Funguje i po StatusDao::finish(); nečte ani nezapisuje session.
     *
     * @throws LogicException pokud entita ještě nebyla načtena
     */
    public function getClone(): EntityInterface {
        if (!isset($this->entity) || $this->entity === null) {
            throw new LogicException(sprintf(
                '%s::getClone() — no entity loaded; call get() before StatusDao::finish().',
                static::class
            ));
        }
        return clone $this->entity;
    }

    /**
     * Striktní režim: po finish() nelze číst/zapisovat přes session.
     */
    protected function assertSessionWritable(): void {
        if ($this->statusDao->isFinished()) {
            throw new SessionFinishedException(sprintf(
                'Cannot use %s after StatusDao::finish(); session is closed.',
                static::class
            ));
        }
    }

    protected function load() {
        $this->assertSessionWritable();
        if (empty($_SESSION)) {
            throw new LogicException("Nejsou data v globálním poli \$_SESSION. Session v tomto běhu skriptu ještě nebyla spuštěna");
        }

        if (!isset(self::$loadedFragment[static::FRAGMENT_NAME])) {
            $row = $this->statusDao->get(static::FRAGMENT_NAME);
            if ($row) {
                $this->entity = $row[0];
                  // tato situace nastává při změně třídy nebo přejmenování namespace objektu entity
                  // po deserializaci vznikne __PHP_Incomplete_Class
                // pak se všem uživatelům, kteří přistupovali k webu před změnou kódu na serveru objeví chyba
                // obvykle FATAL Error - návratová hodnota repo->get musí být ... ale je __PHP_Incomplete_Class
                if (!($this->entity instanceof EntityInterface)) {  // tato situace nastává při změně třídy nebo přejmenování namespace objektu entity - vznikne __PHP_Incomplete_Class
                    $this->entity = null;
                }
            }
            self::$loadedFragment[static::FRAGMENT_NAME] = true;
        }
    }

    public function flush(): void {
        // Bez pending zápisu: po finish() (např. UnlockStatus) nesmí __destruct padat.
        if (!isset(self::$loadedFragment[static::FRAGMENT_NAME])) {   // pokud není loaded -> není entita
            return;
        }
        $this->assertSessionWritable();
        if ($this->entity) {
            $this->statusDao->set(static::FRAGMENT_NAME, [$this->entity]);
        } else {
            $this->statusDao->delete(static::FRAGMENT_NAME);
        }
        // smaže fragment
        unset(self::$loadedFragment[static::FRAGMENT_NAME]);
    }

    public function __destruct() {
        $this->flush();
    }
}
