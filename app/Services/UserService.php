<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/25
 * Time: 18:20
 */

namespace App\Services;

use App\Daos\UserDao;
use App\Libs\Toolkit;

/**
 * 用户服务类
 *
 * Class UserService
 * @package App\Services
 */
class UserService extends BService
{
    private $_userDao;
    private $_pwdStart = 'weikantou';
    private $_pwdEnd = '@#$%^&';
    private $_tranferkey = 'weikantou';

    public function __construct()
    {
        $this->_userDao = new UserDao();
    }

    /**
     * 增加新用户（通过邮箱）
     *
     * @param string $email
     * @param string $pwd
     * @param string $invite_code
     * @return object|null $save_user
     */
    public function createUserByEmail($email, $pwd, $invite_code = '')
    {
        $email = Toolkit::is_email($email) ? trim($email) : null;
        if (!isset($email)) {
            return null;
        }

        $pwd = Toolkit::is_string($pwd) ? trim($pwd) : null;
        if (!isset($pwd) || strlen($pwd) < 6) {
            return null;
        }

        $invite_code = Toolkit::is_string($invite_code) ? trim($invite_code) : '';

        // 解密传输+加密密码
        $pwd = $this->decryptTransferPwd($pwd);
        $pwd = $this->encryptPwd($pwd);

        $inviter = $this->_userDao->getUserByInvitecode($invite_code);
        $pid = (Toolkit::is_object($inviter) && Toolkit::is_integer($inviter->id) && intval($inviter->id, 10) > 0) ? $inviter->id : 0;

        // 不允许邮箱相同
        $exist_user = $this->_userDao->getUserByEmail($email);
        if (Toolkit::is_object($exist_user) && Toolkit::is_integer($exist_user->id) && intval($exist_user->id, 10) > 0) {
            return null;
        }

        // 执行创建
        $save_user_id = $this->_userDao->createUserByEmail($email, $pwd, $pid);
        if (!Toolkit::is_integer($save_user_id) || intval($save_user_id, 10) < 1) {
            return null;
        }

        // 创建成功后的后继处理
        $fail_times = $this->afterCreateUser($save_user_id);
        if (Toolkit::is_integer($fail_times) || intval($fail_times, 10) > 0) {
            //有错提示/写入日志等...
        }

        $save_user = $this->_userDao->getUserById($save_user_id);

        return $save_user;
    }

    public function bindUserByEmail($email, $pwd)
    {

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

        $user = $this->_userDao->getUserById($user_id);

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

        $user = $this->_userDao->getUserByEmail($email);

        return $user;
    }

    /**
     * 获得用户的基本信息（通过Email,pwd）
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
        if (!isset($pwd) || strlen($pwd) < 6) {
            return null;
        }

        // 解密传输+加密密码
        $pwd = $this->decryptTransferPwd($pwd);
        $pwd = $this->encryptPwd($pwd);

        $user = $this->_userDao->getUserByEmailPwd($email, $pwd);

        return $user;
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

        $affected_rows = $this->_userDao->updateUserNickname($user_id, $nick_name);

        return $affected_rows;
    }


    /**
     * 更新用户开放平台登录id
     *
     * @param int|string $user_id
     * @param int|string $useropened_id
     * @param int|string $useropened_type
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

        $affected_rows = $this->_userDao->updateUserOpenedid($user_id, $useropened_id, $useropened_type);

        return $affected_rows;
    }


    /**
     * 加密明文密码为入库密文密码
     *
     * @param string $pwd
     * @return string encryptPwd
     */
    private function encryptPwd($pwd)
    {
        $pwd = Toolkit::is_string($pwd) ? trim($pwd) : null;
        if (!isset($pwd) || $pwd == '') {
            return '';
        }

        $full_pwd = $this->_pwdStart . $pwd . $this->_pwdEnd;
        $encryptPwd = md5(md5($full_pwd, false) . sha1($full_pwd, false), false);

        return $encryptPwd;
    }

    /**
     * 解密传输的密码为明文密码
     *
     * @param string $transfer_pwd
     * @return string decryptPwd
     */
    private function decryptTransferPwd($transfer_pwd)
    {
        $transfer_pwd = Toolkit::is_string($transfer_pwd) ? trim($transfer_pwd) : null;
        if (!isset($transfer_pwd) || $transfer_pwd == '') {
            return '';
        }

        $pwd = $transfer_pwd;

        return $pwd;
    }

    /**
     * 创建成功后的后继处理
     *
     * @param $user_id
     * @return int $fail_times
     */
    private function afterCreateUser($user_id)
    {
        $fail_times = 0;

        $user_id = Toolkit::is_integer($user_id) ? trim($user_id) : null;
        if (!isset($user_id) || intval($user_id, 10) < 1) {
            $fail_times = $fail_times + 1;
            return $fail_times;
        }

        $invite_code = Toolkit::make_id_key($user_id, 4);
        if (!Toolkit::is_string($invite_code) || $invite_code == '') {
            $fail_times = $fail_times + 1;
        } else {
            $affected_rows = $this->_userDao->updateUserInvitecode($user_id, $invite_code);  //更新邀请码
            if (!Toolkit::is_integer($affected_rows) || intval($affected_rows, 10) < 1) {
                $fail_times = $fail_times + 1;
                //不成功写入日志...
            }
        }

        return $fail_times;
    }
}