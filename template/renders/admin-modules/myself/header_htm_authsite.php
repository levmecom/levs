<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-24 08:23
 *
 * 项目：rm  -  $  - header_htm_authsite.php
 *
 * 作者：liwei 
 */

use lev\helpers\UrlHelper;
?>

<?php if (!empty($authSiteUrl)):?>
<script>
jQuery(function () {
    Levme.showNotices('正在授权站点，请稍候...', 0, 100000);
    window.setTimeout(function () {
        Levme.ajaxv.getv('<?=$authSiteUrl?>', function (data, status) {
            Levme.showNoticesAdd('<div class="inblk font12 scale9 transl">'+ data.message +'</div>');
            window.setTimeout(function () {
                window.location = changeUrlArgs('<?=UrlHelper::adminModules()?>', {disableBack:1});
            }, 1500);
        }, {inajax:1,disableBack:1});
    }, 1000);
});
</script>
<?php endif;?>
