<?php
/**
 *	@link https://levme.com
 */

(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');

defined('INSTALL_MODULE') || define('INSTALL_MODULE', true);

require __DIR__ . '/iden.const.php';

if (is_file(__DIR__ . '/migrations/_update.php')) {
    require_once LEVROOT . '/Lev.php';
    $config = include LEVROOT . '/dz/dzConfig.php';
    Lev::actionObjectMethod('Lev', [$config], 'init');

    Lev::actionObjectMethod('\modules\\'.Lev::$app['iden'].'\migrations\_update', [], 'actionUpdate');

    header("Content-type: text/html; charset=".Lev::$app['charset']);
}

$finish = true;



