<?php
/**
 * AddOns
 * Wikipedia
 */

    $page['title'] = 'Wikipedia AddOns';

    $_AJAX_ON = true;
    $_API_ON  = false;

    $word = $_REQUEST['w'] ?? false;
    if (!$word) {
        $error = true;
    } else {
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => array("Cookie: foo='bar'"),
                'user_agent' => $_SERVER['HTTP_USER_AGENT']
            )
        );
        $context = stream_context_create($opts);
        $word = trim($word);
        $url  = "https://en.wikipedia.org/w/api.php?action=query&format=json&list=search&srwhat=text&srsearch=".str_replace(' ','%20',$word);
        $data = file_get_contents($url, false, $context);
        $json = json_decode($data, true);
        if ($json['query']['search'] ?? false) foreach ($json['query']['search'] as $page) $_RUN->res[] = $page;
    }

    $_RUN->e = $error;