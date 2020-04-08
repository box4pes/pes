<?php
namespace Pes\Logger;

use Psr\Log\AbstractLogger;
use Pes\Text\Template;

/**
 * Logger loguje do paměti (zapisuje do pole, které je svlastností instance).
 * @author Petr Svoboda
 */
class ArrayLogger extends AbstractLogger
{
	private $log = array();

        /**
         * Metoda vrací obsah logu ve formě pole. Každá položka pole obsahuje jeden zápis do logu. Každá položka obsahuje to,
         * co do ní bylo zapsáno. Pokud byly zapsány proměnné různých typů, obsahují položky proměnné těch typů, které byly zapsány.
         * @return array
         */
	public function getLogArray() {
		return $this->log;
	}

        /**
         * Metoda vrací obsah logu ve formě textu. Pro převod obsahu do textu je používána funkce print_r().
         * @return string
         */
	public function getLogText() {
		return implode(PHP_EOL, $this->log);
	}

        /**
         * Magická metoda. Umožňuje například předávat objekt loggeru jako proměnnou do kontextu View - pak dojde k volání této metody
         * obvykle až když dochází k převodu view a proměnných kontextu na string. To se v Pes view obvykle dějě až na konci běhu skriptu nebo při
         * vytváření bydy responsu a v té době již log obsahuje údaje zapsané v průběhu běhu skriptu.
         *
         * @return string
         */
        public function __toString() {
            return $this->getLogText();
        }

        /**
         * Zápis jednoho záznamu do logu. Metoda přijímá argumenty, které lze převést do čitelné podoby.
         *
         * @param mixed $level
         * @param string $message
         * @param array $context
         * @return null
         */
        public function log($level, $message, array $context = array()) {
            $completedMessage = isset($context) ? Template::interpolate($message, $context) : $message;
            $completedMessage = preg_replace("/\r\n|\n|\r/", PHP_EOL.self::ODSAZENI, $completedMessage);  //odsazení druhé a dalších řádek víceřádkového message
            $this->log[] = '['.$level.'] '.$completedMessage.PHP_EOL;
	}

    /**
     * Použije $message jako šablonu a nahradí slova ve složených závorkách hodnotami pole $context s klíčem rovným nahrazovanému slovu.
     */
    private function interpolate($message, array $context = array()) {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}

