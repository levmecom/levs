<?php !defined('INLEV') && exit('Access Denied LEV');
 return array(
'0'=> array(
'moduleidentifier'=>'ipban',
'classify'=>'iprecord',
'title'=>'开启IP统计',
'placeholder'=>'统计每个页面IP访问数量',
'inputname'=>'openIPrecord',
'inputtype'=>'radio',
'inputvalue'=>'1',
'settings'=>'',
'displayorder'=>'0',
'status'=>'0',
'uptime'=>'1637725032',
'addtime'=>'1637725032',
),
'1'=> array(
'moduleidentifier'=>'ipban',
'classify'=>'iprecord',
'title'=>'过虑爬虫蜘蛛IP',
'placeholder'=>'一行一个过虑关键词。关键词从useragent中提取
爬虫IP将被单独记录',
'inputname'=>'spiderIP',
'inputtype'=>'textarea',
'inputvalue'=>'spider
http://
https://
Win64; 
compatible
Build/LRX21T',
'settings'=>'',
'displayorder'=>'0',
'status'=>'0',
'uptime'=>'1637725276',
'addtime'=>'1637725276',
),
'2'=> array(
'moduleidentifier'=>'ipban',
'classify'=>'iprecord',
'title'=>'IP记录文件数量',
'placeholder'=>'最低两个；高并发、大访问量时建议设置大一点。
10万IP建议设置20以上',
'inputname'=>'fileNum',
'inputtype'=>'number',
'inputvalue'=>'20',
'settings'=>'',
'displayorder'=>'0',
'status'=>'0',
'uptime'=>'1637730663',
'addtime'=>'1637730663',
),
'3'=> array(
'moduleidentifier'=>'ipban',
'classify'=>'ipban',
'title'=>'是否开启IP禁止功能',
'placeholder'=>'开启，立即生效',
'inputname'=>'openIP',
'inputtype'=>'radio',
'inputvalue'=>'',
'settings'=>'',
'displayorder'=>'0',
'status'=>'0',
'uptime'=>'1632104974',
'addtime'=>'1632104974',
),
'4'=> array(
'moduleidentifier'=>'ipban',
'classify'=>'ipban',
'title'=>'入口文件检测',
'placeholder'=>'为入口文件加入IP禁止代码
仅对加入代码的文件有效',
'inputname'=>'gateFiles',
'inputtype'=>'buttons',
'inputvalue'=>'',
'settings'=>'查看入口文件=={homeUrl}levs.php?id=levs:ipban&r=admin/set-gate-ip-file==red==',
'displayorder'=>'3',
'status'=>'0',
'uptime'=>'1632107155',
'addtime'=>'1632106927',
),
'5'=> array(
'moduleidentifier'=>'ipban',
'classify'=>'ipban',
'title'=>'禁止IP设置',
'placeholder'=>'多个IP用等号【=】分隔
禁止IP将直接EXIT 不会连接数据库
<tips>【注意】管理后台加入禁止IP代码也在禁止范围内</tips>',
'inputname'=>'banIPs',
'inputtype'=>'textarea',
'inputvalue'=>'1.85.2.113',
'settings'=>'',
'displayorder'=>'13',
'status'=>'0',
'uptime'=>'1632113924',
'addtime'=>'1632103724',
),
'6'=> array(
'moduleidentifier'=>'ipban',
'classify'=>'ipban',
'title'=>'IP rewrite设置',
'placeholder'=>'<tips>功能开发中...！<a href=https://dz.levme.com target=_blank _bk=1>投票支持开发</a></tips>
设置后被禁用的IP自动写入rewrite规则文件',
'inputname'=>'rewriteIP',
'inputtype'=>'tabletrForm',
'inputvalue'=>serialize(array (
  0 => 
  array (
    'id' => 0,
    'status' => '',
    'serverType' => '0',
    'fileRoot' => '',
  ),
)),
'settings'=>serialize(array (
  'tablesForm' => 
  array (
    8 => 
    array (
      'id' => 8,
      'order' => '0',
      'title' => 'ID',
      'inputname' => 'id',
      'inputtype' => 'number',
      'width' => '40',
      'settings' => '',
      'placeholder' => '',
      'inputvalue' => '',
    ),
    5 => 
    array (
      'id' => 5,
      'order' => '2',
      'title' => '开关',
      'inputname' => 'status',
      'inputtype' => 'radio',
      'width' => '40',
      'settings' => '',
      'placeholder' => '',
      'inputvalue' => '',
    ),
    3 => 
    array (
      'id' => 3,
      'order' => '3',
      'title' => '服务器类型',
      'inputname' => 'serverType',
      'inputtype' => 'select',
      'width' => '100',
      'settings' => '0=nginx
1=apache
2=iis',
      'placeholder' => '',
      'inputvalue' => '',
    ),
    6 => 
    array (
      'id' => 6,
      'order' => '6',
      'title' => 'rewrite文件地址',
      'inputname' => 'fileRoot',
      'inputtype' => 'text',
      'width' => '',
      'settings' => '',
      'placeholder' => '&0quot;/&0quot;开头视为绝对路径；“@webroot”开头视为网站根录',
      'inputvalue' => '',
    ),
  ),
)),
'displayorder'=>'20',
'status'=>'0',
'uptime'=>'1632105222',
'addtime'=>'0',
),
);