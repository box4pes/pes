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
     * Provede akce pro smazání informací závislých na security kontextu. Uloží login name uživatele, který se právě odhlašuje pro použití v příštím requestu.
     * 
     * Akce jejichž stav byl zaznamenáván v databázi je pak třeba provést v budoucnu, 
     * v middleware s přístupem k databázi s uloženými informacemi zavíslými na stavu.
     * 
     * @param string|null $loggedOffUserName
     */
    public function processActionsForLossOfSecurityContext(?string $loggedOffUserName=null);
}
