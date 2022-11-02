<?php /** @noinspection ALL */

namespace AI;
/* Generation Time Calculator */
include_once 'sys/lib/genTime.php';
$genTime = new genTime('Handler');

// Session Change
$old_session_id = session_id();
session_write_close();
session_id($_REQUEST['sessionId']);
session_start();


// Handler ID
$h_old_id = ($_SESSION['handler']) ? array_key_last($_SESSION['handler']) : 0;
$h_new_id = $h_old_id+1;

// Input Getter
$input_text = $_REQUEST['text'] ?? null;
if(!$input_text) {   # Input is null
    $_SESSION['handler'][$h_new_id]['error'][] = 'No Input!';

    // Output > Error
    $_RUN->e = $_SESSION['handler'][$h_new_id]['error'];

    // Output > Timer
    $genTime->end('Handler');
    $_RUN->genTime = $genTime->get();

}
else {   # Input exist
    global $db_aifx;
    global $db_admin;
    $insert = array(
        'user_id' => ($_SESSION['id'] ?? null),
        'session_id' => session_id(),
        'socket_id' => $_REQUEST['socketId'] ?? null,
        'input_text' => $input_text,
        'engine' => null,
        'agent_id' => null,
        'msg' => null,
        'res' => null,
        'feedback' => null
    );
    $message_id = $db_aifx->insert('messages_archive', $insert);
    unset($insert);
    $_RUN->input = $input_text;

    if (!$message_id) {
        $_SESSION['handler'][$h_new_id]['error'][] = end($db_aifx->log());
    } else {
        $_RUN->UUID = $_REQUEST['uuid'];
        $_RUN->MSG_ID = $message_id;
        $insert = array(
            'id' => $message_id,
        );
        $db_aifx->insert('messages_feedml', $insert);
        unset($insert);

        $_SESSION['handler'][$h_new_id]['input'] = $input_text;

        /*  Listener */
        $genTime->start('Check Listener');
        if ($_REQUEST['listener'] ?? false) $listener = $_REQUEST['listener'];
        if (!$listener && $_SESSION['handler'][$h_old_id]['listener']) $listener = $_SESSION['handler'][$h_old_id]['listener'];
        if (!$listener && $h_new_id == 1) $listener = 'initial::start()';
        if (!$listener && $h_new_id > 1) $listener = 'initial::idle()';
        $genTime->end('Check Listener');

        /*  Sanitize */
        $genTime->start('Sanitize');
        include_once 'sys/lib/sanitize.php';
        $sanitized_input = sanitize::string($_REQUEST['text']);
        if ($sanitized_input) {
            $update = array(
                'sanitize' => json_encode($sanitized_input)
            );
            $db_aifx->updateId('messages_feedml', $message_id, $update);
            unset($update);
        }
        $genTime->end('Sanitize');

        /*  AI Pre Func */
        $genTime->start('AIPF');
        $where = "instr('" . $sanitized_input['trim'] . "', text) > 0";
        $_RUN->AIPF = $db_aifx->select('pre_functions', $where);

        if ($_RUN->AIPF) {
            $_SESSION['handler'][$h_new_id]['engine'] = 'AIPF';
            $_SESSION['handler'][$h_new_id]['res'] = 'AIPF';
            $_RUN->brain['PICKED'] = 'AIPF';

            if ($_SESSION['id']) {

                $extra_word = str_replace($_RUN->AIPF[0]['text'], '', $sanitized_input['trim']);
                $extra_word = trim(str_replace('  ', ' ', $extra_word));

                if (count($_RUN->AIPF) == 1) {   // One Function

                    if ($_RUN->AIPF[0]['handler_type'] == 'getter') {
                        require_once 'sys/lib/aipf/' . $_RUN->AIPF[0]['class'] . '.php';
                        $called_class = '\AI\\' . $_RUN->AIPF[0]['class'];
                        $called_function = $_RUN->AIPF[0]['function'];
                        $class = new $called_class;
                        $aipf = $class->$called_function($extra_word);
                        if ($aipf) {
                            $_SESSION['handler'][$h_new_id]['res'] = $aipf;
                        } else {
                            $_SESSION['handler'][$h_new_id]['res'] = "I can't find anything related to " . $extra_word;
                        }
                    } else if ($_RUN->AIPF[0]['handler_type'] == 'setter') {
                        require_once 'sys/lib/aipf/' . $_RUN->AIPF[0]['class'] . '.php';
                        $called_class = '\AI\\' . $_RUN->AIPF[0]['class'];
                        $called_function = $_RUN->AIPF[0]['function'];
                        $class = new $called_class;
                        $aipf = $class->$called_function($extra_word);
                        if ($aipf) {
                            $_SESSION['handler'][$h_new_id]['res'] = 'OK, Use the form to update ' . $extra_word;
                            $_SESSION['handler'][$h_new_id]['engine'] = 'AIPF';
                            $_SESSION['handler'][$h_new_id]['nengine'] = 'Form';
                            $_SESSION['handler'][$h_new_id]['listener'] = 'CommandUpdate';
                            $_SESSION['handler'][$h_new_id]['topic'] = $_SESSION['handler'][$h_old_id]['topic'] ?? array();

                            require_once 'sys/lib/aipf/' . $aipf[0]['class'] . '.php';
                            $item_val_class = '\AI\\' . $aipf[0]['class'];
                            $item_val = $item_val_class::getCustom($extra_word);

                            $_RUN->form = array(
                                0 => [
                                    'class' => $aipf[0]['class'],
                                    'title' => $aipf[0]['title'],
                                    'item' => $aipf[0]['item'],
                                    'block' => $aipf[0]['block'],
                                    'value' => $item_val[0]['res'],
                                    'val_html_type' => $aipf[0]['val_html_type'],
                                    'form_type' => $aipf[0]['item']['form_type'] ?? 'updateItem'
                                ]
                            );
                        } else {
                            $_SESSION['handler'][$h_new_id]['res'] = "There is not any option to update " . $extra_word;
                        }

                    }

                } else if (count($_RUN->AIPF) > 1) {   // Multi Function
                    $res = array();
                    foreach ($_RUN->AIPF as $AIPF_current) {
                        require_once 'sys/lib/aipf/' . $AIPF_current['class'] . '.php';
                        $called_class = '\AI\\' . $AIPF_current['class'];
                        $called_function = $AIPF_current['function'];
                        $class = new $called_class;
                        $aipf = $class->$called_function($extra_word);
                        $res[] = $aipf;
                    }
                    if ($res) {
                        $_SESSION['handler'][$h_new_id]['res'] = $res;
                    } else {
                        $_SESSION['handler'][$h_new_id]['res'] = "I can't find anything related to " . $extra_word;
                    }


                } else {   // No Function
                    $_SESSION['handler'][$h_new_id]['res'] = "I can't understand you...";
                }
            } else {
                $_SESSION['handler'][$h_new_id]['nengine'] = 'Command';
                $_RUN->brain['FIG_TYPE']['isCmd'][] = 'login.cpp';
                $_SESSION['handler'][$h_new_id]['res'] = 'You need to login first. Do you like to login now?';
                $_SESSION['handler'][$h_new_id]['listener'] = 'Login';
            }
            $genTime->end('AIPF');
        } else {
            $genTime->end('AIPF');

            /*  Brain */
            $genTime->start('Brain');
            include_once 'sys/lib/brain.php';
            $brain = new brain();
            $brain_processed = $brain->process($sanitized_input);
            if ($brain_processed) {

                $update = array(
                    'fig_type' => json_encode($brain->FIG_TYPE),
                    'fig_data' => json_encode($brain->FIG_DATA),
                    'picked' => json_encode($brain->PICKED),
                    'accuracy' => json_encode($brain->ACCURACY)
                );
                $db_aifx->updateId('messages_feedml', $message_id, $update);
                unset($update);

                $_SESSION['handler'][$h_new_id]['engine'] = $brain->PICKED;
                $_SESSION['handler'][$h_new_id]['nengine'] = $brain->PICKED;
                $_SESSION['handler'][$h_new_id]['res'] = $brain->PICKED;

                $_RUN->brain['PICKED'] = $brain->PICKED;
                $_RUN->brain['ACCURACY'] = $brain->ACCURACY;
                $_RUN->brain['APTNESS'] = $brain->APTNESS;
                $_RUN->brain['UNIFORM'] = $brain->UNIFORM;
                $_RUN->brain['FIG_TYPE'] = $brain->FIG_TYPE;
                $_RUN->brain['FIG_DATA'] = $brain->FIG_DATA;
            }
            $genTime->end('Brain');

            /*  Session Handler */
            $_SESSION['handler'][$h_new_id]['MSG_ID'] = $message_id;
            $_SESSION['handler'][$h_new_id]['PICKED'] = $brain->PICKED;
            $_SESSION['handler'][$h_new_id]['ACCURACY'] = $brain->ACCURACY;

            /**
             * Handel Response
             */
            if ($_RUN->brain['PICKED'] == 'Response') {
                $response = array_unique($_RUN->brain['FIG_TYPE']['isResponse'] ?? array());
                if (count($response) == 1) {

                    if (in_array($_SESSION['handler'][$h_old_id]['engine'], array('Command', 'AIPF'))) {
                        if ($response[0] == 'true.cpp') {
                            $_SESSION['handler'][$h_new_id]['res'] = 'OK, Fill the form.';
                            $_SESSION['handler'][$h_new_id]['engine'] = 'Form';
                            $_SESSION['handler'][$h_new_id]['nengine'] = 'AIAML';
                            $_SESSION['handler'][$h_new_id]['listener'] = $_SESSION['handler'][$h_old_id]['nengine'] . $_REQUEST['listener'];
                            $_SESSION['handler'][$h_new_id]['topic'] = $_SESSION['handler'][$h_old_id]['topic'] ?? array();
                        } else {
                            $_SESSION['handler'][$h_new_id]['res'] = "So, now what?";
                        }
                    } else {
                        $_SESSION['handler'][$h_new_id]['res'] = "ok, sure";
                    }
                } else {
                    $_SESSION['handler'][$h_new_id]['res'] = "it's not clear !";
                }
            }

            /**
             * Handel QA
             */
            if ($_RUN->brain['PICKED'] == 'QA') {

                if ($brain->FIG_TYPE['isDatetime']) {   // Query DateTime

                    $items = count($brain->FIG_TYPE['isDatetime']);
                    $date_items = [];
                    foreach ($brain->FIG_DATA as $fig) {
                        if ($fig['isDatetime']) {
                            $date_items[array_key_last($fig['isDatetime'])] = end($fig['isDatetime'])[0];
                        }
                    }
                    $_SESSION['handler'][$h_new_id]['topic'] = $date_items;

                    require_once 'sys/lib/datetime/DateTime.php';
                    require_once 'sys/lib/datetime/timeParser.php';
                    $timeParser = new timeParser();
                    if ($brain->FIG_TYPE['isModifire']) {
                        $number = $modify_val = 0;
                        if ($brain->FIG_DATA['number']['int_char'][0]) $number = matchingPhrases::char2number($brain->FIG_DATA['number']['int_char'][0]);
                        if (!$number) $number = $brain->FIG_DATA['number']['int'][0] ?? 1;
                        if ($brain->FIG_TYPE['isModifire'][0] == 'minus.cpp') {
                            $modify_val -= $number;
                        } else if ($brain->FIG_TYPE['isModifire'][0] == 'plus.cpp') {
                            $modify_val += $number;
                        }
                    }
                    $modify_type = $date_items['dates.cpp'] ?? 'day';
                    if ($modify_type == 'days' || $modify_type == 'day') $modify_type = 'day';
                    else if ($modify_type == 'weeks' || $modify_type == 'week') $modify_type = 'week';
                    else if ($modify_type == 'months' || $modify_type == 'month') $modify_type = 'month';
                    else if ($modify_type == 'years' || $modify_type == 'year') $modify_type = 'year';
                    else $modify_type = false;
                    if ($modify_type && $modify_val) $timeParser->modifire($modify_val, $modify_type);

                    if ($date_items['days.cpp'] && !$date_items['weekdays.cpp'] && !$date_items['months.cpp']) {
                        $date_item = $date_items['days.cpp'];
                        if ($date_item == 'today') $_SESSION['handler'][$h_new_id]['res'] = $timeParser->Time->format('l jS F Y');
                        else if ($date_item == 'yesterday') $_SESSION['handler'][$h_new_id]['res'] = $timeParser->Time->yesterday->format('l jS F Y');
                        else if ($date_item == 'tomorrow') $_SESSION['handler'][$h_new_id]['res'] = $timeParser->Time->tomorrow->format('l jS F Y');
                        else $_SESSION['handler'][$h_new_id]['res'] = 'Your asking day is not clear !';
                    } else if ($date_items['dates.cpp'] && !$date_items['weekdays.cpp'] && !$date_items['months.cpp']) {
                        if ($date_items['dates.cpp'] == 'day' || $date_items['dates.cpp'] == 'days') $_SESSION['handler'][$h_new_id]['res'] = $timeParser->Time->format('l');
                        else if ($date_items['dates.cpp'] == 'week' || $date_items['dates.cpp'] == 'weeks') $_SESSION['handler'][$h_new_id]['res'] = 'The ' . $timeParser->Time->week . ' week of this year';
                        else if ($date_items['dates.cpp'] == 'month' || $date_items['dates.cpp'] == 'months') $_SESSION['handler'][$h_new_id]['res'] = $timeParser->Time->format('F');
                        else if ($date_items['dates.cpp'] == 'year' || $date_items['dates.cpp'] == 'years') $_SESSION['handler'][$h_new_id]['res'] = $timeParser->Time->format('Y');
                        else $_SESSION['handler'][$h_new_id]['res'] = 'Your asking date is not clear !';

                    } else if ($date_items['weekdays.cpp']) {
                        $date_item = $date_items['days.cpp'];
                        $weekday = $date_items['weekdays.cpp'];
                        if ($date_item == 'today') $date_checker = (strtolower($timeParser->Time->format('l')) == $weekday
                            || strtolower($timeParser->Time->format('S')) == $weekday
                            || strtolower($timeParser->Time->format('D')) == $weekday
                        ) ? false : $timeParser->Time->format('l');
                        else if ($date_item == 'yesterday') $date_checker = (strtolower($timeParser->Time->yesterday->format('l')) == $weekday
                            || strtolower($timeParser->Time->yesterday->format('S')) == $weekday
                            || strtolower($timeParser->Time->yesterday->format('D')) == $weekday
                        ) ? false : $timeParser->Time->yesterday->format('l');
                        else if ($date_item == 'tomorrow') $date_checker = (strtolower($timeParser->Time->tomorrow->format('l')) == $weekday
                            || strtolower($timeParser->Time->tomorrow->format('S')) == $weekday
                            || strtolower($timeParser->Time->tomorrow->format('D')) == $weekday
                        ) ? false : $timeParser->Time->tomorrow->format('l');
                        else $date_checker = ' really not clear to me !';
                        $_SESSION['handler'][$h_new_id]['res'] = ($date_checker == false) ? "Yes, it's true" : ("No, it's " . $date_checker);

                    } else if ($date_items['months.cpp'] && $date_items['dates.cpp']) {
                        $date_item = $date_items['dates.cpp'];
                        $month = $date_items['months.cpp'];
                        if ($date_item == 'years' || $date_item == 'year') $date_checker =
                            (strtolower($timeParser->Time->format('F')) == $month
                                || strtolower($timeParser->Time->format('M')) == $month
                            ) ? false : $timeParser->Time->format('F');
                        else if ($date_item == 'months' || $date_item == 'month') $date_checker =
                            (strtolower($timeParser->Time->format('F')) == $month
                                || strtolower($timeParser->Time->format('M')) == $month
                            ) ? false : $timeParser->Time->format('F');
                        else if ($date_item == 'weeks' || $date_item == 'week') $date_checker =
                            (strtolower($timeParser->Time->format('F')) == $month
                                || strtolower($timeParser->Time->format('M')) == $month
                            ) ? false : $timeParser->Time->format('F');
                        else if ($date_item == 'days' || $date_item == 'day') $date_checker =
                            (strtolower($timeParser->Time->format('F')) == $month
                                || strtolower($timeParser->Time->format('M')) == $month
                            ) ? false : $timeParser->Time->format('F');
                        else $date_checker = ' really not clear to me !';
                        $_SESSION['handler'][$h_new_id]['res'] = ($date_checker == false) ? "Yes, it's true" : ("No, it's " . $date_checker);

                    } else if ($date_items['months.cpp'] && $date_items['days.cpp']) {
                        $date_item = $date_items['days.cpp'];
                        $month = $date_items['months.cpp'];
                        if ($date_item == 'today') $date_checker = (strtolower($timeParser->Time->format('F')) == $month
                            || strtolower($timeParser->Time->format('M')) == $month
                        ) ? false : $timeParser->Time->format('F');
                        else if ($date_item == 'yesterday') $date_checker = (strtolower($timeParser->Time->yesterday->format('F')) == $month
                            || strtolower($timeParser->Time->yesterday->format('M')) == $month
                        ) ? false : $timeParser->Time->yesterday->format('F');
                        else if ($date_item == 'tomorrow') $date_checker = (strtolower($timeParser->Time->tomorrow->format('F')) == $month
                            || strtolower($timeParser->Time->tomorrow->format('M')) == $month
                        ) ? false : $timeParser->Time->tomorrow->format('F');
                        else $date_checker = ' really not clear to me !';
                        $_SESSION['handler'][$h_new_id]['res'] = ($date_checker == false) ? "Yes, it's true" : ("No, it's " . $date_checker);

                    } else {
                        $_SESSION['handler'][$h_new_id]['res'] = 'What!';

                    }

                }


            }

            /**
             * Handel Command
             */
            if ($_RUN->brain['PICKED'] == 'Command') {
                if ($_SESSION['id']) {
                    $command = array(
                        'register.cpp' => [
                            'listener' => null,
                            'class' => null,
                            'topic' => null,
                            'res' => 'You need logout first!'
                        ],
                        'forgot.cpp' => [
                            'listener' => 'Recover',
                            'class' => null,
                            'topic' => $sanitized_input['email'],
                            'res' => 'Do you need to reset your password?'
                        ],
                        'login.cpp' => [
                            'listener' => null,
                            'class' => null,
                            'topic' => null,
                            'res' => 'You are logged in.',
                        ],
                        'logout.cpp' => [
                            'listener' => 'Logout',
                            'class' => null,
                            'topic' => null,
                            'res' => 'Do you want to logout?'
                        ],
                        'deposit.cpp' => [
                            'listener' => null,
                            'class' => null,
                            'topic' => null,
                            'res' => null
                        ],
                        'withdraw.cpp' => [
                            'listener' => null,
                            'class' => null,
                            'topic' => null,
                            'res' => null
                        ],
                        'buy.cpp' => [
                            'listener' => null,
                            'class' => null,
                            'topic' => null,
                            'res' => null
                        ],
                        'sell.cpp' => [
                            'listener' => null,
                            'class' => null,
                            'topic' => null,
                            'res' => null
                        ],
                        'stop.cpp' => [
                            'listener' => null,
                            'class' => null,
                            'topic' => null,
                            'res' => null
                        ],
                    );
                } else {
                    $command = array(
                        'register.cpp' => [
                            'listener' => 'Register',
                            'class' => null,
                            'topic' => $sanitized_input['email'],
                            'res' => 'Do you want to create new account?'
                        ],
                        'forgot.cpp' => [
                            'listener' => 'Recover',
                            'class' => null,
                            'topic' => $sanitized_input['email'],
                            'res' => 'Do you need to recover your password?'
                        ],
                        'login.cpp' => [
                            'listener' => 'Login',
                            'class' => null,
                            'topic' => $sanitized_input['email'],
                            'res' => 'Do you want to login?'
                        ],
                        'logout.cpp' => [
                            'listener' => null,
                            'class' => null,
                            'topic' => null,
                            'res' => 'You are not logged in!',
                        ]
                    );
                }
                $_SESSION['handler'][$h_new_id]['res'] = array();

                if ($_RUN->brain['FIG_TYPE']['isCmd']) foreach ($_RUN->brain['FIG_TYPE']['isCmd'] as $cmd) {
                    $_SESSION['handler'][$h_new_id]['res'][] = $command[$cmd];
                    if (count($_RUN->brain['FIG_TYPE']['isCmd']) == 1) {
                        $_SESSION['handler'][$h_new_id]['listener'] = $command[$cmd]['listener'];
                        $_SESSION['handler'][$h_new_id]['topic'] = $command[$cmd]['topic'];
                    }
                }

                if ($_RUN->brain['FIG_TYPE']['isCmd']) if (count($_RUN->brain['FIG_TYPE']['isCmd']) > 1) {
                    $_SESSION['handler'][$h_new_id]['topic'] = array();
                    $_SESSION['handler'][$h_new_id]['listener'] = 'Selector';
                }


            }

        }

        // Output > Section
        $_RUN->section = $_SESSION['handler'][$h_new_id]['section'];

        // Output > Extra
        $_RUN->extra = $_SESSION['handler'][$h_new_id]['extra'];

        // Output > Sanitized
        if ($sanitized_input) $_RUN->sanitize = $sanitized_input;

        // Output > Listener
        $_RUN->listener = $_SESSION['handler'][$h_new_id]['listener'] ?? 'AIML';

        // Output > Class
        $_RUN->class = $_SESSION['handler'][$h_new_id]['class'];

        // Output > Topic
        $_RUN->topic = $_SESSION['handler'][$h_new_id]['topic'];

        // Output > Walker
        $_RUN->walker = $_SESSION['handler'][$h_new_id]['walker'];

        // Output > Response
        $_RUN->res = $_SESSION['handler'][$h_new_id]['res'];

        // Output > Engine
        $_RUN->engine = $_SESSION['handler'][$h_new_id]['engine'] ?? 'AIO';

        // Output > Next Engine
        $_RUN->nengine = $_SESSION['handler'][$h_new_id]['nengine'] ?? $_RUN->engine;

    }

    // Output > Error
    $_RUN->e = $_SESSION['handler'][$h_new_id]['error'];

    // Output > Timer
    $genTime->end('Handler');
    $_RUN->genTime = $genTime->get();


    // Update Message Response
    if ($message_id){
        $update['engine'] = $_RUN->engine;
        $update['handler'] = json_encode($_RUN);
        $db_aifx->updateId('messages_archive', $message_id, $update);
        unset($update);
    }
}

// Session Change revers
session_write_close();
session_id($old_session_id);
session_start();