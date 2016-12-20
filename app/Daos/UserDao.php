<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/25
 * Time: 18:28
 */

namespace App\Daos;

use App\Libs\Toolkit;
use App\Models\User;

/**
 * 用户数据操作类
 *
 * Class UserDao
 * @package App\Daos
 */
class UserDao extends BDao
{
    private $_user;

    public function __construct()
    {
        $this->_user = new User();
    }

    /**
     * 增加新用户（通过邮箱）
     *
     * @param string $email
     * @param string $pwd
     * @param int $pid
     * @return int $save_user_id
     */
    public function createUserByEmail($email, $pwd, $pid = 0)
    {
        $email = Toolkit::is_email($email) ? trim($email) : null;
        if (!isset($email)) {
            return 0;
        }

        $pwd = Toolkit::is_string($pwd) ? trim($pwd) : null;
        if (!isset($pwd) || $pwd == '') {
            return 0;
        }

        $pid = Toolkit::is_integer($pid) ? trim($pid) : null;
        if (!isset($pid) || intval($pid, 10) < 0) {
            return 0;
        }

        // 不允许邮箱相同
        $exist_user = $this->getUserByEmail($email);
        if (Toolkit::is_object($exist_user) && Toolkit::is_integer($exist_user->id) && intval($exist_user->id, 10) > 0) {
            return 0;
        }

        $save_user = new User();
        $save_user->pid = $pid;
        $save_user->pwd = $pwd;
        $save_user->email = $email;
        $save_user->create_time = TIME_BJ;
        $save_user->is_effect = 5;  //需要通过邮箱激活

        $save_user->exists = false;
        $result_save = $save_user->save();

        $save_user_id = 0;
        if ($result_save && Toolkit::is_integer($save_user->id) && intval($save_user->id, 10) > 0) {
            $save_user_id = $save_user->id;
        }

        unset($save_user);

        return $save_user_id;
    }

    /**
     * 获得用户的基本信息（通过Id）
     *
     * @param int $user_id
     * @return object|null $user
     */
    public function getUserById($user_id)
    {
        $user_id = Toolkit::is_integer($user_id) ? trim($user_id) : null;
        if (!isset($user_id) || intval($user_id, 10) < 1) {
            return null;
        }

        $user = $this->_user->where(array('id' => $user_id))->select('id', 'email', 'pwd', 'invite_code', 'user_name', 'nick_name', 'real_name', 'mobile', 'score')->first();

        return $user;
    }

    /**
     * 获得用户的基本信息（通过Email）
     *
     * @param string $email
     * @return object|null $user
     */
    public function getUserByEmail($email)
    {
        $email = Toolkit::is_email($email) ? trim($email) : null;
        if (!isset($email)) {
            return null;
        }

        $user = $this->_user->where(array('email' => $email))->select('id', 'email', 'pwd', 'invite_code', 'user_name', 'nick_name', 'real_name', 'mobile', 'score')->first();

        return $user;
    }

    /**
     * 获得用户的基本信息（通过Email,Pwd）
     *
     * @param string $email
     * @param string $pwd
     * @return object|null $user
     */
    public function getUserByEmailPwd($email, $pwd)
    {
        $email = Toolkit::is_email($email) ? trim($email) : null;
        if (!isset($email)) {
            return null;
        }

        $pwd = Toolkit::is_string($pwd) ? trim($pwd) : null;
        if (!isset($pwd) || $pwd == '') {
            return null;
        }

        $user = $this->_user->where(array('email' => $email, 'pwd' => $pwd))->select('id', 'email', 'pwd', 'invite_code', 'user_name', 'nick_name', 'real_name', 'mobile', 'score')->first();

        return $user;
    }

    /**
     * 获得邀请人的基本信息（通过Invite_code）
     *
     * @param string $invite_code
     * @return object|null $user
     */
    public function getUserByInvitecode($invite_code)
    {
        $invite_code = Toolkit::is_string($invite_code) ? trim($invite_code) : null;
        if (!isset($invite_code) || $invite_code == '') {
            return null;
        }

        $user = $this->_user->where(array('invite_code' => $invite_code))->select('id', 'email', 'pwd', 'invite_code', 'user_name', 'nick_name', 'real_name', 'mobile', 'score')->first();

        return $user;
    }

    /**
     * 更新用户邀请码
     *
     * @param int|string $user_id
     * @param string $invite_code
     * @return int $affected_rows
     */
    public function updateUserInvitecode($user_id, $invite_code)
    {
        $user_id = Toolkit::is_integer($user_id) ? trim($user_id) : null;
        if (!isset($user_id) || intval($user_id, 10) < 1) {
            return 0;
        }

        $invite_code = Toolkit::is_string($invite_code) ? trim($invite_code) : null;
        if (!isset($invite_code) || $invite_code == '') {
            return 0;
        }

        $save_user_data = array('invite_code' => $invite_code);
        $save_user_where = array('id' => $user_id);
        $affected_rows = $this->_user->where($save_user_where)->update($save_user_data);

        return $affected_rows;
    }

    /**
     * 更新用户积分
     *
     * @param int|string $user_id
     * @param int|string $score
     * @return int $affected_rows
     */
    public function updateUserScore($user_id, $score)
    {
        $user_id = Toolkit::is_integer($user_id) ? trim($user_id) : null;
        if (!isset($user_id) || intval($user_id, 10) < 1) {
            return 0;
        }

        $score = Toolkit::is_integer($score) ? trim($score) : null;
        if (!isset($score) || intval($score, 10) < 1) {
            return 0;
        }

        $save_user_data = array('score' => $score);
        $save_user_where = array('id' => $user_id);
        $affected_rows = $this->_user->where($save_user_where)->update($save_user_data);

        return $affected_rows;
    }

    /**
     * 更新用户登录时间
     *
     * @param int|string $user_id
     * @return int $affected_rows
     */
    public function updateUserLogintime($user_id)
    {
        $user_id = Toolkit::is_integer($user_id) ? trim($user_id) : null;
        if (!isset($user_id) || intval($user_id, 10) < 1) {
            return 0;
        }

        $save_user_data = array('login_time' => TIME_BJ);
        $save_user_where = array('id' => $user_id);
        $affected_rows = $this->_user->where($save_user_where)->update($save_user_data);

        return $affected_rows;
    }

    /**
     * 更新用户昵称
     *
     * @param int|string $user_id
     * @param int|string $nick_name
     * @return int $affected_rows
     */
    public function updateUserNickname($user_id, $nick_name)
    {
        $user_id = Toolkit::is_integer($user_id) ? trim($user_id) : null;
        if (!isset($user_id) || intval($user_id, 10) < 1) {
            return 0;
        }

        $nick_name = Toolkit::is_string($nick_name) ? trim($nick_name) : null;
        if (!isset($nick_name) || $nick_name == '') {
            return 0;
        }

        $save_user_data = array('nick_name' => $nick_name);
        $save_user_where = array('id' => $user_id);
        $affected_rows = $this->_user->where($save_user_where)->update($save_user_data);

        return $affected_rows;
    }

    /**
     * 更新用户开放平台登录id
     *
     * @param int|string $user_id
     * @param int|string $useropened_id
     * @param int|string $useropened_type 平台类型（1手机,2QQ,3微信,4新浪微博）
     * @return int $affected_rows
     */
    public function updateUserOpenedid($user_id, $useropened_id, $useropened_type)
    {
        $user_id = Toolkit::is_integer($user_id) ? trim($user_id) : null;
        if (!isset($user_id) || intval($user_id, 10) < 1) {
            return 0;
        }

        $useropened_id = Toolkit::is_integer($useropened_id) ? trim($useropened_id) : null;
        if (!isset($useropened_id) || intval($useropened_id, 10) < 1) {
            return 0;
        }

        $useropened_type = Toolkit::is_integer($useropened_type) ? trim($useropened_type) : null;
        if (!isset($useropened_type) || intval($useropened_type, 10) < 1) {
            return 0;
        }

        $save_user_data = array();

        $useropened_type = intval($useropened_type, 10);
        switch ($useropened_type) {
            case 1: {
                $save_user_data = array('mobile_openid' => $useropened_id);
                break;
            }
            case 2: {
                $save_user_data = array('qq_openid' => $useropened_id);
                break;
            }
            case 3: {
                $save_user_data = array('wx_openid' => $useropened_id);
                break;
            }
            case 4: {
                $save_user_data = array('wb_openid' => $useropened_id);
                break;
            }
        }
        $save_user_where = array('id' => $user_id);

        $affected_rows = $this->_user->where($save_user_where)->update($save_user_data);

        return $affected_rows;
    }
}