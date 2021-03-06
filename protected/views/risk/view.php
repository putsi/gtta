<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl('risk/view', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('risk/edit', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('risk/editcategory', array( 'id' => $template->id )) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Category'); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($risks) > 0): ?>
                <table class="table risk-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Category'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($risks as $risk): ?>
                            <tr data-id="<?php echo $risk->id; ?>" data-control-url="<?php echo $this->createUrl('risk/controlcategory', array( 'id' => $template->id )); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('risk/editcategory', array( 'id' => $template->id, 'category' => $risk->id )); ?>"><?php echo CHtml::encode($risk->localizedName); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $risk->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'risk/view', 'params' => array('id' => $template->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No categories yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
