<?php
/**
 * Copyright (c) 2021-2222   All rights reserved.
 *
 * 创建时间：2021-12-14 13:59
 *
 * 项目：levs  -  $  - tab.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');
?>


<div class="with-toolbar tabmain-box tab tab-<?=$v['tabid']?>" data-id="<?=$v['tabid']?>">
    <div class="page-content appbg infinite-scroll" style="padding-top: 0">
        <div class="preloader preloader-white initLoader" style="margin:auto;display: flex;"></div>
        <div class="page-content-inner" style="padding:70px 0 0;display: none;<?=isset($v['tabWidth'])?'max-width:'.$v['tabWidth']:''?>">
            <div class="tab-contentv">
                <?php if (!empty($v['title'])):?>
                <div class="card-header font12" style="color: yellow">
                    <?=$v['title']?>
                </div>
                <?php endif;?>

                <div class="init-data-box"><?php include __DIR__ . '/initdata.php'?></div>

                <div class="listLoadBox virtual-list" page="<?=$v['page']?>" url="<?=$v['url']?>" not="<?=$v['not']?>"></div>
            </div>

            <?php if ($v['url']):?>
                <div class="date infinite-scroll-preloader hiddenx" style="transform: scale(0.5) !important;margin: 7px;"><div class="preloader preloader-red"></div></div>
                <a class="date loadStart <?=$v['loadStart']?>" data-box=".tab-<?=$v['tabid']?> "><?=$v['loadStartTip']?></a>
            <?php endif;?>


        </div>

        <?php include __DIR__  .'/footer.php'; ?>
    </div>

    <?php include __DIR__  .'/toolbar.php'; ?>
</div>

