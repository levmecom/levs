<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-13 21:46
 *
 * 项目：upload  -  $  - settings.php
 *
 * 作者：liwei 
 */

/* @var $inputs array */

use lev\widgets\inputs\inputsWidget;

?>

<?php foreach ($inputs as $v) :?>
    <div class="card-footer formFieldBox field-settingsmodel-<?php echo $v['inputname']?>">
        <div class="hint-block"><?php echo inputsWidget::replaceKeyword($v['placeholder'])?></div>
        <inpt class="item-input">
            <label class="control-label" for="settingsmodel-<?php echo $v['inputname']?>"><?php echo $v['title']?></label>
            <?php echo inputsWidget::run($v['inputtype'], 'settings['.$v['inputname'].']', $v['inputvalue'], $v); ?>
        </inpt>
    </div>
<?php endforeach;?>

