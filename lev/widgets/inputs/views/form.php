<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-13 21:51
 *
 * 项目：upload  -  $  - form.php
 *
 * 作者：liwei 
 */
use lev\widgets\inputs\inputsWidget;

/* @var $inputs array */
/* @var $values array */
/* @var $pre string */
?>

<?php foreach ($inputs as $v) :?>
    <div class="card-footer formFieldBox field-settingsmodel-<?php echo $v['inputname']?>">
        <div class="item-after"><div class="hint-block"><?php echo inputsWidget::replaceKeyword($v['placeholder'])?></div></div>
        <inpt class="item-input">
            <label class="control-label" for="settingsmodel-<?php echo $v['inputname']?>"><?php echo $v['title']?></label>
            <?php
            $v['inputvalue'] = isset($values[$v['inputname']]) ? $values[$v['inputname']] : $v['inputvalue'];
            echo inputsWidget::run(
                    $v['inputtype'],
                    inputsWidget::getPreInputname($v['inputname'], $pre),
                    $v['inputvalue'],
                    $v
            );
            ?>
        </inpt>
    </div>
<?php endforeach;?>
