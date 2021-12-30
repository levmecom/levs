<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-06 10:21
 *
 * 项目：rm  -  $  - navbar_inner.php
 *
 * 作者：liwei 
 */
?>



<div class="left">
    <?php if (\lev\base\Requestv::getReferer()):?>
        <a class="link scale9 historyBackBtn" href="javascript:window.history.back();">
            <svg class="icon"><use xlink:href="#fa-back"></use></svg>
        </a>
    <?php endif;?>

    <a class="link icon-only" href="<?php echo \lev\helpers\UrlHelper::homeMud()?>">
        <svg class="icon"><use xlink:href="#fa-home"></use></svg>
    </a>
</div>
<div class="title nowrap"><?php echo Lev::$app['title']?></div>
<div class="right">
    <?php if (\lev\widgets\shares\sharesWidget::openShareBtn()):?>
    <a class="link icon-only scale9 showShareGridBtn"><svg class="icon"><use xlink:href="#fa-share"></use></svg></a>
    <?php endif;?>
    <?php echo Lev::actionObjectMethodIden('levsign', 'modules\levsign\levsignHelper', [], 'signNavbarBtn')?>
    <a class="link icon-only <?=Lev::$app['uid'] <1 ? 'openLoginBtn' : 'is_ajax_a" href="'.\lev\helpers\UrlHelper::my(0)?>">
        <img class="date bradius lazy" data-src="<?php echo \lev\helpers\UserHelper::avatar()?>">
        <?php echo Lev::$app['username']?>
    </a>
</div>