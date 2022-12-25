<?php
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\Error\Warning;

use Pes\Text\Text;

use Pes\Logger\FileLogger;

/**
 *
 * @author pes2704
 */
class TextTest extends TestCase {

    /**
     *
     * @var Pes\Logger\FileLogger
     */
    private static $logger;


    public static function setUpBeforeClass(): void {

    }

    public function testMonoPredlozkySpojky() {
        // toto je test, který netestuje nijak dobře a taky ta metoda nefunguje
        $t = Text::mono('Testovací text na pevné mezery  ! ! . k s v z o u i a K S V Z O U I A halelujá. A co p r d m n ?');
        $this->assertEquals("Testovací text na pevné mezery  ! ! . k&nbsp;s v&nbsp;z o&nbsp;u i&nbsp;a K&nbsp;S V&nbsp;Z O&nbsp;U I&nbsp;A halelujá. A&nbsp;co p r d m n ?", $t);
    }

    /**
     * Test mono pro plné datum - den, mesíc i rok
     */
    public function testMonoDatumDMR() {
        // toto je test, který netestuje nijak dobře a taky ta metoda nefunguje
        $t = Text::mono('to se stane 12. 3. 2999');
        $this->assertEquals("to se stane 12.&nbsp;3.&nbsp;2999", $t);
    }

    /**
     * Test mono pro datum složené jen z den, měsíc
     */
    public function testMonoDatumDM() {
        // toto je test, který netestuje nijak dobře a taky ta metoda nefunguje
        $t = Text::mono('12. 3.');
        $this->assertEquals("12.&nbsp;3.", $t);
    }

    public function testDateCsSpaces() {
        $t = Text::dateCsSpaces('12.        3.    2999');
        $this->assertEquals("12. 3. 2999", $t);
        $t = Text::dateCsSpaces('12.3.2999');
        $this->assertEquals("12. 3. 2999", $t);
    }

    public function testDateCsNbsp() {
        $t = Text::dateCsNbsp('12.        3.    2999');
        $this->assertEquals("12.&nbsp;3.&nbsp;2999", $t);
        $t = Text::dateCsNbsp('12.3.2999');
        $this->assertEquals("12.&nbsp;3.&nbsp;2999", $t);
    }

    /**
     * Test testuje pouze to, že metoda neselže a vrací nějak escapovaný text. Metoda spoléhá na správnou funkci htmlspecialchars().
     */
    public function testEsc() {
        $t = Text::esc("<tap qq='onclick(blabla);'>!@#$%^&*()_+<?>:|\\  </tap>");
        $this->assertEquals("&lt;tap qq='onclick(blabla);'&gt;!@#$%^&amp;*()_+&lt;?&gt;:|\  &lt;/tap&gt;", $t);
    }

    /**
     * Test testuje, zda po druhém průchodu nedojde k escapování již existujících html entit
     */
    public function testEscNoDoubleEncode() {
        $t = Text::esc("!@#$%^&*()_+<?>:|\\  ");
        $this->assertEquals("!@#$%^&amp;*()_+&lt;?&gt;:|\  ", $t, "Po prním průchodu.");
        $t = Text::esc($t);
        $this->assertEquals("!@#$%^&amp;*()_+&lt;?&gt;:|\  ", $t, "Po druhém průchodu.");
    }

    public function testEscJs() {
//        $txt = "jaVasCript:/*-/*`/*\`/*'/*"/**/(/* */oNcliCk=alert() )//%0D%0A%0d%0a//</stYle/</titLe/</teXtarEa/</scRipt/--!>\x3csVg/<sVg/oNloAd=alert()//>\x3e";
        $attribute  = Text::esc_js('onLoad=alert(&"<>);');
        $this->assertEquals("onLoad=alert(&amp;&quot;&lt;&gt;);", $attribute);
    }
}
