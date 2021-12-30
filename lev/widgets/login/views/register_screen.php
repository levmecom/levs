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
?>

<!-- 注册窗口开始 -->
<div class="popup my-register-screen login_screen">
    <div class="view">
        <div class="page">
            <div class="navbar navbar-bgcolor-red">
                <div class="navbar-inner">
                    <div class="left"><a class="item-link openLoginBtn">
                            <svg class="icon"><use xlink:href="#fa-back"></use></svg>
                        </a></div>
                    <div class="title">用户注册</div>
                    <div class="right">
                        <a class="item-link closePP">
                            <svg class="icon"><use xlink:href="#fa-closer"></use></svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="page-content login-screen-content">
                <div class="list-block virtual-list registerFormMb">
                    <ul style="padding-right: 15px;">
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
                                <svg class="icon"><use xlink:href="#fa-lock"></use></svg>
                            </div>
                            <div class="item-inner">
                                <div class="item-input">
                                    <input type="password" name="password2" placeholder="请再次输入密码">
                                </div>
                            </div>
                        </li>
                        <li class="item-content">
                            <div class="item-media">
                                <svg class="icon"><use xlink:href="#fa-email"></use></svg>
                            </div>
                            <div class="item-inner">
                                <div class="item-input">
                                    <input type="text" name="email" placeholder="请输入邮箱(Email)">
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
                                        <input type="checkbox" name="useragreement" value="1">
                                        我已阅读并同意
                                    </label>
                                    <a class="scale7 openPP" href="<?php echo \lev\helpers\UrlHelper::useragreement()?>">用户协议</a>
                                    和
                                    <a class="scale7 openPP" href="<?php echo \lev\helpers\UrlHelper::privacypolicy()?>">隐私政策</a>
                                </div>
                            </div>
                        </li>
                        <li class="item-content">
                            <div class="item-inner" style="padding-right: 0;padding-top: 35px;">
                                <div class="item-input">
                                    <input type="button" name="dosubmit" value=" 注 册 " class="button-fill button color-red doRegisterSubmit">
                                </div>
                            </div>
                        </li>
                        <li class="item-content">
                            <div class="item-inner" style="padding-right: 0;padding-top:0;padding-bottom: 50px">
                                <div class="item-title">
                                    <a class="scale7 inblk transl openLoginBtn">返回登陆</a>
                                </div>
                                <?php if ($logu = \lev\widgets\login\loginWidget::registerUrl()):?>
                                <div class="item-after">
                                    <a class="date openPP inblk transr" href="<?php echo $logu[0]?>"><?php echo $logu[1]?></a>
                                </div>
                                <?php endif;?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

