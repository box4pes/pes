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
 * Po StatusDao::finish() je session uzavřená: get/add/remove/load vyhodí SessionFinishedException.
 * flush() po finish() je no-op (kvůli __destruct u cascade requestů).
 * Pro read-only snapshot po finish() použijte isFinished() + getClone().
 * Pro mutable snapshot (např. flash consume) použijte getClone(false) + replaceEntityInMemory() a po reopen() flush().
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
     * @param bool $immutable true = makeImmutable() (default); false = mutable clone (např. flash getMessages)
     * @throws LogicException pokud entita ještě nebyla načtena
     */
    public function getClone(bool $immutable = true): EntityInterface {
        if (!isset($this->entity) || $this->entity === null) {
            throw new LogicException(sprintf(
                '%s::getClone() — no entity loaded; call get() before StatusDao::finish().',
                static::class
            ));
        }
        $clone = clone $this->entity;
        if ($immutable && method_exists($clone, 'makeImmutable')) {
            $clone->makeImmutable();
        }
        return $clone;
    }

    /**
     * Nahradí entitu v paměti repo (bez zápisu do session).
     * Použití po mutate getClone(false) — např. spotřebované flash messages — před reopen()+flush().
     */
    public function replaceEntityInMemory(EntityInterface $entity): void {
        $this->entity = $entity;
        self::$loadedFragment[static::FRAGMENT_NAME] = true;
    }

    /**
     * Po StatusDao::finish() nelze volat get() přes session.
     * Pro read-only snapshot použijte getClone() (entita musí být načtená před finish()).
     */
    protected function assertSessionWritableForGet(): void {
        if ($this->statusDao->isFinished()) {
            throw new SessionFinishedException(sprintf(
                '%s::get() failed: session is closed after StatusDao::finish(). '
                . 'For read-only access use %s::getClone() (entity must already be loaded before finish()). '
                . 'Otherwise remove/move the code that calls StatusDao::finish() (e.g. UnlockStatus) so session stays open for this request.',
                static::class,
                static::class
            ));
        }
    }

    /**
     * Po StatusDao::finish() nelze volat metody měnící / znovu načítající session fragment.
     *
     * @param string $methodName jméno volané metody (get se řeší assertSessionWritableForGet)
     */
    protected function assertSessionWritable(string $methodName): void {
        if ($this->statusDao->isFinished()) {
            throw new SessionFinishedException(sprintf(
                '%s::%s() failed: session is closed after StatusDao::finish(). '
                . 'This method needs a writable session. '
                . 'Remove/move the code that calls StatusDao::finish() (e.g. UnlockStatus) so session stays open for this request, '
                . 'or avoid calling %s::%s() after finish().',
                static::class,
                $methodName,
                static::class,
                $methodName
            ));
        }
    }

    protected function load() {
        $this->assertSessionWritable('load');
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
        // Cascade finish bez reopen: nelze zapisovat. __destruct volá flush() — nesmí vyhodit výjimku.
        // Po reopen() je session zase writable a flush zapíše in-memory změny (flash / presenteddriver).
        if ($this->statusDao->isFinished()) {
            unset(self::$loadedFragment[static::FRAGMENT_NAME]);
            return;
        }
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
