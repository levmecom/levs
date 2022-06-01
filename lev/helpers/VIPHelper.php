<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-06-19 12:47
 *
 * 项目：rm  -  $  - VIPHelper.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

use Lev;
use modules\levvv\table\vipUserHelper;

!defined('INLEV') && exit('Access Denied LEV');

class BaseVIP {

    public static function vipInfo($uid = 0) {
        return $uid ? Lev::actionObjectMethodIden('levvv', 'modules\levvv\table\vipUserHelper', [$uid], 'myInfo') : [];
    }

    public static function errorVipMessage($Lv, $vipInfo = null, $goodsId = null, $exit = false) {
        if (ModulesHelper::isInstallModule('levvv')) {
            $errMsg = vipUserHelper::errorVipMessage($Lv, $vipInfo, $goodsId);
            $exit && $errMsg && Lev::showMessages($errMsg);
            return $errMsg;
        }
        return Lev::responseMsg(-444, '未安装VIP模块', ['tourl'=>UrlHelper::storeView('levvv')]);
    }

    /**
     * 未通过，它将直接退出程序
     * @param $ckPm
     * @param bool $force
     */
    public static function isVIPLink($ckPm, $force = true) {
        Lev::actionObjectMethodIden('levvv', 'modules\levvv\helpers\setHelper', [$ckPm, $force], 'isVipLink');
    }
}

class VIPHelper extends BaseVIP
{

}