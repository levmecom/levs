<?php !defined('INLEV') && exit('Access Denied LEV');
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-09-15 12:52
 *
 * 项目：rm  -  $  - levs_ip_ban.php
 *
 * 作者：liwei 
 */


if (!isset($ipBanStr)) {
    $ipBanStr = ',{{$ipBanStr}}';//1.85.2.113 阿里安全扫描IP，并发100以上
    if (
        $ipBanStr &&
        isset($_SERVER['REMOTE_ADDR']) &&
        strpos($ipBanStr, $_SERVER['REMOTE_ADDR']) !== false
    ) {
        exit;
    }
}