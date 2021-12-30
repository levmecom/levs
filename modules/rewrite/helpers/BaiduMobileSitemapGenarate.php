<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-09 11:12
 *
 * 项目：rm  -  $  - BaiduMobileSitemapGenarate.php
 *
 * 作者：liwei 
 */

namespace modules\levs\modules\rewrite\helpers;

use Lev;
use lev\helpers\cacheFileHelpers;

!defined('INLEV') && exit('Access Denied LEV');


class BaiduMobileSitemapGenarate
{

    public static function getGenSitemaps() {
        return glob(static::getSitemapsDir() . '/*.xml');
    }

    public static function getSitemapsDir($root = true) {
        $childDir = '/data/sitemaps';
        if (!$root) {
            return Lev::$aliases['@web'] . $childDir;
        }
        $dir = Lev::$aliases['@webroot'] . $childDir;
        cacheFileHelpers::mkdirv($dir);
        return $dir;
    }

    public static function gen($urls, $name = '') {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
        xmlns:mobile="http://www.baidu.com/schemas/sitemap-mobile/1/">';
        $date = date('Y-m-d');
        foreach ($urls as $r) {
            $v = empty($r['url']) && !is_array($r) ? ['url'=>$r, 'addtime'=>$date] : $r;
            $xml .= '
    <url>
        <loc>'.$v['url'].'</loc>
        <mobile:mobile type="pc,mobile"/>
        <lastmod>'.(is_numeric($v['addtime']) ? date('Y-m-d', $v['addtime']) : $v['addtime']).'</lastmod>
        <changefreq>always</changefreq>
        <priority>0.8</priority>
    </url>
';
        }
        $xml .= '</urlset>';
        $dir = static::getSitemapsDir();
        file_put_contents($dir . '/'.$name.'baidu-pc-mobile-sitemap.xml', $xml);
    }

}