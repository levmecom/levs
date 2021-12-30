<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-24 23:40
 *
 * 项目：rm  -  $  - recordModelHelper.php
 *
 * 作者：liwei 
 */

namespace modules\levs\modules\ipban\table;

use modules\levs\modules\ipban\table\ipban_record\ipbanRecordModelHelper;

!defined('INLEV') && exit('Access Denied LEV');

class recordModelHelper extends ipbanRecordModelHelper
{

    public static function statuses() {
        return [
            '正常',
            '关闭',
            '蜘蛛'
        ];
    }

}