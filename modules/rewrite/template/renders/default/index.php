<?php

$soft = Lev::arrv('SERVER_SOFTWARE', $_SERVER, '');
?>

<div class="page">

    <style>
        .navbar .buttons-row a {min-width:88px}
        .tabs-animated-wrap {width:99%;margin:55px auto;height: calc(100% - 75px);}
        .tabs-animated-wrap p img {max-width: 600px;margin:15px auto;}
        .tabs-animated-wrap .page-content {background: rgba(0,0,0,0.7);color:#fff;}
        .tabs-animated-wrap .page-content .page-content-inner {max-width: 960px;}
        .tabs-animated-wrap .page-content img {max-width: 100%;}
    </style>

    <div class="navbar">
        <div class="navbar-inner">
            <div class="center">
                <div class="buttons-row scale9">
                    <a href="#tab-nginx" class="tab-link active button is_ajax_a color-black">Nginx</a>
                    <a href="#tab-apache" class="tab-link button is_ajax_a color-black">Apache</a>
                    <a href="#tab-iis" class="tab-link button is_ajax_a color-black">IIS</a>
                    <a href="#tab-other" class="tab-link button is_ajax_a color-black">其它</a>
                </div>
            </div>
            <div class="right" <?=Lev::GPv('install') ?'style="display:none"':''?>>
                <a class="button button-fill scale9" href="<?=Lev::toReRoute(['default/settings', 'id'=>'rewrite'])?>">启用伪静态</a>
                <a class="button button-fill scale9 color-gray" href="<?=Lev::toReRoute(['default/settings', 'id'=>'rewrite', 'close'=>1])?>">关闭伪静态</a>
            </div>
        </div>
        <div class="subnavbar">
            <div class="subnavbar-inner" style="font-size: 12px;text-align: center;max-width: 100%;overflow: hidden;">
                <span style="white-space: nowrap;display: inline-block;text-overflow: ellipsis;overflow: hidden;max-width: 100%;" title="<?=$soft?>">服务器检测结果：<?=$soft?></span><br>
                <tips>请根据服务器类型设置相关伪静态规则</tips>
            </div>
        </div>
    </div>

    <div class="tabs-animated-wrap">
        <div class="tabs">
            <div id="tab-nginx" class="page-content tab active content-block ck-content"><div class="page-content-inner">
                <p> Nginx伪静态代码（将以下内容加入伪静态配置文件即可）：
                    <b class="color-yellow">【重要】请将网站目录定位到web，否则只能访问首页</b>
                </p>

                <pre>
<code class="language-nginx"><?=file_get_contents(\modules\levs\modules\rewrite\rewriteHelper::ngnixRewriteFile())?></code></pre>

                <p>Nginx完整配置文件（配置文件因人而异不一定完全相同）：</p>

                <pre>
<code class="language-nginx">server {
    charset utf-8;
    client_max_body_size 128M;

    listen 80; ## listen for ipv4
    #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

    server_name mysite.local;
    root        /path/to/basic/web;
    index       index.php;

    access_log  /path/to/basic/log/access.log;
    error_log   /path/to/basic/log/error.log;

    location / {
        # Redirect everything that isn't a real file to levs_rewrite.php
        # 将所有非真实文件重定向到levs_rewrite.php
        try_files $uri $uri/ /levs_rewrite.php?$args;
    }

    # uncomment to avoid processing of calls to non-existing static files by Yii
    #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
    #    try_files $uri =404;
    #}
    #error_page 404 /404.html;

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/$fastcgi_script_name;
        fastcgi_pass   127.0.0.1:9000;
        #fastcgi_pass unix:/var/run/php5-fpm.sock;
        try_files $uri =404;
    }

    location ~ /\.(ht|svn|git) {
        deny all;
    }
}</code></pre>

                <p> </p>
                </div>
            </div>
            <div id="tab-apache" class="page-content tab content-block ck-content"><div class="page-content-inner">
                <p>1. Apache伪静态代码：</p>

                <pre><code class="language-nginx"><?=file_get_contents(\modules\levs\modules\rewrite\rewriteHelper::apacheRewriteFile())?></code></pre>

                <p>Apache伪静态一般启用后直接可用，若不可用按下面提示操作</p>
                <p>提示1：如果之前有设置过其它伪静态规则，请手动添加</p>
                <p>提示2：Apache配置文件中直接将 AllowOverride None 改为 AllowOverride All</p>
            </div>
            </div>
            <div id="tab-iis" class="page-content tab content-block ck-content"><div class="page-content-inner">
                <p> IIS伪静态代码：</p>
                <pre style="font-size: 12px;"><code class="language-nginx"><?=htmlspecialchars(file_get_contents(\modules\levs\modules\rewrite\rewriteHelper::iisRewriteFile()))?></code>
                </pre>
                <p>提示：直接使用IIS管理器导入/source/plugin/levs/modules/rewrite/migrations/conf/.htaccess 文件即可，导入方式，如下图：</p>
                <p>第一步：</p>
                <p><img src="<?=$assetsBaseUrl?>/img/1.png"></p>
                <p>第二步：</p>
                <p><img src="<?=$assetsBaseUrl?>/img/2.png"></p>
                <p>第三步：</p>
                <p><img src="<?=$assetsBaseUrl?>/img/3.png"></p>
            </div>
            </div>
            <div id="tab-other" class="page-content tab content-block ck-content"><div class="page-content-inner">
                <p>其它伪静态代码请联系我们！</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(function () {
        <?php if (stripos($soft, 'apache') !== false) :?>
        myApp.showTab('#tab-apache');
        <?php elseif (stripos($soft, 'nginx') !== false) :?>
        myApp.showTab('#tab-nginx');
        <?php elseif (stripos($soft, 'iis') !== false) :?>
        myApp.showTab('#tab-iis');
        <?php else:?>
        myApp.showTab('#tab-other');
        <?php endif;?>
    });
</script>