<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/12/15
 * Time: 18:20
 */

namespace App\Services;

use App\Daos\UseropenedDao;
use App\Libs\Toolkit;

/**
 * 用户开放平台服务类
 *
 * Class UseropenedService
 * @package App\Services
 */
class UseropenedService extends BService
{
    private $_useropenedDao;

    public function __construct()
    {
        $this->_useropenedDao = new UseropenedDao();
    }

    /**
     * 创建用户开放平台信息（通过手机open_loginid-即手机号）
     *
     * @param string $open_loginid 开放平台登录ID
     * @return object|null $save_useropened
     */
    public function createUseropenedByMobile($open_loginid)
    {
        return $this->createUseropenedByOpenloginid($open_loginid, 1);
    }

    /**
     * 创建用户开放平台信息（通过QQ open_loginid）
     *
     * @param string $open_loginid 开放平台登录ID
     * @return object|null $save_useropened
     */
    public function createUseropenedByQq($open_loginid)
    {
        return $this->createUseropenedByOpenloginid($open_loginid, 2);
    }

    /**
     * 创建用户开放平台信息（通过微信open_loginid）
     *
     * @param string $open_loginid 开放平台登录ID
     * @return object|null $save_useropened
     */
    public function createUseropenedByWeixin($open_loginid)
    {
        return $this->createUseropenedByOpenloginid($open_loginid, 3);
    }

    /**
     * 创建用户开放平台信息（通过新浪微博open_loginid）
     *
     * @param string $open_loginid 开放平台登录ID
     * @return object|null $save_useropened
     */
    public function createUseropenedByWeibo($open_loginid)
    {
        return $this->createUseropenedByOpenloginid($open_loginid, 4);
    }

    /**
     * 创建用户开放平台信息（通过open_loginid）
     *
     * @param string $open_loginid 开放平台登录ID
     * @param int $type_id 平台类型（1手机,2QQ,3微信,4新浪微博）
     * @return object|null $save_useropened
     */
    public function createUseropenedByOpenloginid($open_loginid, $type_id)
    {
        $open_loginid = Toolkit::is_string($open_loginid) ? trim($open_loginid) : null;
        if (!isset($open_loginid) || $open_loginid == '') {
            return null;
        }

        $type_id = Toolkit::is_integer($type_id) ? trim($type_id) : null;
        if (!isset($type_id) || intval($type_id, 10) < 1 || intval($type_id, 10) > 4) {
            return null;
        }

        // 不允许 $open_loginid+$type_id 相同
        $exist_useropened = $this->_useropenedDao->getUseropenedByOpenloginid($open_loginid, $type_id);
        if (Toolkit::is_object($exist_useropened) && Toolkit::is_integer($exist_useropened->id) && intval($exist_useropened->id, 10) > 0) {
            return null;
        }

        // 执行创建
        $save_useropened_id = $this->_useropenedDao->createUseropenedByOpenloginid($open_loginid, $type_id);
        if (!Toolkit::is_integer($save_useropened_id) || intval($save_useropened_id, 10) < 1) {
            return null;
        }

        $save_useropened = $this->_useropenedDao->getUseropenedById($save_useropened_id);

        return $save_useropened;
    }

    /**
     * 获得用户开放平台信息（通过手机open_loginid-即手机号）
     *
     * @param string $open_loginid 开放平台登录ID（手机号）
     * @return object|null $useropened
     */
    public function getUseropenedByMobile($open_loginid)
    {
        return $this->getUseropenedByOpenloginid($open_loginid, 1);
    }

    /**
     * 获得用户开放平台信息（通过QQ open_loginid）
     *
     * @param string $open_loginid 开放平台登录ID
     * @return object|null $useropened
     */
    public function getUseropenedByQq($open_loginid)
    {
        return $this->getUseropenedByOpenloginid($open_loginid, 2);
    }

    /**
     * 获得用户开放平台信息（通过微信open_loginid）
     *
     * @param string $open_loginid 开放平台登录ID
     * @return object|null $useropened
     */
    public function getUseropenedByWeixin($open_loginid)
    {
        return $this->getUseropenedByOpenloginid($open_loginid, 3);
    }

    /**
     * 获得用户开放平台信息（通过新浪微博open_loginid）
     *
     * @param string $open_loginid 开放平台登录ID
     * @return object|null $useropened
     */
    public function getUseropenedByWeibo($open_loginid)
    {
        return $this->getUseropenedByOpenloginid($open_loginid, 4);
    }

    /**
     * 获得用户开放平台信息（通过open_loginid）
     *
     * @param string $open_loginid 开放平台登录ID
     * @param int $type_id 平台类型（1手机,2QQ,3微信,4新浪微博）
     * @return object|null $useropened
     */
    public function getUseropenedByOpenloginid($open_loginid, $type_id)
    {
        $open_loginid = Toolkit::is_string($open_loginid) ? trim($open_loginid) : null;
        if (!isset($open_loginid) || $open_loginid == '') {
            return null;
        }

        $type_id = Toolkit::is_integer($type_id) ? trim($type_id) : null;
        if (!isset($type_id) || intval($type_id, 10) < 1 || intval($type_id, 10) > 4) {
            return null;
        }

        $useropened = $this->_useropenedDao->getUseropenedByOpenloginid($open_loginid, $type_id);

        return $useropened;
    }

    /**
     * 获得用户开放平台信息（通过id）
     *
     * @param string $useropened_id 开放平台ID
     * @return object|null $useropened
     */
    public function getUseropenedById($useropened_id)
    {
        $useropened_id = Toolkit::is_integer($useropened_id) ? trim($useropened_id) : null;
        if (!isset($useropened_id) || intval($useropened_id, 10) < 1 || intval($useropened_id, 10) > 4) {
            return null;
        }

        $useropened = $this->_useropenedDao->getUseropenedById($useropened_id);

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

        $affected_rows = $this->_useropenedDao->updateUseropenedUserid($useropened_id, $user_id);

        return $affected_rows;
    }

    /**
     * 更新用户开放平台（通过open_loginid）
     *
     * @param string $open_loginid 开放平台登录ID
     * @param int $type_id 平台类型（1手机,2QQ,3微信,4新浪微博）
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
        if (!isset($type_id) || intval($type_id, 10) < 1 || intval($type_id, 10) > 4) {
            return null;
        }

        $user_id = Toolkit::is_integer($user_id) ? trim($user_id) : null;
        if (!isset($user_id) || intval($user_id, 10) < 1) {
            return 0;
        }

        $affected_rows = $this->_useropenedDao->updateUseropenedUseridByOpenloginid($open_loginid, $type_id, $user_id);

        return $affected_rows;
    }


}