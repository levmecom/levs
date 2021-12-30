<?php if ($CnzzJs = implode('', Lev::$app['CnzzJs'])):?>
<div class="CnzzJSBOX00" style="display: none !important;">
    <div class="CnzzJSBOX00-Box"><?=Lev::decodeHtml($CnzzJs, false)?></div>
    <script>
        window.setTimeout(function () {
            jQuery('.CnzzJSBOX00').html(Levme.decodeHTML(jQuery('.CnzzJSBOX00-Box').html())).removeAttr('class');
        }, 2000+levrandom(100, 1000));
    </script>
</div>
<?php endif;?>