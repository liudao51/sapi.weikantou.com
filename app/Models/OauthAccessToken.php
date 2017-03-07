<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/10
 * Time: 10:51
 */

namespace App\Models;

/**
 * Oauth访问凭证模型类
 *
 * Class OauthAccessToken
 * @package App\Models
 */
class OauthAccessToken extends BModel
{
    protected $table = 'oauth_access_token';
    protected $primaryKey = 'id';

}