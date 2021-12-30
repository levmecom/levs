(function () {
    'use strict';

    jQuery(function () {
        Superman.init();
    });

    var Superman = {
        init:function () {

            if (jQuery('html').width() < 1200) {
                jQuery('html').css('overflow-x', 'auto');
            }

            jQuery(document).on('keyup', '.searchbarIndex input[type="search"]', function (e) {
                if (e.which === 13) {
                    window.location = changeUrlArgs(window.location.href, {srhkey:this.value, isSrhkeyup:1, page:0});
                }
            });

            Superman.adminOp();
        },
        adminOp:function () {

            Levme.onClick('.deleteOneBtn', function () {//删除一条数据
                var opid = jQuery(this).attr('opid');
                myApp.confirm('您确定要删除【ID：'+opid+'】这条数据吗？', function () {
                    showIconLoader();
                    Levme.ajaxv.postv('', function (data, status) {
                        status >0 && window.setTimeout(function () { window.location.reload(); }, 1200);
                    }, {'ids[]':opid, adminop:'deleteIds', _csrf:_csrf, inajax:1, _:Math.random()});
                });
            });

            Levme.onClick('.deleteCheckAll', function () {//删除选中数据
                var cklen = jQuery('.data-listb input[name="ids[]"]:checked').length;
                if (cklen >0) {
                    myApp.confirm('您确定要删除已选中的 <absx> '+ cklen +' </absx> 条数据吗？', '', function () {
                        showIconLoader();
                        jQuery('.data-listb').ajaxSubmit({
                            type: 'post',
                            dataType: 'json',
                            data: {adminop:'deleteIds', _csrf:_csrf, inajax:1, _:Math.random()},
                            success: function (data) {
                                hideIconLoader();
                                levtoast(data.message);
                                if (data && parseInt(data.status) >0) {
                                    window.setTimeout(function () {
                                        window.location = window.location.href;
                                    }, 200)
                                }
                            },
                            error: function (data) {
                                hideIconLoader();
                                errortips(data);
                            }
                        });
                    });
                }else {
                    levtoast('您至少选中一项');
                }
            });

//状态字段修改
            jQuery(document).on('change', '.setStatus', function (e) {
                var opid = jQuery(this).attr('opid');
                var field = jQuery(this).attr('field');
                var status = jQuery(this).find('input[type=checkbox]:checked').val() ? 0 : 1;
                if (jQuery(this).hasClass('isReverse')) {
                    status = status ? 0 : 1;
                }

                doAjax(0);
                function doAjax(doit) {
                    jQuery.ajax({
                        type: 'post',
                        dataType: 'json',
                        data: {
                            adminop: 'setStatus',
                            status: status,
                            field: field,
                            opid: opid,
                            doit: doit ? 1 : 0,
                            _csrf: _csrf,
                            inajax: 1,
                            _: Math.random()
                        },
                        success: function (data) {
                            hideIconLoader();
                            if (data) {
                                if (data.doit) {
                                    myApp.confirm('', data.message, function () {
                                        doAjax(1);
                                    });
                                } else {
                                    levtoast(data.message);
                                    if (parseInt(data.status) > 0) {
                                        //window.setTimeout(function () { window.location.reload(); }, 200)
                                    }
                                }
                            }
                        },
                        error: function (data) {
                            hideIconLoader();
                            errortips(data);
                        }
                    });
                }
            });

//字段修改 - 通用
            jQuery(document).on('change', '.setField', function (e) {
                var opid = jQuery(this).attr('opid');
                var field = jQuery(this).attr('name');
                var val = jQuery(this).val();

                doAjax(0);
                function doAjax(doit) {
                    jQuery.ajax({
                        url: window.location.href.split('#!/').pop(),
                        type: 'post',
                        dataType: 'json',
                        data: {
                            adminop: 'setField',
                            field: field,
                            val: val,
                            opid: opid,
                            doit: doit ? 1 : 0,
                            _csrf: _csrf,
                            inajax: 1,
                            _: Math.random()
                        },
                        success: function (data) {
                            hideIconLoader();
                            if (data) {
                                if (data.doit) {
                                    myApp.confirm(data.message, '', function () {
                                        doAjax(1);
                                    }, function () {
                                        levtoast('您取消了操作，刷新可恢复');
                                    });
                                } else {
                                    levtoast(data.message);
                                    if (parseInt(data.status) > 0) {
                                        data.reload &&
                                        window.setTimeout(function () { window.location.reload(); }, 1200);
                                    }
                                }
                            }
                        },
                        error: function (data) {
                            hideIconLoader();
                            errortips(data);
                        }
                    });
                }
            });

            Levme.onClick('.doDeleteDay', function () {//删除几天前数据
                var day = parseFloat(jQuery('form[name="deleteDay"] input[name=day]').val());
                if (isNaN(day)) {
                    levtoast('天数非法');
                    return false;
                }
                myApp.confirm('您确定要删除 <absx> '+ day +'天 </absx> 前的数据吗？', '', function () {
                    showIconLoader();
                    jQuery('form[name="deleteDay"]').ajaxSubmit({
                        type: 'post',
                        dataType: 'json',
                        data: {adminop:'deleteDay', _csrf:_csrf, inajax:1, _:Math.random()},
                        success: function (data) {
                            hideIconLoader();
                            levtoast(data.message);
                            if (data && parseInt(data.status) >0) {
                                window.setTimeout(function () { window.location.reload(); }, 200)
                            }
                        },
                        error: function (data) {
                            hideIconLoader();
                            errortips(data);
                        }
                    });
                });
            });

        }
    };

})();

//表单字段设置
(function () {
    'use strict';

    jQuery(function () {
        formForm.init();
        formForm.inputtypeChange();
    });

    var formForm = {
        init:function () {
            jQuery(document).on('change', '.setClassify', function () {
                window.location = changeUrlArg(jQuery(this).attr('url'), 'classify', this.value);
            });

            Levme.onClick('.openPP', function () {
                var src = levToRoute(jQuery(this).attr('href'));
                jQuery(this).attr('href', src);
                Levme.popupIframe.popupShow(this, src);
                //aToLoginScreenForce(this, src);
                return false;
            });

            jQuery(document).on('change', '.field-settingsmodel-inputtype select', function () {
                formForm.inputtypeChange();
            });


        },
        inputtypeChange:function () {
            var _val = jQuery('.field-settingsmodel-inputtype select').val();
            var hint = jQuery('.field-settingsmodel-inputtype select option:selected').attr('hint');
            jQuery('.field-settingsmodel-settings').show();
            jQuery('.field-settingsmodel-settings__tablesForm').hide();
            if (hint) {
                jQuery('.field-settingsmodel-settings .hint-block').html(Levme.decodeHTML(hint));
            }else if (_val === 'tables' || _val === 'navs' || _val === 'tablesnav' || _val === 'slides' || _val === 'tabletr') {
                jQuery('.field-settingsmodel-settings .hint-block').html(
                    '1. 一行一个字段；功能描述：可自由删减此项设置,类似数据表。' +
                    '<br>2. 建表每行格式：表字段名<b class=red>=</b>描述名称<b class=red>=</b>功能描述<b class=red>=</b>' +
                    '调用类(命名空间+类名，自动调用类下的字段名方法"field_字段名")<b class=red>=</b>输入框宽度；' +
                    '<br>3. 一行一条。其中等号【<b class=red>=</b>】是分隔符，各项参数不能包含【<b class=red>=</b>】' +
                    '<br>例：<br>name=名称' +
                    '<br>target=打开方式=描述=lev\\helpers\\SettingsHelper（自动调用SettingsHelper类下的<b class="red">settarget(即 set+inputname )</b>静态方法，返回数组[key=>name]或直接返回html）' +
                    '<br><br>注意：1、<b class=red>id</b>为保留字段且唯一并自动排序，也可对其进行设置（例：<tips>id=ID=int，int</tips>表示ID必须是大于0整数）；' +
                    '2、<b class=red>status</b>结尾的字段，为保留开关字段(0:开启,1:关闭)；' +
                    '<br>3、<b class=red>upload</b>结尾的字段，为保留上传字段；' +
                    '4、logo前缀支持@、#fa-icon自动格式化存入<b class=red>=logo</b>，link前缀自动补全存入新字段<b class=red>=link</b>；' +
                    '5、<b class=red>order</b>为排序字段，默认使用ID排序'
                );
            }else if (_val === 'buttons') {
                jQuery('.field-settingsmodel-settings .hint-block').html(
                    '<tips>{homeUrl}</tips>代表首页地址。以"/"结尾<br>' +
                    '<br>格式：链接名称==链接地址==铵钮颜色(red,black,green,gray)==新页打开(_blank)；其中双等号【==】是分隔符。一行一条');
            }else if (_val === 'selectcode' || _val === 'selectscode' || _val === 'selectSrhcode' || _val === 'selectSearch') {
                jQuery('.field-settingsmodel-settings .hint-block').html(
                    '请设置要调用的类。<br>' +
                    '例：lev\\helpers\\SettingsHelper（自动调用SettingsHelper类下的<b class="red">set+inputname（例：settarget）静态</b>' +
                    '方法，返回数组[key=>name]）' +
                    '<br><tips>【主意】key值具有分类搜索功能，尽量避免与其它字段的key值重复</tips>'
                );
            }else if (_val === 'select' || _val === 'selects') {
                jQuery('.field-settingsmodel-settings .hint-block').html(
                    '一行一条，格式：key=name；等号【=】是分隔符。例：<br>1=小号<br>2=大号' +
                    '<br><tips>【主意】key值具有分类搜索功能，尽量避免与其它字段的key值重复</tips>'
                );
            }else if (_val === 'uploadimg') {
                jQuery('.field-settingsmodel-settings .hint-block').html(
                    '格式：<tips>5M以内|jpg,png,gif|300宽x300高</tips>；其中竖线【|】英文字母【x】英文逗号【,】是分隔符。'
                    +'<br><br><tips>【注意】上传大小仅支持【M】单位，否则一律按【KB】计算</tips>' +
                    '<br><br>【主意】大文件可能上传失败，这与服务器配置有关，插件并没有问题'
                );
            }else if (_val === 'uploadattach') {
                jQuery('.field-settingsmodel-settings .hint-block').html(
                    '格式：<tips>30M以内|zip,rar,txt</tips>；其中竖线【|】英文逗号【,】是分隔符。'
                    +'<br><br><tips>【注意】上传大小仅支持【M】单位，否则一律按【KB】计算</tips>' +
                    '<br><br>【主意】大文件可能上传失败，这与服务器配置有关，插件并没有问题'
                );
            }else if (_val === 'usescore') {
                jQuery('.field-settingsmodel-settings .hint-block').html(
                    '格式：积分ID=数量；其中等号【=】是分隔符。例：2=100，表示信息发表成功奖励100金钱；-100就是消耗100金钱'
                );
            }else if (_val === 'viewscore') {
                jQuery('.field-settingsmodel-settings .hint-block').html(
                    '格式：积分ID=数量；其中等号【=】是分隔符。例：2=100，表示阅读信息需支付100金钱；-100就是奖励100金钱'
                );
            }else if (_val === 'tablesForm' || _val === 'tabletrForm' || _val === 'tableSubnavForm') {
                jQuery('.field-settingsmodel-settings .hint-block').html(
                    ''
                );
                jQuery('.field-settingsmodel-settings').hide();
                jQuery('.field-settingsmodel-settings__tablesForm').show();
            }else if (jQuery('.field-settingsmodel-inputtype select').length >0){
                jQuery('.field-settingsmodel-settings').hide();
            }
        }
    };

})();