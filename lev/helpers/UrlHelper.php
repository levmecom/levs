<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-06-12 22:11
 *
 * 项目：rm  -  $  - UrlHelper.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

use Lev;
use lev\widgets\login\loginWidget;
use modules\levpays\controllers\PayController;
use modules\levs\controllers\AdminModulesController;

!defined('INLEV') && exit('Access Denied LEV');


class BaseUrl {

    public static function dropSubTable($tableName) {
        return Lev::toReRoute(['superman/drop-sub-table', 'table'=>dbHelper::tableName($tableName)]);
    }

    public static function createSubTable($tableName) {
        return Lev::toReRoute(['superman/create-sub-table', 'table'=>dbHelper::tableName($tableName)]);
    }

    public static function updateSubTableCache() {
        return Lev::toReRoute(['superman/update-sub-table-cache']);
    }

    /**
     * 模块更新
     * @param $iden
     * @param $classdir
     * @return bool|mixed|string
     */
    public static function updateModule($iden, $classdir)
    {
        return Lev::toReRoute(['superman/update-module', 'iden' => $iden, 'classdir' => $classdir]);
    }

    /**
     * 模块安装
     * @param $iden
     * @param $classdir
     * @return bool|mixed|string
     */
    public static function installModule($iden, $classdir)
    {
        return Lev::toReRoute(['superman/install-module', 'iden' => $iden, 'classdir' => $classdir]);
    }

    /**
     * 模块卸载
     * @param $iden
     * @param $classdir
     * @return bool|mixed|string
     */
    public static function uninstallModule($iden, $classdir)
    {
        return Lev::toReRoute(['superman/uninstall-module', 'iden' => $iden, 'classdir' => $classdir]);
    }

    /**
     * 商城域名
     * @param bool $https
     * @return string
     */
    public static function store($https = true) {
        return ($https ? 'https': 'http').'://appstore.levme.com';
    }

    /**
     * 商店家
     * @return string
     */
    public static function storeHome()
    {
        return 'https://appstore.levme.com/levstore';
    }

    /**
     * 商店我的
     * @return string
     */
    public static function storeMy()
    {
        return 'https://appstore.levme.com/levstore/user';
    }

    /**
     * @param $muid
     */
    public static function storeView($muid, $iden = '') {
        return 'https://appstore.levme.com/levstore/view-'.($iden ?: $muid).'.html';
        //return 'https://appstore.levme.com/levs.php?id=levstore&r=view&'.($iden ? 'iden='.$iden : 'mu='.$muid);
    }

    /**
     * @param $muid
     * @return string
     */
    public static function storeUpdateView($muid)
    {
        is_array($muid) && $muid = implode('_', $muid);
        return 'https://appstore.levme.com/levs.php?id=levstore&r=pay&muid='.$muid.'&site='.Lev::base64_encode_url(Lev::$aliases['@siteurl']);
    }

    /**
     * @param null $cache
     * @return bool|mixed|string
     *
     * @see AdminModulesController::actionCheckNew()
     */
    public static function checkNewMud($cache = null, $pm = []) {
        $pm += ['admin-modules/check-new', 'id'=>'levs', 'cache'=>$cache];
        return Lev::toReRoute($pm);
    }

    /**
     * 上传到模块商店
     * @param string $iden 模块标识符
     * @param integer $mudid  模块ID
     * @return bool|mixed|string
     */
    public static function zipUploadStore($iden, $mudid) {
        return static::zip($iden, $mudid, 1);
    }

    /**
     * 模块压缩
     * @param $iden
     * @param $mudid
     * @param null $store
     * @return bool|mixed|string
     */
    public static function zip($iden, $mudid, $store = null) {
        return Lev::toReRoute(['modules/zip','iden'=>$iden,'opid'=>$mudid, 'store'=>$store, 'id'=>'levmodules']);
    }

    /**
     * FTP上传设置
     * @return bool|mixed|string
     */
    public static function ftpsettings()
    {
        return Lev::toReRoute(['superman/settings', 'classify'=>'1', 'iden'=>'ftp']);
    }

    /**
     * 设置后台管理页面显示数据
     */
    public static function setAdminPage($controllerName)
    {
        return Lev::toReRoute(['admin-modules/set-admin-page', 'controllerName'=>$controllerName, 'id'=>'levs']);
    }

    public static function tradeMyIden($iden) {
        return Lev::toReWrRoute(['my', 'id'=>'levpays', 'iden'=>$iden]);
    }

    /**
     * @param $goodsId
     * @param null $iden
     * @param null $paymoney
     * @param null $tradeId
     * @return bool|mixed|string
     *
     * @see PayController::actionIndex()
     */
    public static function payTrade($goodsId, $iden = null, $paymoney = null, $tradeId = null) {
        return Lev::toReRoute([
                'pay',
                'id'       =>'levpays',
                'payIden'  => $iden ?: Lev::$app['iden'],
                'goodsId'  => $goodsId,
                'paymoney' => $paymoney,
                'tradeId'  => $tradeId,
            ]);
    }

    public static function trade() {
        return Lev::toReWrRoute(['my', 'id'=>'levpays']);
    }

    public static function pay() {
        return Lev::toReWrRoute(['/', 'id'=>'levpays']);
    }

    public static function home() {
        return Lev::$aliases['@siteurl'] . (empty(Lev::$app['isDiscuz']) ? '' : '/'.Lev::$app['homeFile']);
    }
    public static function homeMud($iden = '') {
        $iden ||
        $iden = Lev::$app['iden'];
        if ($iden && !ModulesHelper::isInstallModule($iden)) {
            return static::home();
        }
        return static::toModule($iden);
    }

    /**
     * 隐私政策
     * @return bool|mixed|string
     */
    public static function privacypolicy() {
        return Lev::toReWrRoute(['login/privacypolicy', 'id'=>APPVIDEN]);
    }

    /**
     * 用户协议
     * @return bool|mixed|string
     */
    public static function useragreement() {
        return Lev::toReWrRoute(['login/useragreement', 'id'=>APPVIDEN]);
    }

    /**
     * 法律声明
     * @return bool|mixed|string
     */
    public static function law() {
        return Lev::toReWrRoute(['login/law', 'id'=>APPVIDEN]);
    }

    /**
     * 免责声明
     * @return bool|mixed|string
     */
    public static function disc() {
        return Lev::toReWrRoute(['login/disc', 'id'=>APPVIDEN]);
    }

    public static function rootDir($dir) {
        return Lev::$aliases['@web'].substr($dir, strlen(Lev::$aliases['@webroot']));
    }

    public static function login() {
        if (Lev::$app['uid'] <1) {
            return ($url = loginWidget::loginUrl()) ? $url[0] : static::my();
        }
        return static::my();
    }

    public static function my($scheme = true) {
        return Lev::toReWrRoute(['login/my', 'id'=>'levs'], $scheme);
    }

    public static function toModule($iden = '', $pm = ['/'])
    {
        $pm['id'] = $iden ?: Lev::$app['iden'];
        return Lev::toReWrRoute($pm);
    }

    public static function adminModules($iden = 'levs')
    {
        return Lev::toReWrRoute(['admin-modules', 'id'=>$iden]);
    }

    public static function qqkf($qq = '227248948')
    {
        return 'http://wpa.qq.com/msgrd?v=3&uin='.$qq.'&site='.Lev::$app['SiteName'].'&menu=yes';
    }

    public static function check( $siteurl)
    {
        return (strpos($siteurl, 'http') === 0 && strpos($siteurl, '://') !== false);
    }

}

class UrlHelper extends BaseUrl {
}