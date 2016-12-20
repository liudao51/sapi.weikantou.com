<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/9
 * Time: 18:28
 */

namespace App\Daos;

use App\Libs\Toolkit;
use App\Models\Region;

/**
 * 国家地区数据操作类
 *
 * Class RegionDao
 * @package App\Daos
 */
class RegionDao extends BDao
{
    private $_region;

    public function __construct()
    {
        $this->_region = new Region();
    }

    /**
     * 取得某个区域的信息
     *
     * @param int $region_id
     * @return object
     */
    public function getRegionById($region_id)
    {
        $region_id = Toolkit::is_integer($region_id) ? trim($region_id) : null;
        if (!isset($region_id) || intval($region_id, 10) < 1) {
            return null;
        }

        $region = $this->_region->where(array('id' => $region_id))->select('id', 'name')->first();

        return $region;
    }

    /**
     * 取得某个区域的所有下级信息
     *
     * @param int $pid
     * @param int $rank (下级层次:1,2,3,4, 默认为1层)
     * @return array
     */
    public function getSubRegionsById($pid, $rank = 1)
    {
        $pid = Toolkit::is_integer($pid) ? trim($pid) : null;
        if (!isset($pid) || intval($pid, 10) < 1) {
            return null;
        }

        $rank = Toolkit::is_integer($rank) ? trim($rank) : null;
        if (!isset($rank) || intval($rank, 10) < 1 || intval($rank, 10) > 4) {
            return null;
        }

        $subRegions = array();
        $regions = $this->_region->where('pid', $pid)->select('id', 'name')->get();

        if (!empty($regions)) {
            foreach ($regions as $k => $v) {
                $region = array();
                $region['id'] = $v['id'];
                $region['name'] = $v['name'];
                $region['children'] = ($rank > 1) ? ($this->getSubRegionsById($v['id'], $rank - 1)) : array();  // 递归调用, 获取下级

                $subRegions[] = $region;
            }
        }

        return $subRegions;
    }

    /**
     * 取得相关区域的信息
     *
     * @param string $region_name
     * @param int|string $level (搜索级别:0国家,1省份,2城市,3行政区, 默认为-1全部级别)
     * @return array
     */
    public function getRegionsByName($region_name, $level = -1)
    {
        $region_name = Toolkit::is_string($region_name) ? trim($region_name) : null;
        if (!isset($region_name) || $region_name == '') {
            return null;
        }

        $level = Toolkit::is_integer($level) ? trim($level) : null;
        if (!isset($level) || intval($level, 10) < -1 || intval($level, 10) > 3) {
            return null;
        }

        $relateRegions = array();
        $regions = null;
        if (intval($level) == -1) {
            $regions = $this->_region->where('name', 'like', $region_name . '%')->select('id', 'name')->get();
        } else {
            $regions = $this->_region->where('name', 'like', $region_name . '%')->where('region_level', '=', $level)->select('id', 'name')->get();
        }

        if (!empty($regions)) {
            foreach ($regions as $k => $v) {
                $region = array();
                $region['id'] = $v['id'];
                $region['name'] = $v['name'];

                $relateRegions[] = $region;
            }
        }

        return $relateRegions;
    }
}