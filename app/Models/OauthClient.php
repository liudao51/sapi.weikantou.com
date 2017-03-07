<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/10
 * Time: 10:51
 */

namespace App\Models;

/**
 * 模型类: Oauth2.0客户端
 *
 * Class OauthClient
 * @package App\Models
 */
class OauthClient extends BModel
{
    protected $table = 'oauth_client';
    protected $primaryKey = 'id';

}