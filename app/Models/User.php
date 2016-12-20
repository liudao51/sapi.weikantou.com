<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/25
 * Time: 10:51
 */

namespace App\Models;

/**
 * 用户数据模型类
 *
 * Class User
 * @package App\Models
 */
class User extends BModel
{
    protected $table = 'user';
    protected $primaryKey = 'id';

}