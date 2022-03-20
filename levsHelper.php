<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-23 09:55
 *
 * 项目：upload  -  $  - levmodules.php
 *
 * 作者：liwei 
 */

namespace modules\levs;

use Lev;
use lev\base\Adminv;
use lev\helpers\ModulesHelper;
use lev\helpers\UserLoginModelHelper;


class levsHelper extends \lev\base\Modulesv
{

    public static $mud = [
        'version' => '1.0.2.210621',//
    ];

    public static function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        //Assetsv::registerApp(static::$mud['version']);

        urldecode(Lev::GPv('r')) == 'admin-modules/download-zip' && Adminv::definedISAPI();

//        if (empty(Lev::$app['LevAPP']) && Lev::$app['uid'] >0 && ModulesHelper::isInstallModule('levs')) {
//            $mudInfo = ModulesHelper::getModuleFileInfo('levs');
//            $mudInfo['versiontime'] > strtotime('2021-12-05') &&
//            UserLoginModelHelper::registerUid(Lev::$app['uid'], Lev::$app['username'], substr(Lev::$app['timestamp'], -6), false, Lev::$app['groupid'], Lev::$app['adminid']);
//        }
    }

}