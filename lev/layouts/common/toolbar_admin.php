<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-07-17 15:59
 *
 * 项目：rm  -  $  - toolbar_admin.php
 *
 * 作者：liwei 
 */


/* @var $saveIcon boolean */
/* @var $trashIcon boolean */
?>


<div class="toolbar tabbar toolbar-superman">
    <div class="toolbar-inner">
        <div class="flex-box wdmin">
        <?php if ($saveIcon):?>
        <label class="button button-fill scale7" for="dosubmit">
            <svg class="icon" aria-hidden="true"><use xlink:href="#fa-save"></use></svg>
            <span class="tabbar-label"> 保存 </span>
        </label>
        <?php endif;?>
        <?php if ($trashIcon):?>
            <label class="button button-fill scale7 deleteCheckAll color-gray">
                <svg class="icon" aria-hidden="true"><use xlink:href="#fa-trash"></use></svg>
                <span class="tabbar-label"> 删除 </span>
            </label>
        <?php endif;?>

        <?php if (!empty($btns)): ?>
            <div class="buttons-row scale7"><div class="wd30 nowrap flex-box">
            <?php foreach ($btns as $v):?>
                <a href="<?=$v['link']?>" class="button button-fill <?=$v['attr']?>"><?=$v['name']?></a>
            <?php endforeach;?>
            </div>&nbsp;
            </div>
        <?php endif;?>
        </div>
        <a class="date inblk" target="_blank" _bk=1 href="https://levme.com"><?=Lev::$app['version']?></a>
    </div>
</div>


