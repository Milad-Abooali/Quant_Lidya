<?php

/**
 * Class command
 */
    abstract class command
    {

        public function __construct($input) {
            $this->input = $input;
        }

        use engineMain;

    }