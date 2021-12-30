<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-04 21:39
 *
 * 项目：upload  -  $  - discuzHelper.php
 *
 * 作者：liwei 
 */

namespace lev\dz;

use C;
use discuz_upload;
use image;
use Lev;
use lev\base\Modelv;
use lev\helpers\dbHelper;
use lev\helpers\ScoreHelper;
use lev\helpers\UserHelper;
use lev\widgets\uploads\uploadsWidget;

!defined('IN_DISCUZ') && exit('Access Denied');

class discuzHelper extends dzDBHelper
{

    public static function setLoginAuthkey() {
        global $_G;
//        if(empty($_G['cookie']['saltkey'])) {
//            $_G['cookie']['saltkey'] = random(8);
//            dsetcookie('saltkey', $_G['cookie']['saltkey'], 86400 * 30, 1, 1);
//        }
        if(MAGIC_QUOTES_GPC) {
            $_GET = dstripslashes($_GET);
            $_POST = dstripslashes($_POST);
            $_COOKIE = dstripslashes($_COOKIE);
        }

        $prelength = strlen($_G['config']['cookie']['cookiepre']);
        foreach($_COOKIE as $key => $val) {
            if(substr($key, 0, $prelength) == $_G['config']['cookie']['cookiepre']) {
                $_G['config']['cookie'][substr($key, $prelength)] = $val;
            }
        }
        $_G['uid'] = Lev::$app['uid'];
        $_G['username'] = static::getUserInfo(Lev::$app['uid'])['username'];
        $_G['authkey'] = md5($_G['config']['security']['authkey'].$_G['cookie']['saltkey']);
    }

    public static function isNameExist($username) {
        loaducenter();
        $errno = uc_user_checkname($username);
        if ($errno == '1') {
            return false;
        }
        return Lev::responseMsg(-1, $errno.'用户名不合法或已存在');
    }

    public static function getAvatar($uid, $size = 'middle', $type = '') {
        return discuzHelper::getAvatarDzUcenter($uid, $size, $type);
    }

    public static function getAvatarDzUcenter($uid, $size = 'middle', $type = '', $src = null) {
        global $_G;
        if (!empty($_G['setting']['ucenterurl'])) {
            $src === null &&
            $src = UserHelper::setAvatar($uid, $size, $type);
            $ucenterDir = basename($_G['setting']['ucenterurl']);
            if (is_file(Lev::$aliases['@webroot'] .'/'. $ucenterDir . $src)) {
                return Lev::$aliases['@web'] .'/'. $ucenterDir . $src;
            }
        }
        return '';
    }

    public static function checkPasswordError($uid, $password) {
        loaducenter();
        $user = static::getUserInfo($uid);
        $res = uc_user_login($user['username'], $password);
        if ($res[0] >0 && $res[0] == $uid) {
            return false;
        }
        return Lev::responseMsg(-4, '抱歉，密码错误');
    }

    public static function setPassword($username, $password) {
        loaducenter();
        $status = uc_user_edit($username, $password, $password, '', 1);
        return Lev::responseMsg($status);
    }

    public static function doLogin($username, $password, $cookietime, $questionid = '', $answer = '', $ip = '', $charset = 1) {
        if ($charset) {
            $username = dbHelper::setDataToCharset($username);
            $answer = dbHelper::setDataToCharset($answer);
        }

        require_once libfile('function/member');
        $res = userlogin($username, $password, $questionid, $answer, 'username', $ip);
        if ($res['status'] >0 && $res['ucresult']['uid'] >0) {
            return static::setLogin($res['member'], $ip, $cookietime);
        }else if ($res['ucresult']['uid'] == -4) {
            return Modelv::errorMsg('password', '密码错误次数过多，请稍等15分钟再试', -499, ['loginres'=>$res]);
        }else if ($res['ucresult']['uid'] == -3) {
            return Modelv::errorMsg('answer', '安全问题答案有误', -500, ['loginres'=>$res]);
        }
        return Modelv::errorMsg('password', '登陆失败，账号或密码错误', -501, ['loginres'=>$res]);
    }

    public static function setLogin($member, $ip = '', $cookietime = 2592000) {
        global $_G;

        $ck = static::setLoginStatus($member['uid'], $member, $ip, $cookietime);
        if (!$ck) {
            return Modelv::errorMsg('password', '登陆失败，用户信息不存在', -502, ['ck'=>$ck]);
        }

        $ucsynlogin = '';
        if($_G['setting']['allowsynlogin']) {
            loaducenter();
            $ucsynlogin = uc_user_synlogin($member['uid']);
        }
        Lev::$app['uid'] = $member['uid'];
        return Lev::responseMsg(1, '登陆成功', array('synLoginJs' => $ucsynlogin, 'referer'=>Lev::$app['referer'], 'uid'=>$member['uid']));
    }

    public static function setLoginStatus($uid, $member = [], $ip = '', $cookietime = 2592000) {//30天
        empty($member) && $member = getuserbyuid($uid, 1);
        if(!$member) {
            return false;
        } else {
            C::t('common_member_status')->update($member['uid'], array('lastip'=>$ip, 'lastvisit'=>Lev::$app['timestamp'], 'lastactivity' => Lev::$app['timestamp']));
        }

        function_exists('userlogin') || require_once libfile('function/member');
        empty($member['password']) && $member = getuserbyuid($member['uid'], 1);
        setloginstatus($member, $cookietime);
        return true;
    }

    public static function onlyRegister($username, $password, $email, $questionid = '', $answer = '', $ip = '') {
        return static::doRegister($username, $password, $email, $questionid, $answer, $ip, false);
    }
    public static function doRegister($username, $password, $email, $questionid = '', $answer = '', $ip = '', $doLogin = true) {
        $username = dbHelper::setDataToCharset($username);
        $answer = dbHelper::setDataToCharset($answer);

        global $_G;

        if (!isemail($email)) {
            return Modelv::errorMsg('email', '抱歉，邮箱不合法', -561);
        }

        !$ip && $ip = Lev::$app['ip'];
        loaducenter();
        $uid = uc_user_register($username, $password, $email, $questionid, $answer, $ip);
        switch ($uid) {
            case -1:
            case -2:
            case -3:
                return Modelv::errorMsg('username', '抱歉，用户名已被注册', -53, ['uc'=>$uid]); break;
            case -4:
            case -5:
            case -6:
                return Modelv::errorMsg('email', '抱歉，邮箱已被注册', -56, ['uc'=>$uid]); break;
            default:break;
        }
        if ($uid >0) {
            $init_arr = explode(',', $_G['setting']['initcredits']);
            $groupid  = $_G['setting']['regverify'] ? 8 : $_G['setting']['newusergroupid'];
            C::t('common_member')->insert($uid, $username, md5(random(10)), $email, $ip, $groupid, $init_arr);
            return !$doLogin
                ? Lev::responseMsg(1, '注册成功', ['uid'=>$uid])
                : static::doLogin($username, $password, 2592000, $questionid, $answer, $ip, 0);
        }
        return Lev::responseMsg(-50, '注册失败');
    }

    public static function doLogout() {
        global $_G;

        function_exists('clearcookies') || require_once libfile('function/member');

        clearcookies();
        $_G['groupid'] = $_G['member']['groupid'] = 7;
        $_G['uid'] = $_G['member']['uid'] = 0;
        $_G['username'] = $_G['member']['username'] = $_G['member']['password'] = '';

        if($_G['setting']['allowsynlogin']) {
            loaducenter();
            uc_user_synlogout();
        }

    }

    public static function pluginIden() {
        static $iden;
        return $iden = isset($iden) ? $iden : basename(dirname(__DIR__));
    }

    public static function stget($key, $iden = false) {
        global $_G;
        static $loaded;
        !$loaded && defined('IN_ADMINCP') && loadcache('plugin') && $loaded = 1;
        $iden === false && $iden = static::pluginIden();
        return isset($_G['cache']['plugin'][$iden][$key]) ? $_G['cache']['plugin'][$iden][$key] : '';
    }

    public static function getUsers($lists, $key = ['uid']) {
        $uids = empty($key) ? $lists : Lev::getArrayColumn($lists, $key, 1);
        return dzDBHelper::getUsers($uids);
    }

    public static function formhashValidation() {
        return Lev::csrfValidation();
    }

    public static function toRoute($pm = [], $scheme = true) {
        $pm[0] = '/plugin.php?id='.static::pluginIden().(!empty($pm[0]) ? ':'.$pm[0] : '');
        return Lev::toRoute($pm, $scheme);
    }

    public static function forumUrl() {
        return Lev::toRoute(['/forum.php']);
    }

    public static function viewThreadUrl($tid) {
        return Lev::toRoute(['/forum.php?mod=viewthread', 'tid'=>$tid]);
    }

    public static function myUrl() {
        return Lev::toRoute(['/home.php?mod=space']);
    }

    public static function scoretypes() {
        global $_G;
        $scoretypes = [];
        foreach ($_G['setting']['extcredits'] as $k => $v) {
            $scoretypes[$k] = dbHelper::getDataToCharset($v['title']);
        }
        return $scoretypes;
    }
    public static function scorename($scoreid) {
        global $_G;
        $scorename = $_G['setting']['extcredits'][$scoreid]['title'];
        return dbHelper::getDataToCharset($scorename);
    }

    public static function myScores($pre = '') {
        global $_G;

        $scores = dzDBHelper::getUserScore(Lev::$app['uid']);
        $myscores = [];
        foreach ($_G['setting']['extcredits'] as $k => $v) {
            $id = $k;
            $myscores[$id]['id']    = $id;
            $myscores[$id]['title'] = dbHelper::getDataToCharset($v['title']);
            $myscores[$id]['score'] = floatval($scores['extcredits'.$k]);
            if ($pre) {
                $id = $pre . $k;
                $myscores[$id]['id']    = $id;
                $myscores[$id]['title'] = dbHelper::getDataToCharset($v['title']);
                $myscores[$id]['score'] = floatval($scores['extcredits' . $k]);
            }
        }
        return $myscores;
    }

    public static function acscore($spend, $notice = '', $type = 0, $uid = 0, $title = '', $scoreArr = []) {
        $notice = dbHelper::setDataToCharset($notice);
        $title = dbHelper::setDataToCharset($title);

        $type = intval($type) ? intval($type) : static::stget('scoretype');
        $uid  = intval($uid) ? intval($uid) : Lev::$app['uid'];
        if ($uid >0 && intval($spend) && $type >0 && $type <9) {
            $score = dzDBHelper::getUserScore($uid);
            $upscore = $score['extcredits'.$type] + $spend;
            if ($upscore >=0) {
                ScoreHelper::setMyScores('='.$type, $upscore);
                $title = $title ? $title : $notice;
                $scoreArr[$type] = $spend;
                updatemembercount($uid, $scoreArr, TRUE, '', 210505, 0, $title, $notice);
                if ($spend >0) $spend = '+'.$spend;
                if ($notice) {
                    notification_add($uid, 'system', $title.' &raquo; '.dbHelper::setDataToCharset(self::scorename($type)).' '.$spend);
                }
                return TRUE;
            }
        }
        return false;
    }

    public static function uploadRealSrc($src) {
        return Lev::uploadRealSrc($src);
    }

    public static function cutImage($src, $width = 100, $height = 100, $type = 1) {
        $sourcesrc = str_replace(Lev::$aliases['@siteurl'], '', Lev::uploadRootSrc($src));
        $imagename = basename($sourcesrc);
        $path = '/thumb_'.$width.'_'.$height.'_'.$imagename;
        $fileroot  = dirname($sourcesrc);
        $rootsrc = $fileroot.$path;
        if (is_file($rootsrc)) {
            return dirname($src) . $path;
        }elseif ($type ==99) {
            return $src;
        }elseif (!in_array($type, array(1, 2))) {
            return $src;
        }
        require_once libfile('class/image');
        $image = new image();
        setglobal('setting/attachdir', rtrim($fileroot, '/').'/');
        $type = in_array($type, array(1, 2)) ? $type : 2;
        if($image->Thumb($sourcesrc, $path, $width, $height, $type)) {
            return dirname($src) . $path;
        }else {
            return $src;
        }
    }

    /**
     * @param $files
     * @param string $dir
     * @param int $isimg
     * @param array $ext
     * @param int $uploadsize
     * @param int $cutWidth
     * @param string $fileroot
     * @param string $dirtype
     * @return array|bool
     */
    public static function upload($files, $dir = '', $isimg = 1, $ext = array(), $uploadsize = 0, $cutWidth = 150, $fileroot = '', $dirtype = 'album') {//X2.5 up
        if (($msg = static::checkUploadError($files, $isimg, $uploadsize))) {
            return $msg;
        }
        if (empty($files) || !is_array($files)) {
            return Lev::responseMsg(-100, '请上传文件');
        }
        if (!$isimg) {
            $ext = $ext ? $ext : static::exts();
        }

        $dir = '/'.Lev::$app['iden'].($dir ? '/'.$dir.'/' : '/');
        !$fileroot && $fileroot = Lev::getAlias('@uploads');
        $fileroot .= $dir;
        dmkdir($fileroot);
        setglobal('setting/attachdir', $fileroot);//更改上传目录到$fileroot插件目录
        require_once libfile('discuz/upload', 'class');
        $upload = new discuz_upload();

        $dirtype = $upload->check_dir_type($dirtype);

        if($upload->init($files, $dirtype) && $upload->save(1)) {//print_r($upload->attach);
            if ($isimg && !$upload->attach['isimage']) {
                @chmod($upload->attach['target'], 0644);
                @unlink($upload->attach['target']);
                return Lev::responseMsg(-1105, '请上传一张图片');
            }elseif (!empty($ext) && !in_array(ltrim($upload->attach['ext'], '.'), $ext)) {
                @chmod($upload->attach['target'], 0644);
                @unlink($upload->attach['target']);
                return Lev::responseMsg(-1106, '上传文件必须是：'.implode(', ', $ext));
            }else{
                if (!$isimg && !in_array($upload->attach['ext'], array('zip', 'rar'))) {
                    rename($upload->attach['target'], $upload->attach['target'].= '.'.$upload->attach['ext']);
                    $upload->attach['attachment'] .= '.'.$upload->attach['ext'];
                }
                $upload = uploadsWidget::formatAttachName($upload);
                $src = $dir.$dirtype.'/'.$upload->attach['attachment'];
                if ($upload->attach['isimage'] && $files['size'] >1024000) {//超过1M图片自动裁剪成缩略图
                    ini_set('memory_limit', '-1'); //图片的裁剪非常占内存
                    $src = static::cutImage($src, $cutWidth, 0);
                }
                return Lev::responseMsg(1, '上传成功', ['src'=>$src, 'realSrc'=>static::uploadRealSrc($src), 'rootSrc'=>$upload->attach['target']]);
            }
        }
        return Lev::responseMsg(-1108, '保存失败', [$upload->errormessage(), $upload->attach]);
    }
    public static function checkUploadError($thumb, $isimage = 1, $uploadsize = 0) {
        if ($thumb['error']) {
            return Lev::responseMsg(-110, '文件上传失败！'.$thumb['error']);
        }elseif ($uploadsize && $thumb['size'] > $uploadsize * 1024) {
            $uploadsizestr = $uploadsize > 1024 ? round($uploadsize/1024, 2).'M' : $uploadsize.'KB';
            return Lev::responseMsg(-1101, '操作失败！上传文件超出限制【'.$uploadsizestr.'】');
        }elseif ($isimage && stripos($thumb['type'], 'image') === false) {
            return Lev::responseMsg(-1102, '操作失败！请上传图片文件！');
        }
        return false;
    }
    public static function exts() {
        return array();//['jpg', 'gif']
    }

}