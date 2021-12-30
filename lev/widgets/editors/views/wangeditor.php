<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-21 13:38
 *
 * 项目：upload  -  $  - wangeditor.php
 *
 * 作者：liwei 
 */

echo \lev\base\Assetsv::highlight(1),
    \lev\base\Assetsv::registerJsFile('@assets/statics/editor/wangEditor.min.js', 1);
?>

<div id="wangEditorMain<?php echo $input['inputname']?>" class="noActiveState"></div>
<textarea class="hiddenx wangEditorTextarea<?php echo $input['inputname']?>" name="<?php echo $inputname?>"><?php echo $inputvalue?></textarea>

<!-- 引入 wangEditor.min.js -->
<script type="text/javascript">
    (function () {
        'use strict';

        jQuery(function () {
            wangEditor.init();
        });

        var wangEditor = {
            init:function () {
                var textareaCls = 'textarea.wangEditorTextarea<?php echo $input['inputname']?>';

                const E = window.wangEditor;
                const editor = new E('#wangEditorMain<?php echo $input['inputname']?>');
                // 或者 const editor = new E( document.getElementById('div1') );

                editor.config.height = <?php echo floatval($editorHeight)?>;
                // 配置菜单栏，设置不需要的菜单
                editor.config.excludeMenus = [
                    'emoticon',
                    'video'
                ]


                //图片上传接口
                editor.config.uploadImgServer = '<?php echo Lev::toCurrent(['r'=>'upload/image', 'input'=>'editor', '_csrf'=>Lev::$app['_csrf'], 'iden'=>Lev::$app['iden'], 'identifier'=>APPVIDEN, 'id'=>APPVIDEN, 'inajax'=>1])?>';
                editor.config.uploadFileName = 'editor';
                editor.config.uploadImgMaxLength = 1;// 一次最多上传 1 个图片
                editor.config.uploadImgMaxSize = 5 * 1024 * 1024; // 5M
                editor.config.uploadImgHooks = {
                    // 图片上传并返回了结果，想要自己把图片插入到编辑器中
                    // 例如服务器端返回的不是 { errno: 0, data: [...] } 这种格式，可使用 customInsert
                    customInsert: function(insertImgFn, result) {
                        // result 即服务端返回的接口
                        //console.log('customInsert', result)

                        // insertImgFn 可把图片插入到编辑器，传入图片 src ，执行函数即可
                        result && result.realSrc && insertImgFn(result.realSrc);
                    }
                }


                // 挂载highlight插件
                typeof hljs !== "undefined" && (editor.highlight = hljs);

                editor.config.placeholder = '<?php echo $input['placeholder']?>';

                editor.create();

                //初始化 编辑器 内容
                editor.txt.html(Levme.decodeHTML(jQuery(textareaCls).val()));

                editor.config.onchange = function (html) {
                    jQuery(textareaCls).val(Levme.encodeHTML(html));
                }

            }
        };
    })();
</script>

