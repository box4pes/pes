<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Pes\Model\Entity;

/**
 *
 * @author pes2704
 */
interface SecurityPersistableEntityInterface extends PersistableEntityInterface {
    
    /**
     * Uloží Login name pro použití v jiném middleware a příštím requestu.
     * 
     * Akce jejichž stav byl zaznamenáván v dané entitě a také v v databázi je pak třeba provést v budoucnu, 
     * v middleware s přístupem k databázi s uloženými informacemi zavíslými na stavu.
     * 
     * @param type $loggedOffUserName
     */
    public function processActionsForLossOfSecurityContext($loggedOffUserName);
}
