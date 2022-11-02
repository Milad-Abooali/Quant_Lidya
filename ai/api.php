<?php
    header('Content-Type: text/xml');
    (file_exists('sys/config.php')) ? require_once  'sys/config.php' : exit();
    $run_page = 'sys/run/'.$_GET['get'].'.php';
    if (file_exists($run_page)) include_once $run_page;
    function arrayToXml($array, $rootElement = null, $xml = null) {
        $_xml = $xml;
        if ($_xml === null) {
            $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
        }
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                arrayToXml($v, $k, $_xml->addChild($k));
            } else {
                if (is_int($k)) $k = 'item-'.$k;
                $_xml->addChild($k, $v);
            }
        }
        return $_xml->asXML();
    }
    if($_RUN && $_API_ON) print arrayToXml($_RUN);