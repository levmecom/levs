<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-22 10:43
 *
 * 项目：rm  -  $  - downZipmudHelper.php
 *
 * 作者：liwei 
 */

namespace modules\levs\helpers;

use Lev;
use lev\base\Adminv;
use lev\base\Controllerv;
use lev\base\Requestv;
use lev\controllers\SupermanController;
use lev\helpers\cacheFileHelpers;
use lev\helpers\curlHelper;
use lev\helpers\ModulesHelper;
use lev\helpers\UrlHelper;
use lev\helpers\ZipFileHelper;

!defined('INLEV') && exit('Access Denied LEV');


class downZipmudHelper extends ZipFileHelper
{

    /**
     * @return array
     *
     * @see ApiController::actionDownloadZip()
     */
    public static function actionDownloadZip($iv, $iden, $classdir, $encrys) {
        $levMudInfo = ModulesHelper::getModuleInfo('levs');
        $idenMudInfo = ModulesHelper::getModuleInfo($iden);
        $pm['url']      = UrlHelper::store(false).'/levs.php?id=levstore&r=api/download-zip&inajax=1&iv='.$iv.'&iden='.$iden.'&classdir='.$classdir;
        $pm['url']     .= '&Levv='.Lev::$app['version'].'&LevVersion='.$levMudInfo['version'].'&versiontime='.$idenMudInfo['versiontime'];
        $pm['url']     .= '&timestamp='.Lev::$app['timestamp'].'&adminSign='.Adminv::getAdminSign('levme.com', Lev::$app['timestamp']);
        $pm['post']     = ['encrys'=>$encrys, 'siteurl'=>Lev::base64_encode_url(Lev::$aliases['@siteurl'])];
        $pm['agent']    = 1;
        $pm['referer']  = Lev::toCurrent();
        $pm['ip']       = Requestv::getRemoteIP();
        $pm['time']     = 1800;//最多30分钟
        set_time_limit(0);
        $res = curlHelper::doCurl($pm);
        if ($res && strpos($res, '</html>') === false) {
            if ($errMsg = json_decode($res, true)) {
                return $errMsg;
            }
            if (!is_dir($zipFileDir = static::setZipSaveDir('/down'))) {
                return Lev::responseMsg(-40010, '文件写入失败，请检查目录是否可写：'.dirname($zipFileDir));
            }
            ($size = file_put_contents($zipFile = $zipFileDir.'/'.$iden.'.zip', cacheFileHelpers::getc('zipsdir') != $zipFileDir ? '' : $res)) && cacheFileHelpers::setc('zipsdir', $zipFile);
            if (!$size) {
                return Lev::responseMsg(-40000, '文件写入失败，请检查目录是否可写：'.dirname($zipFileDir));
            }
            if (!static::unZipModules($zipFile)) {
                return Lev::responseMsg(-40020, '抱歉，压缩包已损坏，解压失败');
            }
            //return SupermanController::InstallOrUpdateModule($iden, $classdir);
            Controllerv::redirect(Lev::toReRoute(['superman/install-or-update-module', 'id'=>APPVIDEN, 'iden'=>$iden, 'classdir'=>$classdir, 'checkFile'=>1, 'doit'=>1]));
            return Lev::responseMsg();
        }else {
            return Lev::responseMsg(-1, '下载失败', [$res]);
        }
    }
    public static function unZipModules($filesrc)
    {
        $res = parent::unZipModules($filesrc); // TODO: Change the autogenerated stub
        @unlink($filesrc);
        return $res;
    }
}