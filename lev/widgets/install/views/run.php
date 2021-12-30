<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-29 14:49
 *
 * 项目：rm  -  $  - run.php
 *
 * 作者：liwei 
 */

?>



<script>
    jQuery(function () {
        Levme.showNoticesAdd('<div class="font14 color-green inblk" style="margin-top: 20px;">正在安装Discuz! 后台插件导航，请耐心等待...</div>', '', 100000);

        function doajax(isGetv, url, sucFunc, param, type) {
            if (isGetv && Levme.ajaxv.isGetv === 1) {
                showIconLoader(true);
                return;
            }
            param === undefined && (param = {});
            param['_csrf'] = _csrf;
            param['_'] = Math.random();
            Levme.ajaxv.isGetv = 1;
            jQuery.ajax({
                url: url,
                data: param,
                dataType: 'html',
                type: type ? type : 'get',
                success: function (data) {
                    Levme.ajaxv.isGetv = isGetv;
                    hideIconLoader();
                    var status = 0;
                    typeof sucFunc === "function" && sucFunc(data, status);
                },
                error: function (data) {
                    Levme.ajaxv.isGetv = isGetv;
                    typeof sucFunc === "function" && sucFunc(data);
                    hideIconLoader();
                    //errortips(data);
                }
            });
        }

        doajax(2, '<?=$insurl?>', function (data, status) {
            var msg = '<div class="font12 color-red inblk">导航安装失败，请手动安装，此失败不影响模块正常使用</div>';
            var succeed = 0;
            if (data) {
                if (status === undefined || typeof data === "object" ) {
                    if (parseInt(data.status) === 200) {
                        succeed = 1;
                        msg = '安装成功！';
                    }else if (parseInt(data.status) === 404) {
                        msg += '！后台访问失败，请确定是admin.php！或手动前往DZ后台安装插件';
                    }
                    msg += data.status;
                } else {
                    var datamsg = jQuery(data).find('.infotitle2').text();
                    msg = datamsg ? datamsg : msg;
                    if (data.indexOf('成功') > 0 || data.indexOf('infotitle2') > 0) {
                        succeed = 1;
                        msg += ' - 安装成功！';
                    }
                }
                if (succeed) {
                    msg += '<tips>正在启用插件，请稍候...</tips>';
                    doajax(0, '<?=$enableurl?>', function (data, status) {
                        var datamsg = jQuery(data).find('.infotitle2').text();
                        Levme.showNoticesAdd('<div class="inblk scale9 font12 transl">' + (datamsg ? datamsg : jQuery(data).text()) + '</div>');
                    }, {iden:'<?=$iden?>',doiden:1});
                }else {
                    if (typeof data !== "object" && data.indexOf('loginform') >0) {
                        msg += '！后台登陆超时，请手动前往DZ后台安装插件';
                    }else {
                        msg += jQuery(data).find('.infobox').text();
                    }
                    Levme.ajaxv.isGetv = 0;
                }
            }else {
                Levme.ajaxv.isGetv = 0;
            }
            Levme.showNoticesAdd('<div>'+ msg +'</div>');
        });
    });
</script>
