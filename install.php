<?php
/**
 *	@link https://levme.com
 */


(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');

defined('INSTALL_MODULE') || define('INSTALL_MODULE', true);

require __DIR__ . '/iden.const.php';

if (is_file(__DIR__ . '/migrations/_install.php')) {
    require_once LEVROOT . '/Lev.php';
    $config = include LEVROOT . '/dz/dzConfig.php';
    Lev::actionObjectMethod('Lev', [$config], 'init');

    Lev::actionObjectMethod('\modules\\'.Lev::$app['iden'].'\migrations\_install', [], 'actionInstall');

    header("Content-type: text/html; charset=".Lev::$app['charset']);//还原论坛编码
}



$finish = true;



