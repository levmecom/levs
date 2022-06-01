<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-03 14:09
 *
 * 项目：gitee  -  $  - BaseLevme.php
 *
 * 作者：liwei 
 */

namespace lev;

use InvalidArgumentException;
use Lev;
use lev\base\Assetsv;
use lev\base\Controllerv;
use lev\db\DBV;
use lev\base\Modulesv;
use lev\dz\dzUserHelper;
use lev\helpers\DBVHelper;
use lev\helpers\ModulesHelper;
use lev\widgets\login\loginWidget;
use modules\levmb\sdk\wxmini\wxminiAuthLogin;

//主要用于检查文件入口是否正确，防止直接访问文件。如果将/web目录设置为网站根目录，可忽略
defined('INLEV') or define('INLEV', 2);

//!defined('INLEV') && exit('Access Denied Lev');

//LEV所在磁盘根目录
//defined('LEVROOT') or define('LEVROOT', __DIR__);

//APP主目录 - 可变 - 在配置中定义
//defined('APPVROOT') or define('APPVROOT', dirname(__DIR__));

class BaseLev
{

    public static $lang = [];//语言包

    /**
     * 自动加载类文件，数组key是命名空间=>value是文件真实路径。命名空间必须可转化成真实路径（可以用别名）
     * 当命名空间不能转化成文件所在真实路径时，此项设置很有用
     * @see autoload()
     */
    public static $classMap = [
        //$classMap内容类似如下
        //'lev\base\helpers' => __DIR__ . '/helpers/ModulesHelper.php',
        //'lev\base\helpers' => __DIR__ . '/helpers/SettingsHelper.php',
    ];

    public static $db = null;

    public static $app = [
        'version'     => 'Lev3.6.6.2105',
        'iden'        => null,
        'homeFile'    => 'index.php',
        'timeZone'    => 'Asia/Shanghai',
        'timestamp'   => null,
        'isAdmin'     => null,
        'layout'      => '@layouts/fk7_v1.php',
        'SiteName'    => '站点名称',
        'charset'     => 'utf-8',
        'title'       => '',//页面标题
        'metakeyword' => '',
        'metadesc'    => '',
        'uid'         => 0,
        'groupid'     => 0,
        'username'    => '',
        'myInfo'      => null,
        '_csrf'       => '',
        'CnzzJs'      => [],
        'referer'     => '',
        'Icp'         => '',
        'ip'          => '',
        'notices'     => '',
        'panelHtm'    => '',

        'openLoginScreen' => null,
        'aginLoadCss'     => null,

        'isDiscuz'    => null,//以dz插件形式兼容性安装
        'LevAPP'      => null,//非null 为独立安装。否则为三方兼容性安装 优先级最高

        'mudInfo'     => [],

        'inputOptionInit' => [],//下拉框、多选框传参

        'settings'    => [],
        'scoretypes'  => [],
        'myScores'    => [],

        'apiSiteurl'  => '',//来自API请求的站点首页地址

        'authkey' => '',
        'cookies' => [
            'pre'    => '',
            'domain' => '',
            'path'   => '/',
        ],

        'db' => [
            'dsn' => null,//'mysql:host=localhost;dbname=dbname',
            'dbname'   => '',
            'username' => null,
            'password' => null,
            'charset'  => 'utf8mb4',
            'prefix'   => 'pre_',
            'slave'    => '',
            'dzconfig' => [],
        ],
    ];

    /**
     * @var array registered path aliases
     * @see getAlias()
     * @see setAlias()
     */
    public static $aliases = [
        '@lev'      => __DIR__,
        '@uploads'  => null,
        '@app'      => null,//__DIR__ . '/../',
        '@runtime'  => null,//__DIR__ . '/../runtime',
        '@settings' => null,//__DIR__ . '/../runtime/settings',
        '@views'    => null,//__DIR__ . '/../views',
        '@layouts'  => null,//__DIR__ . '/../views/layouts',
        '@renders'  => null,//__DIR__ . '/../views/renders',
        '@widgets'  => null,//__DIR__ . '/../widgets',
        '@webroot'  => null,//__DIR__ . '/../web',
        '@htmlroot' => null,//__DIR__ . '/../web/html',
        '@appmodule'=> null,
        '@modules'  => null,
        '@controllers'=> null,

        //以下为访问地址 根据实际情况在config中配置
        '@host'       => null, //eg: explame.com
        '@html'       => null,
        '@web'        => '',
        '@appweb'     => '',//特殊情况下设置，@appweb连接在@web后。例：dz插件
        '@webuploads' => '/data/ups',
        '@assets'     => '/assets',
        '@appassets'  => '/assets',//前置、子模块静态文件目录
        '@hostinfo'   => null,//eg: https://explame.com
        '@siteurl'    => null, //eg: https://explame.com/path
    ];

    /**
     * @see actionObjectMethod()
     */
    public static $objects = [];

    /**
     * 将命名空间定义为路径别名来自动寻找并加载类文件
     */
    public static function autoload($className)
    {
        ($classFile = static::getClassFileRoot($className, true)) && include $classFile;

    }
    public static function getClassFileRoot($className, $throwException = false) {
        if (isset(static::$classMap[$className])) {
            $classFile = static::$classMap[$className];
            if ($classFile[0] === '@') {
                $classFile = static::getAlias($classFile, $throwException);
            }
        } elseif (strpos($className, '\\') !== false) {
            $classFile = static::getAlias('@' . str_replace('\\', '/', trim($className, '\\')) . '.php', false);
            if ($classFile === false || !is_file($classFile)) {
                //echo $classFile.$className;exit('--');
                //throw new Exception('Oops! System file lost: '.$className);
                return false;
            }
        } else {
            return false;
        }

        return $classFile;
    }

    private static function initDB() {
        $driver = Lev::$app['db']['slave']
            ? (function_exists('mysql_connect') ? 'lev\db\dbDriverMysqlSlave' : 'lev\db\dbDriverMysqliSlave')
            : (function_exists('mysql_connect') ? 'lev\db\dbDriverMysql' : 'lev\db\dbDriverMysqli');
        DBV::init($driver, Lev::$app['db']['dzconfig']);
        static::$db = new DBVHelper();
        return static::$db;
    }

    /**
     * @return DBVHelper|dzUserHelper
     */
    public static function getDB() {
        return static::$db !== null ? static::$db : static::initDB();
    }

    public static function setModule($iden, $classdir = false) {
        if ($iden) {
            static::$app['iden'] = $iden;
            static::$app['mudInfo'] = Modulesv::getModuleFileInfo(Lev::$app['iden']);
            static::$aliases['@appmodule'] = static::$aliases['@modules'] . '/' . Modulesv::getIdenDir($iden, $classdir);
            static::$aliases['@controllers'] = static::$aliases['@appmodule'] . '/controllers';
            static::$aliases['@views'] = static::$aliases['@appmodule'] . '/template';
            static::$aliases['@renders'] = static::$aliases['@appmodule'] . '/template/renders';

            static::$aliases['@appassets'] = Assetsv::getAppassets();
        }
    }

    /**
     * 转化一个已设置的别名
     * @param $alias
     * @param bool $throwException
     * @return bool|mixed|string
     * @see setAlias()
     */
    public static function getAlias($alias, $throwException = false)
    {
        if (strncmp($alias, '@', 1)) {
            // not an alias
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
            }

            foreach (static::$aliases[$root] as $name => $path) {
                if (strpos($alias . '/', $name . '/') === 0) {
                    return $path . substr($alias, strlen($name));
                }
            }
        }

        if ($throwException) {
            throw new InvalidArgumentException("Invalid path alias: $alias");
        }

        return false;
    }

    /**
     * 设置一个别名；所有别名以@开头
     * @param $alias
     * @param $path
     * @see getAlias()
     */
    public static function setAlias($alias, $path)
    {
        if (strncmp($alias, '@', 1)) {
            $alias = '@' . $alias;
        }
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if ($path !== null) {
            $path = strncmp($path, '@', 1) ? rtrim($path, '\\/') : static::getAlias($path);
            if (!isset(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [$alias => $path];
                }
            } elseif (is_string(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [
                        $alias => $path,
                        $root => static::$aliases[$root],
                    ];
                }
            } else {
                static::$aliases[$root][$alias] = $path;
                krsort(static::$aliases[$root]);
            }
        } elseif (isset(static::$aliases[$root])) {
            if (is_array(static::$aliases[$root])) {
                unset(static::$aliases[$root][$alias]);
            } elseif ($pos === false) {
                unset(static::$aliases[$root]);
            }
        }
    }

    public static function classExists($className, $autoLoad = true) {
        static $cks;
        if (isset($cks[$className])) {
            return $cks[$className];
        }
        if (class_exists($className, false)) {
            return $cks[$className] = true;
        }

        if ($autoLoad && ($classFile = static::getClassFileRoot($className))) {
            include $classFile;
            return $cks[$className] = class_exists($className, false);
        }

        return $cks[$className] = false;
    }

    /**
     * 调用未知类未知方法
     * @param string $className eg: app\className
     * @param array $param
     * @param string $method
     * @param bool $static
     * @return bool|mixed
     */
    public static function actionObjectMethod($className, $param = [], $method = '', $static = true) {
        $key = is_object($className) ? get_class($className) : $className;
        if (isset(static::$objects[$key]) && $method) {
            //!$static && !is_object(static::$objects[$key]) && static::$objects[$key] = new $className;//动静态交叉调用时可用
            return !method_exists(static::$objects[$key], $method) ? false : call_user_func_array([static::$objects[$key], $method], $param);
        }else if (static::classExists($className)) {
            if ($method && method_exists($className, $method)) {
                static::$objects[$key] = $static ? $className : (is_object($className) ? $className : new $className);
                return call_user_func_array([static::$objects[$key], $method], $param);
            }else if (method_exists($className, 'widget')) {
                return call_user_func_array([$className, 'widget'], $param);
            }
        }
        return false;
    }

    public static function actionControllerAction($className, $method = 'index', $iden = '') {
        $className = Controllerv::controllerMaps($className = ucfirst($className).'Controller') ?:
                    'modules\\'.Modulesv::getIdenNs($iden ?: Lev::$app['iden']).'\controllers\\'.$className;
        return static::actionObjectMethod($className, [], 'action'.ucfirst($method), false);
    }

    public static function actionObjectMethodIden($iden, $className, $param = [], $method = '', $static = true) {
        return ModulesHelper::isInstallModule($iden) ? static::actionObjectMethod($className, $param, $method, $static) : false;
    }

    public static function actionObjectMethodSettingsReturn($iden) {
        $className = 'modules\\'.Modulesv::getIdenNs($iden).'\helpers\\'.$iden.'SetHelper';
        return static::actionObjectMethod($className, [], 'SettingsReturn', false);
    }

    public static function actionObjectMethodSettingsHeaderHtm($iden) {
        $className = 'modules\\'.Modulesv::getIdenNs($iden).'\helpers\\'.$iden.'SetHelper';
        return static::actionObjectMethod($className, [], 'HeaderHtm', false);
    }

    public static function actionObjectMethodSettingsFooterHtm($iden) {
        $className = 'modules\\'.Modulesv::getIdenNs($iden).'\helpers\\'.$iden.'SetHelper';
        return static::actionObjectMethod($className, [], 'FooterHtm', false);
    }

    public static function actionObjectMethodSettingsFormFooterHtm($iden) {
        $className = 'modules\\'.Modulesv::getIdenNs($iden).'\helpers\\'.$iden.'SetHelper';
        return static::actionObjectMethod($className, [], 'FormFooterHtm', false);
    }

    public static function wxminiLogin() {
        if (Lev::$app['uid'] <1 && ModulesHelper::isInstallModule('levmb')) {
            //$loid = intval(Lev::GPv('loid'));
            //$appid = Lev::stripTags(Lev::GPv('appid'));
            //$accesstoken = Lev::stripTags(Lev::GPv('accesstoken'));
            if (!Lev::GPv('iv') && $accesstoken = Lev::stripTags(Lev::GPv('accesstoken'))) {
                $msg = wxminiAuthLogin::accesstokenLogin($accesstoken, intval(Lev::GPv('loid')));
                //Lev::setNotices($msg['message'], true);
                $msg && Controllerv::redirect(loginWidget::getLoginReferer() ?: Lev::toCurrent(['accesstoken'=>null, 'loid'=>null, 'appid'=>null]));
            }
        }
    }


    public static function isDeveloper($iden, $classdir = false) {
        return is_file(static::$aliases['@modules'] . '/' . ModulesHelper::getIdenDir($iden, $classdir) . '/migrations/data/_lev_dev.bin');
    }

    public static function debug($all = false) {
        ini_set('display_errors', 1);
        $all ? error_reporting(E_ALL) : error_reporting(E_ALL ^ E_NOTICE);
    }

    public static function log($logFileDir, $data, $loop = false) {
        $string = '';
        if ($loop && is_array($data)) {
            foreach ($data as $k => $v) {
                $string .= $k . ' => ' . print_r($v, true) . "\r\n";
            }
        }else {
            $string = print_r($data, true) . "\r\n";
        }
        $logFileDir || $logFileDir = Lev::$aliases['@runtime'];
        file_put_contents($logFileDir . '/log.txt', $string, FILE_APPEND);
    }

    /**
     * 禁止本类通过 new 创建实例
     * BaseLev constructor.
     */
    private function __construct() {}

    /**
     * 禁止克隆
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
}