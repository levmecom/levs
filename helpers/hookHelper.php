<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-07-01 14:23
 *
 * 项目：rm  -  $  - hookHelper.php
 *
 * 作者：liwei 
 */

namespace modules\levs\helpers;

use Lev;
use lev\helpers\dbHelper;

!defined('INLEV') && exit('Access Denied LEV');


class hookHelper
{

    public static function qrcodePosition() {
        !Lev::GPv('dosubmit') && static::createGlobarFooterJs(true);
        return ['左下', '右下'];
    }

    public static function createGlobarFooterJs($force = false) {
        $jsFile = Lev::$aliases['@runtime'] . '/levs_global_footer.js';

        $qrcodeSet = levsSetHelper::qrcodeSrc();
        if (empty($qrcodeSet) || $qrcodeSet['status']) {
            $force && file_put_contents($jsFile, '');
            return '';
        }

        $position = $qrcodeSet['position'] == 1 ? 'right:0;bottom:0;' : 'left:0;bottom:0;';

        $qrcodeJs = file_get_contents(Lev::getAlias('@modules/'.APPVIDEN.'/web/assets/statics/common/qrcode.min.js'));
        $qrcodeJs.= <<<js
        
        
(function() {
    'use strict';
        
    var box = document.getElementById("levs-hook-main");
    box.style.display = 'block';
    var divobj = document.createElement("div");
    divobj.innerHTML = '<div id="levs-qrcode-mainv" onclick="hideQrcodev()" style="position: fixed;{$position}text-align: center;font-size: 12px;background: #fff;padding: 10px;margin: 10px;border: 1px solid #e1dfdf;box-shadow: 0 0 8px #2975c4;">'
    +'<div class="qrcode-titlev" style="font-size: 12px;transform: scale(0.88);transform-origin: top;margin-top: -7px;">{$qrcodeSet['name']}</div>'
    +'<div id="levs-qrcode"></div></div>';
    box.appendChild(divobj);
    
    var qrcode = new QRCode("levs-qrcode", {
        text: "{$qrcodeSet['=link']}",
        width: 100,
        height: 100,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
    
})();

function hideQrcodev() {
    document.getElementById('levs-qrcode-mainv').style.display='none';
}

js;

        file_put_contents($jsFile, dbHelper::setDataToCharset($qrcodeJs));
        return $qrcodeJs;
    }

}