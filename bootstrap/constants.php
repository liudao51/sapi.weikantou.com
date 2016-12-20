<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/12/8
 * Time: 15:01
 */

define('DB_DEFAULT_CACHE_TIME', 60); // 数据库默认缓存时间(分钟)

define('TIME_UTC', time() - date('Z')); // 当前UTC时间戳
define('TIME_BJ', time()); // 当前北京时间戳