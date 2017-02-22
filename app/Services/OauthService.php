<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/25
 * Time: 18:20
 */

namespace App\Services;

use Illuminate\Support\Facades\Request as FacadesRequest;
use LucaDegasperi\OAuth2Server\Facades\Authorizer as FacadesAuthorizer;
use LucaDegasperi\OAuth2Server\Storage;

/**
 * OAuth2.0用户授权服务类
 *
 * Class OauthService
 * @package App\Services
 */
class OauthService extends BService
{
    public function __construct()
    {
    }

    /**
     * 获取Authorization Code授权码（中间令牌）
     *
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
     * @return string Authorization Code
     */
    public function getAuthorizationCode()
    {
        $params = FacadesAuthorizer::getAuthCodeRequestParams();
        $params['user_id'] = 1;

        $code = '';
        if (FacadesRequest::has('approve')) {
            $code = FacadesAuthorizer::issueAuthCode('user', $params['user_id'], $params);
        }

        return $code;
    }

    /**
     * 通过Authorization Code换取access_token令牌
     *
     * 必选参数：$grant_type, $code, client_id, client_secret, redirect_uri
     * 可选参数：$scope = '', $state = true
     * string $grant_type 必须参数，此值固定为“authorization_code”
     * string $code 必须参数，通过第一步所获得的Authorization Code
     * string client_id 必须参数，应用的API Key
     * string client_secret 必须参数，应用的Secret Key
     * bool redirect_uri 必须参数，该值必须与获取Authorization Code时传递的“redirect_uri”保持一致
     *
     * @return array Access Code
     */
    public function getAccessToken()
    {
        $access_token = FacadesAuthorizer::issueAccessToken();

        return $access_token;
    }

}