<?php
/**
 * Created by PhpStorm.
 * User: phithon
 * Date: 16/2/29
 * Time: 下午11:30
 */

require_once "WebHook.php";

$git = new GitTool\WebHook([
    "token" => "12345678",
    "type" => "github",
    "items" => [
        [
            'repo' => 'git@github.com:phith0n/webhook.git',
            'branch' => 'master',
            'script' => '.'
        ]
    ]
]);
$git->run();