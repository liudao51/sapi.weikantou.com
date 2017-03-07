<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/10
 * Time: 10:51
 */

namespace App\Models;

/**
 * Oauth授权码模型类
 *
 * Class OauthAuthCode
 * @package App\Models
 */
class OauthAuthCode extends BModel
{
    protected $table = 'oauth_auth_code';
    protected $primaryKey = 'id';

}