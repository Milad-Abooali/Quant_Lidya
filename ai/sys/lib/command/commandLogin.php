<?php

    require_once 'sys/lib/command/command.php';

/**
 * Class session
 */
    class commandLogin extends command
    {

        public function walk($step, $parts) {
            if (!$step) {
                $step = 1;
            }
            switch ($step) {
                case 1:
                    // null
                    break;
                case 2:
                    // null
                    break;
                case 3:
                    // null
                    break;
            }
        }
        public function callback() {

        }
        public function tend($status) {

        }

    }