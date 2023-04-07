<?php

namespace Rubricate\Logger;

ini_set ('xdebug.overload_var_dump', 0);

use Rubricate\Logger\ConstLogger as C;

abstract class AbstractLogger
{
    protected static $ex = '.txt';
    abstract protected static function getDirFullPath();
    abstract protected static function getPrefixToFile();

    public static function info()
    {
        foreach (func_get_args() as $info) {
            self::set(C::TYPE_INFO, $info);
        }
    }

    public static function error()
    {
        foreach (func_get_args() as $error) {
            self::set(C::TYPE_ERROR, $error);
        }
    }

    public static function debug()
    {
        foreach (func_get_args() as $debug) {
            self::set(C::TYPE_DEBUG, $debug);
        }
    }

    public static function trace()
    {
        $type = null;
        $data = '';

        foreach (func_get_args() as $log) {

            $data = self::getDataToWrite($type, $log);
            self::set(C::TYPE_TRACE, $data);
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

        $data .= "Trace: " . print_r($trace, true);
        self::set(C::TYPE_TRACE, $data);
    }

    private static function set($type, $log)
    {
        $logdir = static::getDirFullPath();
        $file   = $logdir . self::getFile('_trace_');
        $data   = '';

        if ($type != C::TYPE_TRACE) {
            
            $data  = self::getDataToWrite($type, $log);
            $file = $logdir . self::getFile();
        }

        @file_put_contents($file, $data, FILE_APPEND | FILE_TEXT);

        if (!file_exists($file)) {

            @chmod($file, C::STORAGE_MOD);

            foreach (new \DirectoryIterator($logdir) as $fileInfo) {

                if (
                    !$fileInfo->isDot() && !$fileInfo->isDir() &&
                    time() - $fileInfo->getCTime() >= C::CHANGE_TIME) {

                    @unlink($fileInfo->getRealPath());
                }
            }
        }
    }

    private static function getFile()
    {
        $p = trim(static::getPrefixToFile());
        $d = date('Y-m-d');
        $e = '.txt';

        return $p . $d . $e;
    }

    private static function get($type, $log)
    {
        $d = date('Y-m-d H:i:s');
        $s = '%s %s: %s';

       return sprintf($s, $d, $type, $log);
    }

    private static function getDataToWrite($type, $log)
    {
        if (is_array($log) || is_object($log)) {
            return self::get($type, print_r($log, true));
        }

        if (self::isException($log)) {
            return self::get($type, $log->getTraceAsString());
        }

        if (is_string($log)) {
            return self::get($type, $log . PHP_EOL);
        }

        return self::get($type, self::getVarDump($log));
    }

    private static function isException($log)
    {
        return (
            is_object($log) && (
                (get_class($log) == "Exception") ||
                is_subclass_of($log, "Exception")
            ));
    }

    private static function getVarDump($log)
    {
        ob_start();
        var_dump($log);

        return ob_get_clean();
    }

}

