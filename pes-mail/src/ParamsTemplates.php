<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Mail;

use Pes\Mail\Assembly;
use Pes\Mail\AssemblyInterface;
use Pes\Mail\Assembly\Host;
use Pes\Mail\Assembly\SmtpConnection;
use Pes\Mail\Assembly\Party;
use Pes\Mail\Assembly\Content;
use Pes\Mail\Assembly\Attachment;
use Pes\Mail\Assembly\Headers;

/**
 * Description of ParamsTemplates
 * 
 * Sada šablon parametrů, obsahují zejména připojovací informace k SMTP serverům a základní konfiguraci
 *
 * @author pes2704
 */
class ParamsTemplates {

    private static $paramsArray = [ ];

    /**
     *
     * @param string $name
     * @return Assembly
     */
    public static function params($name) {
        if (array_key_exists($name, self::$paramsArray)) {
            return self::$paramsArray[$name];
        } else {
            self::$paramsArray[$name] = self::$name();
            return self::$paramsArray[$name];
        }
    }
    //-----------------------------------------------------------------------------------------------------------------------
    /**
     * Parametry pro odesílání prostřednictvím smtp.gmail.com
     * Pro přihlášení k SMTP serveru se používají údaje emailového účtu it.grafia@gmail.com
     *
     * Parametry neobsahují: Content a Party, tyto porametry musí být doplněy.
     *
     * @return Assembly
     */
    private static function smtp4dev() {

        $params = new Assembly();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('localhost')
                    )
            ->setSmtp(
                    (new SmtpConnection())
                        ->setSmtpAuth(true)
                        ->setUserName('pes@localhost')
                        ->setPassword('Heslo')
                        ->setEncryption(SmtpConnection::NONE)
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders(['X-Mailer' => 'web mail test'])
                    )
            ;

        return $params;
    }
    //-----------------------------------------------------------------------------------------------------------------------
    /**
     * Parametry pro integrační testy odesílání mailů přes lokální fake SMTP (smtp4dev apod.).
     *
     * @return Assembly
     */
    private static function smtp4devIntegrationTest() {

        $params = new Assembly();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('localhost')
                    )
            ->setSmtp(
                    (new SmtpConnection())
                        ->setSmtpAuth(true)
                        ->setUserName('test@localhost')
                        ->setPassword('test')
                        ->setEncryption(SmtpConnection::NONE)
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders(['X-Mailer' => 'pes-mail-integration-test'])
                    )
            ;

        return $params;
    }
    //-----------------------------------------------------------------------------------------------------------------------
    /**
     * Parametry pro odesílání prostřednictvím smtp.gmail.com
     * Pro přihlášení k SMTP serveru se používají údaje emailového účtu it.grafia@gmail.com
     *
     * Parametry neobsahují: Content a Party, tyto porametry musí být doplněy.
     *
     * @return Assembly
     */
    private static function itGrafiaGmail() {

        $params = new Assembly();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('smtp.gmail.com')
                    )
            ->setSmtp(
                    (new SmtpConnection())
                        ->setSmtpAuth(true)
                        ->setUserName('it.grafia@gmail.com')
                        ->setPassword('ItDiskAdmin')
                        ->setEncryption(SmtpConnection::STARTTLS)
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders(['X-Mailer' => 'web mail'])
                    )
            ;

        return $params;
    }
    //-----------------------------------------------------------------------------------------------------------------------

    /**
     * Parametry pro odesílání prostřednictvím smtp.cesky-hosting.cz
     * Pro přihlášení k SMTP serveru se používají údaje emailového účtu 'info@najdisi.cz' zřízeného na doméně najdisi.cz
     *
     * Parametry neobsahují: Content a Party, tyto porametry musí být doplněy.
     *
     * @return Assembly
     */
    private static function najdisi() {
         // funkční na hostingu
        $params = new Assembly();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('smtp.cesky-hosting.cz')
                    )
            ->setSmtp(
                    (new SmtpConnection())
                        ->setSmtpAuth(true)
                        ->setUserName('info@najdisi.cz')
                        ->setPassword('Kostrčnenihouba')
                        ->setEncryption(SmtpConnection::SMTPS)
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders(['X-Mailer' => 'web mail'])
                    )
            ;

        return $params;
    }

    private static function najdisiWebSMTP() {
        // není nastaveno SMTP => používá PHP funkci mail()
        $params = new Assembly();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('websmtp.cesky-hosting.cz')
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders(['X-Mailer' => 'web mail'])
                    )
            ;

        return $params;
    }
    //-----------------------------------------------------------------------------------------------------------------------
    /**
     * Parametry pro odesílání prostřednictvím smtp.cesky-hosting.cz
     * Pro přihlášení k SMTP serveru se používají údaje emailového účtu 'info@najdisi.cz' zřízeného na doméně najdisi.cz
     *
     * Parametry neobsahují: Content a Party, tyto porametry musí být doplněy.
     *
     * @return Assembly
     */
    private static function grafiaInterni() {

        $params = new Assembly();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('posta.grafia.cz')
//                        ->setHost('localhost')
                    )
            ->setSmtp(
                    (new SmtpConnection())
                        ->setSmtpAuth(true)
                        ->setUserName('allmail')
                        ->setPassword('Liamlla123')
                        ->setEncryption(SmtpConnection::NONE)
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders(['X-Mailer' => 'web mail'])
                    )
            ;

        return $params;
    }
    //-----------------------------------------------------------------------------------------------------------------------
    
        private static function test() {
        $subject =  'Předmět!!!';
        $body  =
"
<h3>Kdo je hloupější</h3>

<p>Chalupník se vydal do světa hledat člověka hloupějšího, než je jeho žena. Oklame bohatou paní báchorkou, že spadl z nebe a chce se tam vrátit. Ona mu dala peníze a košile pro svého syna, který před tím zemřel. Když se její muž vrátil a o její hlouposti se dověděl, začal chalupníka pronásledovat. Dostihl ho a ptal se ho, zda tu neviděl někoho utíkat s uzlíkem, on pravil že ano, že mu má pán pohlídat klobouk, že mám pod ním vzácného ptáka, že on toho lstivého chlapíka dohoní. Místo toho však jel domů s tím, že ženu nezbije, neboť potkal lidi hloupější, než byla ona.</p>

<h3>Chytrá horákyně</h3>

<p>Manka je dcerou chudého chalupníka, který se dostane do sporu se svým bohatým bratrem. Díky dceřině důvtipu dostane chalupník to, co mu patří, a ještě vystrojí dceři svatbu. Ta si bere pana soudce, ale musí slíbit, že se mu nebude plést do jeho věcí a soudů. Když však vidí, že její muž rozhodl jednou nespravedlivě, poruší slib a poradí chudému sedlákovi, jak přesvědčit soudce o pravdě. Muž ji za to pošle z domu, ale dovolí ji vzít si s sebou její nejmilejší věc. Chytrá horákyně ho opije a vezme si domů jeho. Soudce ráno musí uznat, že je opravdu chytrá, a spolu se vrací domů.</p>
";
        $attachments = [
            (new Attachment())
                    ->setFileName('logo_grafia.png')
                    ->setAltText('Logo Grafia')
        ];

        $params = new Assembly();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('smtp.cesky-hosting.cz')
                    )
            ->setSmtp(
                    (new SmtpConnection())
                        ->setSmtpAuth(true)
                        ->setUserName('info@najdisi.cz')
                        ->setPassword('KroKF56uJ2pp')
                        ->setEncryption(SmtpConnection::NONE)
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders(['X-Mailer' => 'web mail'])
                    )
            ->setParty(
                    (new Party())
                        ->setFrom('info@najdisi.cz', 'web veletrhprace')
                        ->addReplyTo('svoboda@grafia.cz', 'seb veletrhprace')
                        ->addTo('svoboda@grafia.cz', 'Pes')
                        ->addTo('selnerova@grafia.cz', 'vlse')
//                        ->addCc($ccAddress, $ccName)
//                        ->addBcc($bccAddress, $bccName)
                    )
            ;

        return $params;
    }

    ##########################################

    private static function vzor() {
        $params = new Assembly();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('smtp.example.com')
                    )
            ->setSmtp(
                    (new SmtpConnection())
                        ->setSmtpAuth($smtpAuth)
                        ->setUserName($userName)
                        ->setPassword($password)
                        ->setEncryption(SmtpConnection::NONE)
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders($headers)
                    )
            ->setParty(
                    (new Party())
                        ->setFrom($from)
                        ->addReplyTo($replyTo)
                        ->addTo($toAddress, $toName)
                        ->addCc($ccAddress, $ccName)
                        ->addBcc($bccAddress, $bccName)
                    )
            ->setContent(
                    (new Content())
                        ->setSubject($subject)
                        ->setHtml($body)
                        ->setAttachments(
                            (new Attachment())
                                ->setFileName($filaName)
                                ->setAltText($altText)
                            )
                    )
            ;

        return $params;
    }
}
