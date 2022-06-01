<?php
/* 
 * Copyright (c) 2018-2021  * 
 * 创建时间：2021-04-23 12:27
 *
 * 项目：upload  -  $  - cacheHelpers.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

!defined('INLEV') && exit('Access Denied Lev');

use Lev;

class cacheFileHelpers
{

    public static $cacheDir = '/cache';

    public static $fileExt = '.bin';

    /**
     * 设置并创建缓存路径
     * @param null $val
     * @return bool|mixed|string
     */
    public static function cacheDir($val = null) {
        if ($val !== null) static::$cacheDir = $val;
        static::mkdirv($dir = Lev::$aliases['@runtime'] . static::$cacheDir);
        return $dir;
    }

    /**
     * 创建运行目录
     */
    public static function createRuntimeDir() {
        Lev::$aliases['@runtime'] && !is_dir(Lev::$aliases['@runtime']) && static::mkdirv(Lev::$aliases['@runtime'], 0777, true);
    }

    /**
     * 清除、设置、获取一体化操作
     * @param $ckey
     * @param null $value
     * @param int $timeout
     * @param null $clear
     * @param bool $checkTimeout
     * @return bool|mixed|string
     */
    public static function optc($ckey, $value = null, $timeout = 0, $clear = null, $checkTimeout = true) {
        if ($clear) {
            return static::clearc($ckey);
        }
        if ($value !== null) {
            return static::setc($ckey, $value, $timeout);
        }
        return static::getc($ckey, $checkTimeout);
    }

    /**
     * 给已知缓存$pkey加入新数据
     * @param $pkey
     * @param null $value
     * @param null $key
     * @param int $timeout
     * @return array
     */
    public static function joinc($pkey, $value = null, $key = null, $timeout = 0) {
        $values = static::getc($pkey, false) ?: [];
        if ($value !== null) {
            if ($key === null) {
                $values[] = $value;
            }else {
                $values[$key] = $value;
            }
            static::setc($pkey, $values, $timeout);
        }
        return $values;
    }

    /**
     * 设置缓存文件
     * @param $key
     * @param $value
     * @param int $timeout 秒，默认一年
     */
    public static function setc($key, $value, $timeout = 0) {
        $keys = static::getCacheKey($key);
        static::mkdirv($dir = static::cacheDir() . '/' .$keys[1]);
        $file = $dir . '/' . $keys[0] . static::$fileExt;
        return file_put_contents($file, serialize($value), LOCK_EX) !== false &&
            touch($file, Lev::$app['timestamp'] + ($timeout ?: 365 * 24 * 3600));
    }

    /**
     * 获取缓存内容 缓存文件还存在，可以通过设置$checkTimeout = false获取过期缓存内容
     * @param string $key
     * @param bool $checkTimeout
     * @return mixed|string
     */
    public static function getc($key, $checkTimeout = true) {
        return ($file = static::filec($key)) &&
            (!$checkTimeout || filemtime($file) > Lev::$app['timestamp']) ? unserialize(file_get_contents($file)) : '';
    }

    /**
     * @param $key
     * @param bool $dir
     * @return bool
     */
    public static function clearc($key, $dir = false) {
        if ($dir) {
            return static::rmdirv(dirname(static::filec($key)));
        }
        return ($file = static::filec($key)) && @unlink($file);
    }

    public static function filec($key) {
        $keys = static::getCacheKey($key);
        return is_file($file = static::cacheDir() . '/' .$keys[1] . '/' . $keys[0] . static::$fileExt) ? $file : '';
    }

    /**
     * 缓存文件名规则，自动创建目录
     * @param $key
     * @return array
     */
    public static function getCacheKey($key) {
        $md5 = md5($key);
        $subDir = strpos(trim($key, '/'), '/') !== false ? dirname($key) : substr($md5, 0, 3);
        return [$md5, $subDir];
    }

    /**
     * 递归创建目录及文件
     * @param $dir
     * @param int $mode
     * @param bool $makeindex
     * @return bool
     */
    public static function mkdirv($dir, $mode = 0777, $makeindex = false){
        if(!is_dir($dir)) {
            static::mkdirv(dirname($dir), $mode, $makeindex);
            mkdir($dir, $mode);
            if(!empty($makeindex)) {
                touch($dir.'/index.html');
                chmod($dir.'/index.html', 0777);
            }
        }
        return true;
    }

    /**
     * 递归删除目录及文件
     * @param $dirname
     * @param bool $keepdir
     * @return bool
     */
    public static function rmdirv($dirname, $keepdir = FALSE) {
        $dirname = str_replace(array( "\n", "\r", '..'), array('', '', ''), $dirname);

        if(!is_dir($dirname)) {
            return FALSE;
        }
        $handle = opendir($dirname);
        while(($file = readdir($handle)) !== FALSE) {
            if($file != '.' && $file != '..') {
                $dir = $dirname . DIRECTORY_SEPARATOR . $file;
                is_dir($dir) ? static::rmdirv($dir) : unlink($dir);
            }
        }
        closedir($handle);
        return !$keepdir ? (@rmdir($dirname) ? TRUE : FALSE) : TRUE;
    }

    /**
     * 文件修改时间是否超时
     * @param $filename
     * @param int $timeout
     * @return bool
     */
    public static function checkcTimeout($filename, $timeout = false) {
        $cFile = static::cacheDir().'/checkcTimeout/'.$filename.static::$fileExt;
        static::mkdirv(dirname($cFile));
        $mTime = !is_file($cFile) && touch($cFile) ? 0 : filemtime($cFile);
        $timeout !== false && touch($cFile, Lev::$app['timestamp'] + $timeout);
        return $mTime < Lev::$app['timestamp'];
    }

    /**
     * 将serialize字串转换为 serialize($arr);
     * @param $array
     * @return string
     */
    public static function varExportSerialize($array) {
        $str = "array(\n";
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $str .= "'$k'=> array(\n";
                foreach ($v as $f => $a) {
                    $ser = static::serializeToArr($a);
                    $a = is_array($ser) ? 'serialize(' . var_export($ser, true) . ')' : var_export($a, true);
                    $str .= "'$f'=>$a,\n";
                }
                $str .= "),\n";
            }else {
                $ser = static::serializeToArr($v);
                $v = is_array($ser) ? 'serialize(' . var_export($ser, true) . ')' : var_export($v, true);
                $str .= "'$k'=>$v,\n";
            }
        }
        $str .= ');';
        return $str;
    }

    /**
     * 递归 unserialize
     * @param $str
     * @return mixed
     */
    public static function serializeToArr($str) {
        if (static::isSerializeStr($str)) {
            $arr = unserialize($str);
            foreach ($arr as $k => $v) {
                $arr[$k] = static::serializeToArr($v);
            }
            return $arr;
        }
        return $str;
    }

    public static function unserializev($str) {
        return $str && static::isSerializeStr($str) ? unserialize($str) : $str;
    }

    /**
     * 判断是否是serialize字符串
     * @param $data
     * @return bool
     */
    public static function isSerializeStr($data ) {
        if (is_array($data)) {
            return false;
        }
        $data = trim( $data );
        if ( 'N;' == $data ) return true;
        if ( !preg_match( '/^([adObis]):/', $data, $badions ) ) return false;
        switch ( $badions[1] ) {
            case 'a' :
            case 'O' :
            case 's' :
                if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) ) return true;
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) ) return true;
                break;
        }
        return false;
    }
}