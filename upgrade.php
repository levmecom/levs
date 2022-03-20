<?php
/**
 *	@link https://levme.com
 */

(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');

defined('INSTALL_MODULE') || define('INSTALL_MODULE', true);

require __DIR__ . '/iden.const.php';

if (is_file(__DIR__ . '/migrations/_update.php')) {
    if (!is_file($levFile = LEVROOT . '/Lev.php')) {
        cpmsg('抱歉，请先安装【前置插件】：levs。安装前置插件后再重装本插件', 'https://addon.dismall.com/?@levs.plugin', 'error');
        exit;
    }
    require_once LEVROOT . '/Lev.php';
    $config = include LEVROOT . '/dz/dzConfig.php';
    Lev::actionObjectMethod('Lev', [$config], 'init');

    Lev::actionObjectMethod('\modules\\'.Lev::$app['iden'].'\migrations\_update', [], 'actionUpdate');

    stripos(Lev::$app['charset'], 'gbk') === 0 &&
    header("Content-type: text/html; charset=".Lev::$app['charset']);
}

$finish = true;



