<?php

namespace handlers;

function custom_error_handler($er_lvl, $er_msg, $er_file, $er_line){
    $message = "[" . date("Y/m/d-H:i:s") . "][Er Code: " . $er_lvl . "]" . $er_msg . " on file " . $er_file . " in line " . $er_line . "\n";
    $destination = "../log/error.log";
    error_log($message, 3, $destination);
}

set_error_handler("handlers\custom_error_handler");

echo($variable);