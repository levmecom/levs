<?php
/**
 * Copyright (c) 2021-2222   All rights reserved.
 *
 * 创建时间：2021-12-04 22:55
 *
 * 项目：levs  -  $  - welcomeImageWidget.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

namespace modules\levs\widgets\openscreen;

use Lev;
use lev\base\Widgetv;
use modules\levs\helpers\levsSetHelper;

class welcomeImageWidget extends Widgetv
{
    public static $once = 0;

    public static function swiper() {
        if (!levsSetHelper::openWelcome() || Lev::GPv('r')) {
            return '';
        }

        if (static::$once >0) {
            return '';
        }
        static::$once += 1;

        $homes[APPVIDEN] = 1;
        ($SiteIden = Lev::SiteIden()) && $homes[$SiteIden] = 1;
        if (!isset($homes[Lev::$app['iden']])) {
            return '';
        }

        $welcomeImgs = levsSetHelper::welcomeImgs();
        $homeLink = levsSetHelper::homeLink();
        $coolTime = levsSetHelper::coolTime();
        $autoTime = levsSetHelper::autoTime();

        return parent::render(0, __DIR__ . '/views/swiper.php', [
            'welcomeImgs' => $welcomeImgs,
            'homeLink' => $homeLink,
            'coolTime' => $coolTime,
            'autoTime' => $autoTime,
        ]);
    }

}