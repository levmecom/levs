<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-29 15:34
 *
 * 项目：upload  -  $  - ScoreHelper.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

use Lev;
use lev\base\Modulesv;

!defined('INLEV') && exit('Access Denied LEV');


class ScoreHelper
{

    public static function svgScoreIcon($key) {
        $icon = static::scoreIcon($key);
        return '<svg class="icon sc-'.$icon.'" aria-hidden="true"><use xlink:href="#fa-'.$icon.'"></use></svg>';
    }
    public static function scoreIcon($key) {
        $icons = [
            '=1' => 'jyz',
            '=2' => 'copper',
            '=3' => 'exp',
            '1' => 'jyz',
            '2' => 'copper',
            '3' => 'exp',
            'cnymoney' => 'cnymoney',
            'vcoin' => 'vcoin',
            'money' => 'money',
            'diamond' => 'diamond',
            'gold' => 'gold',
            'coins' => 'jb',
            'moneys' => 'rmb',
        ];
        return isset($icons[$key]) ? $icons[$key] : 'score';
    }

    public static function scoretypes() {
        isset(Lev::$app['scoretypes']) || Lev::$app['scoretypes'] = [];
        return Lev::$app['scoretypes'];
    }

    public static function scoretypesyy($tip = '（论坛）') {
        $scoretypes = Lev::actionObjectMethodIden('levyy', 'modules\levyy\table\userHelper', [], 'wealths') ?: [];
        $scoretype = static::scoretypes();
        foreach ($scoretype as $k => $name) {
            $scoretypes['='.$k] = $name . $tip;
        }
        return $scoretypes;
    }

    public static function scorenamex($scoreid, $tip = '') {
        $scoretypes = static::scoretypesyy($tip);
        $scoretypes+= static::scoretypes();
        return Lev::arrv($scoreid, $scoretypes);
    }

    public static function scorename($scoreid, $yy = 0, $tip = '') {
        $scoretypes = $yy ? static::scoretypesyy($tip) : static::scoretypes();
        return Lev::arrv($scoreid, $scoretypes);
    }

    public static function acscoreUse($spend, $notice = '', $scoretype = '', $uid = 0, $title = '', $scoreArr = []) {
        if (!static::acscore($spend, $notice, $scoretype, $uid, $title, $scoreArr)) {
            return Lev::responseMsg(-1041, '抱歉【'.static::scorename($scoretype).'】积分不足');
        }
        $tip = static::scorename($scoretype) . ($spend >0 ? ' <b class=yellow> +'.$spend : ' <b class=gray> '.$spend).'</b> ';
        return Lev::responseMsg(1, $tip);
    }
    public static function acscore($spend, $notice = '', $type = 0, $uid = 0, $title = '', $scoreArr = []) {
        return Lev::getDB()->acscore($spend, $notice, $type, $uid, $title, $scoreArr);
    }
    public static function acscores($uid, $scoresarr, $logTitle = '', $logDescs = '', $safeFlag = 0) {
        foreach ($scoresarr as $scoreid => $score) {
            break;
        }
        return static::acscoreUse($score, $logTitle, $scoreid, $uid, $logDescs, $scoresarr);
    }

    public static function setMyScores($scoreid, $scoreTotal) {
        empty(Lev::$app['myScores']) && static::myScores('=');
        Lev::$app['myScores'][$scoreid]['score'] = $scoreTotal;
        Lev::$app['myScores'][ltrim($scoreid, '=')]['score'] = $scoreTotal;
    }

    public static function myScores($pre = '', $force = false) {
        if ($force || empty(Lev::$app['myScores'])) {
            $scores = Lev::getDB()->myScores($pre);
            Lev::$app['myScores'] = $scores + static::myWealth();
        }
        return Lev::$app['myScores'];
    }

    public static function myWealth() {
        if (!Modulesv::isInstallModule('levyy')) {
            return [];
        }
        return Lev::actionObjectMethod('modules\levyy\table\userHelper', [], 'myWealth') ?: [];
    }
    public static function useWealth($uid, $wealthArr, $ignore = false, $descs = '') {
        if (!Modulesv::isInstallModule('levyy')) {
            return Lev::responseMsg(-2200, '抱歉，请先安装【来赚钱插件】');
        }
        //return Lev::actionObjectMethod('modules\levyy\table\userHelper', [$uid, $wealthArr, $ignore, $descs], 'useWealth');
        return \modules\levyy\table\userHelper::useWealth($uid, $wealthArr, $ignore, $descs);
    }

    public static function acscoreUses($spend, $notice = '', $scoretype = '', $uid = null, $title = '', $scoreArr = []) {
        $uid === null && $uid = Lev::$app['uid'];
        if ($key = self::isSysscore($scoretype)) {
            $msg = static::acscoreUse($spend, $notice, $key, $uid, $title, $scoreArr);
        }else {
            $scoreArr[$scoretype] = $spend;
            $msg = static::useWealth($uid, $scoreArr, true, $notice.$title);
        }
        return $msg;
    }

    public static function isSysscore($key) {
        return strpos($key, '=') === 0 ? substr($key, 1) : '';
    }

}