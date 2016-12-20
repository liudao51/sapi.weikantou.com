<?php
/**
 * Created by PhpStorm.
 * Userextend: liuzw
 * Date: 2016/11/25
 * Time: 18:28
 */

namespace App\Daos;

use App\Libs\Toolkit;
use App\Models\Userextend;

/**
 * 用户扩展数据操作类
 *
 * Class UserextendDao
 * @package App\Daos
 */
class UserextendDao extends BDao
{
    private $_userextend;

    public function __construct()
    {
        $this->_userextend = new Userextend();
    }

    /**
     * 取得用户扩展的信息
     *
     * @param int $userextend_id
     * @return object
     */
    public function getUserextendById($userextend_id)
    {
        $userextend_id = Toolkit::is_integer($userextend_id) ? trim($userextend_id) : null;
        if (!isset($userextend_id) || intval($userextend_id, 10) < 1) {
            return null;
        }

        $userextend = $this->_userextend->where(array('id' => $userextend_id))->select('id', 'user_id', 'thread_ids', 'media_ids', 'invite_ids')->first();

        return $userextend;
    }
}