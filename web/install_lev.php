<?php
/**
 * Copyright (c) 2021-2222   All rights reserved.
 *
 * 创建时间：2021-11-29 15:38
 *
 * 项目：levs  -  $  - install_lev.php
 *
 * 作者：liwei
 */

INI_SET('display_errors', 1);ERROR_REPORTING(E_ALL ^ E_NOTICE);//开发环境

defined('APPVROOT') or define('APPVROOT', dirname(__DIR__));

use lev\base\Requestv;

header("Content-type: text/html; charset=utf-8");//统一且固定使用utf-8编码 - 通过转码将数据库编码变得一致

$errCls = 'alert-danger';
$errMsg = [];
$btns = [];

$webroot = APPVROOT . '/web';
$levsroot = dirname($webroot);
$modulesroot = dirname($levsroot);

function checkDirWriteable($dir) {
    $writeable = 0;
    if(!is_dir($dir)) {
        @mkdir($dir, 0777);
    }
    if(is_dir($dir)) {
        if($fp = @fopen("$dir/test.txt", 'w')) {
            @fclose($fp);
            @unlink("$dir/test.txt");
            $writeable = 1;
        } else {
            $writeable = 0;
        }
    }
    return $writeable;
}

$configFile = $levsroot . '/runtime/config.php';
if (is_file($configFile)) {
    $data = include $configFile;
    if (!empty($data['db'])) {
        exit('系统已经安装，不能重复安装！安全起见请删除安装文件：'.basename(__FILE__));
    }
}elseif (is_file($levsroot . '/runtime/.install.lock')) {
    exit('系统已经安装，不能重复安装！');
}

if (!empty($_POST['dosubmit'])) {
    $dropTab = $_POST['dropTab'];

    $dbhost   = trim(strip_tags($_POST['dbhost'])) ?: 'localhost';
    $dbname   = trim(strip_tags($_POST['dbname'])) ?: 'lev_levs_app';
    $username = trim(strip_tags($_POST['username']));
    $password = trim(strip_tags($_POST['password']));
    $charset  = strtolower(trim(strip_tags($_POST['charset'])) ?: 'utf8mb4');
    $prefix   = trim(strip_tags($_POST['prefix'])) ?: 'pre_';

    $SiteName = trim(strip_tags($_POST['SiteName']));
    $siteuser = trim(strip_tags($_POST['siteuser'])) ?: 'admin';
    $sitepwd  = trim(strip_tags($_POST['sitepwd'])) ?: 'admin';

    if (!$username) {
        $errMsg[] = '数据库【用户名】不能为空';
    }elseif (!$password) {
        $errMsg[] = '数据库【密码】不能为空';
    }else {
        $configData['db'] = [
            'dsn'      => 'mysql:host=' . $dbhost . ';dbname=' . $dbname,
            'dbhost'   => $dbhost,
            'dbname'   => $dbname,
            'username' => $username,
            'password' => $password,
            'charset'  => $charset,//utf8mb4
            'prefix'   => $prefix,
        ];
        $configData['SiteName'] = $SiteName;
        $configData['site'] = [
            'siteuser' => $siteuser,
            'sitepwd'  => $sitepwd,
        ];
        $configData['authkey'] = md5(microtime(true));
        $configData['cookies']['pre'] = 'cok'.substr($configData['authkey'], 0, 3).'_';
        if ($size = file_put_contents($configFile, '<?php return '.var_export($configData, true).';', LOCK_EX)) {
            $link = @new mysqli($dbhost, $username, $password);
            if (!$link) {
                $errMsg[] = '数据库连接错误，未响应';
            }else if($link->connect_errno) {
                $errno = $link->connect_errno;
                $error = $link->connect_error;
                if($errno == 1045) {
                    $errMsg[] = '数据库连接错误，用户名或密码错误（1045）'.$error;
                } elseif($errno == 2003) {
                    $errMsg[] = '数据库连接失败（2003）'.$error;
                } else {
                    $errMsg[] = '数据库错误 '.$error;
                }
            } else {
                if($query = $link->query("SHOW TABLES FROM $dbname")) {
                    if(!$query) {
                        $errMsg[] = $dbname.'数据库安装失败';
                    }
                    while($row = $query->fetch_row()) {
                        if(preg_match("/^$prefix/", $row[0])) {
                            if ($dropTab) {
                                $link->query("DROP TABLE IF EXISTS `$dbname`.`{$row[0]}`");
                            }else {
                                $errMsg[] = $dbname . '数据库存在相同前缀的表，请修改表前缀或删除表！' . $row[0];
                            }
                        }
                    }
                }
            }
            if (empty($errMsg)) {
                if ($charset == 'utf8mb4') {
                    $dbsql = "CREATE DATABASE `$dbname` DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci";
                } else {
                    $dbsql = "CREATE DATABASE `$dbname` DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";
                }

                $query = $link->query($dbsql);

                require_once $levsroot . '/lev/Lev.php';
                $config = include $levsroot . '/lev/myConfig.php';
                Lev::$app['uid'] = 1;
                Lev::$app['isAdmin'] = 1;
                Lev::actionObjectMethod('Lev', [$config], 'init');
                Lev::$app['uid'] = 1;
                Lev::$app['isAdmin'] = 1;
                Lev::$aliases['@web'] = Requestv::getBaseUrl();
                Lev::$aliases['@webroot'] = $webroot;
                Lev::actionObjectMethod('\modules\levs\migrations\_install', [], 'actionInstall');
                $errMsg[] = '系统模块【levs】安装成功';
                if (is_dir($modulesroot . '/levaward')) {
                    \lev\controllers\SupermanController::InstallModule('levaward', '');
                    $errMsg[] = '【大转盘签到免费抽奖】安装成功';
                    \lev\helpers\cacheFileHelpers::setc('SiteIndex', 'levaward');
                    $errMsg[] = '【大转盘签到免费抽奖】设置为首页成功';
                }
                $errMsg[] = '管理员【'.$siteuser.'】'.\lev\helpers\UserLoginModelHelper::register($siteuser, $sitepwd, true, 0, 1)['message'];
                $errMsg[] = json_encode(\lev\base\Assetsv::moveMudAssets('levs'), JSON_UNESCAPED_UNICODE);

                $errCls = 'alert-success';
                $errMsg[] = '恭喜！安装成功。' . $size;

                $btns[] = [
                    'name' => '<svg class="icon"><use xlink:href="#fa-manage"></use></svg> 管理后台',
                    'link' => 'admin.php',
                    'attr' => 'color-orange" target="_blank',
                ];

                $btns[] = [
                    'name' => '<svg class="icon"><use xlink:href="#fa-home"></use></svg> 首页',
                    'link' => 'levs.php',
                    'attr' => 'color-black" target="_blank',
                ];

                $safeurl = 'https://appstore.levme.com/';
                $tips[] = '重要提示：为了您的站点安全，请将网站根目录设置到：/web';
                $tips[] = '网站根目录(全路径)：'. $webroot;
                $tips[] = '<a class="color-lightblue" href="'.$safeurl.'" target="_blank" _bk="1">网站根目录设置教程 &raquo; 查看</a>';
                $tips[] = '防跨站攻击(open_basedir)文件路径设置到/levs目录的上层目录，如下示例：';
                $tips[] = 'open_basedir=/www/wwwroot/levme.com/:/tmp/';
                $tips[] = '【提示】到模块商城可安装更多功能模块，如：大转盘、多功能表单'.\modules\levs\helpers\siteHelper::getCheckNewMudJs();

                //is_file($file = $levsroot . '/index.php') && !is_file($levsroot . '/migrations/data/_lev_dev.bin') &&
                //file_put_contents($file, '<?php echo '.var_export('安装时间：'.date('Y-m-d H:i:s'), true).';');
            }
        }else {
            $errMsg[] = '安装失败！检查文件是否可写';
            $errMsg[] = '检查文件【'.$configFile.'】是否可写';
        }
    }
}else {
    $mainDir = '/' . basename($levsroot);
    !is_dir($runtimeDir = $levsroot . '/runtime') && mkdir($runtimeDir);
    !is_file($configFile) && file_put_contents($configFile, '<?php return [];');
    file_put_contents($levsroot . '/runtime/.install.lock', date('Y-m-d H:i:s') . ' # ');

    if (!function_exists('mysqli_connect')) {
        $errMsg[] = '安装失败！数据库函数未定义（mysqli_connect）';
    } elseif (PHP_VERSION < '5.4.0') {
        $errMsg[] = '安装失败！数据版本不能低于（5.4.0）当前版本：' . PHP_VERSION;
    } elseif (!checkDirWriteable($levsroot)) {
        $errMsg[] = $mainDir . ' 目录不可写，安装失败！请修改';
    } elseif (!checkDirWriteable($levsroot . '/runtime')) {
        $errMsg[] = $mainDir . '/runtime 目录不可写，安装失败！请修改';
    } elseif (!file_get_contents($configFile)) {
        $errMsg[] = $mainDir . '/runtime/config.php 配置文件不可写，安装失败！请修改';
    } elseif (!function_exists('curl_init')) {
        $errMsg[] = '安装失败！必要函数未定义（curl_init）';
    } elseif (!class_exists('ZipArchive', false)) {
        $errMsg[] = '安装失败！必要扩展【zip压缩】未定义（ZipArchive）';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Lev Install</title>
    <link rel="stylesheet" type="text/css" href="assets/statics/superman/css.css?Lev3.3.5.2105">
    <link rel="stylesheet" type="text/css" href="assets/statics/fk7/v1/framework7.ios.colors.min.css">
    <link rel="stylesheet" type="text/css" href="assets/statics/fk7/v1/framework7.ios.min.css">
    <link rel="stylesheet" type="text/css" href="assets/statics/fk7/fk7.css?v1.0529.3Lev3.3.5.2105">
    <script type="text/javascript" src="assets/statics/common/jquery.min.js"></script>
    <script type="text/javascript" src="assets/statics/fk7/v1/framework7.min.js"></script>
    <script type="text/javascript" src="assets/statics/fk7/fk7.init.js"></script>
    <script type="text/javascript" src="assets/statics/common/iconfont.js"></script>
</head>
<body class="iden-levs">

<div class="navbar navbar-bgcolor-red">
    <div class="navbar-inner">
        <div class="left"></div>
        <div class="title">Lev 数据库安装</div>
        <div class="right"></div>
    </div>
</div>
<div class="page-content appbg" style="padding-top: 10px !important;">
<div class="page-content-inner" style="max-width:680px;background:#fff">
<?php if ($errMsg):?>
    <div class="flex-box <?=$errCls?>" style="min-height: 100px;max-width:680px;padding: 10px;">
        <ol style="margin: auto"><li><?=implode('</li><li>', $errMsg)?></li></ol>
    </div>
    <div class="flex-box ju-sa" style="max-width: 680px;margin: 10px">
        <?php if (!empty($btns)): foreach ($btns as $v): ?>
            <a class="button-fill button scale8 <?=$v['attr']?>" href="<?=$v['link']?>"><?=$v['name']?></a>
        <?php endforeach; else:?>
            <a class="button-fill button scale8" href="javascript:window.history.back()">返回</a>
        <?php endif;?>
    </div>
<?php else:?>

    <form action="" method="post" class="form-mainb">
        <div class="table-inputs">
            <div class="card-footer">
                <div class="hint-block">一般为 localhost</div>
                <inpt class="item-input">
                    <label class="control-label">数据库服务器地址</label>
                    <input type="text" class="form-control" name="dbhost" value="localhost">
                </inpt>
            </div>

            <div class="card-footer">
                <div class="hint-block">推荐（utf8mb4支持emoji表情存储，需MYSQL>=5.5.3,空间占用比utf8大）</div>
                <inpt class="item-input" style="max-width: 220px;">
                    <label class="control-label">数据库编码</label>
                    <select class="form-control" name="charset">
                        <option value="utf8mb4">utf8mb4</option>
                        <option value="utf8">UTF8</option>
                    </select>
                </inpt>
            </div>

            <div class="card-footer">
                <div class="hint-block">Mysql数据库名称</div>
                <div class="flex-box" style="max-width:250px">
                <inpt class="item-input">
                    <label class="control-label">数据库名称</label>
                    <input style="min-width: 120px" type="text" class="form-control" name="dbname" value="lev_levs_app">
                </inpt>
                    <inpt class="item-input">
                        <label class="control-label">数据库表前缀</label>
                        <input style="min-width: 120px" type="text" class="form-control" name="prefix" value="pre_">
                    </inpt>
                </div>
            </div>

            <div class="card-footer">
                <div class="hint-block">连接数据库的用户名</div>
                <div class="flex-box" style="max-width:250px">
                <inpt class="item-input">
                    <label class="control-label">数据库用户名</label>
                    <input style="min-width: 120px" type="text" class="form-control" name="username" value="">
                </inpt>
                    <inpt class="item-input">
                        <label class="control-label">数据库密码</label>
                        <input style="min-width: 120px" type="text" class="form-control" name="password" value="">
                    </inpt>
                </div>
            </div>


            <div class="card-footer">
                <div class="hint-block">取个名字</div>
                <inpt class="item-input">
                    <label class="control-label">站点名称</label>
                    <input type="text" class="form-control" name="SiteName" value="">
                </inpt>
            </div>

            <div class="card-footer">
                <div class="flex-box">
                <inpt class="item-input">
                    <label class="control-label">站点管理员账号</label>
                    <input style="min-width: 120px" type="text" class="form-control" name="siteuser" value="admin">
                </inpt>
                    <inpt class="item-input">
                        <label class="control-label">密码</label>
                        <input style="min-width: 120px" type="text" class="form-control" name="sitepwd" value="admin">
                    </inpt>
                </div>
            </div>

            <div class="card-footer">
                <div class="flex-box">
                    <inpt class="item-input">
                        <label class="control-label">
                            <input type="checkbox" style="vertical-align: middle" name="dropTab" value="1">
                            删除相同前缀的表
                        </label>
                    </inpt>
                </div>
            </div>

            <div class="card-footer">
                <div class="hint-block"></div>
                <inpt class="item-input" style="max-width: 220px;">
                    <label class="control-label">部署</label>
                    <select class="form-control" name="pro">
                        <option value="1">生产环境</option>
                        <option value="0">开发环境</option>
                    </select>
                </inpt>
            </div>



            <div class="card-footer">
                <inpt class="item-input">
                    <input type="submit" class="button button-fill color-lightblue" name="dosubmit" value=" 提交安装 ">
                </inpt>
            </div>
        </div>
    </form>

<?php endif;?>
</div>
    <?php if (!empty($tips)):?>
        <div class="flex-box ju-sa">
            <ol class="color-yellow scale8">
                <li><?=implode('</li><li>', $tips)?></li>
            </ol>
        </div>
    <?php endif;?>
</div>

</body>
</html>
