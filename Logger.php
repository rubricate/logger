<?php

/*
 * @author Andre Alves <andre AT brudam DOT com DOT br>
 * @link   https://github.com/akinalibeer/logger
 * 
 */

class Logger {
// class Log { # original name

    const ROOT_DIR = '';

    const LOG_DIR = "logs";
    const LOG_TIME = 86400 * 5;

    const STORAGE_MOD = 0775;
    const DOMINIO = "dev";


    /**
     * Grava informacoes no Log com label INFO
     * @param type $info
     */
    public static function info() {
            foreach (func_get_args() as $info) {
                self::setLog("INFO", $info);
            }
    }

    /**
     * Grava informacoes no Log com label ERROR
     * @param type $error
     */
    public static function error() {
        foreach (func_get_args() as $error) {
            self::setLog("ERROR", $error);
        }
    }

    /**
     * Grava informacoes no Log com label ERROR
     * @param type $error
     */
    public static function debug() {
        foreach (func_get_args() as $debug) {
            self::setLog("DEBUG", $debug);
        }
    }

    /**
     * Grava informacoes no Log com label ERROR
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
     * Grava dados no log com label $tipo
     * @param type $tipo
     * @param Exception $log
     */
    private static function setLog($tipo, $log) {
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
            $logdir = self::LOG_DIR . "/";
            $file = $logdir . self::DOMINIO . "_" . date('Y-m-d') . ".txt";
        } else {
            $logdir = self::LOG_DIR . "/";
            $file = $logdir . self::DOMINIO . "_trace_" . date('Y-m-d') . ".txt";
        }
        $exists = file_exists($file);
        @file_put_contents($file, $str, FILE_APPEND | FILE_TEXT);
        if (!$exists) {
            @chmod($file, self::STORAGE_MOD);
            foreach (new \DirectoryIterator($logdir) as $fileInfo) {
                if (!$fileInfo->isDot() && !$fileInfo->isDir() && time() - $fileInfo->getCTime() >= self::LOG_TIME) {
                    @unlink($fileInfo->getRealPath());
                }
            }
        }
    }

}
