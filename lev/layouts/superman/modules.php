<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-13 12:37
 *
 * 项目：upload  -  $  - settings.php
 *
 * 作者：liwei 
 */

echo \lev\base\Assetsv::animateCss(1);
//插件组件授权，非目录合并式
?>

<div class="page page-admin">
    <div class="navbar page-admin-navbar adminbar navbar-bgcolor-red">
        <div class="navbar-inner">
            <div class="left transl" style="transform: scale(.97)">
                <?=\lev\widgets\adminModulesNav\adminModulesNav::buttonHtm()?>
                <a class="link tooltip-init" href="javascript:window.history.back();" data-tooltip="后退">
                    <svg class="icon" aria-hidden="true"><use xlink:href="#fa-back"></use></svg>
                </a>
                <a class="link tooltip-init" href="javascript:window.location.reload();" data-tooltip="刷新">
                    <svg class="icon" aria-hidden="true"><use xlink:href="#fa-refresh"></use></svg>
                </a>
            </div>
            <div class="title">
                <?php echo Lev::$app['title']?>
                <tips class="scale9 inblk yellow">提示：关闭状态组件，不影响管理员访问</tips>
            </div>
            <div class="right">
                <a target="_blank" class="button button-fill color-black scale8 transr" href="<?php echo Lev::toReRoute(['superman/set-caches', 'id'=>APPVIDEN])?>">
                    更新缓存
                </a>
                <a _bk=1 target="_blank" class="button button-fill color-yellow scale8" href="<?php echo Lev::toCurrent(['r'=>'superman', 'iden'=>Lev::$app['iden'], 'cl'=>1])?>">
                    组件安装
                </a>
            </div>
        </div>

        <div class="subnavbar">
            <div class="buttons-row scale8 transl">
                <?php echo \lev\helpers\ModulesHelper::getAdminSubnavHtms()?>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="card data-listb">
            <div class="data-xtable">
                <table><thead><tr><th style="width:10px"></th>
                        <th class="wd120" style="padding-right:20px !important;">插件/组件名称</th>
                        <th class="label-cell">功能描述 <tips class="inblk scale9">【提示】组件默认关闭，新安装组件需要在此启用或更新才可以使用</tips></th>
                        <th class="tab-center wd60">状态</th>
                        <th class="numeric-cell wd60">更新/启用</th>
                        <th class="tab-center wd80">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($lists)):foreach ($lists as $v):?>
                        <tr>
                            <td></td>
                            <td>
                                <?php echo $v['identifier'] == Lev::$app['iden'] ? '<absx>主体</absx>' : '<absxk>组件</absxk>'?>
                                <a _bk=1 target=_blank href="<?php echo Lev::toReRoute(['/', 'id'=>$v['identifier']])?>">
                                    <?php echo $v['name']?>
                                    <svg class="icon color-gray"><use xlink:href="#fa-huoj"></use></svg>
                                </a>
                                <p class="date transl"><?php echo $v['version']?></p>
                            </td>

                            <td>
                                <div class="mud-navb buttons-row scale7 transl">
                                    <?php echo \lev\helpers\ModulesHelper::getAdminNavHtms($v)?>
                                </div>
                                <p class="date transl"><?php echo $v['descs']?></p>
                            </td>

                            <td class="tab-center">
                                <label class="label-switch scale8 color-green setStatus" opid="<?php echo $v['id']?>">
                                    <input type="checkbox" <?=$v['status']?'':'checked'?>>
                                    <div class="checkbox"></div>
                                </label>
                            </td>

                            <td class="numeric-cell">
                                <p class="date transr"><?php echo Lev::asRealTime($v['versiontime'])?></p>
                                <p class="date transr"><?php echo Lev::asRealTime($v['addtime'])?></p>
                            </td>
                            <td class="tab-center">
                                <?php if ($v['classdir']):?>
                                <div class="scale7 buttons-row">
                                    <?php if (is_file(\lev\helpers\ModulesHelper::getRouteFile($v['identifier'], $v['classdir'])) || \lev\helpers\ModulesHelper::checkNewConfig($v)):?>
                                    <a class="button-fill button color-red shake animated wdmin" href="<?php echo Lev::toReRoute(['superman/update-module', 'id'=>APPVIDEN, 'iden'=>$v['identifier'], 'classdir'=>$v['classdir']])?>">更新</a>
                                    <?php endif;?>
                                    <a class="button-fill button color-gray wdmin" href="<?php echo Lev::toReRoute(['superman/uninstall-module', 'id'=>APPVIDEN, 'iden'=>$v['identifier'], 'classdir'=>$v['classdir']])?>">卸载</a>
                                </div>
                                <?php else:?>
                                    <a _bk=1 target="_blank" class="button-fill button inblk scale7" href="<?php echo Lev::toCurrent(['r'=>'superman', 'iden'=>$v['identifier']])?>">
                                        详情
                                    </a>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach; endif; if (count($lists) == 1):?>
                        <tr><td colspan="22" class="tab-center">
                                <tips>没有安装组件</tips>
                                <a _bk=1 target="_blank" href="<?php echo Lev::toCurrent(['r'=>'superman', 'iden'=>Lev::$app['iden'], 'cl'=>1])?>">
                                    <absxg>前往应用中心看看</absxg>
                                </a>
                            </td>
                        </tr>
                    <?php endif;?>
                    </tbody>
                </table>
                <div class="card-footer">
                    <tips></tips>
                </div>
            </div>
        </div>

        <?php if ($lists = \lev\helpers\ModulesHelper::getInstallModules()):?>
        <div class="card data-listb">
            <div class="card-header">
                <span>
                    未启用组件
                </span>
            </div>
            <div class="data-xtable">
                <table><thead><tr><th style="width: 10px"></th>
                        <th class="wd120">插件/组件名称</th>
                        <th class="label-cell wd60">版本号</th>
                        <th class="label-cell">功能描述</th>
                        <th class="tab-center wd60">状态</th>
                        <th class="numeric-cell wd60">更新/启用</th>
                        <th class="tab-center wd80">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lists as $v):?>
                        <tr>
                            <td></td>
                            <td>
                                <absxk>组件</absxk><?php echo $v['name']?>
                            </td>
                            <td><p class="date transl"><?php echo $v['version']?></p></td>

                            <td>
                                <p class="date transl"><?php echo $v['descs']?></p>
                            </td>

                            <td class="tab-center">
                                <label class="label-switch scale8 color-green setStatus" opid="<?php echo $v['id']?>">
                                    <input type="checkbox" <?=$v['status']?'':'checked'?>>
                                    <div class="checkbox"></div>
                                </label>
                            </td>

                            <td class="numeric-cell">
                                <p class="date transr"><?php echo Lev::asRealTime($v['versiontime'])?></p>
                                <p class="date transr"><?php echo Lev::asRealTime($v['addtime'])?></p>
                            </td>
                            <td class="tab-center">
                                <p class="date"><?php echo $v['identifier']?></p>
                                <div class="scale7 buttons-row">
                                    <a class="button-fill button color-red" href="<?php echo Lev::toReRoute(['superman/install-module', 'id'=>APPVIDEN, 'iden'=>$v['identifier'], 'classdir'=>$v['classdir']])?>">启用</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <div class="card-footer">
                    <tips></tips>
                </div>
            </div>
        </div>
        <?php endif;?>

    </div>

</div>

