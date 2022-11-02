<?php

/**
 * Brain
 *
 * @package    AI
 * @category   Lib
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  2012 - 2021 Codebox
 * @license    https://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 * @version    1.0.0
 */

namespace AI;

class brain
{

    /**
     * @var bool    $is_word       word checker
     * @var array   $skip_mix      skip in checker loop for special checker method
     * @var array   $searches      list part + part series from 0 to 9
     * @var object  $matching      object of matchingPhrases class
     */
    private bool    $is_word;
    private array   $skip_mix;
    private array   $searches;
    private object  $matching;

    /**
     * @var array   $FIG_TYPE      parts figure & type
     * @var array   $FIG_DATA      parts figure & data
     * @var array   $APTNESS       class aptness
     * @var array   $UNIFORM       class aptness after word counter
     * @var string  $PICKED        picked class based on uniform aptness
     * @var float   $ACCURACY      chance of true classification
     */
    public array    $FIG_TYPE;
    public array    $FIG_DATA;
    public float    $ACCURACY;
    public array    $UNIFORM;
    public string   $PICKED;
    public array    $APTNESS = array (
                                    'Handy'      => 0,
                                    'FAQ'        => 0,
                                    'QA'         => 0,
                                    'Glossary'        => 0,
                                    'Command'    => 0,
                                    'Response'   => 0,
                                    'AIO'        => 0,
                                    'Idle'       => 0.010
                                );

    function __construct() {
        $this->matching = new matchingPhrases('brain/sets/');
    }

    /**
     * Impact Figure To Class Rate
     *
     * @param string $figure
     * @param int $pos
     * @param $extra
     */
    private function _impact($figure, $pos=null, $extra=null) {
        switch ($figure) {
            case 'isWord':
                $this->APTNESS['Handy']      += 0.400 / ($pos+1);
                $this->APTNESS['FAQ']        += 0.050;
                $this->APTNESS['QA']         += 0.050;
                $this->APTNESS['Glossary']        -= 0.050;
                $this->APTNESS['Command']    += 0.015;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['AIO']        += 0.300 / ($pos+2);
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'isUnit':
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.050;
                $this->APTNESS['QA']         += 0.050;
                $this->APTNESS['Glossary']        += 0.050;
                $this->APTNESS['Command']    += 0.100;
                $this->APTNESS['Response']   -= 0.100;
                $this->APTNESS['AIO']        += 0.050;
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'isAsk':
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.100;
                $this->APTNESS['QA']         += 1.000 / ($pos+1);
                $this->APTNESS['Glossary']        -= 0.050;
                $this->APTNESS['Command']    -= 0.100;
                $this->APTNESS['Response']   -= 0.100;
                $this->APTNESS['AIO']        += 0.050;
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'isFaq':
                $this->APTNESS['Handy']      -= 0.100;
                $this->APTNESS['FAQ']        += 0.400 * ($extra['def']);
                $this->APTNESS['QA']         -= 0.100;
                $this->APTNESS['Glossary']        += 0.050;
                $this->APTNESS['Command']    -= 0.050;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['AIO']        -= 0.050;
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'isHandy':
                $this->APTNESS['Handy']      += 1.000 / ($pos+2);
                $this->APTNESS['FAQ']        -= 0.050;
                $this->APTNESS['QA']         -= 0.050;
                $this->APTNESS['Glossary']        -= 0.050;
                $this->APTNESS['Command']    -= 0.050;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['AIO']        += 0.080;
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'isCmd':
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.050;
                $this->APTNESS['QA']         += 0.050;
                $this->APTNESS['Glossary']        -= 0.050;
                $this->APTNESS['Command']    += 1.000 / ($pos+1);
                $this->APTNESS['Response']   -= 0.100;
                $this->APTNESS['AIO']        += 0.050;
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'isSymbol':
                $this->APTNESS['Handy']      -= 0.100;
                $this->APTNESS['FAQ']        += 0.050;
                $this->APTNESS['QA']         += 0.100;
                $this->APTNESS['Glossary']        += 0.100;
                $this->APTNESS['Command']    += 1.000 / ($pos+3);
                $this->APTNESS['Response']   -= 0.100;
                $this->APTNESS['AIO']        += 0.050;
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'isGlossary':
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.150;
                $this->APTNESS['QA']         += 0.100;
                $this->APTNESS['Glossary']        += 0.160 * ($extra['def']/100);
                $this->APTNESS['Command']    += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['AIO']        += 0.050;
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'isResponse':
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        -= 0.050;
                $this->APTNESS['QA']         -= 0.050;
                $this->APTNESS['Glossary']        -= 0.050;
                $this->APTNESS['Command']    -= 0.100;
                $this->APTNESS['Response']   += 1.000 / ($pos+1);
                $this->APTNESS['AIO']        += 0.050;
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'isNumber':
                $this->APTNESS['Handy']      -= 0.100;
                $this->APTNESS['FAQ']        += 0.050;
                $this->APTNESS['QA']         += 0.050;
                $this->APTNESS['Glossary']        -= 0.050;
                $this->APTNESS['Command']    += 0.100;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['AIO']        += 0.050;
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'isDatetime':
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        -= 0.050;
                $this->APTNESS['QA']         += 0.100;
                $this->APTNESS['Glossary']        += 0.050;
                $this->APTNESS['Command']    += 0.100 / ($pos+1);
                $this->APTNESS['Response']   -= 0.100;
                $this->APTNESS['AIO']        += 0.050 / ($pos+1);
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'isOption':
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        -= 0.050;
                $this->APTNESS['QA']         -= 0.050;
                $this->APTNESS['Glossary']        -= 0.050;
                $this->APTNESS['Command']    -= 0.100;
                $this->APTNESS['AIO']        += 1.000 / ($pos+1);
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'trim_faq':
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.400 * $extra;
                $this->APTNESS['QA']         += 0.050;
                $this->APTNESS['Glossary']        -= 0.050;
                $this->APTNESS['Command']    -= 0.1000;
                $this->APTNESS['AIO']        -= 0.050;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       -= 0.010;
                break;
            case 'isModifire':
                $this->APTNESS['Handy']      = 0.000;
                $this->APTNESS['FAQ']        = 0.000;
                $this->APTNESS['QA']         = 0.000;
                $this->APTNESS['Glossary']        = 0.000;
                $this->APTNESS['Command']    += 0.050;
                $this->APTNESS['Response']   = 0.000;
                $this->APTNESS['AIO']        = 0.000;
                $this->APTNESS['Idle']       = 0.010;
                break;
            case 'reset':
            default:
            $this->APTNESS['Handy']      = 0.000;
            $this->APTNESS['FAQ']        = 0.000;
            $this->APTNESS['QA']         = 0.000;
            $this->APTNESS['Glossary']        = 0.000;
            $this->APTNESS['Command']    = 0.000;
            $this->APTNESS['Response']   = 0.000;
            $this->APTNESS['AIO']        = 0.000;
            $this->APTNESS['Idle']       = 0.010;
        }
    }

    /**
     * Process Sanitized Input
     *
     * @param array $sanitized_input
     * @return bool
     */
    public function process($sanitized_input) {

        // Check Marks
        if($sanitized_input['mark']) {
            if($sanitized_input['mark']['`'])
            {   # is '`' Mark
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        += 0.050;
                $this->APTNESS['QA']         += 0.050;
                $this->APTNESS['Glossary']        += 0.050;
                $this->APTNESS['Command']    -= 0.050;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['-'])
            {   # is '-' Mark
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        += 0.050;
                $this->APTNESS['QA']         += 0.050;
                $this->APTNESS['Glossary']        += 0.050;
                $this->APTNESS['Command']    += 0.100;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['='])
            {   # is '=' Mark
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        += 0.050;
                $this->APTNESS['QA']         += 0.050;
                $this->APTNESS['Glossary']        += 0.050;
                $this->APTNESS['Command']    += 0.100;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['~'])
            {   # is '~' Mark
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        += 0.050;
                $this->APTNESS['QA']         += 0.050;
                $this->APTNESS['Glossary']        += 0.050;
                $this->APTNESS['Command']    += 0.100;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['!'])
            {   # is '!' Mark
                $this->APTNESS['Handy']      += 0.100;
                $this->APTNESS['FAQ']        += 0.100;
                $this->APTNESS['QA']         += 0.100;
                $this->APTNESS['Glossary']        -= 0.050;
                $this->APTNESS['Command']    += 0.050;
                $this->APTNESS['AIO']        += 0.060;
                $this->APTNESS['Response']   += 0.085;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['@'])
            {   # is '@' Mark
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        += 0.080;
                $this->APTNESS['QA']         += 0.080;
                $this->APTNESS['Glossary']      += 0.080;
                $this->APTNESS['Command']    += 0.300;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['#'])
            {   # is '#' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.050;
                $this->APTNESS['QA']         += 0.050;
                $this->APTNESS['Glossary']        += 0.080;
                $this->APTNESS['Command']    += 0.100;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['$'])
            {   # is '$' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.055;
                $this->APTNESS['QA']         += 0.065;
                $this->APTNESS['Glossary']        += 0.065;
                $this->APTNESS['Command']    += 0.150;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['%'])
            {   # is '%' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.020;
                $this->APTNESS['QA']         += 0.030;
                $this->APTNESS['Glossary']        += 0.040;
                $this->APTNESS['Command']    += 0.080;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['^'])
            {   # is '^' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        -= 0.020;
                $this->APTNESS['QA']         -= 0.030;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.060;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['&'])
            {   # is '&' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        -= 0.020;
                $this->APTNESS['QA']         -= 0.030;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.060;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['*'])
            {   # is '*' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        -= 0.050;
                $this->APTNESS['QA']         -= 0.050;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.080;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['('])
            {   # is '(' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.020;
                $this->APTNESS['QA']         += 0.040;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.080;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   += 0.020;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark'][')'])
            {   # is ')' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.020;
                $this->APTNESS['QA']         += 0.040;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.080;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   += 0.020;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['_'])
            {   # is '_' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.020;
                $this->APTNESS['QA']         += 0.040;
                $this->APTNESS['Glossary']        += 0.060;
                $this->APTNESS['Command']    += 0.080;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.020;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['\\'])
            {   # is '\\' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.020;
                $this->APTNESS['QA']         += 0.040;
                $this->APTNESS['Glossary']        += 0.060;
                $this->APTNESS['Command']    += 0.090;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.020;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['|'])
            {   # is '|' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        += 0.020;
                $this->APTNESS['QA']         += 0.030;
                $this->APTNESS['Glossary']        += 0.040;
                $this->APTNESS['Command']    += 0.070;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.020;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark'][';'])
            {   # is ';' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        -= 0.020;
                $this->APTNESS['QA']         -= 0.030;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.060;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark'][':'])
            {   # is ':' Mark
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        -= 0.020;
                $this->APTNESS['QA']         -= 0.030;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.060;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']["'"])
            {   # is "'" Mark
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        += 0.020;
                $this->APTNESS['QA']         += 0.030;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.060;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['"'])
            {   # is '"' Mark
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        += 0.020;
                $this->APTNESS['QA']         += 0.030;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.060;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['/'])
            {   # is '/' Mark
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        += 0.020;
                $this->APTNESS['QA']         += 0.030;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.060;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['?'])
            {   # is '?' Mark
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        += 0.070;
                $this->APTNESS['QA']         += 0.090;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    -= 0.060;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['.'])
            {   # is '.' Mark
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        += 0.020;
                $this->APTNESS['QA']         += 0.030;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.060;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['>'])
            {   # is '>' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        -= 0.020;
                $this->APTNESS['QA']         += 0.030;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.060;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark'][','])
            {   # is ',' Mark
                $this->APTNESS['Handy']      += 0.050;
                $this->APTNESS['FAQ']        += 0.020;
                $this->APTNESS['QA']         += 0.030;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.060;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
            if($sanitized_input['mark']['<'])
            {   # is '<' Mark
                $this->APTNESS['Handy']      -= 0.050;
                $this->APTNESS['FAQ']        -= 0.020;
                $this->APTNESS['QA']         += 0.030;
                $this->APTNESS['Glossary']        += 0.030;
                $this->APTNESS['Command']    += 0.060;
                $this->APTNESS['AIO']        += 0.020;
                $this->APTNESS['Response']   -= 0.050;
                $this->APTNESS['Idle']       += 0.005;
            }
        }

        // Check Trim - FAQ
        $trim_is_faq = $this->matching->isFAQ($sanitized_input['trim'], true);
        if ($trim_is_faq) {
            $this->FIG_TYPE['FAQ'] = ($trim_is_faq['set_name']) ?? $trim_is_faq;
            $this->FIG_DATA['FAQ'][$trim_is_faq['set_name']][] = $sanitized_input['trim'];
            $this->_impact('trim_faq',null,$trim_is_faq['def']);
        }

        // Part Detector
        $perv = null;
        $this->is_word = false;
        foreach($sanitized_input['parts'] as $k => $part)
        {

            $this->is_word = true;

            // Make Parts
            $parts = array(
                $part,
                ($sanitized_input['parts'][$k + 1]) ? $sanitized_input[$k + 1] : false,
                ($sanitized_input['parts'][$k + 2]) ? $sanitized_input[$k + 1] : false,
                ($sanitized_input['parts'][$k + 3]) ? $sanitized_input[$k + 1] : false,
                ($sanitized_input['parts'][$k + 4]) ? $sanitized_input[$k + 1] : false,
                ($sanitized_input['parts'][$k + 5]) ? $sanitized_input[$k + 1] : false,
                ($sanitized_input['parts'][$k + 6]) ? $sanitized_input[$k + 1] : false,
                ($sanitized_input['parts'][$k + 7]) ? $sanitized_input[$k + 1] : false,
                ($sanitized_input['parts'][$k + 8]) ? $sanitized_input[$k + 1] : false,
                ($sanitized_input['parts'][$k + 9]) ? $sanitized_input[$k + 1] : false
            );
            $this->searches = array(
                $parts[0],
                $parts[0].' '.$parts[1],
                $parts[0].' '.$parts[1].' '.$parts[2],
                $parts[0].' '.$parts[1].' '.$parts[2].' '.$parts[3],
                $parts[0].' '.$parts[1].' '.$parts[2].' '.$parts[3].' '.$parts[4],
                $parts[0].' '.$parts[1].' '.$parts[2].' '.$parts[3].' '.$parts[4].' '.$parts[5],
                $parts[0].' '.$parts[1].' '.$parts[2].' '.$parts[3].' '.$parts[4].' '.$parts[5].' '.$parts[6],
                $parts[0].' '.$parts[1].' '.$parts[2].' '.$parts[3].' '.$parts[4].' '.$parts[5].' '.$parts[6].' '.$parts[7].' '.$parts[8],
                $parts[0].' '.$parts[1].' '.$parts[2].' '.$parts[3].' '.$parts[4].' '.$parts[5].' '.$parts[6].' '.$parts[7].' '.$parts[8].' '.$parts[9]
            );

            // Prev
            $perv = ($part === 'at') ? 'at' : null;

            // Number
            $is_number = $this->matching->isNumber($parts[0]);
            if ($is_number) {
                $this->is_word = false;
                $this->FIG_TYPE[(($perv=='at') ? 'price' : 'number')][]= ($is_number['set_name']) ?? $is_number;
                $this->FIG_DATA[($perv=='at') ? 'price' : 'number'][$is_number['set_name']][] = $parts[0];
                $this->_impact('isNumber', $k);
                continue;
            }

            // Recursive Part Checker
            $this->skip_mix = array();
            for ($i=9;$i>=0;$i--) {
                if($parts[$i]) {
                    $this->_checker($k, $i, 'isDatetime');
                    $this->_checker($k, $i, 'isModifire');
                    $this->_checker($k, $i, 'isOption');
                    $this->_checker($k, $i, 'isResponse');
                    $this->_checker($k, $i, 'isGlossary');
                    $this->_checker($k, $i, 'isSymbol');
                    $this->_checker($k, $i, 'isCmd');
                    $this->_checker($k, $i, 'isHandy');
                    $this->_checker($k, $i, 'isFaq');
                    $this->_checker($k, $i, 'isAsk');
                    $this->_checker($k, $i, 'isUnit');
                }
            }

            // Woed
            if($this->is_word != false) {
                $this->FIG_TYPE['isWord'][] = $part;
                $this->FIG_DATA['isWord'][] = $part;
                $this->_impact('isWord', $k);
            }

        }


        // Length Controller
        $word_count = count($sanitized_input['parts']);
        if($word_count == 1 && $this->FIG_TYPE['number']) {
            $this->APTNESS['Handy']      -= 0.050;
            $this->APTNESS['FAQ']        -= 0.050;
            $this->APTNESS['QA']         -= 0.050;
            $this->APTNESS['Glossary']        -= 0.050;
            $this->APTNESS['Command']    -= 0.050;
            $this->APTNESS['Response']   += 0.200;
            $this->APTNESS['AIO']        -= 0.050;
            $this->APTNESS['Idle']       -= 0.010;
        }
        if($word_count > 3) {
            $this->APTNESS['Handy']      -= 0.040 * $word_count;
            $this->APTNESS['FAQ']        += 0.025;
            $this->APTNESS['QA']         += 0.030;
            $this->APTNESS['Glossary']        -= 0.040 * $word_count;
            $this->APTNESS['Command']    -= 0.010 * $word_count;
            $this->APTNESS['Response']   -= 0.040 * $word_count;
            $this->APTNESS['AIO']        += 0.021 * $word_count;
            $this->APTNESS['Idle']       -= 0.010;
        }
        if($word_count == 1 && (($this->FIG_TYPE['isWord'] || $this->FIG_TYPE['isAsk'])) ) {
            $this->APTNESS['Handy']      += 0.040;
            $this->APTNESS['FAQ']        += 0.025;
            $this->APTNESS['QA']         += 0.025;
            $this->APTNESS['Glossary']        += 0.025;
            $this->APTNESS['Command']    += 0.030;
            $this->APTNESS['Response']   += 0.040;
            $this->APTNESS['AIO']        += 0.020;
            $this->APTNESS['Idle']       -= 0.010;
        }
        if ($this->FIG_TYPE['isAsk']) {
            $this->APTNESS['Handy']      -= 0.045 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['FAQ']        += 0.025;
            $this->APTNESS['QA']         += 0.045;
            $this->APTNESS['Glossary']        -= 0.020 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['Command']    += ($this->FIG_TYPE['isCmd']) ? 0.040: 0.010;
            $this->APTNESS['Response']   -= 0.030;
            $this->APTNESS['AIO']        += 0.020;
            $this->APTNESS['Idle']       -= 0.010;
        }
        if ($this->FIG_TYPE['isCmd'] ) {
            $this->APTNESS['Handy']      -= 0.080 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['FAQ']        += 0.025;
            $this->APTNESS['QA']         -= 0.025;
            $this->APTNESS['Glossary']        -= 0.020 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['Command']    += 0.080 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['Response']   += 0.040;
            $this->APTNESS['AIO']        -= 0.020;
            $this->APTNESS['Idle']       -= 0.010;
        }
        if ($this->FIG_TYPE['isUnit'] ) {
            $this->APTNESS['Handy']      -= 0.040 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['FAQ']        += 0.025;
            $this->APTNESS['QA']         += 0.025;
            $this->APTNESS['Glossary']        -= 0.020 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['Command']    += 0.040;
            $this->APTNESS['Response']   += 0.030;
            $this->APTNESS['AIO']        += 0.020;
            $this->APTNESS['Idle']       -= 0.010;
        }
        if ($this->FIG_TYPE['isDatetime'] && $this->FIG_TYPE['isAsk']) {
            $this->APTNESS['Handy']      -= 0.010 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['FAQ']        -= 0.025;
            $this->APTNESS['QA']         += 0.1065;
            $this->APTNESS['Glossary']        -= 0.020 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['Command']    -= 0.040;
            $this->APTNESS['Response']   -= 0.030;
            $this->APTNESS['AIO']        -= 0.020;
            $this->APTNESS['Idle']       -= 0.010;
        }
        if ($this->FIG_TYPE['isDatetime'] ) {
            $this->APTNESS['Handy']      -= 0.010 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['FAQ']        -= 0.025;
            $this->APTNESS['QA']         += 0.025;
            $this->APTNESS['Glossary']        -= 0.020 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['Command']    += 0.040;
            $this->APTNESS['Response']   += 0.030;
            $this->APTNESS['AIO']        += 0.020;
            $this->APTNESS['Idle']       -= 0.010;
        }
        if ($this->FIG_TYPE['isSymbol'] ) {
            $this->APTNESS['Handy']      -= 0.040 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['FAQ']        += 0.025;
            $this->APTNESS['QA']         += 0.025;
            $this->APTNESS['Glossary']        -= 0.020 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['Command']    += 0.040;
            $this->APTNESS['Response']   += 0.030;
            $this->APTNESS['AIO']        += 0.020;
            $this->APTNESS['Idle']       -= 0.010;
        }
        if ($this->FIG_TYPE['isOption']['comparision.cpp'] ) {
            $this->APTNESS['Handy']      -= 0.040 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['FAQ']        -= 0.025;
            $this->APTNESS['QA']         += 0.025;
            $this->APTNESS['Glossary']        -= 0.020 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['Command']    -= 0.040;
            $this->APTNESS['Response']   -= 0.030;
            $this->APTNESS['AIO']        += 0.030 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['Idle']       -= 0.010;
        }
        if (!$this->FIG_TYPE['FAQ']) {
            $this->APTNESS['Handy']      += 0.025 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['FAQ']        -= 0.450;
            $this->APTNESS['QA']         += 0.045;
            $this->APTNESS['Glossary']        += 0.025 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['Command']    += 0.025;
            $this->APTNESS['Response']   += 0.025;
            $this->APTNESS['AIO']        += 0.030 * count((array)$this->FIG_TYPE['isWord']);
            $this->APTNESS['Idle']       -= 0.010;
        }

        $this->UNIFORM = array_map( fn($value) => $value/$word_count , $this->APTNESS);
        arsort($this->UNIFORM);
        $this->PICKED = array_keys($this->UNIFORM)[0];
        $this->ACCURACY = max($this->UNIFORM);
        return true;
    }

    /**
     * Recursive Part Checker
     *
     * @param string $checker
     * @param int $i
     */
    private function _checker($k, $i, $checker) {
        if($this->skip_mix[$checker]) {
            $this->skip_mix[$checker] = false;
            //return;
        }
        $checking = $this->matching->$checker($this->searches[$i]);
        if($checking) {
            $this->is_word = false;
            $this->FIG_TYPE[$checker][]= ($checking['set_name']) ?? $checking;
            $this->FIG_DATA[][$checker][$checking['set_name']][] = $this->searches[$i];
            $this->_impact($checker, $k, $checking);
            $this->skip_mix[$checker] = true;
        } else {
            $checking = $this->matching->$checker(trim($this->searches[$i]));
            if($checking) {
                $this->is_word = false;
                $this->FIG_TYPE[$checker][]= ($checking['set_name']) ?? $checking;
                $this->FIG_DATA[$checker][$checking['set_name']][] = trim($this->searches[$i]);
                $this->_impact($checker, $k, $checking);
                $this->skip_mix[$checker] = true;
            }
        }
    }

}
