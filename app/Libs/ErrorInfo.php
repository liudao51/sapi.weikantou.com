<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/7
 * Time: 17:17
 */

namespace App\Libs;

/**
 * 错误信息类
 *
 * Class ErrorInfo
 * @package App\Libs
 */
class ErrorInfo
{
    const TYPE_MSG = 'msg';
    const TYPE_CODE = 'code';
    const TYPE_SUB_MSG = 'sub_msg';

    /**
     * 取得错误信息
     *
     * @param int|string $key 错误码
     * @param string $type 所取得字段信息
     * @return string
     */
    public static function Errors($key, $type = self::TYPE_MSG)
    {
        $key = (isset($key) && (is_numeric($key) || is_string($key))) ? trim($key) : '2000';

        /**
         * TODO: $errors的下标{$code}与{$key}的值必须一样,否则数据对不上
         */
        $errors['0'] = array('code' => '0', 'msg' => 'Ok', 'sub_msg' => '成功');

        //1001~1999为前端自定义错误
        //$errors['1001']...$errors['1999']

        //系统错误
        $errors['2000'] = array('code' => '2000', 'msg' => 'Unknowed Error', 'sub_msg' => '未知错误');
        $errors['2001'] = array('code' => '2001', 'msg' => 'Request Parameter Error', 'sub_msg' => '请求参数错误');

        //CRUD操作——Create(创建)、Read(读取)、Update(更新)、Delete(删除)
        $errors['3001'] = array('code' => '3001', 'msg' => 'Create Fail', 'sub_msg' => '创建失败');
        $errors['3002'] = array('code' => '3002', 'msg' => 'Read Fail', 'sub_msg' => '读取失败');
        $errors['3003'] = array('code' => '3003', 'msg' => 'Update Fail', 'sub_msg' => '更新失败');
        $errors['3004'] = array('code' => '3004', 'msg' => 'Delete Fail', 'sub_msg' => '删除失败');
        $errors['3005'] = array('code' => '3005', 'msg' => 'Is Exist', 'sub_msg' => '已存在');

        $errors['3101'] = array('code' => '3101', 'msg' => 'Register Fail', 'sub_msg' => '注册失败');
        $errors['3102'] = array('code' => '3102', 'msg' => 'Login Fail', 'sub_msg' => '登录失败');

        if (isset($errors[$key]) && isset($errors[$key][$type])) {
            return $errors[$key][$type];
        }

        return $errors['2000'][$type];  //未知错误
    }
}