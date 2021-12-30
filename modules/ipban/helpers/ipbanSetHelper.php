<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-27 16:34
 *
 * 项目：upload  -  $  - setHelper.php
 *
 * 作者：liwei 
 */

namespace modules\levs\modules\ipban\helpers;

use Lev;
use modules\levs\modules\ipban\ipRecordCacheHelper;

!defined('INLEV') && exit('Access Denied LEV');

class BaseipbanSet {

    public static function fileNum() {
        return ($fileNum = intval(Lev::stget('fileNum', 'ipban'))) < 2 ? 2 : $fileNum;
    }

    public static function spiderIP() {
        return Lev::stget('spiderIP', 'ipban');
    }

    public static function openIPrecord() {
        return !Lev::stget('openIPrecord', 'ipban');
    }

    public static function openIP() {
        return !Lev::stget('openIP', 'ipban');
    }

    public static function banIPs() {
        return trim(Lev::stget('banIPs', 'ipban'));
    }

    public static function rewriteIP() {
        return Lev::stget('rewriteIP', 'ipban');
    }

}

class ipbanSetHelper extends BaseipbanSet {

    /**
     * superman/settings 保存成功回调函数
     * @return array
     */
    public static function SettingsReturn() {
        $msgBanIPs = '';
        if (isset($_POST['settings']['banIPs'])) {
            $ipstr = static::openIP() ? static::banIPs() : '';
            $data = str_replace('{{$ipBanStr}}', $ipstr, file_get_contents(dirname(__DIR__) . '/migrations/levs_ip_ban.php'));
            $data = str_replace('<?php !defined(\'INLEV\')', '<?php //!defined(\'INLEV\')', $data);
            $size = file_put_contents(Lev::$aliases['@webroot'].'/levs_ip_ban.php', $data, LOCK_EX);
            $msgBanIPs = $size >0 ? '文件写入成功' : '文件写入失败！请检查目录是否可写：'.Lev::$aliases['@webroot'];
        }
        if (isset($_POST['tablesFormv__addtr']['rewriteIP'])) {
            if (!static::rewriteIP()['status']) {

            }
        }
        $msgBanIPs .= static::formatSpiderIP();
        return Lev::responseMsg(1, $msgBanIPs);
    }

    public static function formatSpiderIP() {

        if (isset($_POST['settings']['spiderIP'])) {
            $arr = Lev::explodev($_POST['settings']['spiderIP'], "\n");
            ipRecordCacheHelper::setcSpiderIP($arr);
        }
        return '';
    }
}