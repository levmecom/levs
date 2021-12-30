<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-04 21:35
 *
 * 项目：upload  -  $  - dzConfig.php
 *
 * 作者：liwei 
 */

!defined('IN_ADMINCP') && exit('Access Denied Admin');

$config = include __DIR__ . '/dzConfig.php';

Lev::$app['layout'] = '@layouts/fk7_v1_for_dz_admin.php';

Lev::GPv('r') || $_GET['r'] = Lev::GETv('pmod');

return $config;