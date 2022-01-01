<?php !defined('INLEV') && exit('Access Denied LEV');
 return array(
'name'=>'Lev 轻量系统',
'identifier'=>'levs',
'classdir'=>'0',
'descs'=>'Lev 轻量系统是一款极其精简的模块化系统，除去常用静态文件，大小不足1M。支持php composer 安装',
'copyright'=>'Lev',
'version'=>'20210621.33',
'versiontime'=>'1640858686',
'settings'=>serialize(array (
  '_adminNavs' => 
  array (
    1 => 
    array (
      'id' => 1,
      'order' => '1',
      'name' => '组件管理',
      'target' => '1',
      'status' => '0',
      'tableName' => '0',
      'link' => 'superman/modules',
      'forceGen' => '1',
    ),
    2 => 
    array (
      'id' => 2,
      'order' => '2',
      'name' => 'Lev模块管理',
      'target' => '1',
      'status' => '',
      'tableName' => '{{%lev_modules}}',
      'link' => 'admin-modules',
      'forceGen' => '0',
    ),
    3 => 
    array (
      'id' => 3,
      'order' => '3',
      'name' => '登陆用户',
      'target' => '1',
      'status' => '',
      'tableName' => '{{%lev_users_login}}',
      'link' => 'admin-users',
      'forceGen' => '',
    ),
  ),
  '_adminClassify' => 
  array (
    'app' => 
    array (
      'id' => 'app',
      'order' => '0',
      'status' => '0',
      'name' => '便民应用',
      'descs' => '',
    ),
    'welcome' => 
    array (
      'id' => 'welcome',
      'order' => '3',
      'status' => '0',
      'name' => '欢迎界面',
      'descs' => '',
    ),
    'webpg' => 
    array (
      'id' => 'webpg',
      'order' => '5',
      'status' => '0',
      'name' => '全局导航',
      'descs' => '',
    ),
    'login' => 
    array (
      'id' => 'login',
      'order' => '7',
      'status' => '0',
      'name' => '登陆设置',
      'descs' => '',
    ),
    'global' => 
    array (
      'id' => 'global',
      'order' => '10',
      'status' => '0',
      'name' => '公共设置',
      'descs' => '',
    ),
    'seo' => 
    array (
      'id' => 'seo',
      'order' => '20',
      'status' => '0',
      'name' => 'SEO优化',
      'descs' => '',
    ),
  ),
  'dropTables' => 
  array (
  ),
  '_navTitle' => '',
  '_dzinstall' => '1',
)),
'displayorder'=>'0',
'status'=>'0',
'uptime'=>'1640424390',
'addtime'=>'1638201511',
);;