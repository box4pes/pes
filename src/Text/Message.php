<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Text;

use Pes\Text\Template;
use Psr\Log\LoggerInterface;

use LogicException;

/**
 * Description of T
 *
 * @author pes2704
 */
class Message {

//    const DEFAULT_SOURCE_LANGUAGE = 'en-US';
//    const DEFAULT_APP_LANGUAGE = 'en-US';

    private static $appLocale;
    private static $internalErrorWarning = true;

    /**
     * @var LoggerInterface
     */
    private static $logger;

    private static $translations =
            [
            // pes
                "Není překlad pro message: {text}" => [],
                "Message: Není nastaveno povinné locale metodou setAppLocale()." => [],
                "Message: Není překlad pro message: {text} a nastavený jazyk {jazyk}." => []
            ];

    /**
     * Přijímá kód locale. Akceptuje krátkou i dlouhou verzi (např. 'cs' i 'cs-CZ', 'en' i 'en-US', ale pro nastavení atributu appLocale používá vždy jen krátkou verzi, tedy jazyk
     * (první část zadaného parametru - npř. je 'en' nebo 'cs').
     *
     * Interně používá PHP třídu Locale. Locale potřebuje mít povoleno rozšíření intl (viz php.ini).
     *
     * @param string $appLocale
     */
    public static function setAppLocale($appLocale) {
        // \Locale potřebuje mít povoleno rozšíření intl (php.ini)
//        self::$appLocale = explode('-', $appLocale)[0];
        if (extension_loaded('intl')) {
            throw new LogicException("Metoda Message::setAppLocale() potřebuje mít povoleno rozšíření intl (php.ini).");
        }
            self::$appLocale = \Locale::getPrimaryLanguage($appLocale);
    }

    public static function setLogger(LoggerInterface $logger) {
        self::$logger = $logger;
    }

    /**
     *
     * @param type $translations
     */
    public static function addTranslations($translations) {
        self::$translations = array_merge_recursive(self::$translations, $translations);
    }


    /**
     * Vyhledá šablonu překladu k textu zadanému jako parametr. Nalezenou šablonu převede s použitím Pes\Text\Template::interpolate() metody.
     * Překlady tedy mohou být zadány jako přímý text nebo mohou používat spaceholdery, které doplněny vždy při překladu v této metodě. Hodnoty, kterými
     * budou nahrazeny spaceholdery je možno zadat jako nepovinný parametr $context.
     *
     *
     * @param type $text Text
     * @param array $context Pole hodnot pro náhradu spaceholderů
     * @return string
     */
    public static function t($text, array $context = []) {
                if (array_key_exists($text, self::$translations)) {
                    if (isset(self::$appLocale)) {
                        if (array_key_exists(self::$appLocale, self::$translations[$text])) {
                            $template =  self::$translations[$text][self::$appLocale];
                        } else {
                            if (self::$internalErrorWarning) {
                                self::$internalErrorWarning = false;   //není třeba psát dokola a navíc by zde došlo k zacyklení metody t
                                if (self::$logger) {
                                    self::$logger->warning("Message: Není překlad pro message: {text} a nastavený jazyk {jazyk}.", ['text'=>$text, 'jazyk'=>self::$appLocale]);
                                }
                                user_error(Message::t("Message: Není překlad pro message: {text} a nastavený jazyk {jazyk}.", ['text'=>$text, 'jazyk'=>self::$appLocale]), E_USER_NOTICE);
                            }
                            $template = $text;
                            self::$internalErrorWarning = true;
                        }
                    } else {
                        if (self::$internalErrorWarning) {
                            self::$internalErrorWarning = false;   //není třeba psát dokola a navíc by zde došlo k zacyklení metody t
                            if (self::$logger) {
                                self::$logger->warning("Message: Není nastaveno povinné locale metodou setAppLocale().");
                            }
                            user_error(Message::t("Message: Není nastaveno povinné locale metodou setAppLocale()."), E_USER_WARNING);
                        }
                        $template = $text;
                        self::$internalErrorWarning = true;
                    }
                } else {
                    if (self::$internalErrorWarning) {
                        self::$internalErrorWarning = false;   //není třeba psát dokola a navíc by zde došlo k zacyklení metody t
                        if (self::$logger) {
                            self::$logger->notice("Message: Není překlad pro message: {text}", ['text'=>$text]);
                        }
                        user_error(Message::t("Message: Není překlad pro message: {text}", ['text'=>$text]), E_USER_NOTICE);
                    }
                    $template = $text;
                    self::$internalErrorWarning = true;
                }

        return Template::interpolate($template, $context);
    }

}
