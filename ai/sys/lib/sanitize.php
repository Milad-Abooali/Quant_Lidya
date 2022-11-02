<?php

/**
 * Sanitize
 *
 * @package    AI
 * @category   Lib
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  2012 - 2021 Codebox
 * @license    https://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 * @version    1.0.0
 */

namespace AI;

class sanitize
{

    /**
     * Sanitize Input
     *
     * @todo Lang Detection
     *
     * @param $input
     * @return array
     */
    public static function string($input) {
        $output = array();
        $output['original'] = $input;
        // Check & Remove Email
        $output['email'] = matchingPhrases::emailExtractor(strtolower($input));
        if ($output['email']) {
            foreach($output['email'] as $email) {
                $input = str_replace($email,'',$input);
            }
        }

        // Check & Remove Marks
        $marks = array(
                        '`',
                        '-',
                        '=',
                        '~',
                        '!',
                        '@',
                        '#',
                        '$',
                        '%',
                        '^',
                        '&',
                        '*',
                        '(',
                        ')',
                        '_',
                        '\\',
                        '|',
                        ';',
                        ':',
                        "'",
                        '"',
                        '/',
                        '?',
                        '.',
                        '>',
                        ',',
                        '<'
                    );
        foreach($marks as $mark) if(!(!strpos($input, $mark))) {
            $output['mark'][$mark] = true;
            $input = str_replace($mark,'',$input);
        }

        // trim
        $output['trim'] = trim(strtolower($input));
        $output['trim_length'] = strlen($output['trim']);

        // Atom
        $output['atom'] = str_replace(' ', '', $output['trim']);
        $output['atom_length'] = strlen($output['atom']);

        // Atom vs Trim
        similar_text($output['trim'], $output['atom'], $output['atom_vs_trim']);

        // First & Last Character
        $output['first_char'] = substr($output['trim'],0,1);
        $output['last_char']  = substr($output['trim'],-1);

        // Parts
        $output['parts'] = explode(' ', $output['trim']);
        foreach ($output['parts'] as $k => $val) {
            $output['pp'][] = $val.($output['parts'][$k+1] ?? false);
            similar_text($val, $output['atom'], $output['atom_vs_parts'][$val]);
        }
        foreach ($output['pp'] as $val) similar_text($val, $output['atom'], $output['atom_vs_pp'][$val]);
        foreach ($output['parts'] as $part) $output['PFC'] .= strtolower (substr($part,0,1));
        foreach ($output['parts'] as $part) $output['PEC'] .= strtolower (substr($part,-1));
        $output['LID'] = strlen($output['PFC']) . $output['atom_length'] . $output['trim_length'];

        return $output;
    }

}
