<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl("check/viewcontrol", array("id" => $category->id, "control" => $control->id)); ?>"><?php echo Yii::t("app", "View"); ?></a></li>
            <li><a href="<?php echo $this->createUrl("check/editcontrol", array("id" => $category->id, "control" => $control->id)); ?>"><?php echo Yii::t("app", "Edit"); ?></a></li>
            <li><a href="<?php echo $this->createUrl("check/sharecontrol", array("id" => $category->id, "control" => $control->id)); ?>"><?php echo Yii::t("app", "Share"); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id )) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Check'); ?></a>&nbsp;
        <a class="btn" href="<?php echo $this->createUrl('check/copycheck', array( 'id' => $category->id, 'control' => $control->id )) ?>"><i class="icon icon-retweet"></i> <?php echo Yii::t('app', 'Copy Check'); ?></a>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($checks) > 0): ?>
                <table class="table check-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Check'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($checks as $check): ?>
                            <tr data-id="<?php echo $check->id; ?>" data-control-url="<?php echo $this->createUrl('check/control/check/control'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo CHtml::encode($check->localizedName); ?></a>

                                    <?php if ($check->automated): ?>
                                        <i class="icon-cog" title="<?php echo Yii::t('app', 'Automated'); ?>"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="#up" title="<?php echo Yii::t('app', 'Move Up'); ?>" onclick="system.control.up(<?php echo $check->id; ?>);"><i class="icon icon-arrow-up"></i></a>
                                    <a href="#down" title="<?php echo Yii::t('app', 'Move Down'); ?>" onclick="system.control.down(<?php echo $check->id; ?>);"><i class="icon icon-arrow-down"></i></a>
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $check->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'check/viewcontrol', 'params' => array('id' => $category->id, 'control' => $control->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No checks yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
