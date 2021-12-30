<?php !defined('INLEV') && exit('Access Denied LEV');
 return array(
'name'=>'禁止IP访问',
'identifier'=>'ipban',
'classdir'=>'levs',
'descs'=>'禁止IP直接EXIT，或者rewrite IP',
'copyright'=>'Lev',
'version'=>'20210919.03',
'versiontime'=>'1638599694',
'settings'=>serialize(array (
  '_adminNavs' => 
  array (
    1 => 
    array (
      'id' => 1,
      'order' => '1',
      'name' => 'IP访问记录',
      'target' => '1',
      'status' => '',
      'tableName' => '{{%ipban_record}}',
      'link' => 'admin-record',
      'forceGen' => '0',
    ),
    2 => 
    array (
      'id' => 2,
      'order' => '2',
      'name' => 'IP统计',
      'target' => '1',
      'status' => '',
      'tableName' => '{{%ipban_record_census}}',
      'link' => 'admin-census',
      'forceGen' => '',
    ),
  ),
  '_adminClassify' => 
  array (
    'ipban' => 
    array (
      'id' => 'ipban',
      'order' => '1',
      'status' => '0',
      'name' => '禁止IP设置',
      'descs' => '',
    ),
    'iprecord' => 
    array (
      'id' => 'iprecord',
      'order' => '2',
      'status' => '0',
      'name' => 'IP统计设置',
      'descs' => '按天统计访问IP',
    ),
  ),
  'dropTables' => 
  array (
    0 => '{{%ipban_record}}',
    1 => '{{%ipban_record_census}}',
  ),
)),
'displayorder'=>'0',
'status'=>'0',
'uptime'=>'1638247212',
'addtime'=>'1638247212',
);;