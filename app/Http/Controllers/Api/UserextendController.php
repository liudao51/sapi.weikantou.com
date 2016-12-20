<?php
/**
 * Created by PhpStorm.
 * Userextend: liuzw
 * Date: 2016/11/25
 * Time: 17:28
 */

namespace App\Http\Controllers\Api;

use App\Libs\ErrorInfo;
use App\Libs\Toolkit;
use App\Services\UserextendService;
use Illuminate\Http\Request;

/**
 * 用户扩展控制器类
 *
 * Class UserextendController
 * @package App\Http\Controllers\Api
 */
class UserextendController extends BController
{
    /**
     * 取得用户扩展的信息
     *
     * @param $request
     * 必选参数：int|string $id
     * @return object
     */
    public function postUserextend(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $user_id = ($request_data->has('id') && Toolkit::is_integer($request_data->get('id'))) ? trim($request_data->get('id')) : null;
        if (!isset($user_id) || intval($user_id, 10) < 1) {
            return $this->responseFail('[id]' . ErrorInfo::Errors(2001), 2001);
        }

        $userService = new UserextendService();
        $user = $userService->getUserextendById($user_id);
        $data['userextend'] = $user;

        return $this->responseSucc($data);
    }
}