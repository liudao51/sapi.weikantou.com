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
use App\Services\UseropenedService;
use Illuminate\Http\Request;

/**
 * 用户开放平台控制器类
 *
 * Class UseropenedController
 * @package App\Http\Controllers\Api
 */
class UseropenedController extends BController
{
    /**
     * 增加用户开放平台
     *
     * @param $request
     * 必选参数：string $email, string $pwd, string $invite_code
     * @return object
     */
    public function postCreateuseropened(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $create_type = ($request_data->has('create_type') && Toolkit::is_integer($request_data->get('create_type'))) ? trim($request_data->get('create_type')) : null;
        if (!isset($create_type) || ($create_type = intval($create_type, 10)) < 1) {
            return $this->responseFail('[create_type]' . ErrorInfo::Errors(2001), 2001);
        }

        $useropenedService = new UseropenedService();
        $useropened = null;

        /**
         *  TODO: $create_type(1开放平台登录id)
         */
        switch ($create_type) {
            case 1: {
                $open_loginid = ($request_data->has('open_loginid') && Toolkit::is_email($request_data->get('open_loginid'))) ? trim($request_data->get('open_loginid')) : null;
                if (!isset($open_loginid)) {
                    return $this->responseFail('[open_loginid]' . ErrorInfo::Errors(2001), 2001);
                }

                $type_id = ($request_data->has('type_id') && Toolkit::is_string($request_data->get('type_id'))) ? trim($request_data->get('type_id')) : null;
                if (!isset($type_id) || ($type_id = intval($type_id, 10)) < 1 || intval($type_id, 10) > 4) {
                    return $this->responseFail('[type_id]' . ErrorInfo::Errors(2001), 2001);
                }

                // 不允许 $open_loginid+$type_id 相同
                $exist_useropened = $useropenedService->getUseropenedByOpenloginid($open_loginid, $type_id);
                if (Toolkit::is_object($exist_useropened) && Toolkit::is_integer($exist_useropened->id) && intval($exist_useropened->id, 10) > 0) {
                    return $this->responseFail('[useropened]' . ErrorInfo::Errors(3005), 3005);
                }

                $useropened = $useropenedService->createUseropenedByOpenloginid($open_loginid, $type_id);

                if (!Toolkit::is_object($useropened) || !Toolkit::is_integer($useropened->id) || intval($useropened->id, 10) < 1) {
                    return $this->responseFail(ErrorInfo::Errors(3001), 3001); //创建失败
                }

                break;
            }
        }

        $data['useropened'] = $useropened;

        return $this->responseSucc($data);
    }

    /**
     * 读取用户开放平台
     *
     * @param $request
     * 必选参数：int|string $id
     * @return object
     */
    public function postReaduseropened(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $read_type = ($request_data->has('read_type') && Toolkit::is_integer($request_data->get('read_type'))) ? trim($request_data->get('read_type')) : null;
        if (!isset($read_type) || ($read_type = intval($read_type, 10)) < 1) {
            return $this->responseFail('[read_type]' . ErrorInfo::Errors(2001), 2001);
        }

        $UseropenedService = new UseropenedService();
        $useropened = null;

        /**
         *  TODO: $read_type(1开放平台id, 2开放平台登录id+开放平台类型)
         */
        switch ($read_type) {
            case 1: {
                $useropened_id = ($request_data->has('id') && Toolkit::is_integer($request_data->get('id'))) ? trim($request_data->get('id')) : null;
                if (!isset($useropened_id) || intval($useropened_id, 10) < 1) {
                    return $this->responseFail('[id]' . ErrorInfo::Errors(2001), 2001);
                }

                $useropened = $UseropenedService->getUseropenedById($useropened_id);

                break;
            }

            case 2: {
                $open_loginid = ($request_data->has('open_loginid') && Toolkit::is_email($request_data->get('open_loginid'))) ? trim($request_data->get('open_loginid')) : null;
                if (!isset($open_loginid)) {
                    return $this->responseFail('[open_loginid]' . ErrorInfo::Errors(2001), 2001);
                }

                $type_id = ($request_data->has('type_id') && Toolkit::is_string($request_data->get('type_id'))) ? trim($request_data->get('type_id')) : null;
                if (!isset($type_id) || ($type_id = intval($type_id, 10)) < 1 || intval($type_id, 10) > 4) {
                    return $this->responseFail('[type_id]' . ErrorInfo::Errors(2001), 2001);
                }

                $useropened = $UseropenedService->getUseropenedByOpenloginid($open_loginid, $type_id);

                break;
            }
        }

        $data['useropened'] = $useropened;

        return $this->responseSucc($data);
    }

    /**
     * 更新用户开放平台
     *
     * @param $request
     * 必选参数：int|string $id
     * @return object
     */
    public function postUpdateuseropened(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $update_type = ($request_data->has('update_type') && Toolkit::is_integer($request_data->get('update_type'))) ? trim($request_data->get('update_type')) : null;
        if (!isset($update_type) || ($update_type = intval($update_type, 10)) < 1) {
            return $this->responseFail('[update_type]' . ErrorInfo::Errors(2001), 2001);
        }

        $UseropenedService = new UseropenedService();
        $affected_rows = 0;

        /**
         *  TODO: $read_type(1开放平台id, 2开放平台登录id+开放平台类型)
         */
        switch ($update_type) {
            case 1: {
                $useropened_id = ($request_data->has('id') && Toolkit::is_integer($request_data->get('id'))) ? trim($request_data->get('id')) : null;
                if (!isset($useropened_id) || intval($useropened_id, 10) < 1) {
                    return $this->responseFail('[id]' . ErrorInfo::Errors(2001), 2001);
                }

                $user_id = ($request_data->has('user_id') && Toolkit::is_string($request_data->get('user_id'))) ? trim($request_data->get('user_id')) : null;
                if (!isset($user_id) || intval($user_id, 10) < 1) {
                    return $this->responseFail('[user_id]' . ErrorInfo::Errors(2001), 2001);
                }

                $affected_rows = $UseropenedService->updateUseropenedUserid($useropened_id, $user_id);

                break;
            }

            case 2: {
                $open_loginid = ($request_data->has('open_loginid') && Toolkit::is_email($request_data->get('open_loginid'))) ? trim($request_data->get('open_loginid')) : null;
                if (!isset($open_loginid)) {
                    return $this->responseFail('[open_loginid]' . ErrorInfo::Errors(2001), 2001);
                }

                $type_id = ($request_data->has('type_id') && Toolkit::is_string($request_data->get('type_id'))) ? trim($request_data->get('type_id')) : null;
                if (!isset($type_id) || ($type_id = intval($type_id, 10)) < 1 || intval($type_id, 10) > 4) {
                    return $this->responseFail('[type_id]' . ErrorInfo::Errors(2001), 2001);
                }

                $user_id = ($request_data->has('user_id') && Toolkit::is_string($request_data->get('user_id'))) ? trim($request_data->get('user_id')) : null;
                if (!isset($user_id) || intval($user_id, 10) < 1) {
                    return $this->responseFail('[user_id]' . ErrorInfo::Errors(2001), 2001);
                }

                $affected_rows = $UseropenedService->updateUseropenedUseridByOpenloginid($open_loginid, $type_id, $user_id);

                break;
            }
        }

        $data['affected_rows'] = $affected_rows;

        return $this->responseSucc($data);
    }

    /**
     * 删除用户开放平台
     *
     * @param $request
     * 必选参数：int|string $id
     * @return object
     */
    public function postDeleteuseropened(Request $request)
    {
        $request_data = $this->requestHandle($request);
    }
}