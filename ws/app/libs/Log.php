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
        self::setLog(DOMINIO . " INFO", $info);
    }

    /**
     * Grava informações no Log com label ERROR
     */
    public static function error($error) {
        self::setLog(DOMINIO . " ERROR", $error);
    }

    /**
     * Grava dados no log
     */
    private static function setLog($tipo, $log) {
        $file = __DIR__ . "/../logs/" . date('Y-m-d') . ".txt";
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

        $exists = file_exists($file);
        file_put_contents($file, $str, FILE_APPEND | FILE_TEXT);
        //Se criou o arquivo agora
        if (!$exists) {
            chmod($file, 0775);
            chown($file, 'apache');
            chgrp($file, 'brudam');
            //Apagar arquivos com mais de 5 dias
            foreach (new DirectoryIterator(__DIR__ . "/../logs") as $fileInfo) {
                if ($fileInfo->isDot())
                    continue;
                if (time() - $fileInfo->getCTime() >= 5 * 24 * 60 * 60) {
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
