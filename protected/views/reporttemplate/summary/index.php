<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl('reporttemplate/edit', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            <li class="active"><a href="<?php echo $this->createUrl('reporttemplate/summary', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Summary Blocks'); ?></a></li>
        </ul>
    </div>
    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('reporttemplate/editsummary', array( 'id' => $template->id )) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Summary Block'); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($summaryBlocks) > 0): ?>
                <table class="table summary-list">
                    <tbody>
                        <tr>
                            <th class="summary-block"><?php echo Yii::t('app', 'Summary'); ?></th>
                            <th class="range"><?php echo Yii::t('app', 'Range'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($summaryBlocks as $summaryBlock): ?>
                            <tr data-id="<?php echo $summaryBlock->id; ?>" data-control-url="<?php echo $this->createUrl('reporttemplate/controlsummary'); ?>">
                                <td class="summary-block">
                                    <a href="<?php echo $this->createUrl('reporttemplate/editsummary', array( 'id' => $template->id, 'summary' => $summaryBlock->id )); ?>"><?php echo CHtml::encode($summaryBlock->localizedTitle); ?></a>
                                </td>
                                <td class="range">
                                    <?php echo sprintf('%.2f', $summaryBlock->rating_from); ?>
                                    ..
                                    <?php echo sprintf('%.2f', $summaryBlock->rating_to); ?>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $summaryBlock->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($p->pageCount > 1): ?>
                    <div class="pagination">
                        <ul>
                            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('reporttemplate/summary', array( 'id' => $template->id, 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                                <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                    <a href="<?php echo $this->createUrl('reporttemplate/summary', array( 'id' => $template->id, 'page' => $i )); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('reporttemplate/summary', array( 'id' => $template->id, 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No summary blocks yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>