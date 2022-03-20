<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-06-09 15:10
 *
 * 项目：rm  -  $  - my.php
 *
 * 作者：liwei 
 */

?>

<div class="page">

    <div class="page-content appbg">
        <div class="page-content-inner">
            <div class="list-block card">
                <ul>
                    <?php foreach ($scores as $v):?>
                    <li class="item-content">
                        <div class="item-media">
                            <?=\lev\helpers\ScoreHelper::svgScoreIcon($v['id'])?>
                        </div>
                        <div class="item-inner">
                            <div class="item-title scale8"><?php echo $v['title']?></div>
                            <div class="item-after"><span class="color-red"><?php echo $v['score']?></span></div>
                        </div>
                    </li>
                    <?php endforeach;?>
                    <?php if (\lev\helpers\ModulesHelper::isOpenModule('levyy')):?>
                    <li>
                        <a class="item-content item-link" href="<?=Lev::toReWrRoute(['log', 'id'=>'levyy'])?>">
                            <div class="item-media">
                                <svg class="icon"><use xlink:href="#fa-list"></use></svg>
                            </div>
                            <div class="item-inner">
                                <div class="item-title scale8">积分记录</div>
                                <div class="item-after"></div>
                            </div>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (\lev\helpers\ModulesHelper::isOpenModule('levpays')):?>
                    <li class="item-content">
                        <div class="item-inner">
                            <div class="item-title scale8">
                                <a class="button-fill button color-yellow scale9 is_ajax_a" href="<?=\lev\helpers\UrlHelper::pay()?>">
                                    <svg class="icon"><use xlink:href="#fa-wxpay"></use></svg>
                                    充值积分
                                </a>
                            </div>
                            <div class="item-after scale8">
                                <a class="button-fill button color-yellow scale9 is_ajax_a" href="<?=\lev\helpers\UrlHelper::trade()?>">
                                    <svg class="icon"><use xlink:href="#fa-cz"></use></svg>
                                    我的订单
                                </a>
                            </div>
                        </div>
                    </li>
                    <?php endif;?>
                </ul>
            </div>
            <?php if (\lev\widgets\login\loginWidget::otherLogin()):?>
            <div class="list-block card">
                <ul>
                    <li>
                        <a class="item-content item-link" href="<?=\lev\helpers\UrlHelper::toModule('levmb')?>">
                            <div class="item-media icon-img">
                                <svg class="icon"><use xlink:href="#fa-mmsg"></use></svg>
                            </div>
                            <div class="item-inner">
                                <div class="item-title scale8 transl">我的授权</div>
                                <div class="item-after date transr">手机短信、QQ等登陆</div>
                            </div>
                        </a>
                    </li>
                    <?php if ($apkInfo = \lev\widgets\login\loginWidget::getApk()):?>
                    <li class="apkDownBox">
                        <a class="item-content item-link" target="_blank" _bk=1 href="<?php echo $apkInfo['upload']?>">
                            <div class="item-media icon-img">
                                <?php echo $apkInfo['=logoupload']?>
                            </div>
                            <div class="item-inner">
                                <div class="item-title scale8 transl">Android APP下载</div>
                                <div class="item-after date transr">v<?php echo $apkInfo['version']?></div>
                                <div class="item-after date transr">
                                    <?php echo $apkInfo['desc']?>
                                    <cir v="<?php echo $apkInfo['version']?>" class="inblk scale9 bg-red v_dofade" style="position: absolute;top: -5px;right: -20px;z-index: 9999999999;height: 10px;border-radius: 50%;width: 10px !important;border: 1px solid #fbf85d;display:none"></cir>
                                </div>
                            </div>
                        </a>
                    </li>
                    <?php endif;?>
                </ul>
            </div>
            <?php endif;?>
            <div class="list-block card">
                <ul>
                <?php if (Lev::$app['uid'] <1):?>
                <li>
                    <a class="item-content openLoginBtn item-link">
                        <div class="item-media">
                            <svg class="icon"><use xlink:href="#fa-user"></use></svg>
                        </div>
                        <div class="item-inner">
                            <div class="item-title scale8">登陆</div>
                            <div class="item-after date"></div>
                        </div>
                    </a>
                </li>
                <?php else:?>
                    <li class="item-content item-link">
                        <div class="item-media">
                            <a class="is_ajax_a" href="<?=Lev::toReWrRoute(['upload/set-avatar'])?>">
                            <img src="<?php echo \lev\helpers\UserHelper::avatar()?>" style="border-radius:50%;width:25px;height:25px">
                            </a>
                        </div>
                        <div class="item-inner">
                            <?php if (!Lev::stget('openEditUsername', 'levs')):?>
                            <a class="item-title scale8 editField transl color-black" href="<?=Lev::toReRoute(['login/edit-username'])?>" title="<?=\lev\widgets\login\loginWidget::editUsernameTips()?>" opname="username" opval="<?=Lev::$app['username']?>">
                                <svg class="icon"><use xlink:href="#fa-compose"></use></svg>
                                <username style="font-size:16px"><?php echo Lev::$app['username']?></username>
                                <inputs class="hiddenx">
                                    <input type="password" name="pwd" value="" placeholder="请输入密码">
                                </inputs>
                            </a>
                            <?php else:?>
                                <a class="item-title scale8 transl color-black"><?=Lev::$app['username']?></a>
                            <?php endif;?>
                            <a class="item-after date exitLoginOutBtn">退出</a>
                        </div>
                    </li>
                    <li class="item-content item-link">
                        <div class="item-media">
                            <svg class="icon"><use xlink:href="#fa-lock"></use></svg>
                        </div>
                        <div class="item-inner">
                            <a class="item-title scale8 editField transl color-black" href="<?=Lev::toReRoute(['login/edit-password'])?>" title="请输入新密码" opname="newpwd" opval="">
                                修改密码
                                <inputs class="hiddenx">
                                    <input type="password" name="pwd" value="" placeholder="请输入旧密码，未设置请留空">
                                </inputs>
                            </a>
                            <a class="item-after date" onclick="Levme.showNotices('忘记密码，请通过绑定三方登陆找回')">忘记密码?</a>
                        </div>
                    </li>
                <?php endif;?>
                </ul>
            </div>
            <?php if (\lev\helpers\ModulesHelper::isOpenModule('levbdu')):?>
            <a class="list-block card flex-box ju-sa button-fill button color-gray is_ajax_a" href="<?=\lev\helpers\UrlHelper::toModule('levbdu')?>">切换账号</a>
            <?php endif;?>
        </div>
    </div>

    <?php Lev::navbar();Lev::toolbar();?>
</div>



<?php if (Lev::$app['uid'] <1):?>
<script>
    jQuery(function () {
        window.setTimeout(function () {
            openLoginScreen();//Levme.loginv.win();
        }, 400);
    });
</script>
<?php endif;?>
