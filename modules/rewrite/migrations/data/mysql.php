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
];

//DROP TABLE IF EXISTS `pre_lev_modules`;
$sql = \lev\base\Migrationv::getLevBaseTableCreateSql();
/*
$sql = <<<EOF

CREATE TABLE IF NOT EXISTS `pre_lev_modules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `typeid` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '模块名称',
  `identifier` varchar(220) NOT NULL DEFAULT '' COMMENT '唯一标识',
  `classdir` varchar(255) NOT NULL DEFAULT '',
  `descs` varchar(255) NOT NULL DEFAULT '' COMMENT '简短描述',
  `copyright` varchar(255) NOT NULL DEFAULT '' COMMENT '版权',
  `version` varchar(255) NOT NULL DEFAULT '' COMMENT '版本号',
  `versiontime` bigint(20) NOT NULL DEFAULT '0' COMMENT '版本时间号',
  `settings` mediumtext NOT NULL COMMENT '通用设置',
  `displayorder` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态',
  `uptime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addtime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) $tableOptions;

CREATE TABLE IF NOT EXISTS `pre_lev_settings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `moduleidentifier` varchar(220) NOT NULL DEFAULT '' COMMENT '模块标识符',
  `classify` varchar(32) NOT NULL DEFAULT '' COMMENT '设置分类',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `placeholder` text NOT NULL COMMENT '输入框内提示',
  `inputname` varchar(255) NOT NULL DEFAULT '' COMMENT '输入框名',
  `inputtype` varchar(255) NOT NULL DEFAULT '' COMMENT '输入框类型',
  `inputvalue` mediumtext NOT NULL COMMENT '输入框值',
  `settings` mediumtext NOT NULL COMMENT '通用设置',
  `displayorder` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态',
  `uptime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addtime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `moduleidentifier` (`moduleidentifier`)
) $tableOptions;

EOF;
*/

return [
    0 => $sql,
    1 => $tableNames
];
