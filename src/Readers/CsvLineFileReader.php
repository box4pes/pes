<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Readers;

/**
 * Description of CsvLineReader
 *
 * @author pes2704
 */
class CsvLineFileReader extends LineFileReaderAbstract {

    private $length;
    private $delimiter;
    private $enclosure;
    private $escape;

    /**
     * Čte soubor po jednotlivých řádcích a parsuje řádky do CSV polí. Interně požívá funkci fgetcsv().
     *
     * @param int $length <p>Must be greater than the longest line (in characters) to be found in the CSV file (allowing for trailing line-end characters). Otherwise the line is split in chunks of <code>length</code> characters, unless the split would occur inside an enclosure.</p> <p>Omitting this parameter (or setting it to 0 in PHP 5.1.0 and later) the maximum line length is not limited, which is slightly slower.</p>
     * @param string $delimiter <p>The optional <code>delimiter</code> parameter sets the field delimiter (one character only).</p>
     * @param string $enclosure <p>The optional <code>enclosure</code> parameter sets the field enclosure character (one character only).</p>
     * @param string $escape <p>The optional <code>escape</code> parameter sets the escape character (at most one character). An empty string (<i>""</i>) disables the proprietary escape mechanism.</p> <p><b>Note</b>:  Usually an <code>enclosure</code> character is escaped inside a field by doubling it; however, the <code>escape</code> character can be used as an alternative. So for the default parameter values <i>""</i> and <i>\"</i> have the same meaning. Other than allowing to escape the <code>enclosure</code> character the <code>escape</code> character has no special meaning; it isn't even meant to escape itself. </p>
     * @see fgetcsv()
     */
    public function __construct(int $length = 0, string $delimiter = ",", string $enclosure = '"', string $escape = "\\") {
        $this->length = $length;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    /**
     *
     * @return array <p>Returns an indexed array containing the fields read.</p><p><b>Note</b>:</p><p>A blank line in a CSV file will be returned as an array comprising a single <code>null</code> field, and will not be treated as an error.</p> <p><b>Note</b>: If PHP is not properly recognizing the line endings when reading files either on or created by a Macintosh computer, enabling the auto_detect_line_endings run-time configuration option may help resolve the problem.</p><p><b>fgetcsv()</b> returns <b><code>NULL</code></b> if an invalid <code>handle</code> is supplied or <b><code>FALSE</code></b> on other errors, including end of file.</p>
     */
    protected function getLineSpecial() {
        return fgetcsv($this->handler, $this->length, $this->delimiter, $this->enclosure, $this->escape);
    }
}
