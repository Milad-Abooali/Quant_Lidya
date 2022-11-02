<?php

/**
 * Matching Phrases
 *
 * @package    AI
 * @category   Lib
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  2012 - 2021 Codebox
 * @license    https://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 * @version    1.0.0
 */

namespace AI;

class matchingPhrases
{

    private $datasets=array();

    function __construct($sets) {
        foreach(new \DirectoryIterator($sets) as $item) {
            if($item->isFile())
                $this->datasets[$item->getFilename()] = file($sets.$item->getFilename(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            else if($item->isDir() && !$item->isDot())
                foreach(new \DirectoryIterator($sets.$item->getFilename()) as $sitem)
                    if($sitem->isFile())
                        $this->datasets[$item->getFilename()][$sitem->getBasename()] = file($sets.$item->getFilename().'/'.$sitem->getFilename(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }
    }

    private function col2Array ($csvArray) {
        foreach ($csvArray as $k => $v) {
            if (!is_array($v)) $output[$k] = explode(',',$v);
        }
        return $output;
    }

    private function col2ArrayH (array $csvArray) {
        foreach ($csvArray as $k => $v) {
            $ec = explode(',',$v);
            if (!is_array($v)) $output[$ec[0]] = $ec;
        }
        return $output;
    }

    private function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) return true;
        return false;
    }

    /**
     * Email Extractor
     * @param $string
     * @return false|array
     */
    public static function emailExtractor($string) {
        preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $string, $matches);
        return $matches[0] ?? false;
    }

    /**
     * Q Mark
     * @param $string
     * @return false|int
     */
    public function isQmark($string) {
        return strpos($string,'?');
    }
    
    /**
     * Response
     * @param string $string
     * @return bool|string
     */
    public function isResponse($string) {
        foreach( $this->datasets['response'] as $set_name => $set ) if( in_array($string, $set) ) return array('set_name' => $set_name);
        return false;
    }

    /**
     * Option
     * @param string $string
     * @return bool|string
     */
    public function isOption($string) {
        foreach( $this->datasets['option'] as $set_name => $set ) if( in_array($string, $set) ) return array('set_name' => $set_name);
        return false;
    }

    /**
     * Symbol
     * @param $string
     * @return bool|int|string
     */
    public function isSymbol($string) {
        foreach( $this->datasets['symbol'] as $set_name => $set ) if( $this->in_array_r($string, $this->col2ArrayH($set)) ) return array('set_name' => $set_name);
        return false;
    }

    /**
     * glossary
     * @param $string
     * @return bool|array
     */
    public function isGlossary($string) {
        $def = 1;
        if(strlen($string)>1 && !$this->isNumber($string))
            foreach($this->datasets['glossary'] as $set_name => $set)
                foreach($this->col2ArrayH($set) as $row) {
                    $sim = similar_text($row[0], $string, $perc);
                    if (intval($perc) > 50 && strpos($row[0], $string) !== false) {
                        if ($perc>85) $def += $perc;
                        $output[] = array(
                            'word'       =>    $string,
                            'set_name'   =>    $set_name,
                            'glossary'        =>    $row[0],
                            'cat'        =>    $row[1],
                            'perc'       =>    $perc,
                            'sim'        =>    $sim
                        );
                    }
                }
        if ($output ?? false) $output['def'] = $def;
        return $output ?? false;
    }

    /**
     * Number from Char
     */
    public static function char2number($string) {
        $search = array(
            'zero',
            'one',
            'two',
            'three',
            'four',
            'five',
            'six',
            'seven',
            'eight',
            'nine',
            'ten',
            'eleven',
            'twelve',
            'thirteen',
            'fourteen',
            'fifteen',
            'sixteen',
            'seventeen',
            'eighteen',
            'nineteen',
            'twenty',
            'thirty',
            'forty',
            'fifty',
            'sixty',
            'seventy',
            'eighty',
            'ninety'
        );
        $replace  = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,30,40,50,60,70,80,90);
        return str_replace($search, $replace, $string);
    }
    /**
     * Number
     * @param $string
     * @return bool|string
     */
    public function isNumber($string) {
        if(ctype_digit($string)) return array('set_name' => 'int');
        if(is_numeric($string)) return array('set_name' => 'float');
        $string = $this::char2number($string);
        if(ctype_digit($string)) return array('set_name' => 'int_char');


        return false;
    }

    /**
     * CMD
     * @param $string
     * @return bool|string
     */
    public function isCmd($string) {
        if (strpos($string, '##') !== false) return array('set_name' => str_replace('#','',$string));
        foreach( $this->datasets['cmd'] as $set_name => $set ) if( in_array($string, $set) ) return array('set_name' => $set_name);
        return false;
    }

    /**
     * Handy
     * @param $string
     * @return bool|string
     */
    public function isHandy($string) {
        foreach( $this->datasets['handy'] as $set_name => $set ) if( in_array($string, $set) ) return array('set_name' => $set_name);
        return false;
    }

    /**
     * FAQ
     * @param $string
     * @param bool $include
     * @return bool|string
     */
    public function isFAQ($string, $include=false) {
        $def = 1;
        foreach($this->datasets['faq'] as $set_name => $set)
            foreach($this->col2ArrayH($set) as $row) {
                $sim = similar_text($row[0], $string, $perc);
                $rate= ($include) ? 80 : 50;
                if(!$include) $include = (strpos($row[0], $string) !== false);
                if (intval($perc) > $rate && $include ){
                    if ($perc>50) $def += $perc;
                    $output[] = array(
                        'set_name'   =>    $set_name,
                        'faq'        =>    $row[0],
                        'a'          =>    $row[1],
                        'perc'       =>    $perc,
                        'sim'        =>    $sim
                    );
                }
            }
        if ($output ?? false) $output['def'] = $def;
        return $output ?? false;
    }

    /**
     * ASK
     * @param $string
     * @return bool|string
     */
    public function isAsk($string) {
        foreach( $this->datasets['ask'] as $set_name => $set ) if( in_array($string, $set) ) return array('set_name' => $set_name);
        return false;
    }

    /**
     * Unit
     * @param $string
     * @return bool|string
     */
    public function isUnit($string) {
        foreach( $this->datasets['unit'] as $set_name => $set ) if( in_array($string, $set) ) return array('set_name' => $set_name);
        return false;
    }

    /**
     * Datetime
     * @param $string
     * @return bool|string
     */
    public function isDatetime($string) {
        $regex['yyyy-mm-dd'] = "/^((((19|[2-9]\d)\d{2})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})\-02\-(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\-02\-29))$/";
        $regex['yyyy/mm/dd'] = "/^((((19|[2-9]\d)\d{2})\/(0[13578]|1[02])\/(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})\/(0[13456789]|1[012])\/(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})\/02\/(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\/02\/29))$/";
        $regex['mm-dd-yyyy'] = "/^(((0[13578]|1[02])\-(0[1-9]|[12]\d|3[01])\-((19|[2-9]\d)\d{2}))|((0[13456789]|1[012])\-(0[1-9]|[12]\d|30)\-((19|[2-9]\d)\d{2}))|(02\-(0[1-9]|1\d|2[0-8])\-((19|[2-9]\d)\d{2}))|(02\-29\-((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/";
        $regex['mm/dd/yyyy'] = "/^(((0[13578]|1[02])\/(0[1-9]|[12]\d|3[01])\/((19|[2-9]\d)\d{2}))|((0[13456789]|1[012])\/(0[1-9]|[12]\d|30)\/((19|[2-9]\d)\d{2}))|(02\/(0[1-9]|1\d|2[0-8])\/((19|[2-9]\d)\d{2}))|(02\/29\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/";
        $regex['dd/mm/yyyy'] = "/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/";
        $regex['dd-mm-yyyy'] = "/^(((0[1-9]|[12]\d|3[01])\-(0[13578]|1[02])\-((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\-(0[13456789]|1[012])\-((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\-02\-((19|[2-9]\d)\d{2}))|(29\-02\-((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/";
        foreach($regex as $k => $v) if (preg_match_all($v, $string)) return array('set_name' => $k);
        foreach( $this->datasets['datetime'] as $set_name => $set ) if( in_array($string, $set) ) return array('set_name' => $set_name);
        return false;
    }

    /**
     * Modifire
     * @param $string
     * @return bool|string
     */
    public function isModifire($string) {
        foreach( $this->datasets['modifire'] as $set_name => $set ) if( in_array($string, $set) ) return array('set_name' => $set_name);
        return false;
    }

}