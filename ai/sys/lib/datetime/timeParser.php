<?php

/**
 * Time Paser
 *
 * @package    AI
 * @category   Lib
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  2012 - 2021 Codebox
 * @license    https://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 * @version    1.0.0
 */

namespace AI;

use ICanBoogie\DateTime;

class timeParser
{
    public $Time;

    function __construct() {
        $this->Time = new DateTime('now', 'Europe/Athens');
    }

    public function modifire($val, $type){
        $this->Time->modify($val.' '.$type);
        return $this->Time;
    }

}