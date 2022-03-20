<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-25 17:16
 *
 * 项目：rm  -  $  - header_htm_update.php
 *
 * 作者：liwei 
 */

?>


<?php if (!empty($storeUpdateMud)):?>
    <div class="card no-hairlines storeUpdateMud">
        <div class="card-header bg-lightblue">
            <div class="wdmin nowrap">新版上架</div>
            <div class="scale7 data-xtable" style="overflow: hidden;">
                <div class="buttons-row newStoreMudBtnsBox">
                    <?php foreach ($storeUpdateMud as $iden => $v):$muidarr[] = $v['id']?>
                        <a class="button button-fill color-black toLevStoreFormSubmit ckTimeout" target="_blank" _bk="1" href="<?=\lev\helpers\UrlHelper::storeView($v['id'])?>"><?=$v['name'],$v['identifier']?></a>
                    <?php endforeach;?>
                </div>
            </div>
            <div class="buttons-row wdmin">
                <a class="button-fill button button-small color-red newMudsLocalDetail">只看更新(<?=count($storeUpdateMud)?>)</a>
                <a class="button-fill button button-small color-yellow toLevStoreFormSubmit ckTimeout" href="<?=\lev\helpers\UrlHelper::storeUpdateView($muidarr)?>" target="_blank" _bk="1">查看全部更新</a>
                <a class="button-fill button button-small color-gray clearActionsZip" storeUpdateMud="1">忽略全部</a>
            </div>
        </div>
    </div>
<?php endif;?>

<script>
    jQuery(function () {
        Levme.onClick('.newMudsLocalDetail', function () {
            jQuery('.data-listb .data-xtable.gen-tab tr').each(function () {
                if ( jQuery(this).find('.isUpdateBtn').length <1 ) {
                    jQuery(this).hasClass('hiddenx')
                        ? jQuery(this).removeClass('hiddenx')
                        : jQuery(this).addClass('hiddenx');
                }
            })
        })
    });
</script>