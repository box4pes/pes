<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Pes\Database\Handler;

use Psr\Log\LoggerAwareInterface;

/**
 *
 * @author pes2704
 */
interface HandlerPDOAdapterInterface extends LoggerAwareInterface {
    
    /**
     * Vrací objekt Logger nastavený v konstruktoru. Umožňuje přidat zprávy do logu, který je vytvářen objektem Handler.
     * 
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface; 
}
