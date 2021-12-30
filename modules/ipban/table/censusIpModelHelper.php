<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-25 11:03
 *
 * 项目：rm  -  $  - censusIpModelHelper.php
 *
 * 作者：liwei 
 */

namespace modules\levs\modules\ipban\table;

use Lev;
use modules\levs\modules\ipban\table\ipban_record_census\ipbanRecordCensusModelHelper;

!defined('INLEV') && exit('Access Denied LEV');


class censusIpModelHelper extends ipbanRecordCensusModelHelper
{

    public static $iptotal   = [];
    public static $pagetotal = [];

    public static function statuses() {
        return recordModelHelper::statuses();
    }

    public static function censusIp($ip = '', $time = 0, $num = 1, $status = 0) {
        static $ips;
        !$time && $time = Lev::$app['timestamp'];
        $key = $ip.'-'.$time;
        if (!isset($ips[$key])) {
            $ymd = date('Ymd', $time);
            $ips[$key] = static::findOne(['ip'=>$ip, 'addtime'=>$ymd]);
            if (empty($ips[$key])) {
                $data = [
                    'ip' => $ip,
                    'addtime' => $ymd,
                    'iptotal' => $num,
                    'status'  => $status,
                ];
                $data['id'] = static::insert($data, true);
                $ips[$key] = $data;
                $iptotal = $num;
            }
        }
        if (!isset($iptotal)) {
            $ips[$key]['iptotal'] = $iptotal = $ips[$key]['iptotal'] + $num;
            static::update(['iptotal'=>$iptotal], ['id'=>$ips[$key]['id']]);
        }
        static::$iptotal[$ip] = $iptotal;
    }

    public static function censusPagename($pagename = '', $time = 0, $num = 1) {
        static $pagenames;
        !$time && $time = Lev::$app['timestamp'];
        $key = $pagename.'-'.$time;
        if (!isset($pagenames[$key])) {
            $ymd = date('Ymd', $time);
            $pagenames[$key] = static::findOne(['pagename'=>$pagename, 'addtime'=>$ymd]);
            if (empty($pagenames[$key])) {
                $data = [
                    'pagename' => $pagename,
                    'addtime' => $ymd,
                    'pagetotal' => $num,
                ];
                $data['id'] = static::insert($data, true);
                $pagenames[$key] = $data;
                $pagetotal = $num;
            }
        }
        if (!isset($pagetotal)) {
            $pagenames[$key]['pagetotal'] = $pagetotal = $pagenames[$key]['pagetotal'] + $num;
            static::update(['pagetotal'=>$pagetotal], ['id'=>$pagenames[$key]['id']]);
        }
        static::$pagetotal[$pagename] = $pagetotal;
    }

}