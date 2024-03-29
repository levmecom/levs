<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-09-24 17:14
 *
 * 项目：rm  -  $  - sharesWidget.php
 *
 * 作者：liwei 
 */

namespace lev\widgets\shares;

use Lev;
use lev\base\Widgetv;
use lev\helpers\ModulesHelper;
use modules\levmb\sdk\bdumini\bduminiAuthLogin;
use modules\levmb\sdk\weixinmp\weixinmpWidget;
use modules\levpays\modules\alipay\widgets\pay\payWidget;

!defined('INLEV') && exit('Access Denied LEV');

class sharesWidget extends Widgetv
{
    public static function openShareBtn() {
        if (Lev::checkHideT()) {
            return false;
        }
        return !Lev::stget('openShareBtn', 'levs');
    }

    /**
     * @param bool $show
     * @return string|void
     */
    public static function run($show = false)
    {
        //parent::run($show); // TODO: Change the autogenerated stub
//        if (!static::openShareBtn()) {
//            return '';
//        }

        $shareJs = '';
        if (Lev::checkBaiduboxappUserAgent()) {
            $shareJs .= static::baiduMiniShare();
        }elseif (Lev::checkAlipayUserAgent()) {
            $shareJs .= static::alipayShare();
        }elseif (Lev::checkWxUserAgent('', false)) {
            $shareJs .= static::wxShare();
        }
        return static::render($show, __DIR__ . '/views/run.php', [
            'shareJs' => $shareJs,
        ]);
    }

    public static function baiduMiniShare()
    {
        if (ModulesHelper::isInstallModule('levmb')) {
            return bduminiAuthLogin::shareJs();
        }
        return '';
    }

    public static function alipayShare()
    {
        if (ModulesHelper::isInstallModule('alipay')) {
            return payWidget::miniJs('/');
        }
        return '';
    }

    public static function wxShare()
    {
        if (ModulesHelper::isInstallModule('levmb')) {
            return weixinmpWidget::shareJs();
        }
        return '';
    }

}