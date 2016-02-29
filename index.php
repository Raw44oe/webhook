<?php
/**
 * Created by PhpStorm.
 * User: phithon
 * Date: 16/2/29
 * Time: 下午11:30
 */

require_once "WebHook.php";

try {
    $git = new GitTool\WebHook([
        "token" => "12345678",
        "type" => "github",
        "items" => [
            [
                'repo' => 'phith0n/webhook',
                'script' => 'update.sh'
            ]
        ],
    ]);
    $git->run();
} catch (\ErrorException $e) {
    $error = "Error: " . $e->getMessage() . " File: " . $e->getFile() . ":" . $e->getLine();
    echo $error;
    error_log($error, 0);
}
