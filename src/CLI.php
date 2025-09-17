<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use Slothsoft\Core\Configuration\ConfigurationField;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

class CLI {
    
    private static function totalTimeout(): ConfigurationField {
        static $field;
        if ($field === null) {
            $field = new ConfigurationField(0);
        }
        return $field;
    }
    
    public static function setTotalTimeout(float $value) {
        self::totalTimeout()->setValue($value);
    }
    
    public static function getTotalTimeout(): float {
        return self::totalTimeout()->getValue();
    }
    
    private static function idleTimeout(): ConfigurationField {
        static $field;
        if ($field === null) {
            $field = new ConfigurationField(0);
        }
        return $field;
    }
    
    public static function setIdleTimeout(float $value) {
        self::idleTimeout()->setValue($value);
    }
    
    public static function getIdleTimeout(): float {
        return self::idleTimeout()->getValue();
    }
    
    private static function stdOut(): ConfigurationField {
        static $field;
        if ($field === null) {
            $field = new ConfigurationField(STDOUT);
        }
        return $field;
    }
    
    public static function setStdOut($value) {
        self::stdOut()->setValue($value);
    }
    
    public static function getStdOut() {
        return self::stdOut()->getValue();
    }
    
    private static function stdErr(): ConfigurationField {
        static $field;
        if ($field === null) {
            $field = new ConfigurationField(STDERR);
        }
        return $field;
    }
    
    public static function setStdErr($value) {
        self::stdErr()->setValue($value);
    }
    
    public static function getStdErr() {
        return self::stdErr()->getValue();
    }
    
    public static function execute(string $command, string $workingDirectory = null): int {
        fwrite(self::getStdOut(), PHP_EOL . PHP_EOL . sprintf('[%s]> %s', date('d.m.y H:i:s'), $command) . PHP_EOL);
        $process = Process::fromShellCommandline($command);
        if ($workingDirectory) {
            $process->setWorkingDirectory($workingDirectory);
        }
        $process->setTimeout(self::getTotalTimeout());
        $process->setIdleTimeout(self::getIdleTimeout());
        try {
            $process->start();
            foreach ($process as $type => $data) {
                if ($type === $process::OUT) {
                    fwrite(self::getStdOut(), $data);
                } else {
                    fwrite(self::getStdErr(), $data);
                }
            }
        } catch (ProcessTimedOutException $e) {
            fwrite(self::getStdErr(), $e->getMessage() . PHP_EOL);
            trigger_error($e->getMessage(), E_USER_WARNING);
        }
        
        return $process->getExitCode();
    }
}