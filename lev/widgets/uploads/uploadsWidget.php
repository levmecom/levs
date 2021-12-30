<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-14 22:37
 *
 * 项目：upload  -  $  - uploadsWidget.php
 *
 * 作者：liwei 
 */


namespace lev\widgets\uploads;

use Lev;
use lev\base\Widgetv;
use lev\helpers\cacheFileHelpers;
use lev\helpers\SettingsHelper;
use lev\helpers\UploadHelper;
use lev\widgets\inputs\inputsWidget;
use lev\widgets\inputs\tablesForm;
use modules\levfm\table\LevfmForms;
use modules\levfm\widgets\inputs\inputsFormWidget;
use modules\levfm\widgets\inputs\inputtypes\slidesType;
use modules\levfm\widgets\inputs\inputtypes\tablesFormType;

!defined('INLEV') && exit('Access Denied LEV');

class uploadsWidget extends Widgetv
{

    public static function run($inputName = 'inputName', $uploadInput = 'upload', $inputValue = '', $placeHolder = '上传结果', $uploadUrl = '', $show = false, $jsinit = true)
    {
        return parent::render($show, __DIR__ . '/views/run.php', [
            'jsinit' => $jsinit,
            'inputValue' => $inputValue,
            'inputName' => $inputName,
            'placeHolder' => $placeHolder,
            'uploadInput' => $uploadInput,
            'uploadUrl' => $uploadUrl ?: Lev::toCurrent(['r'=>'upload/settings', 'input'=>$uploadInput, 'iden'=>Lev::$app['iden'], 'identifier'=>APPVIDEN, 'id'=>APPVIDEN, 'inajax'=>1]),
        ]);
    }

    /**
     * 仅允许上传图片
     * @param string $inputName
     * @param string $uploadInput
     * @param string $inputValue
     * @param string $placeHolder
     * @param bool $show
     * @return string|void
     */
    public static function image($inputName = 'inputName', $uploadInput = 'upload', $inputValue = '', $placeHolder = '上传结果', $show = false)
    {
        $uploadUrl = Lev::toCurrent(['r'=>'upload/image', 'input'=>$uploadInput, 'iden'=>Lev::$app['iden'], 'identifier'=>APPVIDEN, 'id'=>APPVIDEN, 'inajax'=>1]);
        return static::run($inputName, $uploadInput, $inputValue, $placeHolder, $uploadUrl, $show);
    }

    /**
     * 上传指定扩展附件
     * @param string $inputName
     * @param string $uploadInput
     * @param string $inputValue
     * @param string $placeHolder
     * @param int $isform 是否来自表单
     * @param bool $show
     * @return string|void
     */
    public static function attach($inputName = 'inputName', $uploadInput = 'upload', $inputValue = '', $placeHolder = '上传结果', $isform = 0, $fieldId = 0, $show = false, $jsinit = true)
    {
        $uploadUrl = Lev::toCurrent(['r'=>'upload/attach', 'input'=>$uploadInput, 'iden'=>Lev::$app['iden'], 'identifier'=>APPVIDEN, 'id'=>APPVIDEN, 'isform'=>$isform, 'fdid'=>$fieldId, 'inajax'=>1]);
        return static::run($inputName, $uploadInput, $inputValue, $placeHolder, $uploadUrl, $show, $jsinit);
    }


    public static function formatUploadSettings($settings, $isimg = 1) {
        $sets = Lev::explodev($settings, '|', true, false);
        $size = static::formatUploadSize($sets[0]);
        $exts = Lev::explodev(str_replace('=', ',', $sets[1]), ',');
        $wds = isset($sets) && $isimg ? Lev::explodev($sets[2], 'x', 'floatval') : [0,0];
        return [$size, $exts, $wds];
    }

    /**
     * 返回KB单位的数字
     * @param $sizestr
     * @return float|int
     */
    public static function formatUploadSize($sizestr) {
        $size = floatval($sizestr);
        return $size ? (stripos($sizestr, 'M') !== false ? $size*1024 : $size) : 0;
    }

    public static function uploadInputExt($field, $dir = 'attach') {
        $fdId = floatval(Lev::GPv('fdid'));
        if (Lev::GPv('isform')) {
            $fdInfo = Lev::actionObjectMethodIden('levfm', 'modules\levfm\table\LevfmFields', [['id'=>$fdId]], 'findOne');
            if (empty($fdInfo)) {
                return Lev::responseMsg(-970, '抱歉，上传类型不存在，上传失败');
            }
            $fdInfo['inputname'] = $fdInfo['inputname'] ?: LevfmForms::tableFieldname($fdInfo['id']);
            if (substr($fdInfo['inputname'], -strlen($field)) != $field) {
                if ($fdInfo['inputtype'] == 'slides') {
                    $tableInputs = slidesType::myInputs();
                    $tableInputs[$field]['settings'] = $fdInfo['settings'];
                }else {
                    $tableInputs = tablesFormType::tablesInputs($fdInfo['settings']);
                }
                if (empty($tableInputs[$field])) {
                    return Lev::responseMsg(-98, '抱歉，上传类型不匹配');
                }
                $fdInfo = $tableInputs[$field];
            }
            $isImg = $fdInfo['inputtype'] == 'uploadimg';
            if (cacheFileHelpers::isSerializeStr($fdInfo['settings'])) {
                $fdInfo['settings'] = inputsFormWidget::formatUploadSettings($fdInfo['settings'], $isImg);
            }
        }else {
            $fdInfo = SettingsHelper::findOne(['id' => $fdId]);
            if (empty($fdInfo)) {
                return Lev::responseMsg(-97, '抱歉，上传类型不存在，上传失败');
            }
            $isImg = $fdInfo['inputtype'] == 'uploadimg';
            if ($fdInfo['inputname'] != $field) {
                if (inputsWidget::checkTablesField($fdInfo['inputtype'])) {
                    $tableInputs = tablesForm::tablesInputs($fdInfo['settings']);
                    if (empty($tableInputs[$field])) {
                        return Lev::responseMsg(-983, '抱歉，上传类型不匹配983');
                    }
                    empty($tableInputs[$field]['settings']) && $fdInfo['settings'] = '5M|jpg,png,jpeg,bmp,gif';
                }else {
                    return Lev::responseMsg(-982, '抱歉，上传类型不匹配982');
                }
            }
        }
        $sets = static::formatUploadSettings($fdInfo['settings'], $isImg);

        if (!$isImg && empty($sets[1])) {
            return Lev::responseMsg(-98, '抱歉，未指定要上传附件扩展，上传失败', [$tableInputs,$fdInfo]);
        }
        $msg = UploadHelper::upload($_FILES[$field], $dir.($isImg ? '/imgs' : ''), $isImg, $sets[1], $sets[0]);
        if ($msg && $msg['status'] <0) {
            return $msg;
        }
        if ($isImg && ($sets[2][0] || $sets[2][1])) {
            $w = $sets[2][0];
            $h = $sets[2][1];
            $imginfo = getimagesize($msg['rootSrc']);
            if ($imginfo[0] < $w || $imginfo[1] < $h) {
                unlink($msg['rootSrc']);
                if ($w == $h) {
                    return Lev::responseMsg(-12, '图片宽高必须相等且>=' . $w . 'px');
                }
                return Lev::responseMsg(-13, '图片宽度必须>=' . $w . 'px且高度必须>=' . $h . 'px');
            }
        }
        if (!Lev::$app['isAdmin']) unset($msg['rootSrc']);
        return $msg;
    }

    /**
     * 将上传文件大小加入附件名称
     * @param $upload
     * @return mixed
     */
    public static function formatAttachName($upload)
    {
        $arr = explode('.', $upload->attach['target']);
        $endKey = count($arr) - 2;
        $name = '.size_'.$upload->attach['size'];
        if ($upload->attach['isimage']) {
            $imginfo = getimagesize($upload->attach['target']);
            $name .= '_'.$imginfo[0].'_'.$imginfo[1];
        }
        $arr[$endKey] .= $name;
        $target = implode('.', $arr);
        if (rename($upload->attach['target'], $target)) {
            $upload->attach['target'] = $target;
            $upload->attach['attachment'] = dirname($upload->attach['attachment']) . '/' . basename($target);
        }
        return $upload;
    }
    public static function formatAttachNameGet($src) {
        $result = [0,0,0];
        $naarr = explode('.', basename($src));
        if (!empty($naarr[$key = count($naarr) - 2])) {
            $arr = explode('_', $naarr[$key]);
            if ($arr[0] == 'size') {
                $result[0] = $arr[1];
                if (isset($arr[3])) {
                    if (strpos($naarr[0], 'thumb_') === 0) {
                        $thumb = explode('_', $naarr[0]);
                        $result[1] = $thumb[1];
                        $result[2] = round($thumb[1]/$arr[2] * $arr[3], 2);
                    }else {
                        $result[1] = $arr[2];
                        $result[2] = $arr[3];
                    }
                }
            }
        }
        return $result;
    }
}











