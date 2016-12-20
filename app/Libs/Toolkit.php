<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/10
 * Time: 16:47
 */

namespace App\Libs;

/**
 * 工具包类库
 *
 * Class Toolkit
 * @package App\Libs
 */
class Toolkit
{
    /**
     * 去除字符串中全部空格
     *
     * @param $var
     * @return mixed
     */
    public static function all_trim($var)
    {
        return str_replace(' ', '', $var); //去除全部空格
    }

    /**
     * 判断是否以指定字符串开头
     *
     * @param string $var
     * @param string $pattern
     * @return bool
     */
    public static function start_with($var, $pattern)
    {
        return (strpos($var, $pattern) === 0);
    }

    /**
     * 判断是否以指定字符串结尾
     *
     * @param string $var
     * @param string $pattern
     * @return bool
     */
    public static function end_with($var, $pattern)
    {
        return (strrchr($var, $pattern) == $pattern);
    }

    /**
     * 是否是整数(兼容数字字符串)
     *
     * @param $var
     * @return bool
     */
    public static function is_integer($var)
    {
        // 1.值为非null -> 值为数字 -> 值为字符串(数字后面带空格的字符串也是数字,所以要加上is_string来包含进来)
        $var = (isset($var) && (is_numeric($var) || is_string($var))) ? trim($var) : null;

        // 2.值为null -> 值为非数字(整数+浮点数) -> 值为非整数数字(这里包含 1=="1a", 所以要加上is_numeric来过滤)
        if (!isset($var) || !is_numeric($var) || (intval($var, 10) != $var)) {
            return false;
        }

        return true;
    }

    /**
     * 是否非空字符串
     *
     * @param $var
     * @return bool
     */
    public static function is_string($var)
    {
        if (isset($var) && is_string($var)) {
            return true;
        }

        return false;
    }

    /**
     * 是否是数组
     *
     * @param $var
     * @return bool
     */
    public static function is_array($var)
    {
        if (isset($var) && is_array($var)) {
            return true;
        }

        return false;
    }

    /**
     * 是否是对象
     *
     * @param $var
     * @return bool
     */
    public static function is_object($var)
    {
        if (isset($var) && is_object($var)) {
            return true;
        }

        return false;
    }

    /**
     * 是否手机
     *
     * @param string $var
     * @return bool
     */
    public static function is_mobile($var)
    {
        $var = (isset($var) && (is_numeric($var) || is_string($var))) ? trim($var) : null;
        if (!isset($var) || $var == '') {
            return false;
        }

        return preg_match('/^(13[0-9]|145|147|15([0-3]|[5-9])|17[6-8]|18[0-9])[0-9]{8}$/', $var);
    }

    /**
     * 是否邮箱
     *
     * @param string $var
     * @return bool
     */
    public static function is_email($var)
    {
        $var = (isset($var) && is_string($var)) ? trim($var) : null;
        if (!isset($var) || $var == '') {
            return false;
        }

        return preg_match('/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/', $var);
    }

    /**
     * 是否QQ
     *
     * @param string $var
     * @return bool
     */
    public static function is_qq($var)
    {
        $var = (isset($var) && (is_numeric($var) || is_string($var))) ? trim($var) : null;
        if (!isset($var) || $var == '') {
            return false;
        }

        return preg_match('/^[1-9][0-9]{4,}$/', $var);
    }

    /**
     * 是否为姓名
     *
     * @param string $var
     * @return bool
     */
    public static function is_realname($var)
    {
        $var = (isset($var) && is_string($var)) ? trim($var) : null;
        if (!isset($var) || $var == '') {
            return false;
        }

        //return preg_match("/^[\u4e00-\u9fa5]{2,4}$/" , $var);
        return preg_match("/^[\x{4e00}-\x{9fa5}]{2,4}$/u", $var);
    }

    /**
     * 是否为有效的身份证
     *
     * @param string $var
     * @return bool
     */
    public static function is_id_card($var)
    {
        $var = (isset($var) && (is_numeric($var) || is_string($var))) ? trim($var) : null;
        if (!isset($var) || $var == '') {
            return false;
        }

        if (strlen($var) == 18) {
            return static::idcard_checksum18($var);
        } elseif ((strlen($var) == 15)) {
            $var = static::idcard_15to18($var);
            return static::idcard_checksum18($var);
        } else {
            return false;
        }
    }

    /**
     * 兼容中文JSON编码
     *
     * @param string|array $var
     * @return mixed
     */
    public static function json_encode_cn($var)
    {
        $var = json_encode($var);

        return preg_replace_callback("/\\\u([0-9a-f]{4})/i", function ($r) {
            return iconv('UCS-2BE', 'UTF-8', pack('H*', $r[1]));
        }, $var);
    }

    /**
     * 格式化手机号(137 6075 4287)
     *
     * @param $var
     * @return string
     */
    public static function format_mobile($var)
    {
        $var = (isset($var) && (is_numeric($var) || is_string($var))) ? trim($var) : null;
        if (!isset($var) || $var == '') {
            return '';
        }

        $l_str = str_replace(' ', '', $var); //去除全部空格

        if (!static::is_mobile($l_str)) {
            return $l_str;
        }

        $var = preg_replace('/^(\d{3})(\d{4})(\d{4})$/i', '$1 $2 $3', $l_str);
        $var = trim($var);

        return $var;
    }

    /**
     * 格式化银行卡(6227 0032 6104 0185 991)
     *
     * @param string $var
     * @return string
     */
    public static function format_bankcard($var)
    {
        $var = (isset($var) && (is_numeric($var) || is_string($var))) ? trim($var) : null;
        if (!isset($var) || $var == '') {
            return '';
        }

        $l_str = str_replace(' ', '', $var);  //去除全部空格
        $var = '';
        $count = 0;

        for ($i = 0, $len = strlen($l_str); $i < $len; $i++) {
            $var .= substr($l_str, $i, 1);
            $count++;
            if ($count == 4) {
                $count = 0;
                $l_str .= ' ';
            }
        }

        $var = trim($var);

        return $var;
    }

    /**
     * 计算身份证校验码(根据国家标准GB 11643-1999)
     *
     * @param string $idcard_base
     * @return bool|mixed
     */
    public static function idcard_verify_number($idcard_base)
    {
        $idcard_base = (isset($idcard_base) && (is_numeric($idcard_base) || is_string($idcard_base))) ? trim($idcard_base) : null;
        if (!isset($idcard_base) || $idcard_base == '') {
            return false;
        }

        if (strlen($idcard_base) != 17) {
            return false;
        }

        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);  //加权因子
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');//校验码对应值
        $checksum = 0;

        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }

        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];

        return $verify_number;
    }

    /**
     * 将15位身份证升级到18位
     *
     * @param string $idcard
     * @return bool|string
     */
    public static function idcard_15to18($idcard)
    {
        $idcard = (isset($idcard) && (is_numeric($idcard) || is_string($idcard))) ? trim($idcard) : null;
        if (!isset($idcard) || $idcard == '') {
            return false;
        }

        if (strlen($idcard) != 15) {
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
            } else {
                $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
            }
        }
        $idcard = $idcard . self::idcard_verify_number($idcard);

        return $idcard;
    }

    /**
     * 18位身份证校验码有效性检查
     *
     * @param $idcard
     * @return bool
     */
    public static function idcard_checksum18($idcard)
    {
        $idcard = (isset($idcard) && (is_numeric($idcard) || is_string($idcard))) ? trim($idcard) : null;
        if (!isset($idcard) || $idcard == '') {
            return false;
        }

        if (strlen($idcard) != 18) {
            return false;
        }

        $idcard_base = substr($idcard, 0, 17);

        if (static::idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
            return false;
        } else {
            $year = substr($idcard, 6, 4);
            $now_year = date('Y');

            if ($year > $now_year || ($now_year - $year) > 200 || substr($idcard, 10, 2) > 12 || substr($idcard, 12, 2) > 31) {
                return false;
            }

            return true;
        }
    }

    /**
     * 根据身份证取得性别(0女, 1男)
     *
     * @param string $idno
     * @return int
     */
    public static function sex_from_idno($idno)
    {
        $idno = (isset($idno) && (is_numeric($idno) || is_string($idno))) ? trim($idno) : null;
        if (!isset($idno) || $idno == '') {
            return -1;
        }

        $l_idno = str_replace(' ', '', $idno); //去除全部空格
        return substr($l_idno, (strlen($l_idno) == 15 ? -1 : -2), 1) % 2 ? 1 : 0;   //0女, 1男
    }

    /**
     * 生成长度为32位的guid
     * http://php.net/manual/zh/function.com-create-guid.php
     *
     * @return string
     */
    public static function make_guid_key()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        // %04X：表示长度为4位的16进制编码,不够4位的自动在前面补0
        // guid格式为(8-4-4-4-12)：11112222-3333-4444-5555-66667777
        $key = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

        return $key;
    }

    /**
     * 生成指定长度的随机数（区分大小写）
     *
     * @param int|string $length $length>0
     * @return string
     */
    public static function make_random_key($length)
    {
        $length = (isset($length) && (is_numeric($length) || is_string($length))) ? trim($length) : null;
        if (!isset($length) || !is_numeric($length) || (intval($length, 10) != $length) || intval($length, 10) < 1) {
            return '';
        }

        $pattern = 'abcdefghijkmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWXYZ';  //共56位(由于容易混淆,这里去除0,o,O,l,I,1)
        $key = '';

        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern[mt_rand(0, 55)];   //生成php随机数
        }

        return $key;
    }

    /**
     * 生成指定id所对应的32进制数值（不区分大小写）
     *
     * @param int|string $id $id>0
     * @param int|string $min_length $min_length>1,默认为1
     * @return string
     */
    public static function make_id_key($id, $min_length = 1)
    {
        $id = (isset($id) && (is_numeric($id) || is_string($id))) ? trim($id) : null;
        if (!isset($id) || !is_numeric($id) || (intval($id, 10) != $id) || (intval($id, 10) < 1)) {
            return '';
        }

        $min_length = (isset($min_length) && (is_numeric($min_length) || is_string($min_length))) ? trim($min_length) : null;
        if (!isset($min_length) || !is_numeric($min_length) || (intval($min_length, 10) != $min_length) || (intval($min_length, 10) < 1)) {
            return '';
        }

        $pattern = 'abcdefghijkmnpqrstuvwxyz23456789';  //32进制(把a作为进位的补位),共32位(由于容易混淆,这里去除0,o,l,1)
        $hex = 32;
        //$pattern = '01';  $hex = 2; //2进制(把0作为进位的补位)
        //$pattern = '01234567'; $hex = 8; //8进制(把0作为进位的补位)
        //$pattern = '0123456789'; $hex = 10; //10进制(把0作为进位的补位)
        $fill = $pattern[0]; //不够最小长度时在左边填充的字符(即作为进位的补位字符)

        $key = '';
        $num = $id;

        while ($num) {
            $mod = $num % $hex;
            $num = (int)($num / $hex);
            $key = $pattern[$mod] . $key;
        }

        if (strlen($key) < $min_length) {
            $key = str_pad($key, $min_length, $fill, STR_PAD_LEFT);
        }

        return $key;
    }
}