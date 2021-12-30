<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-24 11:51
 *
 * 项目：rm  -  $  - ipRecordHelper.php
 *
 * 作者：liwei 
 */

namespace modules\levs\modules\ipban;

use Lev;
use lev\base\Controllerv;
use lev\helpers\cacheFileHelpers;
use modules\levs\modules\ipban\helpers\ipbanSetHelper;
use modules\levs\modules\ipban\table\censusIpModelHelper;
use modules\levs\modules\ipban\table\recordModelHelper;

!defined('INLEV') && exit('Access Denied LEV');


class ipRecordCacheHelper extends cacheFileHelpers
{

    public static $cacheDir = '/ip-record';

    public static $fileExt  = '.bin';

    public static function init()
    {
        ipbanSetHelper::openIPrecord() && static::recordIP();
    }

    public static function dateDir($time = 0, $yearDir = false) {
        !$time && $time = Lev::$app['timestamp'];
        $dir = static::cacheDir() . '/Years/';
        return $yearDir ? $dir : $dir.date('Y', $time) . '/' . date('md', $time). '/';
    }

    public static function recordIP()
    {
        $data = $_SERVER['REMOTE_ADDR'] . ' | ' . $_SERVER['REQUEST_URI'] . ' | ' . $_SERVER['HTTP_REFERER'] . ' | ' . $_SERVER['HTTP_USER_AGENT'] . ' | ' . Lev::$app['timestamp'];

        $dir = static::dateDir();
        $dir.= ((microtime(true) * 10000 + mt_rand(0,1000000)) % ipbanSetHelper::fileNum());
        static::mkdirv($dir);

        $file = $dir . '/' . Controllerv::$pageName . static::$fileExt . date('H', Lev::$app['timestamp']);

        file_put_contents($file, $data . "\r\n", FILE_APPEND);
    }

    public static function setcSpiderIP($arr = null)
    {
        $key = 'spiderIP';
        if ($arr === null) {
            return static::getc($key, false);
        }
        static::setc($key, $arr);
    }

    public static function checkSpiderIP($useragent = null)
    {
        $useragent === null &&
        $useragent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if ($useragent && $arr = static::setcSpiderIP()) {
            foreach ($arr as $v) {
                if (stripos($useragent, $v) !== false) {
                    return $v;
                }
            }
        }
        return '';
    }

    /**
     * @param int $time
     * @param null $total force:强行更新统计
     * @return array|mixed|string
     */
    public static function setTotalIp($time = 0, $total = null) {
        !$time && $time = Lev::$app['timestamp'];
        $key = 'census/'.date('Y', $time).'/setTotalIp';
        is_array($count = static::getc($key, false)) || $count = [];
        if ($total === null) {
            return $count;
        }
        $Ymd = date('Ymd', $time);
        if ($total == 'force' || !isset($count[$Ymd])) {
            //正常用户
            $count[$Ymd]['ip0'] = censusIpModelHelper::total(['addtime' => $Ymd, '`iptotal`>0', 'status' => 0]);
            //搜索引擎蜘蛛
            $count[$Ymd]['ip2'] = censusIpModelHelper::total(['addtime' => $Ymd, '`iptotal`>0', 'status' => 2]);
            static::setc($key, $count);
        }
        return $count;
    }

    public static function getRecordIpFile($time = 0, $H = false) {
        $dateDir = static::dateDir($time);
        $days = glob($dateDir . '*');
        $files = [];
        $H = $H !== false ? $H : floatval(date('H', $time ?: Lev::$app['timestamp']));
        foreach ($days as $src) {
            if (is_dir($src)) {
                $_fs = glob($src . '/*'.static::$fileExt.'*');
                foreach ($_fs as $v) {
                    floatval(substr($v, -2)) < $H && $files[] = $v;
                }
            }
            if (count($files) > 2000) break;
        }
        return $files;
    }

    public static function censusIp($time = 0, $H = false) { Lev::debug();//显示错误信息

        $files = static::getRecordIpFile($time, $H);
        if (empty($files)) {
            static::deleteDateDir($time);
            return Lev::responseMsg(-1, '<b class="color-yellow">记录文件已全部导入数据库</b>');
        }
        $counts = 0;
        $maxCount = 5000;
        $date = Lev::stripTags(Lev::GPv('date'));
        $date && $date = '.date_'.str_replace('/', '_', $date);
        foreach ($files as $v) {
            if (rename($v, $file = $v.(microtime(true) + mt_rand(0,100000)).'.bin')) {
                $data = file_get_contents($file);
                $count = substr_count($data, "\n");
                $name = strstr(basename($file), static::$fileExt, true);
                censusIpModelHelper::censusPagename($name, $time, $count);
                for ($i = 0; $i < $count; ++$i) {
                    $counts += 1;
                    if ($counts > $maxCount) {
                        $i < $count - 1 && file_put_contents($file, $data, LOCK_EX);
                        $js = '<script>window.setTimeout(function() {jQuery(".censusIpBtn'.$date.'").click();}, 2000)</script>';
                        return Lev::responseMsg(-202, '即将进入下次统计，统计达本次上限：'.$maxCount.$js);
                    }
                    $one = strstr($data, "\n", true);
                    $data = substr($data, strlen($one)+1);
                    $arr = explode(' | ', trim($one));
                    $status = static::checkSpiderIP($arr[3]) ? 2 : 0;
                    censusIpModelHelper::censusIp($arr[0], $time, 1, $status);
                    recordModelHelper::insert([
                        'pagetotal'  => censusIpModelHelper::$pagetotal[$name],
                        'iptotal'    => censusIpModelHelper::$iptotal[$arr[0]],
                        'pagename'   => $name,
                        'ip'         => $arr[0],
                        'requesturi' => '#'.$arr[1],
                        'referer'    => '#'.$arr[2],
                        'useragent'  => '#'.$arr[3],
                        'status'     => $status,
                        'addtime'    => $arr[4] ?: $time,
                    ]);
                }
                @unlink($file);
            }
        }

        static::setTotalIp($time, 'force');

        static::deleteDateDir($time);
        return Lev::responseMsg(1, '文件全部导入并统计完成');
    }

    public static function getYearsDay()
    {
        $result = [];

        $dirs = glob(static::dateDir(0, true).'*');
        $dirs && krsort($dirs);
        foreach ($dirs as $v) {
            if (is_dir($v) && is_numeric($year = basename($v))) {
                $days = glob($v.'/*');
                $days && krsort($days);
                foreach ($days as $r) {
                    if (count($result) > 10) break;
                    is_dir($r) && is_numeric($md = basename($r)) && $result[] = $year.'/'.implode('/', str_split($md, 2));
                }
            }
        }
        return $result;
    }

    public static function deleteDateDir($time)
    {
        if ($time ) {
            $ymd = date('Ymd', $time);
            if ($ymd < date('Ymd', Lev::$app['timestamp'])) {
                cacheFileHelpers::rmdirv(static::dateDir($time));
            }
        }

    }
}