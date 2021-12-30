<?php
/**
 * Copyright (c) 2021-2222   All rights reserved.
 *
 * 创建时间：2021-12-18 14:45
 *
 * 项目：levs  -  $  - set_avatar.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

?>

<div class="page">
    <?php Lev::navbar(); Lev::toolbar(); ?>

    <div class="page-content appbg">
        <div class="page-content-inner" style="max-width: 660px">
            <div class="card-header">
                <div class="buttons-row">
                    <a class="button color-white button-small" href="<?=Lev::toCurrent(['type'=>0], false, false)?>">头像1</a>
                    <a class="button color-white button-small" href="<?=Lev::toCurrent(['type'=>1], false, false)?>">头像2</a>
                </div>
                <span class="yellow font12 inblk scale9">【提示】更换头像后需刷新更新缓存后可见</span>
            </div>
            <div class="flex-box cards-box" style="flex-wrap: wrap">
            <?php foreach ($files as $src):?>
                <a class="card goods-box ajaxBtn" confirmmsg="您确定要更换头像吗？" href="<?=Lev::toCurrent(['doit'=>1, 'avatar'=>basename($src)], false, false)?>">
                    <iconi>
                    <img class="lazy wd60" data-src="<?=Lev::$aliases['@web'] . substr($src, $webrootLen)?>">
                    </iconi>
                </a>
            <?php endforeach;?>
            </div>
        </div>
    </div>

</div>
