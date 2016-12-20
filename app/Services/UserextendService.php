<?php
/**
 * Created by PhpStorm.
 * Userextend: liuzw
 * Date: 2016/11/25
 * Time: 18:20
 */

namespace App\Services;

use App\Daos\UserextendDao;
use App\Libs\Toolkit;

/**
 * 用户扩展服务类
 *
 * Class UserextendService
 * @package App\Services
 */
class UserextendService extends BService
{
    private $_userextendDao;

    public function __construct()
    {
        $this->_userextendDao = new UserextendDao();
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

        $userextend = $this->_userextendDao->getUserextendById($userextend_id);

        return $userextend;
    }
}