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
     * Grava informações no Log com label INFO
     */
    public static function info($info) {
        self::setLog("INFO", $info);
    }

    /**
     * Grava informações no Log com label ERROR
     */
    public static function error($error) {
        self::setLog("ERROR", $error);
    }

    /**
     * Grava dados no log
     */
    private static function setLog($tipo, $log) {
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
        
        $logdir = __DIR__ . "/../logs/debug/";
        $file = $logdir . DOMINIO . ".txt";
        if (file_exists($file)) {
            $trace = [];
            foreach (debug_backtrace() as $k => $v) {
                if ($k == 0) {
                    continue;
                }
                if (array_key_exists('file', $v)) {
                    foreach ($v['args'] as &$arg) {
                        if (is_object($arg)) {
                            $arg = '(Object)';
                        }
                    }
                    array_push($trace, array_filter($v, function($key) {
                                return $key != 'object';
                            }, ARRAY_FILTER_USE_KEY));
                }
            }
            $str .= "Trace: " . print_r($trace, true);
        } else {
            $logdir = __DIR__ . "/../logs/";
            $file = __DIR__ . "/../logs/" . DOMINIO . "_ws_" . date('Y-m-d') . ".txt";
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
