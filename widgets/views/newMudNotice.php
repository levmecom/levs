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

<style>
    .data-xtable .tips-new-box122 td {height: auto !important;}
</style>

<div class="newMudsNotice hiddenx">
    <div class="data-xtable">
        <table class="tips-new-box122">
            <tr>
                <th>
                    <div class="card-header minheader">
                        <div>模块更新提示</div>
                        <div class="flex-box">
                            <a class="button-fill button button-small color-yellow toLevStoreFormSubmit ckTimeout inblk scale9 vera" href="<?=\lev\helpers\UrlHelper::storeUpdateView($muidarr)?>" target="_blank" _bk="1">查看全部</a>
                            <a class="button button-fill button-small color-gray vera inblk scale9 closeNewNoticeBtn" optype="1">忽略全部</a>
                            <a class="button button-fill button-small color-red vera inblk scale9 closeNewNoticeBtn" optype="2">关闭提示</a>
                        </div>
                    </div>
                </th>
            </tr>
            <?php foreach ($newMuds as $iden => $v):$muidarr[] = $v['id']?>
                <tr>
                    <td><a class="button button-fill button-small color-black scale8 toLevStoreFormSubmit ckTimeout" target="_blank" _bk="1" href="<?=\lev\helpers\UrlHelper::storeView($v['id'])?>"><?=$v['name'],$v['identifier']?></a>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    </div>

</div>

<div class="LoadPageAjaxJS hiddenx">
    <script>

        jQuery(function () {

            Levme.onClick('.closeNewNoticeBtn', function () {
                var optype = parseInt(jQuery(this).attr('optype'));
                if (optype === 2) {
                    var tip = '<tips>关闭后不再弹窗提示，如需开启请手动清除浏览器缓存</tips>';
                    myApp.confirm(tip, '<p class="font12 scale9">您确定要关闭吗？</p>', function () {
                        if (actionLocalStorage('closeNewNoticeBtn_x', 1)) {
                            levtoast('操作成功');
                        }else {
                            levtoast('缓存写入失败，请确定浏览器storage可用');
                        }
                        setS();
                    });
                }else {
                    setS();
                }

                function setS() {
                    Levme.ajaxv.getv('<?=Lev::toReRoute(['admin-modules/clear-tips', 'storeUpdateMud' => 1, 'id' => 'levs'])?>', function (data, status) {
                    })
                }
            });

            <?php if (Lev::GPv('r') != 'admin-modules'):?>
            window.setTimeout(function () {
                actionLocalStorage('closeNewNoticeBtn_x') ||
                Levme.showNoticesAdd(jQuery('.newMudsNotice.hiddenx').html());
            }, 808);
            <?php endif;?>

        });

    </script>
</div>
