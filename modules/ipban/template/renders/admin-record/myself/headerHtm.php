<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-25 09:57
 *
 * 项目：rm  -  $  - headerHtm.php
 *
 * 作者：liwei 
 */

?>

<div class="card card-header">
    <div class="buttons-row scale8">
        <a class="button-fill button button-small ajaxBtn censusIpBtn" href="<?=Lev::toReRoute(['admin-record/census-ip'])?>">更新</a>
        <a class="button-fill button button-small ajaxBtn censusIpBtn color-blackg" href="<?=Lev::toReRoute(['admin-record/census-ip', 'h'=>24])?>">实时</a>
    </div>
    <div></div>
    <div></div>
</div>

<script>
    jQuery(function () {
        Levme.onClick('.censusIpBtn', function () {
            showIconLoader(true);
            Levme.showNotices('【提示】统计可能需要较长时间，请耐心等候');
        })
    });
</script>