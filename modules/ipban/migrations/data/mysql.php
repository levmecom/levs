<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-06 09:59
 *
 * 项目：upload  -  $  - sql.php
 *
 * 作者：liwei 
 */

!defined('INLEV') && exit('Access Denied LEV');

$charset = Lev::$app['db']['charset'];
$tableOptions = ' CHARACTER SET '.$charset.' ENGINE=MyISAM';
$tableNames = [
    '{{%ipban_record}}',
    '{{%ipban_record_census}}',
];

$sql = \lev\base\Migrationv::getLevBaseTableCreateSql();
//DROP TABLE IF EXISTS `pre_lev_modules`;

defined('ACTION_INSTALL') &&
$sql.= 'DROP TABLE IF EXISTS `{{%ipban_record}}`;';
$sql.= <<<EOF

CREATE TABLE IF NOT EXISTS `{{%ipban_record}}` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `pagename` varchar(100) NOT NULL DEFAULT '' COMMENT '页面标识',
  `ip` varchar(100) NOT NULL DEFAULT '' COMMENT 'IP',
  `iptotal` bigint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'IP计数',
  `pagetotal` bigint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'page计数',
  `requesturi` varchar(220) NOT NULL DEFAULT '' COMMENT '请求地址',
  `referer` varchar(220) NOT NULL DEFAULT '' COMMENT '来源地址',
  `useragent` varchar(255) NOT NULL DEFAULT '' COMMENT '用户标识',
  `settings` mediumtext NOT NULL COMMENT '通用设置',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态',
  `addtime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`, `pagename`, `addtime`),
  KEY `requesturi` (`requesturi`),
  KEY `referer` (`referer`)
) $tableOptions;

EOF;

defined('ACTION_INSTALL') &&
$sql.= 'DROP TABLE IF EXISTS `{{%ipban_record_census}}`;';
$sql.= <<<EOF

CREATE TABLE IF NOT EXISTS `{{%ipban_record_census}}` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `pagename` varchar(100) NOT NULL DEFAULT '' COMMENT '页面标识',
  `ip` varchar(100) NOT NULL DEFAULT '' COMMENT 'IP',
  `iptotal` bigint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'IP计数',
  `pagetotal` bigint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'page计数',
  `requesturi` varchar(220) NOT NULL DEFAULT '' COMMENT '请求地址',
  `referer` varchar(220) NOT NULL DEFAULT '' COMMENT '来源地址',
  `useragent` varchar(255) NOT NULL DEFAULT '' COMMENT '用户标识',
  `settings` mediumtext NOT NULL COMMENT '通用设置',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态',
  `uptime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addtime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`, `pagename`, `addtime`),
  KEY `pagename` (`pagename`),
  KEY `addtime` (`addtime`)
) $tableOptions;

EOF;

return [
    0 => $sql,
    1 => $tableNames
];
