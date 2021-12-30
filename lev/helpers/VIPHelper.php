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

    public static function errorVipMessage($Lv, $vipInfo = null, $goodsId = null) {
        if (ModulesHelper::isInstallModule('levvv')) {
            return vipUserHelper::errorVipMessage($Lv, $vipInfo, $goodsId);
        }
        return Lev::responseMsg(-444, '未安装VIP模块', ['tourl'=>UrlHelper::storeView('levvv')]);
    }
}

class VIPHelper extends BaseVIP
{

}