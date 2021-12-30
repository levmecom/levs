<?php

namespace lev\helpers;

!defined('INLEV') && exit('Access Denied Lev');

use Lev;
use lev\base\Migrationv;
use lev\widgets\inputs\selectSearch;
use lev\widgets\inputs\tablesForm;

class BaseSettings extends LevSettingsModel
{

    public static function apiUrl() {
        return trim(trim(Lev::stget('apiDomain')), '/');
    }

    /**
     * 返回数据应放入html a标签的class属性中 例：class="{返回数据}"
     * @param $target
     * @param string $mySelfBtn
     * @return string
     * @see SettingsHelper::field_target()
     */
    public static function navTarget($target, $mySelfBtn = '') {
        if (strpos($mySelfBtn, '#') === 0 ) {
            $attr = ' mySelfBtn '.substr($mySelfBtn, 1);
        }else {
            switch ($target) {
                case 0  : $attr = ' openPP'; break;
                case 1  : $attr = '" target="_top" _bk="1'; break;
                case 2  : $attr = ' openPP" data-full="1'; break;
                case 3  : $attr = ' openziframescreen" hidetitle="1'; break;
                case 4  : $attr = '" target="_blank" _bk="1'; break;
                case 5  : $attr = ' is_ajax_a'; break;
                case 66 : $attr = ' openPP" clsname="c-'.md5($mySelfBtn); break;
                case 99 : $attr = ' mySelfBtn '.$mySelfBtn; break;
                default : $attr = '" target="'.$target; break;
            }
        }
        return $attr;
    }

    /**
     * @return array
     * @see SettingsHelper::navTarget()
     */
    public static function field_target() {
        return [
            0  => '适配弹窗',
            1  => '本页',
            2  => '底留缝弹窗',
            3  => '全屏弹窗',
            4  => '新窗口',
            5  => '切入（非APP页面无法显示）',
            66 => 'popupAjax',
            99 => '自定义JS按钮',
        ];
    }
    public static function setShowType() {
        return [
            0 => '图标和文字',
            1 => '文字',
            2 => '图标',
        ];
    }
    public static function setBtnColor() {
        return [
            '无色',
            'gray'      => '灰色',
            'black'     => '黑色',
            'yellow'    => '金黄色',
            'orange'    => '柑橘色',
            'red'       => '红色',
            'pink'      => '粉红色',
            'blue'      => '蓝色',
            'lightblue' => '亮蓝色',
            'green'     => '绿色',
            'white'     => '白色',
        ];
    }

    public static function getSelectOptions($key, $iden = false, $tablesFormKey = false) {
        $iden === false && $iden = Lev::$app['iden'];
        $info = static::findOne(['moduleidentifier'=>$iden, 'inputname'=>$key]);
        $info && $tablesFormKey !== false &&
        $info = tablesForm::tablesInputs($info['settings'])[$tablesFormKey];
        return $info ? selectSearch::getSelectItem($info) : [];
    }

    public static function setgroupids() {
        $groups = UserHelper::getGroups();
        $arr    = [0=>'空'];
        foreach ($groups as $v) {
            $arr[$v['groupid']] = $v['grouptitle'];
        }
        return $arr;
    }

    public static function settarget() {
        return static::field_target();
    }
    public static function settargets() {
        return static::field_target();
    }

    public static function getSlides() {
        return static::slidesFormat(Lev::stget('slides') ?: Lev::stget('slides', APPVIDEN));
    }

    public static function getToolbarNavs() {
        return static::tablesnavFormat(Lev::stget('toolbarNavs') ?: Lev::stget('toolbarNavs', APPVIDEN));
    }

    public static function getFooterNavs() {
        return static::tablesnavFormat(Lev::stget('footerNavs') ?: Lev::stget('footerNavs', APPVIDEN));
    }

    /**
     * 导航图标
     * @param $id
     * @param string $logoSrc
     * @param bool $lazy 是否延时加载图片图标
     * @return string
     */
    public static function navIcon($id, $logoSrc = '', $lazy = false) {
        if ($logoSrc) {
            if ($logoSrc[0] == '#') {
                return '<svg class="icon c-'.$id.'" aria-hidden="true"><use xlink:href="'.$logoSrc.'"></use></svg>';
            }
            $src = ($lazy ? 'data-' : '').'src="'. Lev::getAlias($logoSrc).'"';
            return '<img class="lazy c-'.$id.'" '.$src.'>';
        }
        return '';
    }

    /**
     * 二级导航类型设置格式化
     * @param $navs
     * @param bool $lazy
     * @param string $logoField
     * @return array
     */
    public static function tablesnavFormat($navs, $lazy = false, $logoField = false) {
        $navs && !is_array($navs) && $navs = unserialize($navs);
        $res = [];
        if ($navs) {
            $logoField === false && $logoField = 'logo';
            foreach ($navs as $k => $v) {
                if (!$v['status']) {
                    $v['_icon'] = static::navIcon($v['id'], $v[$logoField], $lazy);
                    $v['_target'] = static::navTarget($v['target'], $v['link']);
                    if ($v['target'] != 99) {
                        $v['link'] = Lev::toRoute([$v['link']]);
                        $v['_link'] = $v['link'] ? '" href="' . $v['link'] : '';
                    }else {
                        $v['_link'] = '';
                    }
                    !empty($v['cld__']) && $v['cld__'] = static::tablesnavFormat($v['cld__'], $lazy, $logoField);
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }

    /**
     * 幻灯片设置格式化
     * @param $imgArr
     * @param string $uploadKey
     * @return array
     */
    public static function slidesFormat($imgArr, $uploadKey = 'upload') {
        !is_array($imgArr) && $imgArr = unserialize($imgArr);
        $res = [];
        if ($imgArr) {
            foreach ($imgArr as $k => $v) {
                if (!$v['status']) {
                    $v['_src'] = $v[$uploadKey];
                    $v['_target'] = static::navTarget($v['target'], $v['link']);
                    if ($v['target'] != 99) {
                        $v['link'] = Lev::toRoute([$v['link']]);
                        $v['_link'] = $v['link'] ? '" href="' . $v['link'] : '';
                    }else {
                        $v['_link'] = '';
                    }
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }
}

class SettingsHelper extends BaseSettings {}