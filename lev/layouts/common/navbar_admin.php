<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-07-17 15:59
 *
 * 项目：rm  -  $  - navbar_admin.php
 *
 * 作者：liwei 
 */

/* @var $addurl string */
/* @var $srhtitle string */
/* @var $srhkey string */
/* @var $subnav boolean */
/* @var $tips string */
/* @var $saveIcon boolean */
/* @var $trashIcon boolean */

?>


<div class="navbar page-admin-navbar adminbar navbar-bgcolor-red">
    <div class="navbar-inner">
        <div class="left transl" style="transform: scale(.97)">
            <?=\lev\widgets\adminModulesNav\adminModulesNav::buttonHtm()?>
            <a class="link tooltip-init" href="javascript:window.history.back();" data-tooltip="后退">
                <svg class="icon" aria-hidden="true"><use xlink:href="#fa-back"></use></svg>
            </a>
            <a class="link tooltip-init" href="javascript:window.location.reload();" data-tooltip="刷新">
                <svg class="icon" aria-hidden="true"><use xlink:href="#fa-refresh"></use></svg>
            </a>
            <?php if ($saveIcon):?>
                <a class="link">
                    <label for="dosubmit">
                        <svg class="icon" aria-hidden="true"><use xlink:href="#fa-save"></use></svg>
                    </label>
                </a>
            <?php endif;?>
            <?php if ($trashIcon):?>
            <a class="link deleteCheckAll">
                <svg class="icon" aria-hidden="true"><use xlink:href="#fa-trash"></use></svg>
            </a>
            <?php endif;?>
        </div>
        <div class="title">
            <?php echo Lev::$app['title']?>
            <tips class="date inblk" style="color:yellow !important;"><?=$tips?></tips>
        </div>
        <div class="right">
            <?php if ($addurl):?>
            <a class="button-fill button color-yellow scale9 wdmin admin-create-btnv" href="<?php echo $addurl?>">
                <svg class="icon" style="color:darkred !important" aria-hidden="true"><use xlink:href="#fa-add"></use></svg>
                创建
            </a>
            <?php endif;?>
            <?php if ($srhtitle):?>
            <div class="searchbar searchbarIndex">
                <div class="searchbar-input">
                    <input type="search" placeholder="回车执行搜索【<?php echo $srhtitle?>】" value="<?php echo $srhkey?>">
                    <a class="searchbar-clear"></a>
                </div>
            </div>
            <?php endif;?>
        </div>

        <?php echo $subnav ? \lev\helpers\ModulesHelper::getAdminSubnavHtmsAndBox() : ''?>
    </div>
</div>
