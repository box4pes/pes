<?php
namespace Pes\Type;

class Date
{
    /**
     * @var DateTime
     */
    private $dateTime;

    const SQL_FORMAT = "Y-m-d";
    const STRING_FORMAT = "d.m.Y";

    //TODO: Svoboda Intl!
//        $dt = new DateTime;
//        $formatter = new IntlDateFormatter('de_DE', IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
//        $formatter->setPattern('E d.M.yyyy');
//        echo $formatter->format($dt);


    private function __construct(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     *
     * @return Date
     */
    public static function now() {
        return new self(new \DateTime("now"));
    }

    /**
     *
     * @param string $retezecDatum Řetězec veformátu cškého datumu dd.mm.rrrr
     * @return Date
     */
    public static function createFromCzechStringDate($retezecDatum=false) {
        $retezecDatum=trim($retezecDatum);
//        $regex_pattern="^([1-9]|0[0-9]|1[0-9]|2[0-9]|3[0-1])\.( [1-9]|[1-9]|1[0-2]|0[1-9])\.( [1-2][0-9]{3}|[1-2][0-9]{3})";
//        if (ereg($regex_pattern, $retezecDatum, $regs) && checkdate($regs[2],$regs[1],$regs[3]))   // SVOBODA This function has been DEPRECATED as of PHP 5.3.0. Relying on this feature is highly discouraged.
        //preg_match()
        $regex_pattern="/^([1-9]|0[0-9]|1[0-9]|2[0-9]|3[0-1])\.( [1-9]|[1-9]|1[0-2]|0[1-9])\.( [1-2][0-9]{3}|[1-2][0-9]{3})/";
        if(preg_match($regex_pattern, $retezecDatum, $regs) && checkdate($regs[2],$regs[1],$regs[3]))
        {
            $datum = \DateTime::createFromFormat(self::SQL_FORMAT, trim($regs[3])."-".trim($regs[2])."-".trim($regs[1]));
            return $datum ? new self($datum) : NULL;
        }
    }

    /**
     *
     * @param string $sqlDatum
     * @return Date
     */
    public static function createFromSqlDate($sqlDatum) {
        $datum = \DateTime::createFromFormat(self::SQL_FORMAT, $sqlDatum);
        return $datum ? new self($datum) : NULL;
    }

    /**
     *
     * @param array $pole
     * @return Date
     */
    public static function createFromQuickformArray($pole)
    {
        $datum = \DateTime::createFromFormat(self::SQL_FORMAT, $pole["Y"]."-".$pole["m"]."-".$pole["d"]);
        return $datum ? new self($datum) : NULL;
    }

    /**
     *
     * @return \DateTime
     */
    public function getPhpDateTime() {
        return $this->dateTime();
    }

    /**
     *
     * @return array
     */
    public function getQuickformDate() {
        return array("Y" => $this->dateTime->format("Y"), "m" => $this->dateTime->format("m"), "d" => $this->dateTime->format("d"));
    }

    /**
     *
     * @return string
     */
    public function getSqlDate() {
        return $this->dateTime->format(self::SQL_FORMAT);
    }

    /**
     *
     * @return string
     */
    public function getCzechStringDate() {
        return $this->dateTime->format(self::STRING_FORMAT);
    }

    /**
     *
     * @return string
     */
    public function getCzechStringYear() {
        return $this->dateTime->format('Y');
    }
}