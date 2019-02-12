<?php

namespace app\common;

/**
 * 进程帮助类
 *
 * @uses      Helper
 * @version   2017年11月05日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Cmd
{
    /**
     * 设置当前进程名称
     *
     * @param string $title 名称
     *
     * @return bool
     */
    public static function setProcessTitle(string $title): bool
    {
        if (Helper::isMac()) {
            return false;
        }

        if (\function_exists('cli_set_process_title')) {
            return @cli_set_process_title($title);
        }

        return true;
    }


    /**
     * run a command. it is support windows
     * @param string $command
     * @param string|null $cwd
     * @return array
     * @throws \RuntimeException
     */
    public static function run(string $command, string $cwd = null): array
    {
        $descriptors = [
            0 => ['pipe', 'r'], // stdin - read channel
            1 => ['pipe', 'w'], // stdout - write channel
            2 => ['pipe', 'w'], // stdout - error channel
            3 => ['pipe', 'r'], // stdin - This is the pipe we can feed the password into
        ];

        $process = proc_open($command, $descriptors, $pipes, $cwd);

        if (!\is_resource($process)) {
            throw new \RuntimeException("Can't open resource with proc_open.");
        }

        // Nothing to push to input.
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        // TODO: Write passphrase in pipes[3].
        fclose($pipes[3]);

        // Close all pipes before proc_close! $code === 0 is success.
        $code = proc_close($process);

        return [$code, $output, $error];
    }

    /**
     * 去掉警告信息
     * @param $warn
     * @return mixed
     */
    public static function StripWarning($warn){
        return str_replace("[Warning] Using a password on the command line interface can be insecure.\n",'',$warn);
    }
}