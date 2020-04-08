<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Database\Handler;

/**
 * Description of SecurityContextObjectAbstract
 *
 * @author pes2704
 */
trait SecurityContextObjectTrait {

    /**
     * Ochrana proti neúmyslnému zobrazení obsahu.
     * Objekt může obsahovat citlivá data, pro jistotu bráním jeko serializaci. Nevrací nic, nevyhazuje výjimku.
     * @return NULL
     */
    final public function serialize() {
        ;
    }

    /**
     * Zakázáno vytvoření instance deserializací. Nevrací nic, nevyhazuje výjimku.
     * @param string $serialized
     * @return NULL
     */
    final public function unserialize($serialized) {
        ;
    }

    /**
     * Zakázáno vytvoření instance klonováním.
     * @throws Exception
     */
    final public function __clone()
    {
        throw new \LogicException('Klonování třídy je zakázáno. Její vytváření závisí na bezpečnostním kontextu.');
    }

    final public function __sleep() {
        throw new \LogicException('Serializace třídy je zakázána. Její vytváření závisí na bezpečnostním kontextu.');
    }

    /**
     * Zakázána deserializace objektu.
     * @throws Exception
     */
    final public function __wakeup()
    {
        throw new \LogicException('Deserializace třídy je zakázána. Její vytváření závisí na bezpečnostním kontextu.');
    }
}
