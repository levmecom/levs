<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-09 10:49
 *
 * 项目：rm  -  $  - SitemapController.php
 *
 * 作者：liwei 
 */

namespace modules\levs\modules\rewrite\controllers;

use Lev;
use lev\base\Adminv;
use lev\base\Assetsv;
use lev\base\Controllerv;
use lev\base\Viewv;
use lev\dz\dzDBHelper;
use lev\helpers\ModulesHelper;
use lev\helpers\UrlHelper;
use modules\levs\modules\rewrite\helpers\BaiduApiSitemap;
use modules\levs\modules\rewrite\helpers\BaiduMobileSitemapGenarate;
use modules\levs\modules\rewrite\helpers\rewriteSetHelper;
use modules\levssq\classes\lotteryHelper;
use modules\levssq\modules\ainews\helpers\lotterysHelper;
use modules\levssq\modules\census\helpers\UrlCensusHelper;
use modules\levssq\modules\zst\zsNavs;
use modules\levstore\table\levstore_modules\levstoreModulesModelHelper;
use modules\levstore\table\storeModelHelper;

!defined('INLEV') && exit('Access Denied LEV');

Adminv::checkAccess();
Assetsv::ajaxFormJs();

class SitemapController extends Controllerv
{
    public static $sitemaps = [
        'myself' => '自定义URL地址',
        'zst' => '彩票走势图sitemap提交',
        'store' => 'Lev商城模块提交',
        'dzthread' => 'Discuz!论坛帖子提交',
    ];

    public static function actionIndex() {

        $xmls = BaiduMobileSitemapGenarate::getGenSitemaps();

        Lev::$app['title'] = 'sitemap提交';
        Viewv::render('sitemap/index', [
            'xmls' => $xmls,
            'xmlweb' => BaiduMobileSitemapGenarate::getSitemapsDir(false) . '/',
            'sitemaps' => static::$sitemaps,
            'apiurls' => rewriteSetHelper::apiurls(),
        ]);
    }

    public static function actionMyself() {

        if (Lev::POSTv('dosubmit')) {
            $urls = Lev::explodev(Lev::stripTags(Lev::GPv('urlstrings')), "\n");
            if (Lev::GPv('genxml')) {
                BaiduMobileSitemapGenarate::gen($urls, 'myself-');
                Lev::showMessages(Lev::responseMsg());
            }else {
                static::actionBaiduApi($urls);
            }
            return;
        }

        Lev::$app['title'] = '自定义URL地址';
        Viewv::render('sitemap/myself', [
            'apiurls' => rewriteSetHelper::apiurls(),
        ]);
    }

    public static function actionDzthread() {
        if (!Lev::$app['isDiscuz']) {
            Lev::showMessages(Lev::responseMsg(-40404, '抱歉，非Discuz!论坛，无法提交'));
        }

        if (Lev::POSTv('dosubmit')) {
            if (Lev::GPv('genxml')) {
                BaiduMobileSitemapGenarate::gen(Lev::GPv('urls'), 'dzthread-');
                Lev::showMessages(Lev::responseMsg());
            }else {
                static::actionBaiduApi();
            }
            return;
        }

        $urls[] = ['id'=>null,'identifier'=>null,'name'=>'论坛首页', 'href'=>Lev::toRoute(['/forum.php'])];
        is_array($_urls = dzDBHelper::forumLists()) && $urls = array_merge($urls, $_urls);
        is_array($_urls = dzDBHelper::threadLists([], 2000)) && $urls = array_merge($urls, $_urls);

        Lev::$app['title'] = 'Discuz!论坛帖子提交';
        Viewv::render('sitemap/dzthread', [
            'apiurls' => rewriteSetHelper::apiurls(),
            'urls' => $urls,
        ]);
    }

    public static function actionStore() {
        if (!ModulesHelper::isInstallModule('levstore')) {
            Lev::showMessages(Lev::responseMsg(-40404, '抱歉，商城模块未安装，无法提交'));
        }

        if (Lev::POSTv('dosubmit')) {
            if (Lev::GPv('genxml')) {
                BaiduMobileSitemapGenarate::gen(Lev::GPv('urls'), 'store-');
                Lev::showMessages(Lev::responseMsg());
            }else {
                static::actionBaiduApi();
            }
            return;
        }

        $urls[] = ['id'=>null,'identifier'=>null,'name'=>'商城首页', 'href'=>UrlHelper::toModule('levstore')];
        is_array($_urls = levstoreModulesModelHelper::findAllField('id,identifier,name', 'appstatus=2 ORDER BY id DESC LIMIT 2000')) &&
        $urls = array_merge($urls, $_urls);

        Lev::$app['title'] = 'Lev商城模块提交';
        Viewv::render('sitemap/store', [
            'apiurls' => rewriteSetHelper::apiurls(),
            'urls' => $urls,
        ]);
    }

    public static function actionZst() {
        if (!ModulesHelper::isInstallModule('zst')) {
            Lev::showMessages(Lev::responseMsg(-40404, '抱歉，走势图模块未安装，无法提交'));
        }

        if (Lev::POSTv('dosubmit')) {
            if (Lev::GPv('genxml')) {
                BaiduMobileSitemapGenarate::gen(Lev::GPv('urls'), 'zst-');
                Lev::showMessages(Lev::responseMsg());
            }else {
                static::actionBaiduApi();
            }
            return;
        }

        $code = Lev::GPv('code')?:null;

        $lottInfo = lotterysHelper::lottinfo($code);
        $urls = zsNavs::zsarr($code, $lottInfo);

        $lottTools = ModulesHelper::isInstallModule('census') ? UrlCensusHelper::toolNavs($code) : [];
        $lottTools[] = ['name'=>'彩票开奖首页', 'url'=>UrlHelper::toModule('levssq')];
        $lottTools[] = ['name'=>'走势图首页', 'url'=>UrlHelper::toModule('zst')];

        Lev::$app['title'] = '彩票走势图sitemap提交';
        Viewv::render('sitemap/zst', [
            'code' => $code,
            'xmlweb' => BaiduMobileSitemapGenarate::getSitemapsDir(false) . '/',
            'apiurls' => rewriteSetHelper::apiurls(),
            'urls' => $urls,
            'lotts' => lotteryHelper::getAllLotts(),
            'lottTools' => $lottTools,
        ]);
    }

    public static function actionGenarate() {
        $urls = Lev::GPv('urls');
        $name = Lev::stripTags(Lev::GPv('name'));
        BaiduMobileSitemapGenarate::gen($urls, $name);
    }

    public static function actionBaiduApi($urls = null) {
        $api = floatval(Lev::GPv('api'));
        $urls === null &&
        $urls = Lev::GPv('urls');
        $arr = BaiduApiSitemap::actionBaiduApi($api, $urls);
        echo '<pre>';
        print_r($arr);
    }
}