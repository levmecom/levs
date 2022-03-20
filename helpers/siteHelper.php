<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-18 11:59
 *
 * 项目：rm  -  $  - siteHelper.php
 *
 * 作者：liwei 
 */

namespace modules\levs\helpers;

use Lev;
use lev\base\Adminv;
use lev\helpers\cacheFileHelpers;
use lev\helpers\curlHelper;
use lev\helpers\ModulesHelper;
use lev\helpers\UrlHelper;
use modules\levs\widgets\levsNoticesWidget;

!defined('INLEV') && exit('Access Denied LEV');


class siteHelper extends cacheFileHelpers
{
    public static $cacheDir = '/.site';

    public static function checkNewCache() {
        return static::optCache() ? Lev::responseMsg(-1, '缓存') : static::checkNew();
    }

    public static function checkNew() {
        $idens = [];

        $optCache = static::optCache();

        $muds = ModulesHelper::findAllField('identifier,version,versiontime', 1);
        foreach ($muds as $v) {
            $idens[$v['identifier']] = [
                $v['version'],
                $v['versiontime']
            ];
        }
        $pm['url'] = UrlHelper::store() . '/levs.php?id=levstore&r=api/check-new&'.Adminv::getTemporaryAccesstoken();
        $pm['url'].= '&timestamp='.Lev::$app['timestamp'].'&adminSign='.Adminv::getAdminSign('levme.com', Lev::$app['timestamp']);
        $pm['post'] = [
            'idens' => Lev::base64_encode_url(json_encode($idens)),
            'site'  => Lev::base64_encode_url(Lev::$aliases['@siteurl']),
            'myStoreSiteInfo' => static::myStoreSiteInfo(),
            'accesstoken'     => Lev::arrv('accesstoken', $optCache, 0),
            'siteuid' => Lev::arrv('siteuid', $optCache, 0),
            'iv'      => Lev::arrv('iv', $optCache, 0),
            'reg'     => Lev::arrv('iv', $optCache, 0),
        ];
        $res = curlHelper::doCurl($pm);
        $arr = json_decode($res, true);
        if (!empty($arr[0])) {
            $optCache[0] = $arr[0];
            $optCache = Lev::responseMsg(1, '有新版本', $optCache);
        }else {
            $optCache[0] = $res;
            $optCache = Lev::responseMsg(-2, '', $optCache);//无更新
        }
        isset($arr['siteuid']) && $optCache['siteuid'] = $arr['siteuid'];
        isset($arr['accesstoken']) && $optCache['accesstoken'] = $arr['accesstoken'];//保密 登陆凭证
        if (Adminv::getTemporaryAccesstoken($arr['tempToken']) && !empty($arr['myStoreSiteInfo'])) {
            static::myStoreSiteInfo($arr['myStoreSiteInfo']);
            if (!empty($arr['registerEncry'])) {
                $optCache['extjs'] = static::getCheckNewMudJs(0, 1);
                $optCache['iv'] = $arr['iv'];
            }
        }
        /*
         * @var array $newmuds = ['id'=>'muid'];
         * $newmuds 字段包含：'id,identifier,version,versiontime'
         * */
        static::optCache($optCache, empty($arr) || !empty($arr['iv']) ? 10 : 36000);
        return $optCache;
    }

    public static function authSite($siteuid, $accesstoken, $encrys) {
        $pm['url'] = UrlHelper::store() . '/levs.php?id=levstore&r=api/auth-site&'.Adminv::getTemporaryAccesstoken();
        $pm['url'].= '&timestamp='.Lev::$app['timestamp'].'&adminSign='.Adminv::getAdminSign('levme.com', Lev::$app['timestamp']);
        $pm['post'] = [
            'site'  => Lev::base64_encode_url(Lev::$aliases['@siteurl']),
            'accesstoken' => $accesstoken,
            'siteuid' => $siteuid,
            'encrys' => $encrys,
        ];
        $res = curlHelper::doCurl($pm);
        $arr = json_decode($res, true);
        if (empty($arr)) {
            return Lev::responseMsg(-1, '授权错误', [$res]);
        }elseif ($arr['status'] >0 && !empty($arr['myStoreSiteInfo'])) {
            static::myStoreSiteInfo($arr['myStoreSiteInfo']);
            $newmuds = static::optCache();
            if (isset($arr['siteuid'])) {
                $newmuds['siteuid'] = $arr['siteuid'];
                $newmuds['accesstoken'] = $arr['accesstoken'];//保密 登陆凭证
                static::optCache($newmuds);
            }
        }
        return $arr;
    }

    public static function myStoreSiteInfo($value = null, $clear = null) {
        $ckey = 'myStoreSiteInfo/1'.ModulesHelper::getModuleInfo('levs')['addtime'];
        return static::optc($ckey, $value, 0, $clear, false);
    }

    public static function getNewStoreMuds($muds = null, $clearIden = false) {
        $muds === null &&
        $muds = static::optCache();
        if (!empty($muds[0]) && !empty($muds['status']) && $muds['status'] >0) {
            if ($clearIden) {
                if ($clearIden === true) {
                    unset($muds[0]);
                } else {
                    unset($muds[0][$clearIden]);
                }
                static::optCache($muds);
            }
            return $muds[0];
        }
        return false;
    }

    public static function clearNewStoreMuds($iden) {
        static::getNewStoreMuds(null, $iden);
    }

    /**
     * @param null $value
     * @param int $timeout
     * @param null $clear
     * @param bool $checkTimeout
     * @return bool|mixed|string
     */
    public static function optCache($value = null, $timeout = 36000, $clear = null, $checkTimeout = true) {
        $ckey = 'modules\levs\helpers\checkNew';
        isset($value['accesstoken']) && static::optMysiteAccesstoken($value);
        $res = cacheFileHelpers::optc($ckey, $value, $timeout, $clear, $checkTimeout);
        $value === null && $clear === null && !is_array($res) && $res = [];
        return $res;
    }
    public static function optMysiteAccesstoken($value = null) {
        $ckey = 'modules\levs\helpers\siteHelper\token';
        if ($value !== null) {
            static::setc($ckey, $value);
        }
        return static::getc($ckey, false);
    }

    public static function setCnzzJs($force = false)
    {
        if (Lev::$app['isAdmin'] && !Lev::checkHideT()) {
            ($force || !siteHelper::optCache()) &&
            Lev::setCnzzJs('checkNewMuds', static::getCheckNewMudJs());
            Lev::setCnzzJs('checkNewMudsNotice', levsNoticesWidget::newMudNotice());
        }
    }
    public static function getCheckNewMudJs($cache = 1, $jquery = false, $refreshsite = null) {
        $js = '<script class="checkNewMud" src="'.UrlHelper::checkNewMud($cache, ['inajax'=>1, 'refreshsite'=>$refreshsite]).'"></script>';
        if ($jquery) {
            $js = ['jQuery(function(){
    window.setTimeout(function () {
        jQuery("script.checkNewMud").length <3 && jQuery("body").append(base64DecodeUrl("'.Lev::base64_encode_url($js).'"));
    }, 1000);
});',$js];
        }
        return $js;
    }

    public static function siteLoginUid()
    {
        return Lev::arrv('siteuid', static::optMysiteAccesstoken(), 0);
    }

    public static function encryInfo()
    {
        return static::myStoreSiteInfo();
    }

}