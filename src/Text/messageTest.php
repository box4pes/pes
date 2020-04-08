<?php
include "../vendor/Pes/src/Text/Message.php";
include "../vendor/Pes/src/Text/Template.php";
include "../vendor/Pes/src/Text/Html.php";
include "../vendor/Pes/src/Utils/Directory.php";
include  '../vendor/psr/log/Psr/Log/LoggerInterface.php';
include  '../vendor/psr/log/Psr/Log/LogLevel.php';
include  '../vendor/psr/log/Psr/Log/AbstractLogger.php';
include "../vendor/Pes/src/Logger/FileLogger.php";

use Pes\Text\Message;
use Pes\Text\Html;
use Pes\Logger\FileLogger;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
Message::setLogger(FileLogger::getInstance("Testlog", 'Messages.log'));
echo Html::p(Message::t('Testovací text!!'));
Message::setAppLocale('cs-CZ');
echo Html::p(Message::t('Testovací text!!'));

Message::addTranslations(
            //web
        [
            'Testovací text!!' =>
            [
                'en-US' => 'Text for testing purposes..',
                'cs-CZ' => 'Testovací text správně česky!!'
            ],
        ]);
echo Html::p(Message::t('Testovací text!!'));
Message::setAppLocale('en-US');
echo Html::p(Message::t('Testovací text!!'));

Message::addTranslations(
            //web
        [
            'Neuvedli jste slovo, podle kterého chcete vyhledávat!. Do kolonky níže napište co chcete vyhledat a stiskněte tlačítko "Vyhledat".' =>
            [
                'en-US' => 'You did not state the key word. Please write the key word into the box below and press the "Search" button.',
                'de-DE' => 'You did not state the key word!. Please write the key word into the box below and press the "Search" button.',
                'cs-CZ' => "Neuvedli jste slovo, podle kterého chcete vyhledávat!. Do kolonky níže napište co chcete vyhledat a stiskněte tlačítko 'Vyhledat'."
            ],
        ]);

echo Html::p(Message::t("Neuvedli jste slovo, podle kterého chcete vyhledávat!. Do kolonky níže napište co chcete vyhledat a stiskněte tlačítko \"Vyhledat\"."));
Message::setAppLocale('cs-CZ');
echo Html::filter("e|mono|p", Message::t('Neuvedli jste slovo, podle kterého chcete vyhledávat!. Do kolonky níže napište co chcete vyhledat a stiskněte tlačítko "Vyhledat".'));
Message::setAppLocale('de-DE');
echo Html::p(Message::t('Neuvedli jste slovo, podle kterého chcete vyhledávat!. Do kolonky níže napište co chcete vyhledat a stiskněte tlačítko "Vyhledat".'));


