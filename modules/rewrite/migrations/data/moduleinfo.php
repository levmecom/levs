<?php !defined('INLEV') && exit('Access Denied LEV');
 return array(
'name'=>'URL美化',
'identifier'=>'rewrite',
'classdir'=>'levs',
'descs'=>'SEO友好的URL规则，需设置rewrite规则',
'copyright'=>'Lev',
'version'=>'20211108.02',
'versiontime'=>'1651379692',
'settings'=>serialize(array (
  '_adminClassify' => 
  array (
    1 => 
    array (
      'id' => '1',
      'order' => '1',
      'status' => '0',
      'name' => 'API接口设置',
      'descs' => '',
    ),
  ),
  'dropTables' => 
  array (
  ),
  '_adminNavs' => 
  array (
    1 => 
    array (
      'id' => 1,
      'order' => '1',
      'name' => '伪静态设置',
      'target' => '0',
      'status' => '',
      'tableName' => '0',
      'link' => 'default',
      'forceGen' => '',
    ),
    2 => 
    array (
      'id' => 2,
      'order' => '2',
      'name' => 'sitemap提交',
      'target' => '0',
      'status' => '',
      'tableName' => '0',
      'link' => 'sitemap',
      'forceGen' => '',
    ),
  ),
)),
'displayorder'=>'0',
'uptime'=>'1638247204',
'addtime'=>'1638247204',
);;