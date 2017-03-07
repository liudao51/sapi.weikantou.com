<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/10
 * Time: 10:51
 */

namespace App\Models;

/**
 * Oauth权限类型模型类
 *
 * Class OauthScope
 * @package App\Models
 */
class OauthScope extends BModel
{
    protected $table = 'oauth_scope';
    protected $primaryKey = 'id';

}