<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-06-08 12:20
 *
 * 项目：rm  -  $  - login_screen.php
 *
 * 作者：liwei 
 */

echo \lev\base\Assetsv::ajaxFormJs(1);

\lev\widgets\login\loginWidget::openRegister() && include __DIR__ . '/register_screen.php';
?>

<!-- 登陆窗口开始 -->
<div class="popup my-login-screen login_screen">
    <style>
        .env-quick-login .button img {height: 16px;vertical-align: middle;}
    </style>

    <div class="view">
        <div class="page">
            <div class="navbar navbar-bgcolor-red">
                <div class="navbar-inner">
                    <div class="left"><a class="item-link openRegisterBtn">
                            <svg class="icon"><use xlink:href="#fa-reg"></use></svg>
                        </a></div>
                    <div class="title">用户登陆</div>
                    <div class="right">
                        <a class="item-link closePP">
                            <svg class="icon"><use xlink:href="#fa-closer"></use></svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="page-content login-screen-content">
                <div class="list-block virtual-list loginFormMb">
                    <ul style="padding-right: 15px;padding-bottom:50px">
                        <li class="item-content">
                            <div class="item-media">
                                <svg class="icon"><use xlink:href="#fa-user"></use></svg>
                            </div>
                            <div class="item-inner">
                                <div class="item-input">
                                    <input type="text" name="username" placeholder="请输入用户名">
                                </div>
                            </div>
                        </li>
                        <li class="item-content">
                            <div class="item-media">
                                <svg class="icon"><use xlink:href="#fa-lock"></use></svg>
                            </div>
                            <div class="item-inner">
                                <div class="item-input">
                                    <input type="password" name="password" placeholder="请输入密码">
                                </div>
                            </div>
                        </li>
                        <li class="item-content">
                            <div class="item-media">
                                <svg class="icon"><use xlink:href="#fa-safe"></use></svg>
                            </div>
                            <div class="item-inner">
                                <div class="item-input">
                                    <select class="scale7 gray transl" name="questionid" onchange="this.value > 0 ? jQuery('.answerItemB').removeClass('hiddenx') : jQuery('.answerItemB').addClass('hiddenx')">
                                        <option value="0">安全提问(未设置请忽略)</option>
                                        <option value="1">母亲的名字</option>
                                        <option value="2">爷爷的名字</option>
                                        <option value="3">父亲出生的城市</option>
                                        <option value="4">您其中一位老师的名字</option>
                                        <option value="5">您个人计算机的型号</option>
                                        <option value="6">您最喜欢的餐馆名称</option>
                                        <option value="7">驾驶执照最后四位数字</option>
                                    </select>
                                </div>
                            </div>
                        </li>
                        <li class="item-content answerItemB hiddenx">
                            <div class="item-media">
                                <svg class="icon"><use xlink:href="#fa-safe"></use></svg>
                            </div>
                            <div class="item-inner">
                                <div class="item-input">
                                    <input type="text" name="answer" placeholder="请输入安全问题答案">
                                </div>
                            </div>
                        </li>
                        <li class="item-content">
                            <div class="item-media">
                                <svg class="icon"><use xlink:href="#fa-yijian"></use></svg>
                            </div>
                            <div class="item-inner">
                                <div class="item-input">
                                    <label class="inblk" style="font-size:12px;color:#666;width:100%">
                                    <input type="checkbox" name="autologin" value="1" checked>自动登陆
                                    </label>
                                </div>
                            </div>
                        </li>
                        <li class="item-content useragreementBox">
                            <div class="item-media">
                                <svg class="icon"><use xlink:href="#fa-info"></use></svg>
                            </div>
                            <div class="item-inner">
                                <div class="item-input scale9" style="font-size:12px;color:#666">
                                    <label class="date">
                                        <input type="checkbox" name="useragreement" value="1" style="vertical-align: middle">
                                        我已阅读并同意
                                    </label>
                                    <a class="scale7 openPP" href="<?php echo \lev\helpers\UrlHelper::useragreement()?>">用户协议</a>
                                    和
                                    <a class="scale7 openPP" href="<?php echo \lev\helpers\UrlHelper::privacypolicy()?>">隐私政策</a>
                                </div>
                            </div>
                        </li>
                        <li class="item-content login-btnb">
                            <div class="item-inner" style="padding-right: 0;padding-top: 35px;">
                                <div class="item-input">
                                    <input type="button" name="dosubmit" value=" 登 陆 " class="button-fill button doLoginSubmit">
                                </div>
                            </div>
                        </li>
                        <li class="item-content login-btnb">
                            <div class="item-inner" style="padding-right: 0;padding-top:0;padding-bottom: 25px">
                                <?php if (\lev\widgets\login\loginWidget::openRegister()):?>
                                <div class="item-title">
                                    <a class="scale7 inblk transl openRegisterBtn">立即注册</a>
                                </div>
                                <?php elseif ($logu = \lev\widgets\login\loginWidget::registerUrl()):?>
                                <div class="item-title">
                                    <a class="date inblk transl openPP" href="<?php echo $logu[0]?>"><?php echo $logu[1]?></a>
                                </div>
                                <?php endif;?>
                                <div class="item-after">
                                    <a class="date inblk transr" onclick="levtoast('请联系客服找回')">密码找回</a>
                                </div>
                                <?php if ($logu = \lev\widgets\login\loginWidget::loginUrl()):?>
                                    <div class="item-after">
                                        <a class="date openPP inblk transr" href="<?php echo $logu[0]?>"><?php echo $logu[1]?></a>
                                    </div>
                                <?php endif;?>
                            </div>
                        </li>

                        <?php if (!empty($myphoneOpen)):?>
                        <li class="item-content hiddenx APPLoginBox">
                            <div class="item-inner" style="padding-right: 0;">
                                <div class="item-input">
                                    <input type="button" value=" 本机号码一键登陆 " class="button-fill button doAPPLoginSubmit" style="background: #ff3b30 !important;font-size:14px">
                                </div>
                            </div>
                        </li>
                        <?php endif;?>

                        <?php if (!empty($envBtn)):?>
                            <li class="item-content env-quick-login">
                                <div class="item-inner" style="padding-right: 0;">
                                    <div class="item-input">
                                        <a class="button-fill button openPP" href="<?=$envBtn['authUrl']?>" style="background:lightblue !important;font-size:14px">
                                            <?=$envBtn['_icon']?>&nbsp;<?=$envBtn['name']?>一键登陆
                                        </a>
                                    </div>
                                </div>
                            </li>
                        <?php endif;?>

                    </ul>
                </div>


            </div>

            <?php if (!empty($otherLoginHtm)):?>
            <div class="toolbar tabbar">
                <div class="date" style="justify-content: center;display: flex;position: absolute;bottom:50px;width: 100%;">
                    ------其它登陆方式------
                </div>
                <div class="toolbar-inner">
                    <?php echo $otherLoginHtm?>
                </div>
            </div>
            <?php endif;?>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    jQuery(function () {
        LoginScreenJs.initv();
    });

    var LoginScreenJs = {
        initv:function () {
            Levme.onClick('.doAPPLoginSubmit', function () {//APP登陆
                if (!LoginScreenJs.agreement(2)) return;
                APPmyPhoneLogin(true);
            });
            Levme.onClick('.doLoginSubmit', function () {//登陆
                if (!LoginScreenJs.agreement()) {
                    return;
                }
                doLoginSubmit();
            });
            Levme.onClick('.doRegisterSubmit', function () {//注册
                if (!LoginScreenJs.agreement(1)) return;
                doRegisterSubmit();
            });

            Levme.APP.ckAPP() && jQuery('.APPLoginBox').removeClass('hiddenx');
        },
        agreement:function (reg) {
            if (jQuery('input[name="useragreement"]:checked').length <1) {
                jQuery('.useragreementBox').addClass('shake animated');
                window.setTimeout(function () {
                    jQuery('.useragreementBox').removeClass('shake animated');
                }, 500);
                Levme.confirm('<span class="yellow">您必须阅读并同意用户协议和隐私政策</tips>', '您是否同意？', function () {
                    jQuery('input[name="useragreement"]').attr('checked', true);
                    showIconLoader(true);
                    window.setTimeout(function () {
                        reg ? (reg === 2 ? APPmyPhoneLogin(true) : doRegisterSubmit()) : doLoginSubmit();
                    }, 400);
                });
                return false;
            }
            return true;
        },
    };

    function doRegisterSubmit() {
        showIconLoader(true);
        Levme.ajaxv.dosubmit('.registerFormMb', levToRoute('login/register', {id:APPVIDEN}), function (data, status) {
            if (status > 0) {
                if (data.loginReferer) {
                    window.top.location = data.loginReferer;
                } else {
                    window.top.location = window.location.href.split('#!/')[0];
                }
            }
        });
    }

    function doLoginSubmit() {
        showIconLoader(true);
        Levme.ajaxv.dosubmit('.loginFormMb', levToRoute('login', {id: APPVIDEN}), function (data, status) {
            if (status > 0) {
                if (data.loginReferer) {
                    window.top.location = data.loginReferer;
                } else {
                    window.top.location = window.location.href.split('#!/')[0];
                }
            }
        });
    }
})();
</script>
