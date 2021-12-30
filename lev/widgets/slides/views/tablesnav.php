<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-18 23:52
 *
 * 项目：upload  -  $  - tablesnav.php
 *
 * 作者：liwei 
 */

?>

<?php if ($inputtype == 'navs'):?>
    <div class="card-footer nv-abbox">
        <?php foreach ($navs as $cv):?>
            <a class="button button-fill <?=$cv['_link'] ? $cv['_target'].$cv['_link'] : ''?>"><?=$cv['name']?></a>
        <?php endforeach;?>
    </div>
<?php else:?>
<?php foreach ($navs as $v):?>
    <div class="card">
        <div class="card-header minheader">
            <div class="hd-lf">
                <a class="<?=$v['_link'] ? $v['_target'].$v['_link'] : ''?>">
                    <iconi><?=$v['_icon']?></iconi>
                    <?=$v['name']?>
                </a>
            </div>
        </div>
        <?php if (!empty($v['cld__'])): ?>
        <div class="card-footer nv-abbox">
            <?php foreach ($v['cld__'] as $cv):?>
                <a class="<?=$cv['_link'] ? $cv['_target'].$cv['_link'] : ''?>"><?=$cv['name']?></a>
            <?php endforeach;?>
        </div>
        <?php endif;?>
    </div>
<?php endforeach;?>
<?php endif;?>
