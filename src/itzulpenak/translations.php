<?php
function trans($indexPhrase)
{
 
    static $tranlationsArray = array();


    if (isset($_SESSION["_LANGUAGE"]) && file_exists(APP_DIR . '/itzulpenak/' . $_SESSION["_LANGUAGE"] . '.php')) {
        $url = APP_DIR . '/itzulpenak/';
        $tranlationsArray = include($url . $_SESSION["_LANGUAGE"] . '.php');
    }

    if (!array_key_exists($indexPhrase, $tranlationsArray)) {
        return $indexPhrase;
    } else {
        return $tranlationsArray[$indexPhrase]; 
    }
}
?>
