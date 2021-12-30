<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-06 21:17
 *
 * 项目：upload  -  $  - run.php
 *
 * 作者：liwei 
 */
?>


<div class="<?php echo $screenId?>"><div class="card">
    <div style="width:100%;min-height:<?php echo $height?>px;background: gray;display: flex;justify-content: center;align-items: center;"><div class="preloader preloader-white"></div></div>
    <script>
        (function () {
            'use strict';

            jQuery(function () {
                loadPm.init();
            });

            var loadPm = {
                init:function () {

                    window.setTimeout(function () {
                        loadPm.data('<?php echo $loadUrl?>');
                    }, <?php echo $timeout?>);

                },
                data:function (loadUrl) {
                    jQuery.ajax({
                        url: loadUrl,
                        data:{formhash:_csrf, _csrf:_csrf},
                        dataType:'<?php echo $jsonp ? 'jsonp' : 'json' ?>',
                        type:'get',
                        success:function (data) {
                            data && jQuery('.<?php echo $screenId?>').html(data.data);
                        },
                        error:function (data) {
                            //errortips(data);
                        }
                    });
                }
            };

        })();
    </script>
</div>
</div>


