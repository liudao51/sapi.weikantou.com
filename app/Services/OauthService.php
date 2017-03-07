<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/25
 * Time: 18:20
 */

namespace App\Services;

use App\Daos\OauthAuthCodeDao;
use App\Libs\Toolkit;
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
     * 必选参数：$app_id, $response_type, $redirect_uri
     * 可选参数：$scope = '', $state = true , $is_approve = false
     *
     * @param string $app_id 必须参数，注册应用时获得的API Key
     * @param string $response_type 必须参数，此值固定为“code”
     * @param string $redirect_uri 必须参数，授权后要回调的URI，即接收Authorization Code的URI。如果用户在授权过程中取消授权，会回调该URI，并在URI末尾附上error=access_denied参数
     * @param string $scope 非必须参数，以空格分隔的权限列表，若不传递此参数，代表请求用户的默认权限
     * @param bool $state 非必须参数，用于保持请求和回调的状态，授权服务器在回调时（重定向用户浏览器到“redirect_uri”时），会在Query Parameter中原样回传该参数。
     * @param bool $is_approve 是否允许授权
     *
     * @return string Authorization Code
     * @throws \Exception
     */
    public function getAuthorizationCode($app_id, $response_type, $redirect_uri, $scope = '', $state = true, $is_approve = false)
    {
        $app_id = Toolkit::is_string($app_id) ? trim($app_id) : null;
        if (!isset($app_id) || $app_id == '') {
            return '';
        }

        $response_type = Toolkit::is_string($response_type) ? trim($response_type) : null;
        if (!isset($response_type) || $response_type != 'code') {
            return '';
        }

        $redirect_uri = Toolkit::is_string($redirect_uri) ? trim($redirect_uri) : null;
        if (!isset($redirect_uri) || $redirect_uri == '') {
            return '';
        }

        $scope = Toolkit::is_string($scope) ? trim($scope) : null;
        if (!isset($scope)) {
            return '';
        }

        $state = Toolkit::is_bool($state) ? Toolkit::to_bool($state) : null;
        if (!isset($state)) {
            return '';
        }

        $is_approve = Toolkit::is_bool($is_approve) ? Toolkit::to_bool($is_approve) : null;
        if (!isset($is_approve)) {
            return '';
        }

        /**
         * TODO: 判断是否符合生成authCode
         */
        $oauthClientService = new OauthClientService();
        $oauthScopeService = new OauthScopeService();

        $oauthClient = $oauthClientService->getOauthClientByAppid($app_id);
        $oauthScopes = $oauthScopeService->getOauthScopesByNames(explode(config('oauth2.scope_delimiter'), $scope));

        $oauthClient_scope_id_list = !empty($oauthClient) ? explode(config('oauth2.scope_delimiter'), $oauthClient['scope_ids']) : array();

        $request_scope_id_list = array();
        foreach ($oauthScopes as $oauthScope) {
            $request_scope_id_list[] = $oauthScope['id'];
        }
        //用户请求的权限是否包含在OauthClient所允许的权限内
        $scope_is_ok = empty(array_diff($request_scope_id_list, $oauthClient_scope_id_list)) && count($oauthClient_scope_id_list) > 0 && count($request_scope_id_list) > 0 ? true : false;

        Toolkit::mlog('$is_approve='.json_encode($is_approve));
        Toolkit::mlog('$oauthClient_scope_id_list='.json_encode($oauthClient_scope_id_list));
        Toolkit::mlog('$request_scope_id_list='.json_encode($request_scope_id_list));
        
        //用户拒绝 或 请求权限有误
        if ($is_approve == false || $scope_is_ok == false) {
            dd('禁止请求!');
            return '';
        }

        dd('允许请求!');
        exit();
        return false;

        //生成authCode
        $authCode = $this->generateAuthcode();

        //把auth code保存到数据库中
        $oauthAuthCode = new OauthAuthCodeDao();
        $session_id = 0;

        $result_save = $oauthAuthCode->createOauthAuthCode($authCode, $session_id);
        unset($oauthAuthCode);

        if ($result_save) {
            return $authCode;
        }

        return '';
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

    /**
     * 生成授权码authCode
     *
     * @return mixed
     * @throws \Exception
     */
    private function generateAuthcode()
    {
        $stripped = '';
        $len = 40; //auth code长度
        do {
            $bytes = openssl_random_pseudo_bytes($len, $strong);

            if ($bytes === false || $strong === false) {
                throw new \Exception('Error Generating Key');
            }
            $stripped .= str_replace(['/', '+', '='], '', base64_encode($bytes));
        } while (strlen($stripped) < $len);

        $authCode = substr($stripped, 0, $len);

        return $authCode;
    }

}