<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-14 22:38
 *
 * 项目：upload  -  $  - run.php
 *
 * 作者：liwei 
 */

?>

<div class="<?php echo $uploadInput?>Form inputsUploadx upload-formx" style="max-width:min-content;position: relative">
    <input type="text" style="min-width: 256px" class="srcInput" name="<?php echo $inputName?>" value="<?php echo $inputValue?>" placeholder="<?php echo $placeHolder?>">
    <label style="position: absolute;top: 1.5px;right: 0;">
        <absxk>上传</absxk>
        <input type="file" class="hiddenx <?php echo $uploadInput?>" name="<?php echo $uploadInput?>">
    </label>
    <div class="progressbar" style="background:none;border:0;padding: 0;margin: 0;position: absolute;"></div>
    <input type="hidden" class="_imgIds" name="imgids">
</div>

<?php if ($jsinit):?>
<div class="LoadPageAjaxJS">
<script>
    (function () {
        'use strict';

        jQuery(function () {
            uploadWidget.init();
        });

        var uploadWidget = {
            init:function () {
                var url = '<?php echo $uploadUrl?>';
                Levme.uploadImgForm(
                    '.<?php echo $uploadInput?>',
                    url,
                    '.<?php echo $uploadInput?>Form',
                    uploadWidget.uploadSuccess,
                    '.inputsUploadx'
                );
            },
            uploadSuccess:function (data, formMain) {
                data && data.src && jQuery(formMain).find('.srcInput').eq(0).val(data.src);
            },
        };
    })();
</script>
</div>
<?php endif;?>