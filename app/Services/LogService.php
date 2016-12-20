<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/10/22
 * Time: 15:02
 */

namespace App\Services;

use App\Libs\Toolkit;

/**
 * 日志服务类
 *
 * Class LogService
 * @package App\Services
 */
class LogService extends BService
{
    /**
     * 普通写入日志
     *
     * @param string $logType 日志类型(日志文件名)
     * @param mixed $msg 日志内容信息,可以是字符串或数组
     * @param int $errorType 错误类型,请查看 \Monolog\Logger
     * @param string $dirName 日志目录, 存放在: storage/$dirName/$logType.log
     * @param bool|int $timedFilename 文件名是否带日期
     */
    public function writeToLog($logType, $msg, $errorType = \Monolog\Logger::INFO, $dirName = '', $timedFilename = 1)
    {
        $logType = (isset($logType) && Toolkit::is_string($logType)) ? trim($logType) : '';
        $msg = (isset($msg) && (Toolkit::is_string($msg) || is_array($msg))) ? $msg : '';
        $errorType = (isset($errorType) && (is_int($errorType))) ? $errorType : \Monolog\Logger::INFO;
        $dirName = (isset($dirName) && Toolkit::is_string($dirName)) ? trim($dirName) : '';
        $timedFilename = (isset($timedFilename) && (is_int($timedFilename) || is_bool($timedFilename))) ? $timedFilename : 1;

        $str_msg = '';
        if (is_array($msg)) {
            foreach ($msg as $k => $v) {
                $str_msg .= "\t[{$k}]:{$v}";
            }
        } else {
            $str_msg = (string)$msg;
        }

        $name = ($dirName != '') ? $dirName : "request_log";
        $filename = ($logType != '') ? $logType : "info";
        $path = \App::storagePath() . "/{$name}/{$filename}.log";

        $logger = new \Monolog\Logger($name);
        $handler = $timedFilename ? (new \Monolog\Handler\RotatingFileHandler($path, 0, $errorType)) : (new \Monolog\Handler\StreamHandler($path, 0, $errorType));
        $handler->setFormatter(new \Monolog\Formatter\LineFormatter(null, null, true, true));
        $logger->pushHandler($handler);
        $logger->addInfo($str_msg);
    }
}