<?php

/**
 * Process manager class
 */
class ProcessManager {
    /**
     * Check if process with given PID is running
     * @param $pid integer process id
     * @return boolean
     */
    public static function isRunning($pid) {
        $data = shell_exec('ps ax -o  "%p %r" | grep ' . $pid);

        if (!$data) {
            return false;
        }

        $data = explode("\n", $data);

        if (count($data) >= 2) {
            return true;
        }

        return false;
    }

    /**
     * Kill process
     * @param int $pid integer process id
     * @param int $signal
     * @return boolean
     */
    public static function killProcess($pid, $signal=9) {
        exec("kill -$signal $pid");
        return self::isRunning($pid);
    }


    /**
     * Run a background command
     * @param $cmd string command
     */
    public static function backgroundExec($cmd) {
        exec($cmd . ' > /dev/null 2>&1 &');
    }

    /**
     * Run a command and check its return code
     * @param $cmd
     * @param $throwException
     * @throws Exception
     */
    public static function runCommand($cmd, $throwException=true) {
        $output = array();
        $result = null;

        exec($cmd, $output, $result);

        if ($result !== 0 && $throwException) {
            throw new Exception("Invalid result code: $result ($cmd)");
        }

        return implode("\n", $output);
    }
}