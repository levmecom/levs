<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-15 12:41
 *
 * 项目：rm  -  $  - UploadHelper.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

use Lev;
use lev\base\Imagev;
use lev\base\Uploadv;
use lev\widgets\uploads\uploadsWidget;

!defined('INLEV') && exit('Access Denied LEV');


class UploadHelper extends Uploadv
{

    /**
     * @param $files
     * @param string $dir
     * @param int $isimg
     * @param array $ext
     * @param int $uploadsize KB
     * @param int $cutWidth
     * @param string $fileroot
     * @param string $dirtype
     * @return array|bool
     */
    public static function upload($files, $dir = '', $isimg = 1, $ext = [], $uploadsize = 0, $cutWidth = 150, $fileroot = '', $dirtype = 'album') {
        //return discuzHelper::upload($files, $dir, $isimg, $ext, $uploadsize, $cutWidth, $fileroot, $dirtype);
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
        cacheFileHelpers::mkdirv($fileroot);
        Uploadv::$uploadDir = $fileroot;
        $upload = new Uploadv();

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
                return Lev::responseMsg(1, '上传成功', ['src'=>$src, 'realSrc'=>Lev::uploadRealSrc($src), 'rootSrc'=>$upload->attach['target']]);
            }
        }
        return Lev::responseMsg(-1108, '保存失败', [$upload->errormessage(), $upload->attach]);
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

        $image = new Imagev();
        $image::$fileroot = rtrim($fileroot, '/').'/';
        $type = in_array($type, array(1, 2)) ? $type : 2;
        if($image->Thumb($sourcesrc, $path, $width, $height, $type)) {
            return dirname($src) . $path;
        }else {
            return $src;
        }
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

    private static function exts()
    {
        return [];//['jpg', 'gif']
    }
}