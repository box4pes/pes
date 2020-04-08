<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Text;

use Pes\Text\Template;
use Psr\Log\LoggerInterface;

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
                "Není překlad pro message: {text}" =>
                [

                ],
                "Není nastaveno povinné locale třídy Message. Použijte volání metody Message::setAppLocale()." =>
                [

                ],
            ];

    /**
     * Přijímá kód locale. Akceptuje krátkou i dlouhou verzi (např. 'cs' i 'cs-CZ', 'en' i 'en-US', ale pro potřeby třídy používá vždy jen krátkou verzi.
     *
     * @param string $appLocale
     */
    public static function setAppLocale($appLocale) {
        // \Locale potřebuje mít povoleno rozšíření intl (php.ini)
//        self::$appLocale = explode('-', $appLocale)[0];
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
     *
     * @param type $text
     * @param array $context
     * @param string $messageLocale
     * @return string
     */
    public static function t($text, array $context = []) {
                if (array_key_exists($text, self::$translations)) {
                    if (isset(self::$appLocale)) {
                        if (array_key_exists(self::$appLocale, self::$translations[$text])) {
                            $template =  self::$translations[$text][self::$appLocale];
                        } else {
                            $template = $text;
                        }
                    } else {
                        if (self::$internalErrorWarning) {
                            self::$internalErrorWarning = false;   //neí třeba psát dokola a navíc by zde došlo k zacyklení metody t
                            user_error(Message::t("Není nastaveno povinné locale třídy Message. Použijte volání metody Message::setAppLocale()."), E_USER_WARNING);
                            if (self::$logger) {
                                self::$logger->warning("Message: Není nastaveno povinné locale aplikace.");
                            }
                        }
                        $template = $text;
                    }
                } else {
                    if (self::$internalErrorWarning) {
                        self::$internalErrorWarning = false;   //neí třeba psát dokola a navíc by zde došlo k zacyklení metody t
                        user_error(Message::t("Message: Není překlad pro message: {text}", ['text'=>$text]), E_USER_NOTICE);
                        if (self::$logger) {
                            self::$logger->notice("Message: Není překlad pro message: {text}", ['text'=>$text]);
                        }
                    }
                    $template = $text;
                }

        return Template::interpolate($template, $context);
    }

}
