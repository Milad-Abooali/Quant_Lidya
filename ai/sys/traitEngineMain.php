<?php


/**
 * Traits
 * @package    -
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  -
 * @license    -
 * @version    1.0.0
 * @update     Created by M.Abooali 09-2-2021
 */

    trait engineMain {

        /**
         * @var string $input input text
         */
        public string $input;

        /**
         * Walker
         *
         * @param $step
         * @param $parts
         */
        abstract public function walk($step, $parts);

        /**
         * Tend Other Method
         *
         * @param string $method
         */
        abstract public function tend($method);

        /**
         * Callback
         */
        abstract public function callback();

        /**
         * Change The Class
         *
         * @param string $class
         */
        protected function changeClass($class) { $_SESSION['brain']['class'] = strtolower($class); }

        /**
         * Change The Topic
         *
         * @param string $topic
         */
        protected function changeTopic($topic) { $_SESSION['brain']['topic'] = ucfirst($topic); }

        /**
         * Set Tend
         *
         * @param string $method
         */
        protected function setTend($method) { $_SESSION['brain']['tend'] = $method; }

    }