<?php
/**
 * Copyright (c) 2022-2222   All rights reserved.
 *
 * 创建时间：2022-04-03 11:59
 *
 * 项目：levs  -  $  - tabViewWidget.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');


namespace lev\widgets\tab_view;

use Lev;
use lev\base\Controllerv;
use lev\base\Viewv;
use lev\base\Widgetv;

class tabViewWidget extends Widgetv
{
    public static $deTabId = '';

    public static $tabnav = [];

    public static $param = [
        'pageFile'    => '',
        'footer'      => '',
        'toolbar'     => '',
        'navbarInner' => '',
    ];

    public static function viewv($deTabId = 'default') {
        static::setDeTabId($deTabId);
        $tabnav = static::getTabnav();

        $pageFile = is_file(static::$param['pageFile']) ? static::$param['pageFile'] : static::pageFile();
        Viewv::render($pageFile, [
            'subnavs' => $tabnav,
            'deTabId' => static::$deTabId,
            'pageName'=> Controllerv::$pageName,
            'param'   => static::getParam(),
        ]);
    }

    public static function setDeTabId($deTabId) {
        static::$deTabId = $deTabId;
        $tabnav = static::getTabnav();
        $deTabInfo = isset($tabnav[$deTabId]) ? $tabnav[$deTabId] : reset($tabnav);
        static::$deTabId = $deTabInfo['tabid'];
    }

    public static function swipesFile() {
        return __DIR__ . '/views/pages/swipes/swipe.php';
    }

    public static function page2File() {
        return __DIR__ . '/views/pages/page2.php';
    }

    public static function pageFile() {
        return __DIR__ . '/views/page.php';
    }

    public static function footerTabFile() {
        return __DIR__ . '/views/tab/footer.php';
    }

    public static function footerFile() {
        return __DIR__ . '/views/footer.php';
    }

    public static function navbarInnerFile() {
        return __DIR__ . '/views/navbar_inner.php';
    }

    public static function toolbarFile() {
        return __DIR__ . '/views/toolbar.php';
    }

    public static function clearTabnav() {
        static::$tabnav = [];
    }

    public static function setParam($key, $value) {
        static::$param[$key] = $value;
    }

    public static function getParam() {
        return static::$param;
    }

    public static function getTabnav() {
        return static::$tabnav;
    }

    /**
     * 标签自动加载数据路由
     * @param $tab
     * @return string
     * @see childClass
     */
    public static function loadRoute($tab) {
        return '';//Lev::toReRoute(['app/load-data', 'tab' => $tab, 'id'=>'zst']);
    }

    public static function setTabnav($tabid, $name, $page = 2, $url = null, $title = '', $initData = '', $pm = []) {
        $url === 1 && $url = static::loadRoute($tabid);
        $pm += [
            'loadStart'    => '',
            'loadStartTip' => '点击加载更多',
            'tabWidth'     => null,
            'toolbar'      => '',
            'footer'       => '',
            'extPm'        => [],
            'noswiping'    => 0,//swiper-no-swiping 禁止滑动切换 当内容存在横向滚动时 很有必要
        ];
        isset(static::$tabnav[$tabid]) || static::$tabnav[$tabid] = [];
        static::$tabnav[$tabid] += [
            'tabid' => $tabid,
            'name'  => $name,
            'title' => $title,
            'url'   => $url,
            'attr'  => '',//它应放在class=" {attr}" 例：attr = ' wdmin" target="_blank';
            'page'  => $page, //2,//-2：首次打开不加载url数据
            'not'   => 0,//1：没有数据了

            'tmpFile' => '', // 模板文件路径

            'initData' => $initData,

            'loadStart'    => $pm['loadStart'],//hiddenx 隐藏
            'loadStartTip' => $pm['loadStartTip'],
            'tabWidth'     => $pm['tabWidth'],
            'toolbar'      => $pm['toolbar'],
            'footer'       => $pm['footer'],
            'extPm'        => $pm['extPm'],
            'noswiping'    => $pm['noswiping'],//swiper-no-swiping 禁止滑动切换 当内容存在横向滚动时 很有必要
        ];
    }

    public static function includeTabtmp($v)
    {
        (is_file($tmpFile = $v['tmpFile']) || is_file($tmpFile = __DIR__ .'/views/tab/tab.php')) && include $tmpFile;
    }
}