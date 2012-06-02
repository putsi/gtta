<?php if (User::checkRole(User::ROLE_ADMIN)): ?>
    <div class="pull-right">
        <button class="btn" onclick="location.href='<?php echo $this->createUrl('client/edit') ?>';"><?php echo Yii::t('app', 'New Client'); ?></button>
    </div>
<?php endif; ?>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($clients) > 0): ?>
                <table class="table client-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Name'); ?></th>
                            <th class="projects"><?php echo Yii::t('app', 'Projects'); ?></th>
                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <th class="actions">&nbsp;</th>
                            <?php endif; ?>
                        </tr>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('client/view', array( 'id' => $client->id )); ?>"><?php echo CHtml::encode($client->name); ?></a>
                                </td>
                                <td class="projects">
                                    <?php if ($client->projectCount): ?>
                                        <?php echo $client->projectCount ?>
                                    <?php else: ?>
                                        0
                                    <?php endif; ?>
                                </td>
                                <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                    <td class="actions">
                                        <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="client.del(<?php echo $client->id; ?>);"><i class="icon icon-remove"></i></a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="pagination">
                    <ul>
                        <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('client/index', array( 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                        <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                            <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                <a href="<?php echo $this->createUrl('client/index', array( 'page' => $i )); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('client/index', array( 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <?php echo Yii::t('app', 'No clients yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
