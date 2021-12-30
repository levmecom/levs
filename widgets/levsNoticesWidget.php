<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-27 11:42
 *
 * 项目：rm  -  $  - levsNoticesWidget.php
 *
 * 作者：liwei 
 */

namespace modules\levs\widgets;

use lev\base\Widgetv;
use modules\levs\helpers\siteHelper;

!defined('INLEV') && exit('Access Denied LEV');

class levsNoticesWidget extends Widgetv
{

    public static function newMudNotice() {

        $newMuds = siteHelper::getNewStoreMuds();

        if (!$newMuds) return '';

        return parent::render(0, __DIR__ . '/views/newMudNotice.php', [
            'newMuds' => $newMuds,
        ]);

    }

}