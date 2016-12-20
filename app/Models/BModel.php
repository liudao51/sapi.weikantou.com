<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/10
 * Time: 10:33
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 数据模型基类
 *
 * Class BModel
 * @package App\Models
 */
class BModel extends Model
{
    public $timestamps = false;  // 禁止Eloquent自动维护created_at、updated_at字段
}