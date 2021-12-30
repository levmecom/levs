<?php

/**
 * Lev.levme.com [ ]
 *
 * Copyright (c) 2013-2014 http://www.levme.com All rights reserved.
 *
 * Author: Mr.Lee <675049572@qq.com>
 *
 * Date: 2013-02-17 16:22:17 Mr.Lee $
 */


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once 'hookv.class.php';

class mobileplugin_levs extends plugin_levs {
    
    public static function global_footer_mobile() {}
    
}

class mobileplugin_levs_forum extends mobileplugin_levs {}
class mobileplugin_levs_member extends mobileplugin_levs {}







