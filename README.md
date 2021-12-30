
##

安装
-------------------

1. 在`composer.json`文件中加入如下代码
~~~
{
  "require": {
    "php": ">=5.4.0",
    "levmecom/levs": "*"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "https://gitee.com/levmecom/levs"
    }
  ]
}
~~~
2.在`composer.josn`文件目录，执行cmd命令： 

~~~
composer create-project
~~~

或者更新命令：

~~~
composer update
~~~

安装方式2：
-------------------

此方式可能安装不成功

~~~
composer create-project levmecom/levs levs
~~~

如果需要更新使用

~~~
composer update levmecom/levs
~~~

Discuz! 对接：
-------------------
    
    1.将解压后的levs目录上传到DZ网站的source/plugin目录 
    2.dz后台 -> 插件 -> 安装即可使用

更新
-------------------

2021-12-19 更新

    增加用户名修改。
    增加密码修改。

2021-12-18 更新

    增加默认头像供用户更换。
    默认头像放在levs/web/data/avatars/0/*.jpg
    设置后未设置头像用户根据UID调用不同头像

2021-12-08 更新

    增加ICP备案号设置项，设置路径 levs -> 公共设置 -> Icp备案号
    更新后需要重新设置备案号

2021-12-05 更新

    增加登陆用户管理
    增加欢迎界面关闭开关，默认关闭
    修复欢迎界面读秒BUG
    增加设置默认首页，设置首页同样具备显示欢迎界面功能
    实现独立运行，可独立安装Lev系统

2021-11-25 更新

    更新子模块【IP禁止】
    增加记录访问IP并统计功能，详细查看子模块介绍
    IP记录写入文件，不降低网站性能
    适合高并发，大访问量网站。

2021-11-23 更新

    优化VIP模块公共调用
    新模块【福彩双色球选号评估】上线支持

20211026.31 更新

    新版本发布

20210730.14 更新

    增加PC端强制扫码访问手机端

20210703更新

    1.修复js报错
    2.“我的”页面增加app下载链接，新版本提示。安装【手机本号一键登陆】后可见

20210702更新

    1.修复更新后重置设置项问题
    2.修复提示文件缺失问题

20210701更新

    1.增加全站二维码调用
    2.二维码可在后台【全局设置】中设置二维内容、显示位置
    3.修复关闭登陆界面后，通过导航顶部直接提交登陆、js报错不跳转问题
    4.基础功能完善、优化，其它

20210629更新

    1.增加关闭论坛登陆功能
    2.增加登陆方式设置
    3.增加两个静态资源文件提供给前置版调用
    4.基础功能完善、优化，其它

概述
-------------------

    本插件可以单独使用，我们将不定期为此款插件更新常用功能，详细功能请见功能介绍以及更新记录。
    本插件为主程序，将不断更新、完善功能。所有爱路一维插件都需要安装此款插件作为前替
    本插件主要实现一些常用和必不可少的一些功能，如登陆、注册、导航等。

功能介绍
-------------------
   
    1. 进入插件主页面显示仿APP启动时的欢迎图界面，读秒完成自动跳过
    2. 插件自带登陆、注册、个人中心，与论坛会员互通。
    3. 插件首页内容显示图标导航链接，界面样式如下图所示
    4. 后台可自定义首页图标导航，包括推荐、排序、图标上传、图标名称等
    5. 后台可自定义欢迎界面的图片、描述、读秒时间、读秒完成跳转地址、显示间隔时长等
    6. 后台 -> 全局导航 可自定义首页幻灯片、底部浮动工具栏、页脚图标联系方式。
    7. 后台 -> 登陆设置 可开关本插件的登陆、注册功能以及设置论坛的登陆、注册地址。
    8. 论坛关闭注册功能不影响本插件注册，可以有效防止机器注册
    9. 注册界面配备了全套的用户协议、隐私政策、法律声明、免责声明，其内容基本可以通用。

设计原理
-------------------
    每个APP即可以独立运行，也可以与其它APP同时运行。
    多个APP可以只有一套lev程序，也可以是每个APP都有一套lev程序，
    多个lev程序彼此不冲突，通过定义LEVROOT调用不同APP下的lev程序。
    web目录为APP唯一入口。
    
路由机制
-------------------

    通用：
    通过参数r和已定义的常量MODULEIDEN判断调用的控制器
    在web目录下以MODULEIDEN方式建立模块路由器
    主应用：web/index.php?r=controllerName/actionName
    子应用：web/levroom/index.php?r=controllerName/actionName

    discuz路由机制：
        主应用：/plugin.php?id=levroom&r=controllerName/actionName
        子应用：/plugin.php?id=levroom:iden&r=controllerName/actionName
        
        


程序使用
-------------------
    方法一：一个APP一套lev程序，其它APP以模块模块形式存在。
    目录结构：
            MODULEIDEN/  APP主目录且为APP标识符（它也是一个模块）
                assets/                 存放APP静态文件     @appassets
                controlles/             控制器 目录别名：    @controlles
                lev/                    共用开发程序包       @lev
                migrations/             安装文件、安装数据 
                web/                    网站根目录。        @webroot
                views/                  模板文件、布局文件   @views
                models/                 数据模型            @models
                helpers/                通用助手类           @modules
                modules/                子应用存放目录
                widgets/                小部件目录

    方法二：discuz插件，
        问题：由于dz多入口原因 不能设置web为网站根目录，且可访问任意目录
            由于dz每个插件都是独立的，因此dz插件等于一个APP。
            但在开发中使用一套lev程序即可以方便兼容yii等框架使用。
        解决方案：
            实现功能一：即可多入口也可单入口，两者并存。
            实现方法：定义一个主插件，基于插件以子应用形式开发，
            子应用必须可以很方便的独立运行且只须简单配置。
            子应用运行原理：包含任意的lev程序即可运行。
            子应用独立运行方法：加入lev程序目录即为主应用。
            子应用入口文件配置：
                1.定义常量LEVROOT，
                2.主应用目录APPVROOT，
                3. include APPVROOT . '/web/index.php' 即可
                4.子应用不存在web目录和lev目录

