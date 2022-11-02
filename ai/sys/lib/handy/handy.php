<?php

require_once 'sys/traitEngineMain';

/**
 * Class handy
 */
    abstract class handy
    {

        public function __construct($input) {
            $this->input = $input;
        }

        use engineMain;

    }