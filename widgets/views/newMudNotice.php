<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-27 11:44
 *
 * 项目：rm  -  $  - newMudNotice.php
 *
 * 作者：liwei 
 */


?>

<div class="newMudsNotice hiddenx">
    <div class="buttons-row data-xtable"><div class="flex-box scale9 transl">
            <?php foreach ($newMuds as $iden => $v):$muidarr[] = $v['id']?>
                <a class="button button-fill button-small color-black toLevStoreFormSubmit ckTimeout" target="_blank" _bk="1" href="<?=\lev\helpers\UrlHelper::storeView($v['id'])?>"><?=$v['name'],$v['identifier']?></a>
            <?php endforeach;?>
        </div>
    </div>

<a class="button-fill button button-small color-yellow toLevStoreFormSubmit ckTimeout inblk scale9 vera" href="<?=\lev\helpers\UrlHelper::storeUpdateView($muidarr)?>" target="_blank" _bk="1">查看全部</a>

    <a class="button button-fill button-small color-gray vera inblk scale9 is_ajax_a" href="<?=Lev::toReRoute(['admin-modules/clear-tips', 'storeUpdateMud'=>1, 'id'=>'levs'])?>">忽略全部</a>
</div>

<div class="LoadPageAjaxJS hiddenx">
    <script>

        jQuery(function () {

            window.setTimeout(function () {
                Levme.showNoticesAdd(jQuery('.newMudsNotice.hiddenx').html());
            }, 808);

        });

    </script>
</div>
