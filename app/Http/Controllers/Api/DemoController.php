<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/10/21
 * Time: 13:23
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Libs\ErrorInfo;
use App\Libs\Toolkit;
use App\Services\RegionService;
use Illuminate\Http\Request;

/**
 * 样例控制器类
 *
 * Class DemoController
 * @package App\Http\Controllers\Api
 */
class DemoController extends BController
{
    /**
     * Get request response 不带参数
     *
     * @return array
     *
     * demo: http://xxx.com/demo/test1
     */
    public function getTest1()
    {
        $msg = 'this is in Method getTest1.';

        return $this->responseFail($msg, 1001);
    }

    /**
     * Get request response 带参数
     *
     * @param int|string $id
     * @return array
     *
     * demo: http://xxx.com/demo/test2/102
     */
    public function getTest2($id)
    {
        $region_id = Toolkit::is_integer($id) ? trim($id) : null;

        if (!isset($region_id) || intval($id, 10) < 1) {
            return $this->responseFail('[id]' . ErrorInfo::Errors(2001), 2001);
        }

        $msg = 'this is in Method getTest2.';
        $response_data['id'] = $id;
        $response_data['msg'] = $msg;

        return $this->responseSucc($response_data);
    }

    /**
     * Post request response 带参数(或不带参数)
     *
     * @param Request $request
     * 必选参数：int|string $id
     * 可选参数：int|string $level(下级级别, 默认为1级)
     * @return array
     *
     * demo: http://xxx.com/demo/test3   raw参数：{"id":"102"}
     */
    public function postTest3(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $region_id = ($request_data->has('id') && Toolkit::is_integer($request_data->get('id'))) ? trim($request_data->get('id')) : null; // TODO 必选参数,默认值设为null
        if (!isset($region_id) || intval($region_id, 10) < 1) {
            return $this->responseFail('[id]' . ErrorInfo::Errors(2001), 2001);
        }

        $rank = ($request_data->has('rank') && Toolkit::is_integer($request_data->get('rank'))) ? trim($request_data->get('rank')) : 1; // TODO 可选参数,默认值设为自定义值
        if (!isset($rank) || intval($rank, 10) < 1 || intval($rank, 10) > 4) {
            return $this->responseFail('[rank]' . ErrorInfo::Errors(2001), 2001);
        }

        $regions = null;
        $data['regions'] = $regions;

        return $this->responseSucc($data);
    }
}