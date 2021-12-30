<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-09-09 14:36
 *
 * 项目：rm  -  $  - adminModulesNav.php
 *
 * 作者：liwei 
 */

namespace lev\widgets\adminModulesNav;

!defined('INLEV') && exit('Access Denied LEV');

use Lev;
use lev\base\Widgetv;
use lev\helpers\cacheFileHelpers;
use lev\helpers\ModulesHelper;
use lev\helpers\UrlHelper;

class adminModulesNav extends Widgetv
{
    public static $topQuickNavNum = 7;

    public static function setBtn() {
        return Lev::$app['isAdmin'] && !defined('INADMINLEV') ?
            '<a href="'.Lev::toReRoute(['superman/settings', 'id'=>APPVIDEN, 'iden'=>Lev::$app['iden']]).'" target="_blank" style="position:fixed;right: 0;bottom: 55px;z-index: 10000;background: rgba(0,0,0,0.5)" class="button scale6 button-fill color-black admin-pg-set">设置</a>' : '';
    }

    public static function buttonHtm() {
        Lev::$app['panelHtm'] .= static::panel();
        $quickNav = static::getQuickNav();
        $as = '<a class="link icon-only toLevStoreFormSubmit animated heartBeat wd40" title="模块商城"><svg class="icon" aria-hidden="true" style="color: yellow;font-size: 24px;"><use xlink:href="#fa-shop"></use></svg><i class="dhua_gif_bg bgx sz50"></i></a>';
        $as.= '<a class="link icon-only" title="网站首页" target="_blank" _bk="1" href="'.UrlHelper::home().'"><svg class="icon"><use xlink:href="#fa-home"></use></svg></a>';
        $as.= '<a class="link icon-only" title="模块管理" href="'.UrlHelper::adminModules().'"><svg class="icon"><use xlink:href="#fa-mud"></use></svg></a>';
        if ($quickNav) {
            $quickNav = array_slice($quickNav, 0, static::$topQuickNavNum);
            foreach ($quickNav as $v) {
                $name = $v['icon'] ? '<svg class="icon" aria-hidden="true"><use xlink:href="#'.$v['icon'].'"></use></svg>' : Lev::cutString($v['title'], 2, '');
                $as .= '<a class="link icon-only" href="'.$v['link'].'" title="'.$v['title'].'">'.$name.'</a>';
            }
        }
        return '<a class="link icon-only wd30 open-panel" title="后台导航">
                    <svg class="icon" aria-hidden="true"><use xlink:href="#fa-bars"></use></svg>
                </a>'.$as;
    }

    public static function btnsrowHtm($btns, $attr = 'scale7 transl') {
        $htms = '<div class="btnsrow-1 buttons-row '.$attr.'"><div class="wd30 nowrap flex-box">';
        foreach ($btns as $v) {
            $htms .= '<a href="' . $v['link'] . '" class="button button-fill '.$v['attr'].'">'.$v['name'].'</a>';
        }
        return $htms.'</div>&nbsp;</div>';
    }

    public static function panel ($show = false) {
        static $once;
        if (isset($once)) return ''; $once = 1;

        $barsNav = ModulesHelper::findAll(1, '', ['displayorder ASC, uptime DESC'], 'name,identifier');

        return static::render($show, __DIR__ . '/views/run.php', [
            'iden' => Lev::GPv('iden'),
            'barsNav' => $barsNav,
            'quickNav'=> static::getQuickNav(),
        ]);

    }

    public static function updateQuickNav($link, $icon, $title)
    {
        $data = static::getQuickNav();
        $key = md5($link);
        if ($icon === null) {
            unset($data[$key]);
        }else {
            $data[$key] = ['link' => $link, 'icon' => $icon, 'title' => $title];
        }
        static::opQuickNav($data);
        return $data;
    }
    public static function getQuickNav() {
        return ($data = static::opQuickNav()) && is_array($data) ? $data : [];
    }
    public static function opQuickNav($value = null) {
        $cacheKey = 'admin-quick-nav/1';
        return $value === null ? cacheFileHelpers::getc($cacheKey, false) : cacheFileHelpers::setc($cacheKey, $value);
    }

}