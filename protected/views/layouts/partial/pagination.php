<div class="pagination">
    <?php if ($p->pageCount > 1): ?>
        <ul>
            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl($url, array_merge($params, array('page' => $p->prevPage ? $p->prevPage : $p->page))); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>

            <li <?php if ($p->page == 1) echo 'class="active"'; ?>>
                <a href="<?php echo $this->createUrl($url, array_merge($params, array('page' => 1))); ?>">1</a>
            </li>

            <?php if ($p->page - 1 > 4): ?>
                <li class="disabled"><a href="#">...</a></li>
            <?php endif; ?>

            <?php for ($i = $p->page - 3; $i < $p->page; $i++): ?>
                <?php if ($i <= 1) continue; ?>
                <li><a href="<?php echo $this->createUrl($url, array_merge($params, array('page' => $i))); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>

            <?php if ($p->page > 1): ?>
                <li class="active"><a href="<?php echo $this->createUrl($url, array_merge($params, array('page' => $p->page))); ?>"><?php echo $p->page; ?></a></li>
            <?php endif; ?>

            <?php for ($i = $p->page + 1; $i < $p->page + 4; $i++): ?>
                <?php if ($i > $p->pageCount) continue; ?>
                <li><a href="<?php echo $this->createUrl($url, array_merge($params, array('page' => $i))); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>

            <?php if ($p->page + 4 < $p->pageCount): ?>
                <li class="disabled"><a href="#">...</a></li>
            <?php endif; ?>

            <?php if ($p->page + 4 <= $p->pageCount): ?>
                <li>
                    <a href="<?php echo $this->createUrl($url, array_merge($params, array('page' => $p->pageCount))); ?>"><?php echo $p->pageCount; ?></a>
                </li>
            <?php endif; ?>

            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl($url, array_merge($params, array('page' => $p->nextPage ? $p->nextPage : $p->page))); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
        </ul>
    <?php endif; ?>

    <form class="pagination-item-count">
        <div class="control-group">
            <div class="controls">
                <select class="input-medium" onchange="system.paginator.itemCountChange($(this).val(), $(this).data('url'))" data-url="<?= $this->createUrl($url, array_merge($params, array('page' => 1))); ?>">
                    <?php foreach ([20, 50, 100, -1] as $limit): ?>
                        <option id="paginator_item_count" value="<?= $limit ?>" <?php if ($limit == $this->entriesPerPage) echo 'selected'; ?>><?= $limit == -1 ? Yii::t("app", "All") : $limit; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>
</div>
