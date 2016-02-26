<?php
namespace PHPDatastream;

class ExpressionBuilder
{

    /**
     *
     * @var string
     */
    private $series = '';

    /**
     *
     * @var string
     */
    private $dataTypes = '';

    /**
     *
     * @var string
     */
    private $startDate = '';

    /**
     *
     * @var string
     */
    private $endDate = '';

    /**
     *
     * @var string
     */
    private $frequency = '';

    /**
     *
     * @var string
     */
    private $naValue = 'NA';

    /**
     *
     * @var array
     */
    private $symbols = array();

    /**
     *
     * @var string
     */
    private $instrument = '';

    const ESCAPE_CHAR = '~';

    const FIELD_DELIMITER = ',';

    const SUBSITUTE_FIELD_DELIMITER = '^';

    /**
     *
     * @param string $series            
     * @param string $dataTypes            
     */
    public function __construct($series, $dataTypes = '', $startDate = '', $endDate = '', $frequency = '', $naValue = '')
    {
        $this->series = $this->escapeString($series);
        $this->dataTypes = $this->escapeString($dataTypes);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->frequency = $frequency;
        $this->naValue = $naValue;
        
        $this->build();
    }

    /**
     *
     * @return string
     */
    public function getInstrument()
    {
        return $this->instrument;
    }

    public function getSymbols()
    {
        return $this->symbols;
    }

    /**
     */
    private function build()
    {
        $series = $this->splitSymbols($this->series);
        if (preg_match('/\bX/i', $this->dataTypes)) {
            $nbSeries = count($series);
            $matches = preg_split('/(\bX)/i', $this->dataTypes, - 1, PREG_SPLIT_DELIM_CAPTURE);
            foreach ($series as $iSerie => $serie) {
                foreach ($matches as $pos => $match) {
                    if ($pos % 2) {
                        $this->instrument .= $serie;
                    } else {
                        $this->instrument .= $match;
                    }
                }
                if ($iSerie < $nbSeries - 1) {
                    $this->instrument .= ',';
                }
            }
        } else {
            $this->instrument = $this->series;
            if (strlen($this->dataTypes)) {
                $this->instrument .= '~=' . $this->dataTypes;
            }
        }
        
        if (strlen($this->startDate)) {
            $this->instrument .= '~' . $this->startDate;
        }
        
        if (strlen($this->endDate)) {
            $this->instrument .= '~:' . $this->endDate;
        }
        
        if (strlen($this->frequency)) {
            $this->instrument .= '~' . $this->frequency;
        }
        
        if (strlen($this->naValue)) {
            $this->instrument .= '~NA=' . $this->naValue;
        }
    }

    private function splitSymbols($string)
    {
        $chars = preg_split('//', $string, - 1, PREG_SPLIT_NO_EMPTY);
        $nbParenthesis = 0;
        $translated = '';
        foreach ($chars as $i => $char) {
            switch ($char) {
                case self::FIELD_DELIMITER:
                    if ($nbParenthesis > 0) {
                        $char = self::SUBSITUTE_FIELD_DELIMITER;
                    }
                    break;
                case '(':
                    $nbParenthesis ++;
                    break;
                case ')':
                    $nbParenthesis --;
                    break;
            }
            $translated .= $char;
        }
        $this->symbols = array();
        foreach (preg_split('/' . self::FIELD_DELIMITER . '/', $translated, - 1, PREG_SPLIT_DELIM_CAPTURE) as $index => $symbol) {
            $symbolKey = 'SYMBOL' . ($index > 0 ? '_' . ($index + 1) : '');
            $this->symbols[$symbolKey] = str_replace(self::SUBSITUTE_FIELD_DELIMITER, ',', $symbol);
        }
        
        return $this->getSymbols();
    }

    /**
     *
     * @param unknown $string            
     */
    private function escapeString($string)
    {
        return preg_replace('/\s+/', '', str_replace(self::ESCAPE_CHAR, str_repeat(self::ESCAPE_CHAR, 2), $string));
    }
}