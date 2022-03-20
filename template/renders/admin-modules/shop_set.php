<?php
/**
 * Copyright (c) 2022-2222   All rights reserved.
 *
 * 创建时间：2022-03-20 15:15
 *
 * 项目：levs  -  $  - shop_set.php
 *
 * 作者：liwei
 */

use modules\levs\controllers\AdminModulesController;

//!defined('INLEV') && exit('Access Denied LEV');
?>


<div class="page">
    <div class="navbar">
        <div class="navbar-inner">
            <div class="left"></div>
            <div class="title">新版检查设置</div>
            <div class="right">
                <a class="closePP">
                    <svg class="icon"><use xlink:href="#fa-closer"></use></svg>
                </a>
            </div>
        </div>
    </div>

    <div class="page-content">

        <div class="page-content-inner" style="max-width: 660px">

            <div class="card data-xtable">
                <div class="card-header minheader" style="justify-content: left">
                    <div class="font14">新版本检查结果：</div>
                    <newcheckrs class="red">
                        <span class="wd100 progressbar-infinite color-multi"></span>
                    </newcheckrs>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    jQuery(function () {

        Levme.setTimeout(function () {
            Levme.ajaxv.getv('<?=$checkNewUrl?>', function (data, status) {
                jQuery('newcheckrs').html(data.message);
            });
        }, 1000);

    });
</script>