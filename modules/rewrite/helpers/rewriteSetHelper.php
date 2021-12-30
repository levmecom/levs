<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-27 16:34
 *
 * 项目：upload  -  $  - setHelper.php
 *
 * 作者：liwei 
 */

namespace modules\levs\modules\rewrite\helpers;

use Lev;

!defined('INLEV') && exit('Access Denied LEV');

class BaserewriteSet {

    public static function apiurls() {
        return Lev::stget('apiurls', 'rewrite');
    }

}

class rewriteSetHelper extends BaserewriteSet {

    /**
     * superman/settings 保存成功回调函数
     * @return array
     */
    public static function SettingsReturn() {
        return Lev::responseMsg(1, '--');
    }

}