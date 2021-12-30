<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-08 13:38
 *
 * 项目：rm  -  $  - levs_rewrite.php
 *
 * 作者：liwei 
 */

!defined('INLEV') && exit('Access Denied LEV');

//主要用于检查文件入口是否正确，防止直接访问文件。如果将/web目录设置为网站根目录，可忽略
defined('INLEV') || define('INLEV', 1);

include '{{%APPVROOT}}/lev/base/Rewritev.php';

\lev\base\Rewritev::setGET();

include __DIR__ . '/levs.php';