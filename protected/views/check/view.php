<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl('check/view', array( 'id' => $category->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('check/edit', array( 'id' => $category->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
        </ul>
    </div>
    <div class="pull-right buttons">
        <button class="btn" onclick="location.href='<?php echo $this->createUrl('check/editcheck', array( 'id' => $category->id )) ?>';"><?php echo Yii::t('app', 'New Check'); ?></button>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($checks) > 0): ?>
                <table class="table check-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Name'); ?></th>
                            <th class="automated"><?php echo Yii::t('app', 'Automated'); ?></th>
                            <th class="advanced"><?php echo Yii::t('app', 'Advanced'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($checks as $check): ?>
                            <tr>
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('check/editcheck', array( 'id' => $category->id, 'check' => $check->id )); ?>"><?php echo CHtml::encode($check->localizedName); ?></a>
                                </td>
                                <td class="automated">
                                    <?php if ($check->automated): ?>
                                        <i class="icon-ok"></i>
                                    <?php else: ?>
                                        <i class="icon-minus"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="advanced">
                                    <?php if ($check->advanced): ?>
                                        <i class="icon-ok"></i>
                                    <?php else: ?>
                                        <i class="icon-minus"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="check.del(<?php echo $check->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="pagination">
                    <ul>
                        <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('check/view', array( 'id' => $category->id, 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                        <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                            <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                <a href="<?php echo $this->createUrl('check/view', array( 'id' => $category->id, 'page' => $i )); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('check/view', array( 'id' => $category->id, 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <?php echo Yii::t('app', 'No checks yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
