<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-06 21:16
 *
 * 项目：upload  -  $  - loadScreenWidget.php
 *
 * 作者：liwei 
 */

namespace lev\widgets\loadScreen;

use Lev;
use lev\base\Widgetv;

!defined('INLEV') && exit('Access Denied LEV');

class loadScreenWidget extends Widgetv
{

    /**
     * @param string $screenId
     * @param string $loadUrl
     * @param int $height
     * @param int $timeout
     * @param bool $jsonp
     * @param bool $show
     * @return string
     */
    public static function run($screenId = '', $loadUrl = '', $height = 320, $timeout = 700, $jsonp = false, $show = false)
    {
        return parent::render($show, __DIR__ . '/views/run.php', [
            'screenId' => $screenId ?: 'loadScreenId'. Lev::$app['timestamp'],
            'loadUrl' => $loadUrl,
            'height' => $height,
            'timeout' => $timeout,
            'jsonp' => $jsonp,
        ]);
    }

}