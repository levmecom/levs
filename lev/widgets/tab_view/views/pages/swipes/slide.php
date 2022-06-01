<?php
/**
 * Copyright (c) 2022-2222   All rights reserved.
 *
 * 创建时间：2022-04-19 12:22
 *
 * 项目：levs  -  $  - slide.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');
?>

<div class="swipeSlideBox swipeSlideBox_<?=$v['tabid']?> swiper-slide with-toolbar" data-id="<?=$v['tabid']?>">

    <div class="page-content appbg infinite-scroll infiniteBox_<?=$v['tabid']?>" data-distance="50" style="padding-top: 0">
        <div class="page-content-inner" style="padding:70px 0 0;<?=isset($v['tabWidth'])?'max-width:'.$v['tabWidth']:''?>">
            <div class="data-contents">
                <?php if (!empty($v['title'])):?>
                    <div class="card-header font12" style="color: yellow">
                        <?=$v['title']?>
                    </div>
                <?php endif;?>

                <div class="init-data-box"></div>

                <div class="dataInfiniteBox loadDataShowBox virtual-list <?=empty($v['noswiping']) ? '' : 'swiper-no-swiping'?>" page="<?=$v['page']?>" url="<?=$v['url']?>" not="<?=$v['not']?>" data-boxid="<?=$v['tabid']?>"></div>
            </div>

            <?php if ($v['url']):?>
            <div class="flex-box ju-sa startInfiniteScrollBtnBox" style="margin:20px">
                <a class="startInfiniteScrollBtn color-orange scale7 <?=$v['loadStart']?>"><?=$v['loadStartTip']?></a>
            </div>
            <?php endif;?>


        </div>

        <div class="fotter-box-gen">
            <?php include \lev\widgets\tab_view\tabViewWidget::footerTabFile(); ?>
        </div>

    </div>

</div>
