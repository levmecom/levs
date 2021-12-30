<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-22 10:20
 *
 * 项目：rm  -  $  - header_htm_down.php
 *
 * 作者：liwei 
 */

use lev\helpers\UrlHelper;
?>



<?php if (!empty($DownloadZipMuds)):?>

<script>
jQuery(function () {
    levsAdminModules.tips = '';

    Levme.showNoticesAdd('<div class="color-black">下载运行日志：</div><br>', '', 1000000);
    loopDownloadZipMuds(<?=json_encode($DownloadZipMuds)?>);

    function loopDownloadZipMuds(hrefs, sec) {
        if (Levme.ajaxv.isGetv) {
            sec = sec ? sec : 1;
            levtoast('正在配置，请耐心等待...'+ sec);
            window.setTimeout(function () {
                loopDownloadZipMuds(hrefs, sec+1);
            }, 1200);
            return;
        }

        if (!hrefs || hrefs.length < 1) {
            hideIconLoader();
            levsAdminModules.tips = '执行完成：即将转入！<a class="inblk scale6 button button-fill vera" href="<?=UrlHelper::adminModules()?>">刷新</a><br>';
            levsAdminModules.showTip(levsAdminModules.tips);
            actionLocalStorage('cookieNotices', Levme.getNoticsAddhtm());
            window.setTimeout(function () {
                window.location = changeUrlArgs('<?=UrlHelper::adminModules()?>', {disableBack:1});
            }, 1500);
            return;
        }
        var attr = hrefs.shift();

        window.setTimeout(function () { showIconLoader(true); }, 980);

        levsAdminModules.tips = '正在执行，请耐心等待：'+ attr.name + ' -> ';
        levsAdminModules.showTip(levsAdminModules.tips);
        Levme.ajaxv.getv(attr.href, function (data, status) {
            var acmsg = data.message ? data.message : (status && status >0 ? '成功' : '失败');
            levsAdminModules.tips = '<p class="font12 scale9 inblk">执行结果：'+ acmsg + '</p><br>';
            levsAdminModules.showTip(levsAdminModules.tips);

            showIconLoader(true);
            window.setTimeout(function () {
                loopDownloadZipMuds(hrefs);
            }, 880);

            data && data.extjs && jQuery('body').append(data.extjs);

        }, {doit:1});
    }

});
</script>


<?php endif;?>
