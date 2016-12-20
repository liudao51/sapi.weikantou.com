<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/25
 * Time: 17:28
 */

namespace App\Http\Controllers\Api;

use App\Libs\ErrorInfo;
use App\Libs\Toolkit;
use App\Services\UserService;
use Illuminate\Http\Request;

/**
 * 用户控制器类
 *
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UserController extends BController
{
    /**
     * 增加用户
     *
     * @param $request
     * 必选参数：string $email, string $pwd, string $invite_code
     * @return object
     */
    public function postCreateuser(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $create_type = ($request_data->has('create_type') && Toolkit::is_integer($request_data->get('create_type'))) ? trim($request_data->get('create_type')) : null;
        if (!isset($create_type) || ($create_type = intval($create_type, 10)) < 1) {
            return $this->responseFail('[create_type]' . ErrorInfo::Errors(2001), 2001);
        }

        $userService = new UserService();
        $user = null;

        /**
         *  TODO: $create_type(1邮箱, 2手机号)
         */
        switch ($create_type) {
            case 1: {
                $email = ($request_data->has('email') && Toolkit::is_email($request_data->get('email'))) ? trim($request_data->get('email')) : null;
                if (!isset($email)) {
                    return $this->responseFail('[email]' . ErrorInfo::Errors(2001), 2001);
                }

                $pwd = ($request_data->has('pwd') && Toolkit::is_string($request_data->get('pwd'))) ? trim($request_data->get('pwd')) : null;
                if (!isset($pwd) || strlen($pwd) < 6) {
                    return $this->responseFail('[pwd]' . ErrorInfo::Errors(2001), 2001);
                }

                $invite_code = ($request_data->has('invite_code') && Toolkit::is_string($request_data->get('invite_code'))) ? trim($request_data->get('invite_code')) : '';

                // 不允许邮箱相同
                $exist_user = $userService->getUserByEmail($email);
                if (Toolkit::is_object($exist_user) && Toolkit::is_integer($exist_user->id) && intval($exist_user->id, 10) > 0) {
                    return $this->responseFail('[email]' . ErrorInfo::Errors(3005), 3005);
                }

                $user = $userService->createUserByEmail($email, $pwd, $invite_code);
                if (!Toolkit::is_object($user) || !Toolkit::is_integer($user->id) || intval($user->id, 10) < 1) {
                    return $this->responseFail(ErrorInfo::Errors(3001), 3001); //创建失败
                }

                break;
            }
        }

        $data['user'] = $user;

        return $this->responseSucc($data);
    }

    /**
     * 读取用户
     *
     * @param $request
     * 必选参数：int|string $id
     * @return object
     */
    public function postReaduser(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $read_type = ($request_data->has('read_type') && Toolkit::is_integer($request_data->get('read_type'))) ? trim($request_data->get('read_type')) : null;
        if (!isset($read_type) || ($read_type = intval($read_type, 10)) < 1) {
            return $this->responseFail('[read_type]' . ErrorInfo::Errors(2001), 2001);
        }

        $userService = new UserService();
        $user = null;

        /**
         *  TODO: $read_type(1用户id, 2邮箱, 3邮箱+密码)
         */
        switch ($read_type) {
            case 1: {
                $user_id = ($request_data->has('id') && Toolkit::is_integer($request_data->get('id'))) ? trim($request_data->get('id')) : null;
                if (!isset($user_id) || intval($user_id, 10) < 1) {
                    return $this->responseFail('[id]' . ErrorInfo::Errors(2001), 2001);
                }

                $user = $userService->getUserById($user_id);

                break;
            }
            case 2: {
                $email = ($request_data->has('email') && Toolkit::is_email($request_data->get('email'))) ? trim($request_data->get('email')) : null;
                if (!isset($email)) {
                    return $this->responseFail('[email]' . ErrorInfo::Errors(2001), 2001);
                }

                $user = $userService->getUserByEmail($email);

                break;
            }
            case 3: {
                $email = ($request_data->has('email') && Toolkit::is_email($request_data->get('email'))) ? trim($request_data->get('email')) : null;
                if (!isset($email)) {
                    return $this->responseFail('[email]' . ErrorInfo::Errors(2001), 2001);
                }

                $pwd = ($request_data->has('pwd') && Toolkit::is_string($request_data->get('pwd'))) ? trim($request_data->get('pwd')) : null;
                if (!isset($pwd) || strlen($pwd) < 6) {
                    return $this->responseFail('[pwd]' . ErrorInfo::Errors(2001), 2001);
                }

                $user = $userService->getUserByEmailPwd($email, $pwd);

                break;
            }
        }

        $data['user'] = $user;

        return $this->responseSucc($data);
    }

    /**
     * 更新用户
     *
     * @param $request
     * 必选参数：int|string $id
     * @return object
     */
    public function postUpdateuser(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $update_type = ($request_data->has('update_type') && Toolkit::is_integer($request_data->get('update_type'))) ? trim($request_data->get('update_type')) : null;
        if (!isset($update_type) || ($update_type = intval($update_type, 10)) < 1) {
            return $this->responseFail('[update_type]' . ErrorInfo::Errors(2001), 2001);
        }

        $userService = new UserService();
        $affected_rows = 0;

        /**
         *  TODO: $read_type(1用户id+昵称, 2用户id+开放平台id+开放平台类型)
         */
        switch ($update_type) {
            case 1: {
                $user_id = ($request_data->has('id') && Toolkit::is_integer($request_data->get('id'))) ? trim($request_data->get('id')) : null;
                if (!isset($user_id) || intval($user_id, 10) < 1) {
                    return $this->responseFail('[id]' . ErrorInfo::Errors(2001), 2001);
                }

                $nick_name = ($request_data->has('nick_name') && Toolkit::is_string($request_data->get('nick_name'))) ? trim($request_data->get('nick_name')) : null;
                if (!isset($nick_name) || $nick_name == '') {
                    return $this->responseFail('[nick_name]' . ErrorInfo::Errors(2001), 2001);
                }

                $affected_rows = $userService->updateUserNickname($user_id, $nick_name);

                break;
            }
            case 2: {
                $user_id = ($request_data->has('id') && Toolkit::is_integer($request_data->get('id'))) ? trim($request_data->get('id')) : null;
                if (!isset($user_id) || intval($user_id, 10) < 1) {
                    return $this->responseFail('[id]' . ErrorInfo::Errors(2001), 2001);
                }

                $useropened_id = ($request_data->has('useropened_id') && Toolkit::is_string($request_data->get('useropened_id'))) ? trim($request_data->get('useropened_id')) : null;
                if (!isset($useropened_id) || $useropened_id == '') {
                    return $this->responseFail('[useropened_id]' . ErrorInfo::Errors(2001), 2001);
                }

                $useropened_type = ($request_data->has('useropened_type') && Toolkit::is_string($request_data->get('useropened_type'))) ? trim($request_data->get('useropened_type')) : null;
                if (!isset($useropened_type) || $useropened_type == '') {
                    return $this->responseFail('[useropened_type]' . ErrorInfo::Errors(2001), 2001);
                }

                $affected_rows = $userService->updateUserOpenedid($user_id, $useropened_id, $useropened_type);

                break;
            }
        }

        $data['affected_rows'] = $affected_rows;

        return $this->responseSucc($data);
    }

    /**
     * 删除用户
     *
     * @param $request
     * 必选参数：int|string $id
     * @return object
     */
    public function postDeleteuser(Request $request)
    {
        $request_data = $this->requestHandle($request);
    }
}