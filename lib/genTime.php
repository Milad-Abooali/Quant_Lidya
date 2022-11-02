<?php

/**
 * Generation Time Calculator
 *
 * @package    AI
 * @category   Lib
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  2012 - 2021 Codebox
 * @license    https://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 * @version    1.0.0
 */

class genTime
{

    /**
     * @var array $Errors List Of Errors
     * @var array $blocks List To Keep Blocks Data
     */
    public $Errors;
    private $blocks;

    /**
     * genTime constructor.
     *
     * @param string $block
     */
    function __construct($block) {
        $this->blocks[$block]['start'] = microtime(true);
        return $this->blocks[$block]['start'];
    }

    /**
     * Add Block Start Time
     *
     * @param string $block
     * @return mixed
     */
    public function start($block) {
        if(isset($this->blocks[$block]['start'])) {
            $caller = array_shift(debug_backtrace());
            $this->Errors['block_exist'] = 'Block: '.$block.' | '.$caller['file'].' Line '.$caller['line'];
            return $this->Errors;
        }
        $this->blocks[$block]['start'] = microtime(true);
        return $this->blocks[$block]['start'];
    }

    /**
     * Add Block End Time
     *
     * @param string $block
     * @return mixed
     */
    public function end($block) {
        if (!isset($this->blocks[$block]['start'])) {
            $caller = array_shift(debug_backtrace());

            $this->Errors['miss_start'] = 'Block: '.$block.' | '.$caller['file'].' Line '.$caller['line'];
            return $this->Errors;
        }
        $this->blocks[$block]['end']  = microtime(true);
        $this->blocks[$block]['time'] = round($this->blocks[$block]['end'] - $this->blocks[$block]['start'],4, PHP_ROUND_HALF_UP);
        return $this->blocks[$block]['time'];
    }

    /**
     * Get Block(s) Time
     *
     * @param string|null $block Null to list all blocks
     * @return mixed
     */
    public function get($block=null) {
        if($block) return $this->Errors ?? $this->blocks[$block];
        return $this->Errors ?? $this->blocks;
    }

}