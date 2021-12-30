<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-18 19:17
 *
 * 项目：upload  -  $  - toolbar.php
 *
 * 作者：liwei 
 */

if (Lev::GETv('ziframescreen')) return;

echo Lev::actionObjectMethodIden('levmb', 'modules\levmb\sdk\myphone\myphoneAuthLogin', [], 'APPdownload');

empty($toolbarNavs) && $toolbarNavs = \lev\helpers\SettingsHelper::getToolbarNavs();
if (empty($toolbarNavs)) return;
?>

<div class="toolbar tabbar tabbar-labels tabbar-bgcolor-black common-toolbarx">
    <div class="toolbar-inner">
        <?php foreach ($toolbarNavs as $v): ?>
            <a class="link show-type-<?=$v['ShowType']?> <?php echo $v['_link'] ? $v['_target'].$v['_link'] : ''?>">
                <?php echo $v['_icon']?>
                <span class="tabbar-label"><?php echo $v['name']?></span>
            </a>
        <?php endforeach;?>
    </div>
</div>

