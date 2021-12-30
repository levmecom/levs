<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-16 14:20
 *
 * 项目：rm  -  $  - header_htm.php
 *
 * 作者：liwei 
 */

use lev\helpers\ModulesHelper;
use lev\helpers\UrlHelper;

$btns = '';
if (!empty($ftpUpdateMud)) {
    $k = 0;
    foreach ($ftpUpdateMud as $v) {
        if ($v) {
            $k++;
            $color = $k % 2 ? 'orange' : 'blue';
            $href = ModulesHelper::isInstallModule($v['identifier'])
                ? UrlHelper::updateModule($v['identifier'], $v['classdir'])
                : UrlHelper::installModule($v['identifier'], $v['classdir']);
            !empty($v['##changeFiles']['lastEditTime']) &&
            $v['##changeFiles']['lastEditTime'] = '最后改动时间：'.Lev::asRealTime($v['##changeFiles']['lastEditTime']);
            $btns .= '<a class="button-fill button color-' . $color . '" href="' . $href . '" title="新版本：' . $v['version'] . '&#10;上传时间：' . date('Y-m-d H:i:s', $v['versiontime']) . '&#10;更新内容：' . print_r($v['##changeFiles'], true) . '">' . $v['name'] . '[' . $v['identifier'] . ']<absx>NEW</absx></a>';
        }
    }
}
?>
<style>
    .notification.notification-session .item-text.alert-info {min-height: 280px;}
    .opt-btn-box1 .avd-box1 { display: none; }
    .notification.notification-session .item-text.alert-info p,
    .notification.notification-session .item-text.alert-info div {display: inline-block !important;color: #31708f !important;font-size:12px}
    .item-text.alert-info tips {color: #c128b2 !important; font-size:14px !important;}
    .notification.notification-session .item-text.alert-info {color: #c128b2 !important; font-size:14px !important;}
</style>

<?php include __DIR__ .'/header_htm_down.php'; ?>
<?php include __DIR__ .'/header_htm_authsite.php'; ?>
<?php include __DIR__ .'/header_htm_update.php'; ?>

<div class="card animated heartBeat no-hairlines ftpUpdateMud <?=$btns ? '' : 'hiddenx'?>">
    <div class="card-header bg-lightblue">
        <div class="wdmin">新版本</div>
        <div class="scale7 data-xtable">
            <div class="buttons-row newMudBtnsBox"><?=$btns?></div>
        </div>
        <div class="buttons-row wdmin">
            <a class="button-fill button button-small color-yellow batchActionsZip">更新全部</a>
            <a class="button-fill button button-small color-gray clearActionsZip">忽略全部</a>
        </div>
    </div>
</div>

<script>
    var levsAdminModules = {
        tips: '',
        showTip:function (tip) {
            var str = tip ? tip : levsAdminModules.tips;
            window.setTimeout(function () {
                Levme.showNoticesAdd(str, '', 1000000);
                jQuery('.notifications .item-text').scrollTop(jQuery('.notifications .item-text .item-text').height()+10000);
            }, 201);
        }

    };

jQuery(function () {
    Levme.onClick('.clearActionsZip', function () {
        var obj = this;
        myApp.confirm('您确定要【忽略所有新版本】吗？', function () {
            Levme.ajaxv.getv('<?=Lev::toReRoute(['admin-modules/clear-tips', 'id'=>'levs'])?>', function () {

            }, {storeUpdateMud:jQuery(obj).attr('storeUpdateMud')?1:0});
        });
    });

    Levme.onClick('.batchActionsZip', function () {
        if (levsAdminModules.tips) {
            levtoast('抱歉，已经执行过了，请刷新');
            showTips();
            return;
        }
        myApp.confirm('您确定要批量执行【新版本】的更新或安装吗？', function () {
            var hrefs = getAllActions();
            if (hrefs) {
                Levme.showNoticesAdd('<div class="color-black">运行日志：</div><br>', '', 1000000);
                levsAdminModules.tips = '';
                loopbatchActionsZip(hrefs);
            }
        });
    });

    function loopbatchActionsZip(hrefs, sec) {
        if (Levme.ajaxv.isGetv) {
            sec = sec ? sec : 1;
            levtoast('正在配置，请耐心等待...'+ sec);
            window.setTimeout(function () {
                loopbatchActionsZip(hrefs, sec+1);
            }, 1200);
            return;
        }
        if (!hrefs || hrefs.length < 1) {
            hideIconLoader();
            levsAdminModules.tips = '执行完成：<a class="inblk scale6 button button-fill vera" href="<?=UrlHelper::adminModules()?>">刷新</a>';
            showTips(levsAdminModules.tips);
            levsAdminModules.tips = Levme.getNoticsAddhtm();
            return;
        }
        var attr = hrefs.shift();
        showIconLoader(true);
        levsAdminModules.tips = '<tips>正在执行：'+ attr.title + '</tips> -> ';
        showTips(levsAdminModules.tips);
        Levme.ajaxv.getv(attr.href, function (data, status) {
            var acmsg = data.message ? data.message : (status && status >0 ? '成功' : '失败');
            levsAdminModules.tips = '<p class="font12 scale9 inblk">执行结果：'+ acmsg + '</p><br>';
            showTips(levsAdminModules.tips);

            showIconLoader(true);
            window.setTimeout(function () {
                loopbatchActionsZip(hrefs);
            }, 880);

            data && data.extjs && jQuery('body').append(data.extjs);
        }, {doit:1});
    }

    function getAllActions() {
        var hrefs = [];
        jQuery('.newMudBtnsBox a').each(function () {
            var attr = {href:this.href, title:jQuery(this).html()};
            hrefs.push(attr);
        });
        if (hrefs.length < 1) {
            myApp.alert('抱歉，没有可执行项目', function () {
                Levme.animated('.newMudBtnsBox');
            });
            return false;
        }
        return hrefs;
    }

    function showTips(tip) {
        levsAdminModules.showTip(tip);
    }


    <?=Lev::GETv('disableBack')?'Levme.disableBack();':''?>

});


</script>
