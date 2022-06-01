<?php
/**
 * Copyright (c) 2022-2222   All rights reserved.
 *
 * 创建时间：2022-04-08 13:30
 *
 * 项目：levs  -  $  - footer.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

if ($footer === null) {
    Lev::footer();
}else if (is_file($footer)) {
    include $footer;
}else {
    echo $footer;
}