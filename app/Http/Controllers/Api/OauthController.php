<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/25
 * Time: 17:28
 */

namespace App\Http\Controllers\Api;

use App\Libs\ErrorInfo;
use App\Libs\Toolkit;
use App\Services\OauthService;
use Illuminate\Http\Request;

/**
 * OAuth2.0用户授权控制器类
 *
 * Class OauthController
 * @package App\Http\Controllers\Api
 */
class OauthController extends BController
{
    public function __construct()
    {
        $this->middleware('check-authorization-params', ['only' => ['postAuthorize']]);
    }

    /**
     * 获取Authorization Code授权码（中间令牌）
     *
     * @param $request
     * 必选参数：$client_id, $response_type, $redirect_uri
     * 可选参数：$scope = '', $state = true
     * string $client_id 必须参数，注册应用时获得的API Key
     * string $response_type 必须参数，此值固定为“code”
     * string $redirect_uri 必须参数，授权后要回调的URI，即接收Authorization Code的URI。如果用户在授权过程中取消授权，会回调该URI，并在URI末尾附上error=access_denied参数
     * string $scope 非必须参数，以空格分隔的权限列表，若不传递此参数，代表请求用户的默认权限
     * bool $state 非必须参数，用于保持请求和回调的状态，授权服务器在回调时（重定向用户浏览器到“redirect_uri”时），会在Query Parameter中原样回传该参数。
     * int approve 用户允许授权
     * int deny 用户拒绝授权
     *
     * @return object
     *
     */
    public function postAuthorize(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $client_id = ($request_data->has('client_id') && Toolkit::is_string($request_data->get('client_id'))) ? trim($request_data->get('client_id')) : null;
        if (!isset($client_id) || ($client_id == '')) {
            return $this->responseFail('[client_id]' . ErrorInfo::Errors(2001), 2001);
        }
        $response_type = ($request_data->has('response_type') && Toolkit::is_string($request_data->get('response_type'))) ? trim($request_data->get('response_type')) : null;
        if (!isset($response_type) || ($response_type != 'code')) {
            return $this->responseFail('[response_type]' . ErrorInfo::Errors(2001), 2001);
        }
        $redirect_uri = ($request_data->has('redirect_uri') && Toolkit::is_string($request_data->get('redirect_uri'))) ? trim($request_data->get('redirect_uri')) : null;
        if (!isset($redirect_uri) || ($redirect_uri == '')) {
            return $this->responseFail('[redirect_uri]' . ErrorInfo::Errors(2001), 2001);
        }
        $approve = ($request_data->has('approve') && Toolkit::is_integer($request_data->get('approve'))) ? trim($request_data->get('approve')) : null;
        if (!isset($approve) || (intval($approve, 10) != 1)) {
            return $this->responseFail('[approve]' . ErrorInfo::Errors(2501), 2501);
        }

        try {
            $oauthService = new OauthService();
            $authorize['code'] = $oauthService->getAuthorizationCode();
            $data['authorize'] = $authorize;

            return $this->responseSucc($data);
        } catch (\Exception $e) {
            //Oauth2 系统错误格式：{"httpStatusCode":400,"errorType":"invalid_request","redirectUri":null,"parameter":"code"}
            if (isset($e) && !empty($e)) {
                return $this->responseFail("[" . $e->parameter . "]" . $e->errorType, 2500);
            } else {
                return $this->responseFail(ErrorInfo::Errors(2000), 2000);
            }
        }
    }

    /**
     * 通过Authorization Code换取access_token令牌
     *
     * @param $request
     * 必选参数：$grant_type, $code, client_id, client_secret, redirect_uri
     * string $grant_type 必须参数，此值固定为“authorization_code”
     * string $code 必须参数，通过第一步(oauth/authorize)所获得的Authorization Code
     * string client_id 必须参数，注册应用时获得的API Key
     * string client_secret 必须参数，注册应用时获得的Secret Key
     * bool redirect_uri 必须参数，授权后要回调的URI，该值必须与获取Authorization Code时传递的“redirect_uri”保持一致
     *
     * @return array Access Code
     */
    public function postAccesstoken(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $grant_type = ($request_data->has('grant_type') && Toolkit::is_string($request_data->get('grant_type'))) ? trim($request_data->get('grant_type')) : null;
        if (!isset($grant_type) || ($grant_type == '')) {
            return $this->responseFail('[grant_type]' . ErrorInfo::Errors(2001), 2001);
        }

        $client_id = ($request_data->has('client_id') && Toolkit::is_string($request_data->get('client_id'))) ? trim($request_data->get('client_id')) : null;
        if (!isset($client_id) || ($client_id == '')) {
            return $this->responseFail('[client_id]' . ErrorInfo::Errors(2001), 2001);
        }

        $client_secret = ($request_data->has('client_secret') && Toolkit::is_string($request_data->get('client_secret'))) ? trim($request_data->get('client_secret')) : null;
        if (!isset($client_secret) || ($client_secret == '')) {
            return $this->responseFail('[client_secret]' . ErrorInfo::Errors(2001), 2001);
        }

        switch ($grant_type) {
            /**
             * Auth Code Grant 授权码模式
             */
            case 'authorization_code': {
                $code = ($request_data->has('code') && Toolkit::is_string($request_data->get('code'))) ? trim($request_data->get('code')) : null;
                if (!isset($code) || ($code == '')) {
                    return $this->responseFail('[code]' . ErrorInfo::Errors(2001), 2001);
                }

                $redirect_uri = ($request_data->has('redirect_uri') && Toolkit::is_string($request_data->get('redirect_uri'))) ? trim($request_data->get('redirect_uri')) : null;
                if (!isset($redirect_uri) || ($redirect_uri == '')) {
                    return $this->responseFail('[redirect_uri]' . ErrorInfo::Errors(2001), 2001);
                }

                break;
            }

            /**
             * Refresh Token Grant 刷新凭证模式
             */
            case 'refresh_token': {
                $refresh_token = ($request_data->has('refresh_token') && Toolkit::is_string($request_data->get('refresh_token'))) ? trim($request_data->get('refresh_token')) : null;
                if (!isset($refresh_token) || ($refresh_token == '')) {
                    return $this->responseFail('[refresh_token]' . ErrorInfo::Errors(2001), 2001);
                }

                break;
            }
        }

        try {
            $oauthService = new OauthService();
            $access_token = $oauthService->getAccessToken();
            $data['token'] = $access_token;

            return $this->responseSucc($data);

        } catch (\Exception $e) {
            //Oauth2 系统错误格式：{"httpStatusCode":400,"errorType":"invalid_request","redirectUri":null,"parameter":"code"}
            if (isset($e) && !empty($e)) {
                return $this->responseFail("[" . $e->parameter . "]" . $e->errorType, 2500);
            } else {
                return $this->responseFail(ErrorInfo::Errors(2000), 2000);
            }
        }
    }
}