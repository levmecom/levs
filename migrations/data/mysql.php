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

return [
    0 => $sql,
    1 => $tableNames
];
