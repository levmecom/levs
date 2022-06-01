<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-09-09 14:38
 *
 * 项目：rm  -  $  - run.php
 *
 * 作者：liwei 
 */

use lev\base\Adminv; ?>

<div class="panel panel-left panel-reveal">
    <div class="card-header">
        <div class="flex-box">
            <svg class="icon"><use xlink:href="#fa-caihod"></use></svg>
            <form class="searchbar" style="background: none;">
                <div class="searchbar-input">
                    <input type="search" placeholder="Lev模块导航搜索">
                    <a class="searchbar-clear"></a>
                </div>
                <!-- <a class="searchbar-cancel">取消</a> -->
            </form>
        </div>
    </div>
    <div class="list-block accordion-list no-hairlines" style="margin: 0">
        <ul class="srhmudiden" style="background: #eaf7fb">

            <li class="accordion-item">
                <a class="item-content-38 item-content item-link">
                    <div class="item-inner">
                        <div class="item-title color-orange">我的快捷导航</div>
                        <div class="item-after date scale8 transr">
                            <div class="button-fill button button-small updateQuickTopNavBtn color-yellow">
                                <svg class="icon"><use xlink:href="#fa-add"></use></svg>
                            </div>
                        </div>
                    </div>
                </a>
                <div class="accordion-item-content" style="background: rgba(0,0,0,0.04);">
                    <?php foreach ($quickNav as $v):?>
                    <a class="item-content-32 item-content item-link quickNavOneBox" href="<?=$v['link']?>" target="_top" _bk="1">
                        <div class="item-media">
                            <svg class="icon color-orange"><use xlink:href="#<?=$v['icon']?>"></use></svg>
                        </div>
                        <div class="item-inner">
                            <div class="item-title color-orange"><?=$v['title']?></div>
                            <div class="item-after date scale8 transr">
                                <label class="delQuickNavBtn" link="<?=$v['link']?>">
                                    <svg class="icon"><use xlink:href="#fa-trash"></use></svg>
                                </label>
                            </div>
                        </div>
                    </a>
                    <?php endforeach;?>
                </div>
            </li>

            <?php foreach ($barsNav as $v):?>
                <li><div class="item-content-32 item-content item-link">
                        <div class="item-inner srhtext" style="padding-top: 0;padding-bottom: 0;">
                            <div class="item-title">
                                <a href="<?=Lev::toReRoute(['superman/settings', 'id'=>APPVIDEN, 'iden'=>$v['identifier']])?>" class="scale8 transl inblk color-<?=$iden==$v['identifier']?'red':'black'?>"><?=$v['name']?></a>
                            </div>
                            <a class="item-after date transr scale8" target="_blank" _bk="1" href="<?=\lev\helpers\UrlHelper::toModule($v['identifier'])?>">
                                <?=$v['identifier']?>
                                <svg class="icon"><use xlink:href="#fa-huoj"></use></svg>
                            </a>
                        </div>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
    </div>
    <div class="card-footer">
        <div class="date transl"><?=Lev::$app['version']?></div>
    </div>

    <form class="toLevStoreForm hiddenx" id="toLevStoreForm" action="<?=\lev\helpers\UrlHelper::storeHome()?>" method="get" target="_blank">
        <input type="hidden" name="id" value="levstore">
        <input type="hidden" name="siteLoginUid" value="<?=\modules\levs\helpers\siteHelper::siteLoginUid()?>">
        <input type="hidden" name="encryInfo" value="<?=\modules\levs\helpers\siteHelper::encryInfo()?>">
        <input type="hidden" name="authsiteurl" value="<?=Lev::base64_encode_url(Lev::$aliases['@siteurl'])?>">
    </form>
</div>

<script>
    jQuery(function () {
        var mySearchbar = myApp.searchbar('.panel .searchbar', {
            searchList: '.srhmudiden',
            searchIn: '.srhtext'
        });

        Levme.onClick('.toLevStoreFormSubmit', function () {
            if (jQuery(this).hasClass('ckTimeout')) {
                var ckTimeout = actionLocalStorage('toLevStoreFormSubmit');
                if ( ckTimeout > (new Date().getTime() - 3600 * 24000 * 7) ) {
                    return true;
                }
            }
            actionLocalStorage('toLevStoreFormSubmit', new Date().getTime());

            var action = jQuery(this).attr('href');
            action && jQuery('.toLevStoreForm').attr('action', action);
            jQuery('.toLevStoreForm').submit();
            return false;
        });

        Levme.onClick('.delQuickNavBtn', function () {
            var obj = this;
            myApp.confirm('您确定要删除该快捷导航吗？', '', function () {
                dosubmitQ(1, jQuery(obj).attr('link'), function (data, status) {
                    jQuery(obj).parents('.quickNavOneBox').remove();
                });
            });
            return false;
        });

        Levme.onClick('.updateQuickTopNavBtn', function () {
            var icons = Levme.iconfonts();
            myApp.modal({
                title: '<div class="scale6 nowrap">您确定将以下地址加入快捷导航吗？</div>',
                text: '<div class="msg_box updateQuickTopNavBox">' +
                    '<div style="margin-top:10px;text-align: left;font-size: 12px;color: #666;">快捷地址：</div>' +
                    '<div><input type=text name="navstr" style="width:100%;" value="'+window.location.href+'"></div>' +
                    '<div style="margin-top:10px;text-align: left;font-size: 12px;color: #666;">快捷标题：</div>' +
                    '<div><input type=text name="title" style="width:100%;" value="'+jQuery('title').html().split('-')[0]+'"></div>' +
                    '<div class="flex-box">' +
                    '<input type=text name="icon" style="width:100%;margin-top:10px" value="" placeholder="快捷图标">' +
                    '<label style="margin:10px 0 0 10px;" class="button-small button button-fill wdmin iconSelectWinBtn">选择图标</label>' +
                    '</div>' +
                    '</div>',
                buttons: [{
                    text: '<span class="color-gray">删除</span>',
                    onClick: function () {
                        dosubmitQ(1);
                    }
                },{
                    text: '<span class="color-gray">取消</span>',
                    onClick: function () {}
                },{
                    text: '确定',
                    bold:true,
                    onClick: function () {
                        dosubmitQ();
                    }
                }]
            });
        });

        function dosubmitQ(del, navstr, func) {
            del = del ? 1 : 0;

            navstr = base64EncodeUrl(navstr ? navstr : jQuery('.updateQuickTopNavBox input[name="navstr"]').val());
            var icon   = base64EncodeUrl(jQuery('.updateQuickTopNavBox input[name="icon"]').val());
            var title  = jQuery('.updateQuickTopNavBox input[name="title"]').val();
            Levme.ajaxv.getv('<?=Lev::toReRoute(['superman/update-quick-nav'])?>', function (data, status) {
                typeof func === "function" && func(data, status);
            }, {del:del, navstr:navstr, icon:icon, title:title});
        }
    });
</script>
