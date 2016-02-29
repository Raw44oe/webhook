<?php
/**
 * Created by PhpStorm.
 * User: phithon
 * Date: 16/2/29
 * Time: 下午10:16
 */

namespace GitTool;


class WebHook
{
    protected $items = [];
    protected $token = "webhook";
    protected $variable = [];
    protected $rawHttpBody;
    protected $type = "github";

    function __construct($config)
    {
        set_error_handler([$this, 'exceptCatch']);
        $this->items = $this->parseConfig($config);
        $this->rawHttpBody = file_get_contents('php://input');
        $this->variable = $this->parseVariable($this->rawHttpBody);
    }

    protected function exceptCatch($errno, $errstr, $errfile, $errline, $errcontext)
    {
        throw new \ErrorException($errstr, $errno, $errno, $errfile, $errline);
    }

    protected function checkToken()
    {
        $ret = false;
        switch($this->type) {
            case "github":
                $sign = isset($_SERVER['HTTP_X_HUB_SIGNATURE']) ? $_SERVER['HTTP_X_HUB_SIGNATURE'] : "";
                list ($algo, $sign) = explode("=", $sign);
                $ret = ($algo == "sha1" && $sign === hash_hmac($algo, $this->rawHttpBody, $this->token));
                break;
            case "coding":
                $ret = isset($this->variable['token']) && $this->variable['token'] === $this->token;
                break;
            case "bitbucket":
                $ret = true;
                break;
        }
        return $ret;
    }

    protected function parseConfig($config)
    {
        $items = [];
        if(is_array($config['items'])) {
            foreach($config['items'] as $item) {
                $items[$item['repo']] = realpath(__DIR__ . '/' . basename($item['script']));
            }
        }
        if(is_string($config['token'])) {
            $this->token = $config['token'];
        }
        if(in_array($config['type'], ['github', 'bitbucket', 'coding'])) {
            $this->type = $config['type'];
        }
        return $items;
    }

    protected function parseVariable($data)
    {
        return json_decode($data, true);
    }

    public function run()
    {
        if(!$this->checkToken()) {
            throw new \ErrorException("Bad token", E_USER_ERROR, E_USER_ERROR, __FILE__, __LINE__);
        }
        if(isset($this->variable['repository']['full_name']) &&
            array_key_exists($this->variable['repository']['full_name'], $this->items)) {
            $exec_filename = $this->items[$this->variable['repository']['full_name']];
            error_log("bash " . escapeshellarg($exec_filename) . " " . escapeshellarg($this->rawHttpBody), 0);
            shell_exec("bash " . escapeshellarg($exec_filename) . " " . escapeshellarg($this->rawHttpBody));
        }
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        restore_error_handler();
    }
}