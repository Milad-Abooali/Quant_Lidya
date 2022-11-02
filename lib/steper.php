<?php

    /**
     * Class Steper
     *
     * Steper
     *
     * @package    -
     * @author     Milad Abooali <m.abooali@hotmail.com>
     * @copyright  -
     * @license    -
     * @version    1.0
     */

    class Steper
    {


        private $db,$step=array();

        function __construct() {
            global $db;
            $this->db = $db;
        }

        /**
         * Do
         * @param object $class
         * @param string $action
         * @param $rows
         * @return bool
         */
        public function do($class, $action, $rows) {

            foreach ($rows as $row) {
                $class->$action($row);
            }
            return true;
        }

        /**
         * Walk
         * @param object $class
         * @param string $action
         * @param array $rows
         * @param int $offset
         * @param int $step_length
         * @return int
         */
        public function walk($class, $action, $rows, $offset=0, $step_length=100) {
            $n_offset = $offset + $step_length;
                if(count($rows) > $n_offset) {
                    $this->do($class,$action,$rows,$n_offset);
                }
            return $n_offset;
        }

        public function stop() {
            unset($step);
        }


        public function start($rows,$step_length) {
            $steps = $rows/$step_length;

            $step[1] =  0;
//            foreach ($steps as $st) $step[] =
        }

    }