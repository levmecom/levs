<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-04-25 22:18
 *
 * 项目：upload  -  $  - run.php
 *
 * 作者：liwei 
 */

?>

<style>
    .slides-input-<?=$inputname?> .swiper-slide {height: <?=$height?>px;max-width: 100%}
    .slides-input-<?=$inputname?> .swiper-slide img {height:<?=$height?>px;max-width: 100%;max-height: 100%;}
    .slides-input-<?=$inputname?> .swiper-slide a {align-items: center;display: flex;text-align: center;justify-content: center;height: 100%;background: rgba(0,0,0,0.31);}
</style>

<div class="slides-input-<?=$inputname?>">
    <div class="swiper-container slides-swiper">
        <div class="swiper-wrapper">
            <?php foreach ($slidesArr as $v): ?>
                <div class="swiper-slide">
                    <a class="<?=$v['_target'],$v['_link']?>"><img class="lazy" data-src="<?=$v['_src']?>"></a>
                </div>
            <?php endforeach;?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>

<script>
    jQuery(function () {
        window.setTimeout(function () { Levme.swiper('<?=$inputname?>'); }, 100);
    });
</script>

