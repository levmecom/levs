<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-09 11:20
 *
 * 项目：rm  -  $  - BaiduApiSitemap.php
 *
 * 作者：liwei 
 */


namespace modules\levs\modules\rewrite\helpers;

use Lev;

!defined('INLEV') && exit('Access Denied LEV');

class BaiduApiSitemap
{

    /**
     *
        $api = 'http://data.zz.baidu.com/urls?site=dz.levme.com&token=';
        $urls = array(
            'http://www.example.com/1.html',
            'http://www.example.com/2.html',
        );
     * @param $urls
     */
    public static function actionBaiduApi($api, $urls) {
        $apis = rewriteSetHelper::apiurls();
        if (empty($apis[$api]['link'])) {
            return Lev::responseMsg(-1, '抱歉，接口地址未设置', $apis);
        }
        $apiurl = $apis[$api]['link'];
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $apiurl,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return Lev::responseMsg(1, '提交完成：'.$result, [json_decode($result, true)]);
    }

}