<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-13 12:37
 *
 * 项目：upload  -  $  - settings.php
 *
 * 作者：liwei 
 */

?>

<div class="page page-admin page-formb">
    <div class="navbar page-admin-navbar adminbar navbar-bgcolor-red">
        <div class="navbar-inner">
            <div class="left transl" style="transform: scale(.97)">
                <?=\lev\widgets\adminModulesNav\adminModulesNav::buttonHtm()?>
                <a class="link tooltip-init" href="javascript:window.history.back();" data-tooltip="后退">
                    <svg class="icon" aria-hidden="true"><use xlink:href="#fa-back"></use></svg>
                </a>
                <a class="link tooltip-init" href="javascript:window.location.reload();" data-tooltip="刷新">
                    <svg class="icon" aria-hidden="true"><use xlink:href="#fa-refresh"></use></svg>
                </a>
                <a class="link">
                <label for="dosubmit">
                    <svg class="icon" aria-hidden="true"><use xlink:href="#fa-save"></use></svg>
                </label>
                </a>
            </div>
            <div class="title">
                <?php echo Lev::$app['title']?>
                <tips class="date" style="color:red !important;"></tips>
            </div>
            <div class="right">
                <a target="_blank" class="button button-fill color-red scale8 transr" href="<?php echo Lev::toReRoute(['superman/set-caches', 'id'=>APPVIDEN])?>">
                    更新缓存
                </a>
                <?php echo $sltOption?>
            </div>
        </div>

        <div class="subnavbar">
            <div class="buttons-row scale8 transl">
                <?php echo \lev\helpers\ModulesHelper::getAdminSubnavHtms()?>
            </div>
        </div>
    </div>

    <?php Lev::toolbarAdmin(1);?>

    <div class="page-content">

        <?=Lev::actionObjectMethodSettingsHeaderHtm($iden)?>

        <div class="form-mainb">
        <form id="saveSettings" class="card" action="" method="post">

            <?php echo \lev\widgets\inputs\inputsWidget::settingsForm($inputs)?>

            <?=Lev::actionObjectMethodSettingsFormFooterHtm($iden)?>

            <div class="card-footer">
                <div class="flex-box">
                <button type="submit" id="dosubmit" class="button-fill button wd100 doSaveSettingsBtn">
                    <svg class="icon" aria-hidden="true"><use xlink:href="#fa-save"></use></svg>
                    保 存
                </button>
                    <tips>【提示】设置的改动，点击保存后才会生效</tips>
                </div>
            </div>
        </form>
        </div>

        <?=Lev::actionObjectMethodSettingsFooterHtm($iden)?>

    </div>

</div>

<?php echo \lev\base\Assetsv::ajaxFormJs(1);?>
<script>
(function () {
    'use strict';

    jQuery(function () {
        settingsForm.init();
    });

    var settingsForm = {
        init:function () {
            jQuery(document).on('change', '.setClassify', function () {
                window.location = changeUrlArg(jQuery(this).attr('url'), 'classify', this.value);
            });
            Levme.onClick('.doSaveSettingsBtn', function () {
                return settingsForm.doSaveSettings();
            });

            Levme.onClick('.openPP', function () {
                var src = levToRoute(jQuery(this).attr('href'));
                aToLoginScreenForce(this, src);
                return false;
            });

        },
        doSaveSettings:function () {
            var form = 'form#saveSettings';
            jQuery(form +' input, '+ form +' textarea').each(function () {
                this.value && this.name &&
                this.name.indexOf('html') >=0 &&
                Levme.checkXssCode(this.value) &&
                (this.value = Levme.encodeHTML(this.value));
            });
            showIconLoader(true);
            jQuery(form).ajaxSubmit({
                url: '',
                data: {dosubmit:1, _csrf:_csrf, inajax:1, _:Math.random()},
                type:'post',
                dataType: 'json',
                success: function(data){
                    hideIconLoader();
                    if (parseInt(data.status) >0) {
                        levtoast(data.message);
                        window.setTimeout(function () {
                            window.location = window.location;
                        }, 1200);
                    }else if (data && data.message) {
                        levtoast(data.message, 15000);
                    }
                    showFormErrors(data.errors);
                },
                error: function(data) {
                    hideIconLoader();
                    errortips(data);
                }
            });
            return false;
        }
    };

})();
</script>

