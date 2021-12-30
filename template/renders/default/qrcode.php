<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-07-29 21:21
 *
 * 项目：rm  -  $  - qrcode.php
 *
 * 作者：liwei 
 */


?>

<div class="page">

    <?php Lev::navbar();?>

    <div class="page-content appbg">
        <div class="page-content-inner" style="max-width: 660px;height:90%;align-items: center;display: flex;justify-content: center;">
            <div class="flex-box ju-sa"><div id="qrcodeMbox" class="card"></div></div>
        </div>
    </div>

</div>

<script>

    jQuery(function() {
        'use strict';

        var qrcode = new QRCode("qrcodeMbox", {
            text: "<?=Lev::toRoute([$qrcodeLink])?>",
            width: 300,
            height: 300,
            //colorDark : "#000000",
            //colorLight : "#ffffff",
            //correctLevel : QRCode.CorrectLevel.H
        });

    });

</script>