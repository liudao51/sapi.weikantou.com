<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/9
 * Time: 17:28
 */

namespace App\Http\Controllers\Api;

use App\Libs\ErrorInfo;
use App\Libs\Toolkit;
use App\Services\RegionService;
use Illuminate\Http\Request;

/**
 * 国家地区控制器类
 *
 * Class RegionController
 * @package App\Http\Controllers\Api
 */
class RegionController extends BController
{
    /**
     * 取得某个区域的信息
     *
     * @param $request
     * 必选参数：int|string $id
     * @return object
     */
    public function postReadregion(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $read_type = ($request_data->has('read_type') && Toolkit::is_integer($request_data->get('read_type'))) ? trim($request_data->get('read_type')) : null;
        if (!isset($read_type) || ($read_type = intval($read_type, 10)) < 1) {
            return $this->responseFail('[read_type]' . ErrorInfo::Errors(2001), 2001);
        }

        $regionService = new RegionService();
        $region = null;

        /**
         *  TODO: $read_type(1区域id)
         */
        switch ($read_type) {
            case 1: {
                $region_id = ($request_data->has('id') && Toolkit::is_integer($request_data->get('id'))) ? trim($request_data->get('id')) : null;
                if (!isset($region_id) || intval($region_id, 10) < 1) {
                    return $this->responseFail('[id]' . ErrorInfo::Errors(2001), 2001);
                }

                $region = $regionService->getRegionById($region_id);

                break;
            }
        }

        $data['region'] = $region;

        return $this->responseSucc($data);
    }

    /**
     * 取得某个区域的所有下级信息
     *
     * @param $request
     * 必选参数：int|string $id
     * 可选参数：int|string $rank(下级层次:1,2,3,4, 默认为1层)
     * @return object
     */
    public function postReadsubregions(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $read_type = ($request_data->has('read_type') && Toolkit::is_integer($request_data->get('read_type'))) ? trim($request_data->get('read_type')) : null;
        if (!isset($read_type) || ($read_type = intval($read_type, 10)) < 1) {
            return $this->responseFail('[read_type]' . ErrorInfo::Errors(2001), 2001);
        }

        $regionService = new RegionService();
        $regions = null;

        /**
         *  TODO: $read_type(1区域id+下级层次)
         */
        switch ($read_type) {
            case 1: {

                $region_id = ($request_data->has('id') && Toolkit::is_integer($request_data->get('id'))) ? trim($request_data->get('id')) : null; // TODO 必选参数,默认值设为null
                if (!isset($region_id) || intval($region_id, 10) < 1) {
                    return $this->responseFail('[id]' . ErrorInfo::Errors(2001), 2001);
                }

                $rank = ($request_data->has('rank') && Toolkit::is_integer($request_data->get('rank'))) ? trim($request_data->get('rank')) : 1; // TODO 可选参数,默认值设为自定义值
                if (!isset($rank) || intval($rank, 10) < 1 || intval($rank, 10) > 4) {
                    return $this->responseFail('[rank]' . ErrorInfo::Errors(2001), 2001);
                }

                $regions = $regionService->getSubRegionsById($region_id, $rank);

                break;
            }
        }

        $data['regions'] = $regions;

        return $this->responseSucc($data);
    }

    /**
     * 取得相关区域的信息
     *
     * @param $request
     * 必选参数：string $name
     * 可选参数：int|string $level(搜索级别:0国家,1省份,2城市,3行政区, 默认为-1全部级别)
     * @return array
     */
    public function postReadrelateregions(Request $request)
    {
        $request_data = $this->requestHandle($request);

        $read_type = ($request_data->has('read_type') && Toolkit::is_integer($request_data->get('read_type'))) ? trim($request_data->get('read_type')) : null;
        if (!isset($read_type) || ($read_type = intval($read_type, 10)) < 1) {
            return $this->responseFail('[read_type]' . ErrorInfo::Errors(2001), 2001);
        }

        $regionService = new RegionService();
        $regions = null;

        /**
         *  TODO: $read_type(1区域名+搜索级别)
         */
        switch ($read_type) {
            case 1: {

                $region_name = ($request_data->has('name') && Toolkit::is_string($request_data->get('name'))) ? trim($request_data->get('name')) : null;
                if (!isset($region_name) || $region_name == '') {
                    return $this->responseFail('[name]' . ErrorInfo::Errors(2001), 2001);
                }

                $level = ($request_data->has('level') && Toolkit::is_integer($request_data->get('level'))) ? trim($request_data->get('level')) : -1;
                if (!isset($level) || intval($level, 10) < -1 || intval($level, 10) > 3) {
                    return $this->responseFail('[level]' . ErrorInfo::Errors(2001), 2001);
                }

                $regions = $regionService->getRegionsByName($region_name, $level);
                break;
            }
        }

        $data['regions'] = $regions;

        return $this->responseSucc($data);
    }
}