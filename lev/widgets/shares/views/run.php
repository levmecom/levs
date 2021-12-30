<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-09-24 17:16
 *
 * 项目：rm  -  $  - run.php
 *
 * 作者：liwei 
 */

?>
<?=$shareJs?>

<style>
    .levtoast2.qrcodeShareBox {top:calc(50% - 150px);}
    .levtoast2.qrcodeShareBox .qrcodeShare {margin:10px;padding:10px 10px 7px;background:#fff;}
    .popupSharesWidgetv tr {height:96px;}
    .popupSharesWidgetv tr.tr-frst td::before {height:0;}
    .popupSharesWidgetv a {color:#fff;text-align: center;line-height:30px;font-size: 18.8px !important;cursor: pointer;}
    .popupSharesWidgetv a span {color:#333;font-size: 12px !important;}
    i.faW {background: #00bc0d;border-radius: 50%;display:block;width:32px !important;height:32px !important;line-height: 0px;text-align: center;margin: auto;font-style: normal;vertical-align: middle;}
    .popupSharesWidgetv svg.icon {font-size:18.8px !important;color:#fff !important;margin:auto !important;margin-top:6.5px !important;line-height: 0;}
    i.faW.faqzone {background:#fab619;}
    i.faW.faweibo {background:#fb5555;}
    i.faW.faqqhy, i.faW.falink {background:#297fd6;}
    .share-img-btnv {width:70px;margin: 10px auto;}
    .share-img-btnv p {font-size: 12px;margin: 7px auto;}
</style>

<div class="popup popupSharesWidgetv" style="width: calc(100% - 30px) !important;height: 50% !important;margin: auto;max-width: 660px;max-height: 200px;left: 15px !important;right: 15px !important;border-radius: 10px;bottom: 15px !important;top: unset !important;background: rgba(255,255,255,0.94);">
    <div class="flex-box ju-sas">
        <div class="flex-box ju-sa copyShareUrlBtn" style="flex-wrap: wrap;margin:20px">
            <div class="share-img-btnv showWxhyShareBtn">
                <i class="faW"><svg class="icon" aria-hidden="true"><use xlink:href="#fa-wxhy"></use></svg></i>
                <p class="scale9">微信好友</p>
            </div>
            <div class="share-img-btnv showWxShareBtn">
                <i class="faW"><svg class="icon" aria-hidden="true"><use xlink:href="#fa-wxpyq"></use></svg></i>
                <p class="scale9">朋友圈</p>
            </div>
            <div class="share-img-btnv showQQShareBtn">
                <i class="faW faqzone"><svg class="icon" aria-hidden="true"><use xlink:href="#fa-qqhy"></use></svg></i>
                <p class="scale9">QQ好友</p>
            </div>
            <div class="share-img-btnv showQzoneShareBtn">
                <i class="faW faqzone"><svg class="icon" aria-hidden="true"><use xlink:href="#fa-qzone"></use></svg></i>
                <p class="scale9">QQ空间</p>
            </div>
            <div class="share-img-btnv showWeiboShareBtn">
                <i class="faW faweibo"><svg class="icon" aria-hidden="true"><use xlink:href="#fa-weibo"></use></svg></i>
                <p class="scale9">新浪微博</p>
            </div>
            <div class="share-img-btnv showAlipayShareBtn">
                <svg class="icon" style="font-size: 32px !important;margin: 0 !important;border-radius: 50%;"><use xlink:href="#fa-alipay"></use></svg>
                <p class="scale9">支付宝</p>
            </div>
            <div class="share-img-btnv showQrcodeShareBtn">
                <i class="faW falink"><svg class="icon" aria-hidden="true"><use xlink:href="#fa-qrcode"></use></svg></i>
                <p class="scale9">二维码</p>
            </div>
            <div class="share-img-btnv">
                <i class="faW falink"><svg class="icon" aria-hidden="true"><use xlink:href="#fa-link"></use></svg></i>
                <p class="scale9">复制链接</p>
            </div>
    </div>
    </div>
</div>

<script>
    (function () {
        'use strict';
        typeof window.sharesWidget === "undefined" && (window.sharesWidget = {});
        jQuery(function () {
            Levme.onClick('.showShareGridBtn', function () {
                //jQuery(this).hasClass('mySelfBtn') && myApp.closeModal();

                if (typeof window.sharesWidget.baiduMiniShare === "function") {
                    window.sharesWidget.baiduMiniShare();
                }else if (typeof my !== "undefined" && typeof my.startShare === "function") {
                    my.startShare();
                }else if (typeof wx !== "undefined") {
                    typeof window.sharesWidget.wxShare === "function" && window.sharesWidget.wxShare();
                }else {
                    myApp.popup('.popup.popupSharesWidgetv');
                }
                return false;
            });

            Levme.onClick('.showQrcodeShareBtn', function () {
                myApp.alert('<div class="font12 scale9 color-red">微信、支付宝扫一扫</div><div class="flex-box ju-sa" style="height:200px" id="share-qrcode"></div>');
                loadQrcode();
                return false;
            });

            Levme.onClick('.showQzoneShareBtn', function () {
                qzoneShare(Levme.locationHref(), jQuery('title').text());
                return false;
            });

            Levme.onClick('.showWeiboShareBtn', function () {
                shareModel.sinaWeiBo(Levme.locationHref(), jQuery('title').text());
                return false;
            });

            Levme.onClick('.showWxhyShareBtn', function () {
                window.setTimeout(function () {
                    Levme.openWX();
                }, 101);
                return false;
            });

            Levme.onClick('.showWxShareBtn', function () {
                window.setTimeout(function () {
                    Levme.openWX();
                }, 101);
                return false;
            });

            Levme.onClick('.showQQShareBtn', function () {
                window.setTimeout(function () {
                    Levme.openQQ();
                }, 101);
                return false;
            });

            Levme.onClick('.showAlipayShareBtn', function () {
                window.setTimeout(function () {
                    Levme.openAlipay();
                }, 101);
                return false;
            });

            var clipboard = null;
            jQuery(document).on('popup:open', '.popupSharesWidgetv', function () {
                if (clipboard) return;

                loadClipboard();

                typeof QRCode === "undefined"
                    ? jQuery.getScript('<?=\lev\base\Assetsv::qrcodeJs('src')?>', function () {
                        loadQrcode();
                    })
                    : loadQrcode();

            });

            function loadQrcode() {
                jQuery('#share-qrcode').html('');
                jQuery('#share-qrcode').length > 0 &&
                window.setTimeout(function () {
                    new QRCode('share-qrcode', {
                        text: Levme.locationHref(),
                        width: 200,
                        height: 200,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.Q
                    });
                }, 500);
            }

            function loadClipboard() {
                typeof ClipboardJS === "undefined"
                    ? jQuery.getScript('<?=\lev\base\Assetsv::clipboardJs('src')?>', function () {
                        initClipboard();
                    })
                    : initClipboard();

                function initClipboard() {
                    clipboard = new ClipboardJS('.copyShareUrlBtn', {
                        text: function() {
                            return Levme.locationHref();
                        }
                    });
                    clipboard.on('success', function(e) {
                        Levme.showNoticesAdd('已为您复制分享地址，分享给朋友', 0, 3000);
                    });

                    clipboard.on('error', function(e) {
                        levtoast('复制失败');
                    });
                }
            }

            //4：豆瓣
            //
            //https://www.douban.com/share/service?href=&name=&text=

            /**
             * qq空间分享
             * @param url       链接地址 你需要分享的网站 如：www.baidu.com
             * @param desc      描述
             * @param title     标题
             * @param summary   内容
             * @param pics      图片
             */
            function qzoneShare(url,title,summary,pics){
                var urlPath = "https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url="+ encodeURIComponent(url) +
                    "&desc=&title=" + title +
                    "&summary=" + (summary || '') +
                    "&pics=" + (pics || '');
                window.open (urlPath, 'qq分享', 'height=637, width=1053, top=195,left=459, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')
            }

            var shareModel = {
                /**
                 * 分享新浪微博
                 * @param  {[type]} title [分享标题]
                 * @param  {[type]} url   [分享url链接，默认当前页面]
                 * @param  {[type]} pic   [分享图片]
                 * @return {[type]}       [description]
                 */
                sinaWeiBo: function (url, title, pic) {
                    var param = {
                        url: url || window.location.href,
                        type: '3',
                        count: '1', /** 是否显示分享数，1显示(可选)*/
                        appkey: '', /** 您申请的应用appkey,显示分享来源(可选)*/
                        title: title || '', /** 分享的文字内容(可选，默认为所在页面的title)*/
                        pic: pic || '', /**分享图片的路径(可选)*/
                        ralateUid:'', /**关联用户的UID，分享微博会@该用户(可选)*/
                        rnd: new Date().valueOf()
                    }
                    var temp = [];
                    for( var p in param ) {
                        temp.push(p + '=' +encodeURIComponent( param[p ] || '' ) )
                    }
                    var targetUrl = 'http://service.weibo.com/share/share.php?' + temp.join('&');
                    window.open(targetUrl, 'sinaweibo', 'height=430, width=400');
                }
            };
        });
    })();
</script>


