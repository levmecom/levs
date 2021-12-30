<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-19 12:52
 *
 * 项目：upload  -  $  - infiniteLoad.php
 *
 * 作者：liwei 
 */

namespace lev\widgets\infiniteLoad;

use lev\base\Widgetv;

!defined('INLEV') && exit('Access Denied LEV');


class infiniteLoadWidget extends Widgetv
{

    /**
     * .box必须包含.infinite-scroll，子级需要在$box后加空格
     * @param string $box eg: .box
     * @param string $loadUrl
     * @param bool $initJs
     * @param bool $jsonp
     * @param bool $show
     * @return string|void
     */
    public static function run($box = '.box ', $loadUrl = '', $initJs = false, $jsonp = false, $show = false) {
        return parent::render($show, __DIR__ . '/views/run.php', [
            'box' => $box,
            'loadUrl' => $loadUrl,
            'initJs' => $initJs,
            'jsonp' => $jsonp,
        ]);
    }

}