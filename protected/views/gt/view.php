<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl('gt/view', array('id' => $category->id)); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('gt/edit', array('id' => $category->id)); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('gt/edittype', array('id' => $category->id)) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Type'); ?></a>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($types) > 0): ?>
                <table class="table category-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Type'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($types as $type): ?>
                            <tr data-id="<?php echo $type->id; ?>" data-control-url="<?php echo $this->createUrl('gt/controltype'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('gt/viewtype', array('id' => $category->id, 'type' => $type->id)); ?>"><?php echo CHtml::encode($type->localizedName); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $type->id; ?>, '<?php echo Yii::t('app', 'WARNING! ALL MODULES WITHIN THIS TYPE WILL BE DELETED!'); ?>');"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'gt/view', 'params' => array('id' => $category->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No types yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
