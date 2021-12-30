<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-09-20 11:07
 *
 * 项目：rm  -  $  - set_gate_ip_file.php
 *
 * 作者：liwei 
 */

?>


<div class="page page-admin">
    <?php Lev::toolbarAdmin(); Lev::navbarAdmin(0,0,0,0,'自动读取网站根目录与二级目录内.php后缀文件',1); ?>

    <div class="page-content">
        <div class="page-content-inner" style="max-width: 660px">
            <form action="" method="post">
                <input type="hidden" name="_csrf" value="<?=Lev::$app['_csrf']?>">
                <input type="hidden" name="dosubmit" value="1">
            <div class="list-block no-hairlines card" style="margin-top: 0;padding: 10px">
                <ul>
                    <?php foreach ($gateFiles as $_src):if (basename($_src) == $phpcodes['filename']) continue;?>
                    <?php $srcs = is_dir($_src) ? glob($_src.'/*') : [-1=>$_src];?>
                    <?php foreach ($srcs as $k=>$src): if (substr($src, -4) == '.php'):?>
                    <li>
                        <label class="label-checkbox item-content">
                            <input type="checkbox" name="addGateFiles[]" value="<?=$basename = ($src);?>" <?= $k == -1 ? 'checked' : ''?>>
                            <div class="item-media">
                                <i class="icon icon-form-checkbox"></i>
                            </div>
                            <div class="item-inner">
                                <div class="item-title"><?=$basename?></div>
                                <div class="item-after date">
                                    <?=\modules\levs\modules\ipban\ipbanHelper::checkSeted($src)? '已设置' : '-'?>
                                </div>
                            </div>
                        </label>
                    </li>
                    <?php endif;endforeach;endforeach;?>
                </ul>
                <div class="card-footer">
                    <button type="submit" id="dosubmit" class="button-fill button wd100">
                        <svg class="icon" aria-hidden="true"><use xlink:href="#fa-save"></use></svg>
                        保 存
                    </button>
                    <label><input type="checkbox" name="clearIpBan" value="1">清除IP<tips>勾选后清除文件中禁止代码</tips></label>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
