<?php

class Logger {

    public static function log($param) {

        $out = fopen('php://stdout', 'w');
        fwrite($out, print_r($param, true) . "\n");
    }

    public static function logWithMsg($msg, $param) {

        $out = fopen('php://stdout', 'w');
        fwrite($out, $msg . " : " . print_r($param, true) . "\n");
    }
}