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

namespace modules\levs\modules\composer\helpers;

use Lev;
use lev\base\Viewv;

!defined('INLEV') && exit('Access Denied LEV');

class BasecomposerSet {

}

class composerSetHelper extends BasecomposerSet {

    /**
     * superman/settings 保存成功回调函数
     * @return array
     *
     * @see SupermanController::actionSettings()
     */
    public static function SettingsReturn() {
        return Lev::responseMsg(1, '--');
    }

    /**
     * @return string
     * @see SupermanController::actionSettings()
     */
    public static function HeaderHtm() {
        if (Lev::GPv('classify') == 2879) {
            return '<div class="card card-header"></div>';
        }
        return '';
    }

    /**
     * @return string
     * @see SupermanController::actionSettings()
     */
    public static function FooterHtm() {//Lev::debug();
//        if (Lev::GPv('classify') == 2788 && is_file($file = dirname(__DIR__) . '/template/renders/superman-settings/footerhtm.php')) {
//            return Viewv::renderPartial($file, []);
//        }
        return '';
    }

    /**
     * @return string
     * @see SupermanController::actionSettings()
     */
    public static function FormFooterHtm() {
        if (Lev::GPv('classify') == 2879) {
            return '<div class="card card-header"></div>';
        }
        return '';
    }
}