<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-06-22 12:24
 *
 * 项目：rm  -  $  - navbar.php
 *
 * 作者：liwei 
 */


?>

<style>
    .page-aboutb .content-block {padding:30px 15px;background: #fff !important;margin-top: 0;}
</style>

<div class="navbar navbar-bgcolor-red">
    <div class="navbar-inner">
        <div class="left">
            <a class="link icon-only" href="javascript:window.history.back();">
                <svg class="icon" aria-hidden="true"><use xlink:href="#fa-back"></use></svg>
            </a>
        </div>
        <div class="title" style="margin: auto"><?=Lev::$app['title']?></div>
        <div class="right <?=$hide?>">
            <a class="external icon-only" target="_top" href="<?=$homeUrl?>">
                <svg class="icon" aria-hidden="true"><use xlink:href="#fa-home"></use></svg>
            </a>
        </div>
    </div>
</div>


