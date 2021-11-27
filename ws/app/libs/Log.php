<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author Andre
 */
class Log {

    /**
     * Enable debug mode as default
     * @var type 
     */
    public static $enabled = DOMINIO == 'dev';

    /**
     * Grava informações no Log com label INFO
     */
    public static function info() {
        if (self::$enabled || defined('ENABLE_LOG')) {
            foreach (func_get_args() as $info) {
                self::setLog("INFO", $info);
            }
        }
    }

    /**
     * Grava informações no Log com label ERROR
     */
    public static function error() {
//        if (self::$enabled || defined('ENABLE_LOG')) {
        foreach (func_get_args() as $error) {
            self::setLog("ERROR", $error);
        }
//        }
    }

    /**
     * Grava informações no Log com label ERROR
     * @param type $error
     */
    public static function debug() {
        foreach (func_get_args() as $debug) {
            self::setLog("DEBUG", $debug);
        }
    }

    /**
     * Grava informações no Log com label ERROR
     * @param type $error
     */
    public static function trace() {
        foreach (func_get_args() as $log) {
            if (is_array($log)) {
                $str = date('d/m/Y H:i:s') . ": ";
                $str .= print_r($log, true);
            } else if (is_object($log)) {
                if (get_class($log) == "Exception" || is_subclass_of($log, "Exception")) {
                    $str = date('d/m/Y H:i:s') . ": {$log->getTraceAsString()}";
                } else {
                    $str = date('d/m/Y H:i:s') . ": ";
                    $str .= print_r($log, true);
                }
            } else {
                $str = date('d/m/Y H:i:s') . ": {$log}\n";
            }
            self::setLog("TRACE", $str);
        }
        $trace = [];
        foreach (debug_backtrace() as $v) {
            foreach ($v['args'] as &$arg) {
                if (is_object($arg)) {
                    $arg = '(Object)';
                }
            }
            array_push($trace, array_filter($v, function($key) {
                        return $key != 'object';
                    }, ARRAY_FILTER_USE_KEY));
        }
        $str .= "Trace: " . print_r($trace, true);
        self::setLog("TRACE", $str);
    }

    /**
     * Grava dados no log
     */
    private static function setLog($tipo, $log) {
        $logdir = __DIR__ . "/../logs/";
        if ($tipo != 'TRACE') {
            if (is_array($log)) {
                $str = date('d/m/Y H:i:s') . " {$tipo}: ";
                $str .= print_r($log, true);
            } else if (is_object($log)) {
                if (get_class($log) == "Exception" || is_subclass_of($log, "Exception")) {
                    $str = date('d/m/Y H:i:s') . " {$tipo}: {$log->getTraceAsString()}";
                } else {
                    $str = date('d/m/Y H:i:s') . " {$tipo}: ";
                    $str .= print_r($log, true);
                }
            } else {
                $str = date('d/m/Y H:i:s') . " {$tipo}: {$log}\n";
            }
            $file = __DIR__ . "/../logs/" . DOMINIO . "_ws_" . date('Y-m-d') . ".txt";
        } else {
            $file = __DIR__ . "/../logs/" . DOMINIO . "_ws_trace_" . date('Y-m-d') . ".txt";
            $str = $log;
        }
        $exists = file_exists($file);
        file_put_contents($file, $str, FILE_APPEND | FILE_TEXT);
        //Se criou o arquivo agora
        if (!$exists) {
            @chmod($file, 0777);
            @chown($file, "apache");
            @chgrp($file, "brudam");
            //Apagar arquivos com mais de 5 dias
            foreach (new DirectoryIterator($logdir) as $fileInfo) {
                if (!$fileInfo->isDot() && !$fileInfo->isDir() && time() - $fileInfo->getCTime() >= 432000) {//5 dias ( 5 * 24 * 60 * 60)
                    unlink($fileInfo->getRealPath());
                }
            }
        }
    }

    /**
     * Devolver tempo de excução do script, até o momento da chamada
     */
    public static function execTime() {
        $mtime = explode(" ", microtime());
        $endtime = $mtime[1] + $mtime[0];
        return ($endtime - STARTTIME);
    }

}
