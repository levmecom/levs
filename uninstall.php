<?php
/**
 *	Version: 1.0
 *	Date: 2013-8-16 22:44
 */


(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');

defined('INSTALL_MODULE') || define('INSTALL_MODULE', true);

require __DIR__ . '/iden.const.php';

if (is_file(__DIR__ . '/migrations/_uninstall.php') && is_file($levFile = LEVROOT . '/Lev.php')) {
    require_once $levFile;
    $config = include LEVROOT . '/dz/dzConfig.php';
    Lev::actionObjectMethod('Lev', [$config], 'init');

    Lev::actionObjectMethod('\modules\\'.Lev::$app['iden'].'\migrations\_uninstall', [], 'actionUninstall');

    stripos(Lev::$app['charset'], 'gbk') === 0 &&
    header("Content-type: text/html; charset=".Lev::$app['charset']);
}
$finish = true;
