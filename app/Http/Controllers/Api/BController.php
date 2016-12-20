<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 16/10/21
 * Time: 11:25
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libs\ErrorInfo;
use App\Libs\ResponseError;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * 控制器基类
 *
 * Class BController
 * @package App\Http\Controllers\Api
 */
class BController extends Controller
{
    public function __construct()
    {
        if (method_exists('Controller', '__construct')) {
            parent::__construct();
        }

        // 设定信任IP,来获取X-Forwarded-For,作为真实IP
        $ips = array(
            '23.89.224.239'
        );
        Request::setTrustedProxies($ips);
    }

    /**
     * 返回失败响应
     *
     * @param string $errorMsg 错误信息
     * @param string|int $errorCode 错误码
     * @param string|int $errorType 错误类型
     * @param string|int $statusCode http状态码
     * @return array
     */
    protected function responseFail($errorMsg, $errorCode, $errorType = ResponseError::ERROR_TYPE_FATAL, $statusCode = '200')
    {
        $errorMsg = (isset($errorMsg) && is_string($errorMsg)) ? trim($errorMsg) : null;
        if (!isset($errorMsg) || $errorMsg == '') {
            $errorMsg = ErrorInfo::Errors('2000', ErrorInfo::TYPE_MSG);  //未知错误
        }

        $errorCode = (isset($errorCode) && (is_numeric($errorCode) || is_string($errorCode))) ? trim($errorCode) : null;
        if (!isset($errorCode) || $errorCode == '') {
            $errorCode = '2000';  //未知类型
        }

        $errorType = (isset($errorType) && (is_numeric($errorType) || is_string($errorType))) ? trim($errorType) : null;
        if (!isset($errorType) || $errorType == '') {
            $errorType = ResponseError::ERROR_TYPE_FATAL;
        }

        $statusCode = (isset($statusCode) && (is_numeric($statusCode) || is_string($statusCode))) ? trim($statusCode) : null;
        if (!isset($statusCode) || $statusCode == '') {
            $statusCode = '200';
        }

        $error = new ResponseError();
        $error->statusCode = $statusCode . '';
        $error->errorCode = $errorCode . '';
        $error->errorType = $errorType . '';
        $error->errorMsg = $errorMsg . '';

        return $this->_response(null, $error);
    }

    /**
     * 返回成功响应
     *
     * @param array $data 数组数据
     * @return array
     */
    protected function responseSucc($data)
    {
        $error = new ResponseError();
        $error->statusCode = '200';
        $error->errorCode = '0';
        $error->errorType = ResponseError::ERROR_TYPE_SUCCESS;
        $error->errorMsg = ErrorInfo::Errors('0', ErrorInfo::TYPE_MSG); //成功

        return $this->_response($data, $error);
    }

    /**
     * 处理请求数据
     *
     * @param Request $request 原生请求数据
     * @return mixed
     */
    protected function requestHandle(Request $request)
    {
        $contentType = $request->getContentType();

        $request_data = array();
        if ($contentType == 'json') {
            $request_data = $request->json(); //处理 "Content-Type: application/json" 格式的请求
        } elseif ($contentType == 'html') {
            $request_data = new ParameterBag($request->all());  //处理 "Content-Type: text/html" 格式的请求
        } else {
            $request_data = $request->json(); //默认处理 "Content-Type: application/json" 格式的请求
        }

        /**
         * TODO 数据解密、格式化
         */

        /*
        //数据请求类型(int)
        $i_type = $request_data->has('i_type') ? $request_data->get('i_type') : 0;
        $i_type = (isset($i_type) && (is_numeric($i_type) || is_string($i_type))) ? trim($i_type) : null;

        //数据返回类型(int)
        $o_type = $request_data->has('o_type') ? $request_data->get('o_type') : 0;
        $o_type = (isset($o_type) && (is_numeric($o_type) || is_string($o_type))) ? trim($o_type) : null;

        //请求客户端(string)
        $c_type = $request_data->has('c_type') ? $request_data->get('c_type') : '';
        $c_type = (isset($c_type) && (is_numeric($c_type) || is_string($c_type))) ? trim($c_type) : null;

        //请求版本号(string)
        $v_type = $request_data->has('v_type') ? $request_data->get('v_type') : '';
        $v_type = (isset($v_type) && (is_numeric($v_type) || is_string($v_type))) ? trim($v_type) : null;

        if (!isset($i_type) || !is_numeric($i_type) || (intval($i_type) != $i_type) || intval($i_type) < 0) {
            return $this->responseFail(ErrorInfo::Errors('2001', ErrorInfo::TYPE_MSG), 2001);
        }*/

        return $request_data;
    }

    /**
     * 处理响应数据
     *
     * @param mixed $response 原生返回数据
     * @return mixed
     */
    protected function responseHandle($response)
    {
        $response_data = $response;

        /**
         * TODO 数据加密、格式化
         */

        return $response_data;
    }

    /**
     * 格式化返回数据
     *
     * @param array $data 返回数据
     * @param MyError|null $error 错误信息对象
     * @return array
     */
    private function _response($data, ResponseError $error = null)
    {
        if (!$error) {
            return null;
        }

        if (!headers_sent()) {
            header('HTTP/1.1 ' . $error->statusCode . ' ' . $this->_getStatusCodeMessage($error->statusCode));
            header('Content-type: application/json');
        }

        $result = [
            'statusCode' => $error->statusCode . '',
            'responseBody' => [
                'responseInfo' => [
                    'reasons' => [
                        'code' => $error->errorCode . '',
                        'type' => $error->errorType . '',
                        'msg' => $error->errorMsg . ''
                    ]
                ],
                'data' => $data
            ]
        ];

        return $result;
    }

    /**
     * 通过状态码获取对应的描述信息
     *
     * @param int|string $status
     * @access private
     * @return string
     */
    private function _getStatusCodeMessage($status)
    {
        $status = (isset($status) && (is_numeric($status) || is_string($status))) ? trim($status) : null;
        if (!isset($status) || $status == '') {
            return '';
        }

        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = [
            '100' => 'Continue',
            '101' => 'Switching Protocols',
            '200' => 'OK',
            '201' => 'Created',
            '202' => 'Accepted',
            '203' => 'Non-Authoritative Information',
            '204' => 'No Content',
            '205' => 'Reset Content',
            '206' => 'Partial Content',
            '300' => 'Multiple Choices',
            '301' => 'Moved Permanently',
            '302' => 'Found',
            '303' => 'See Other',
            '304' => 'Not Modified',
            '305' => 'Use Proxy',
            '306' => '(Unused)',
            '307' => 'Temporary Redirect',
            '400' => 'Bad Request',
            '401' => 'Unauthorized',
            '402' => 'Payment Required',
            '403' => 'Forbidden',
            '404' => 'Not Found',
            '405' => 'Method Not Allowed',
            '406' => 'Not Acceptable',
            '407' => 'Proxy Authentication Required',
            '408' => 'Request Timeout',
            '409' => 'Conflict',
            '410' => 'Gone',
            '411' => 'Length Required',
            '412' => 'Precondition Failed',
            '413' => 'Request Entity Too Large',
            '414' => 'Request-URI Too Long',
            '415' => 'Unsupported Media Type',
            '416' => 'Requested Range Not Satisfiable',
            '417' => 'Expectation Failed',
            '500' => 'Internal Server Error',
            '501' => 'Not Implemented',
            '502' => 'Bad Gateway',
            '503' => 'Service Unavailable',
            '504' => 'Gateway Timeout',
            '505' => 'HTTP Version Not Supported'
        ];

        return (isset($codes[$status])) ? $codes[$status] : '';
    }
}