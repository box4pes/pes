<?php

namespace Pes\Type;


/**
 * Description of ContextDataUsage
 * Specializovaný objekt pro logování ststusu Pes\Type\ContextDataInterface objektů.
 *
 * @author pes2704
 */
class ContextDataUsage {

    public function __construct(LoggerInterface $logger) {
        ;
    }
    /**
     * Transformuje status objektu a zapíše do logu užití dat objektu.
     * @param type $logFileName
     * @param Framework_Type_ContextData $contextData
     */
    public function logStatus($logFileName, ContextDataInterface $contextData) {
        $status = $contextData->getStatus();
        $warning = array();
        $notice = array();
        $debug = array();
        $info = array();
        $statusLogger = Framework_Logger_File::getInstance('Logs', $logFileName);
        $statusLogger->debug('ContextDataUsage: Historie volání proměnných kontextu '.$statusLogger->getLogFilePath().':');
        if ($status) {
            foreach ($status as $index=>$varStatus) {
                $history = implode(' / ', $varStatus);
                if (in_array($contextData::GET_NONEXISTING_VALUE, $varStatus)) {
                    $warning[] = $index.': '.$history;
                } elseif (in_array($contextData::IS_NONEXISTING_VALUE, $varStatus)) {
                    $notice[] = $index.': '.$history;
                } else {
                    $debug[] = $index.': '.$history;
                }
            }
        }
        foreach ($contextData as $index=>$value) {
            if (!$status OR !array_key_exists($index, $status)) {
                $info[] = $index.': not used';
            }
        }
        if ($warning) {
            $statusLogger->warning("ContextDataUsage: ".print_r($warning, TRUE));
        }
        if ($notice) {
            $statusLogger->notice("ContextDataUsage: ".print_r($notice, TRUE));
        }
        if ($debug) {
            $statusLogger->debug("ContextDataUsage: ".print_r($debug, TRUE));
        }
        if ($info) {
            $statusLogger->info("ContextDataUsage: ".print_r($info, TRUE));
        }
        if ($notice) {
            trigger_error('Proběhl dotaz na existenci neexistující proměnné kontextu. Log: '.$logFileName, E_USER_NOTICE);
        }
        if ($warning) {
            trigger_error('Proběhl pokus o použití neexistující proměnné kontextu. Log: '.$logFileName, E_USER_WARNING);
        }
    }
}
