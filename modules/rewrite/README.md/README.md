
2021-12-06 更新 （重要）
    修改开关按钮，不再默认开启rewrite。
    本次更新后需要在后台重新开启rewrite

2021-11-26 更新
    增加自定义URL地址，可自定义提交sitemap地址

自动美化discuz插件URL、lev模块URL；提升SEO
支持百度搜索引擎API提交sitemap
支持生成百度sitemap.xml
支持Discuz!论坛帖子生成xml以及百度API提交

APACHE 伪静态规则

    RewriteEngine on
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule . levs_rewrite.php [L]

NGNIX 伪静态规则

    location / {
        # Redirect everything that isn't a real file to levs_rewrite.php
        # 将所有非真实文件重定向到levs_rewrite.php
        try_files $uri $uri/ /levs_rewrite.php?$args;
    }
    
IIS 伪静态规则

    <configuration>
        <system.webServer>
            <rewrite>
                <rules>
                    <rule name="已导入的规则 1" stopProcessing="true">
                        <match url="." ignoreCase="false" />
                        <conditions logicalGrouping="MatchAll">
                            <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                            <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                        </conditions>
                        <action type="Rewrite" url="levs_rewrite.php" />
                    </rule>
                </rules>
            </rewrite>
        </system.webServer>
    </configuration>
    
    