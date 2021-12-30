<?php

/* @var $tourl array|string */
/* @var $referer string */
/* @var $timeout integer */

$tourlhtm = '';
if (is_array($tourl)) {
    foreach ($tourl as $_name => $_url) {
        $tourlhtm.= '<div class="item-after">
                       <a href="'.$_url.'" onclick="return showIconLoader(this)" class="button button-active">'.$_name.'</a>
                     </div>';
    }
}elseif ($name == 'submit') {
    $tourlhtm = '<div class="item-after">
                    <a href="'.$tourl.'" onclick="return showIconLoader(this)" class="button button-active">确定</a>
                 </div>';
}elseif ($tourl) {
    $tourlhtm = '<div class="item-after">
                    <a href="'.$tourl.'" class="button button-active">前往</a>
                 </div>';
}

!$title && $title = Lev::$app['title'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>提示 - <?php echo $title?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">

    <style>
        body {background: #fff !important;}
        .list.block {max-width: 760px;margin:10px auto;}
        .alert {padding: 15px;margin-bottom: 20px;border: 1px solid transparent;border-radius: 4px;word-wrap: break-word;word-break: break-all;white-space: pre-line;}
        .button-box .item-after + .item-after {margin-left: 15px;}
        .button-box .item-after + .item-after {margin-left: 15px;}
        .button {text-decoration: none;text-align: center;display: block;color: #fff;padding: 2px 10px;border-radius: 5px;font-size: 14px;background: gray;}
        .item-content.button-box {display: flex;}
        .item-after {margin-left: auto;}
        .button.button-active {background: #007aff;}
        .alert-success {color: #3c763d;background-color: #dff0d8;border-color: #d6e9c6;}
        .alert-error, .alert-danger {color: #a94442;background-color: #f2dede;border-color: #ebccd1;}
        .alert-info {color: #31708f;background-color: #d9edf7;border-color: #bce8f1;}
        .alert-warning, .alert-submit {color: #8a6d3b;background-color: #fcf8e3;border-color: #faebcc;}
    </style>
    <?=\lev\base\Assetsv::Jquery(1)?>

</head>
<body id="messagev">

<div class="block list">

    <div class="block"><h3 style="font-size: 17px"><?php echo $title?></h3></div>

    <div class="block showMsgBox">
        <div class="alert alert-<?php echo $name = (isset($name) && $name) ? $name : 'danger'?>" >
            <?php echo isset($message) && $message ? $message : '操作成功！' ?><i id="timeoutbox"></i>
        </div>
    </div>

    <div class="list block">
        <div class="item-content button-box">
            <?php if ($name == 'submit') :?>
            <?php echo $tourlhtm?>
                <div class="item-after">
                    <a href="<?php echo Lev::$app['referer']?>" class="button button-fill color-gray">取消</a>
                </div>
            <?php else : ?>
                <div class="item-after">
                    <a href="<?php echo \lev\helpers\UrlHelper::my() ?>" class="button" style="background: firebrick">我的</a>
                </div>
                <div class="item-after">
                    <a href="<?php echo \lev\helpers\UrlHelper::homeMud() ?>" class="button button-active">首页</a>
                </div>
                <div class="item-after">
                    <a href="javascript:window.history.back(-1);" class="button button-fill color-gray">后退</a>
                </div>
                <div class="item-after">
                    <a href="<?php echo $referer = $referer ?: Lev::$app['referer']?>" class="button button-fill color-gray">返回</a>
                </div>
            <?php echo $tourlhtm?>
            <?php endif;?>
        </div>
    </div>
</div>

<script>
    if (typeof showIconLoader != 'function') {
        function showIconLoader(obj) {
            if (obj.innerHTML == '正在执行，请稍候...') return false;
            obj.innerHTML = '正在执行，请稍候...';
            obj.setAttribute('class', 'button');
        }
    }
</script>

<?php if ($timeout >0) :?>

    <script>
        var _referer = '<?php echo $referer?>';
        if (_referer != window.location.href) {
            window.setTimeout(function () {
                window.location = _referer;
            }, <?php echo $timeout * 1000?>);

            function timerSec(sec) {
                if (sec > 0) {
                    window.setTimeout(function () {
                        timerSec(sec - 1);
                    }, 1000);
                }
                var showsec = sec > 0 ? sec : 0;
                document.getElementById('timeoutbox').innerHTML = sec + '秒后跳转';
            }

            timerSec(<?php echo $timeout?>);
        }
    </script>

<?php endif;?>
</body>
</html>