<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/10
 * Time: 16:22
 */

namespace App\Services;

use App\Libs\Toolkit;

/**
 * 调试服务类
 *
 * Class DebugService
 * @package App\Services
 */
class DebugService extends BService
{
    /**
     * 调试写入日志
     * 日志保存在: storage/logger/mlog.log
     *
     * @param string $msg 日志内容
     * @return no return
     */
    public function mlog($msg)
    {
        $msg = (isset($msg) && Toolkit::is_string($msg)) ? $msg : '';
        $msg = "\r\n" . $msg . "\r\n";

        $logService = new LogService();
        $logService->writeToLog('mlog', $msg, \Monolog\Logger::INFO, 'logger', 0);
    }

    /**
     * 允许查询日志
     *
     * @return no return
     */
    public function enableQueryLog()
    {
        \DB::connection()->enableQueryLog();   // 开启查询日志, 从5.0开始需要先执行这句
    }

    /**
     * 获取最后执行的sql
     *
     * @return string
     */
    public static function getLastSql()
    {
        $lastSql = array();
        $sqlList = \DB::getQueryLog();

        if (is_array($sqlList)) {
            $lastSql = end($sqlList);
        }

        return $lastSql;
    }
}