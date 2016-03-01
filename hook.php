<?php
/**
 * Created by PhpStorm.
 * User: phithon
 * Date: 16/2/29
 * Time: 下午11:30
 */

require_once "WebHook.php";

$config = json_decode(file_get_contents("./config.json"), true);
try {
    $git = new GitTool\WebHook($config);
    $git->run();
} catch (\ErrorException $e) {
    $error = "Error: " . $e->getMessage() . " File: " . $e->getFile() . ":" . $e->getLine();
    echo $error;
    error_log($error, 0);
}
