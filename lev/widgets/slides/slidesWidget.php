<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-04-25 22:16
 *
 * 项目：upload  -  $  - slidesWidget.php
 *
 * 作者：liwei 
 */

namespace lev\widgets\slides;

!defined('INLEV') && exit('Access Denied LEV');

use Lev;
use lev\base\Widgetv;
use lev\helpers\SettingsHelper;

class slidesWidget extends Widgetv
{

    //静态run方法
    public static function run($slidesArr = false, $height = 130, $show = true)
    {
        $slidesArr === false && $slidesArr = static::defaultSlidesArr();
        if (!$slidesArr) {
            return '';
        }
        return parent::render($show, __DIR__ . '/views/run.php', [
            'slidesArr' => $slidesArr,
            'height'    => $height,
        ]);
    }

    public static function input($inputname, $slidesArr = false, $height = 130) {
        return parent::render(false, __DIR__ . '/views/input.php', [
            'inputname' => $inputname,
            'slidesArr' => $slidesArr,
            'height'    => $height,
        ]);
    }

    public static function defaultSlidesArr() {
        return SettingsHelper::getSlides();
//        return [
//            [
//                '_target' => ' openPP',
//                '_link' => '" _href="#',
//                '_src' => \Lev::getAlias('@assets/statics/images/slide-1.jpg'),
//            ]
//        ];
    }
}