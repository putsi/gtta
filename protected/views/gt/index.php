<div class="active-header">
    <div class="pull-right">
        <a class="btn" href="<?php echo $this->createUrl('gt/edit') ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Category'); ?></a>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($categories) > 0): ?>
                <table class="table category-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Category'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($categories as $category): ?>
                            <tr data-id="<?php echo $category->id; ?>" data-control-url="<?php echo $this->createUrl('gt/control'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('gt/view', array('id' => $category->id)); ?>"><?php echo CHtml::encode($category->localizedName); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $category->id; ?>, '<?php echo Yii::t('app', 'WARNING! ALL TYPES & MODULES WITHIN THIS CATEGORY WILL BE DELETED!'); ?>');"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($p->pageCount > 1): ?>
                    <div class="pagination">
                        <ul>
                            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('gt/index', array('page' => $p->prevPage ? $p->prevPage : $p->page)); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                                <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                    <a href="<?php echo $this->createUrl('gt/index', array('page' => $i)); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('gt/index', array('page' => $p->nextPage ? $p->nextPage : $p->page)); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No categories yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>