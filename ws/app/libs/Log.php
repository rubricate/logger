<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Acesso
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
        if (is_array($log) || is_object($log)) {
            $str = date('d/m/Y H:i:s') . " {$tipo}: ";
            $str .= print_r($log, true);
        } else {
            $str = date('d/m/Y H:i:s') . " {$tipo}: {$log}\n";
        }
        file_put_contents($file, $str, FILE_APPEND | FILE_TEXT);
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
