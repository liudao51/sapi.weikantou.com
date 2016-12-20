<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 16/10/21
 * Time: 11:25
 */

namespace App\Libs;

/**
 * 响应错误类
 *
 * Class ResponseError
 * @package App\Libs
 */
class ResponseError
{
    const ERROR_TYPE_SUCCESS = '0';
    const ERROR_TYPE_NOTICE = '1';
    const ERROR_TYPE_WARNING = '2';
    const ERROR_TYPE_FATAL = '3';

    public $statusCode;
    public $errorCode;
    public $errorType = self::ERROR_TYPE_FATAL;
    public $errorMsg;
}