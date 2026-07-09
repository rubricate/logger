<?php

declare(strict_types=1);

namespace Rubricate\Logger;

use DirectoryIterator;
use Throwable;

ini_set('xdebug.overload_var_dump', '0');

abstract class AbstractLogger
{
    protected const TYPE_INFO  = 'INFO';
    protected const TYPE_ERROR = 'ERROR';
    protected const TYPE_DEBUG = 'DEBUG';
    protected const TYPE_TRACE = 'TRACE';

    protected static string $ex = '.txt';

    protected static int $changeTime = 86400 * 5;
    protected static int $storageMod = 0775;

    abstract protected static function getDirFullPath(): string;
    abstract protected static function getPrefixToFile(): string;

    public static function info(mixed ...$messages): void
    {
        foreach ($messages as $info) {
            self::set(self::TYPE_INFO, $info);
        }
    }

    public static function error(mixed ...$messages): void
    {
        foreach ($messages as $error) {
            self::set(self::TYPE_ERROR, $error);
        }
    }

    public static function debug(mixed ...$messages): void
    {
        foreach ($messages as $debug) {
            self::set(self::TYPE_DEBUG, $debug);
        }
    }

    public static function trace(mixed ...$messages): void
    {
        $type = null;
        $data = '';

        foreach ($messages as $log) {
            $data = self::getDataToWrite($type, $log);
            self::set(self::TYPE_TRACE, $data);
        }

        $trace = [];
        foreach (debug_backtrace() as $v) {
            if (isset($v['args'])) {
                foreach ($v['args'] as &$arg) {
                    if (is_object($arg)) {
                        $arg = '(Object)';
                    }
                }
            }

            $trace[] = array_filter($v, static fn($key) => $key !== 'object', ARRAY_FILTER_USE_KEY);
        }

        $data .= "Trace: " . print_r($trace, true);
        self::set(self::TYPE_TRACE, $data);
    }

    private static function set(?string $type, mixed $log): void
    {
        $logdir = rtrim(static::getDirFullPath(), '/') . '/';
        $file   = $logdir . self::getFile('_trace_');
        $data   = '';

        if ($type !== self::TYPE_TRACE) {
            $data = self::getDataToWrite($type, $log);
            $file = $logdir . self::getFile();
        }

        @file_put_contents($file, $data, FILE_APPEND | FILE_TEXT);

        if (!file_exists($file)) {
            @chmod($file, static::$storageMod);

            if (is_dir($logdir)) {
                foreach (new DirectoryIterator($logdir) as $fileInfo) {
                    if (!$fileInfo->isDot() && !$fileInfo->isDir() && 
                        (time() - $fileInfo->getCTime() >= static::$changeTime)) {
                        @unlink($fileInfo->getRealPath());
                    }
                }
            }
        }
    }

    private static function getFile(string $suffix = ''): string
    {
        return trim(static::getPrefixToFile()) . $suffix . date('Y-m-d') . self::$ex;
    }

    private static function get(?string $type, string $log): string
    {
        return sprintf('%s %s: %s', date('Y-m-d H:i:s'), $type ?? '', $log);
    }

    private static function getDataToWrite(?string $type, mixed $log): string
    {
        return match (true) {
            is_array($log) || is_object($log) && !($log instanceof Throwable) 
                => self::get($type, print_r($log, true)),

            $log instanceof Throwable 
                => self::get($type, $log->getTraceAsString()),

            is_string($log) 
                => self::get($type, $log . PHP_EOL),

            default 
                => self::get($type, self::getVarDump($log)),
        };
    }

    private static function getVarDump(mixed $log): string
    {
        ob_start();
        var_dump($log);
        return (string) ob_get_clean();
    }
}
