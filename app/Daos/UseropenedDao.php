<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/12/15
 * Time: 18:23
 */

namespace App\Daos;

use App\Libs\Toolkit;
use App\Models\Useropened;

/**
 * 用户开放平台数据操作类
 *
 * Class UseropenedDao
 * @package App\Daos
 */
class UseropenedDao extends BDao
{
    private $_useropened;

    public function __construct()
    {
        $this->_useropened = new Useropened();
    }

    /**
     * 创建用户开放平台信息（通过open_loginid）
     *
     * @param string $open_loginid 开放平台登录ID
     * @param int $type_id 平台类型（1手机,2QQ,3微信,4微博）
     * @return int $save_useropened_id
     */
    public function createUseropenedByOpenloginid($open_loginid, $type_id)
    {
        $open_loginid = Toolkit::is_string($open_loginid) ? trim($open_loginid) : null;
        if (!isset($open_loginid) || $open_loginid == '') {
            return null;
        }

        $type_id = Toolkit::is_integer($type_id) ? trim($type_id) : null;
        if (!isset($type_id) || intval($type_id, 10) < 1) {
            return null;
        }

        // 不允许 $open_loginid+$type_id 相同
        $useropened = $this->getUseropenedByOpenloginid($open_loginid, $type_id);
        if (Toolkit::is_object($useropened) && Toolkit::is_integer($useropened->id) && intval($useropened->id, 10) > 0) {
            return 0;
        }

        $save_userpened = new Useropened();
        $save_userpened->type_id = $type_id;
        $save_userpened->open_loginid = $open_loginid;
        $save_userpened->open_logintime = TIME_BJ;

        $save_userpened->exists = false;
        $result_save = $save_userpened->save();

        $save_useropened_id = 0;
        if ($result_save && Toolkit::is_integer($save_userpened->id) && intval($save_userpened->id, 10) > 0) {
            $save_useropened_id = $save_userpened->id;
        }

        unset($save_userpened);

        return $save_useropened_id;
    }

    /**
     * 获用户开放平台信息（通过Id）
     *
     * @param int $useropened_id
     * @return object|null $useropened
     */
    public function getUseropenedById($useropened_id)
    {
        $useropened_id = Toolkit::is_integer($useropened_id) ? trim($useropened_id) : null;
        if (!isset($useropened_id) || intval($useropened_id, 10) < 1) {
            return null;
        }

        $user = $this->_useropened->where(array('id' => $useropened_id))->select('id', 'user_id', 'type_id', 'open_username', 'open_loginid')->first();

        return $user;
    }

    /**
     * 获得用户开放平台信息（通过open_loginid）
     *
     * @param string $open_loginid 开放平台登录ID
     * @param int $type_id 平台类型（1手机,2QQ,3微信,4微博）
     * @return object|null $useropened
     */
    public function getUseropenedByOpenloginid($open_loginid, $type_id)
    {
        $open_loginid = Toolkit::is_string($open_loginid) ? trim($open_loginid) : null;
        if (!isset($open_loginid) || $open_loginid == '') {
            return null;
        }

        $type_id = Toolkit::is_integer($type_id) ? trim($type_id) : null;
        if (!isset($type_id) || intval($type_id, 10) < 1) {
            return null;
        }

        $useropened = $this->_useropened->where(array('open_loginid' => $open_loginid, 'type_id' => $type_id))->select('id', 'user_id', 'type_id', 'open_username', 'open_loginid')->first();

        return $useropened;
    }

    /**
     * 更新用户开放平台（通过id）
     *
     * @param $useropened_id
     * @param $user_id
     * @return int $affected_rows
     */
    public function updateUseropenedUserid($useropened_id, $user_id)
    {
        $useropened_id = Toolkit::is_integer($useropened_id) ? trim($useropened_id) : null;
        if (!isset($useropened_id) || intval($useropened_id, 10) < 1) {
            return 0;
        }

        $user_id = Toolkit::is_integer($user_id) ? trim($user_id) : null;
        if (!isset($user_id) || intval($user_id, 10) < 1) {
            return 0;
        }

        $save_useropened_data = array('user_id' => $user_id);
        $save_useropened_where = array('id' => $user_id);
        $affected_rows = $this->_useropened->where($save_useropened_where)->update($save_useropened_data);

        return $affected_rows;
    }


    /**
     * 更新用户开放平台（通过open_loginid）
     *
     * @param string $open_loginid 开放平台登录ID
     * @param int $type_id 平台类型（1手机,2QQ,3微信,4微博）
     * @param $user_id
     * @return int $affected_rows
     */
    public function updateUseropenedUseridByOpenloginid($open_loginid, $type_id, $user_id)
    {
        $open_loginid = Toolkit::is_string($open_loginid) ? trim($open_loginid) : null;
        if (!isset($open_loginid) || $open_loginid == '') {
            return null;
        }

        $type_id = Toolkit::is_integer($type_id) ? trim($type_id) : null;
        if (!isset($type_id) || intval($type_id, 10) < 1) {
            return null;
        }

        $user_id = Toolkit::is_integer($user_id) ? trim($user_id) : null;
        if (!isset($user_id) || intval($user_id, 10) < 1) {
            return 0;
        }

        $save_useropened_data = array('user_id' => $user_id);
        $save_useropened_where = array('open_loginid' => $open_loginid, 'type_id' => $type_id);
        $affected_rows = $this->_useropened->where($save_useropened_where)->update($save_useropened_data);

        return $affected_rows;
    }


}