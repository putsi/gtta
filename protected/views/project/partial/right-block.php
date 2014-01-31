<?php if ((User::checkRole(User::ROLE_USER) || Yii::app()->user->getShowDetails()) && $quickTargets): ?>
    <div id="project-quick-nav-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#project-quick-nav');"><i class="icon-chevron-up"></i></div>
    <h3><a href="#toggle" onclick="system.toggleBlock('#project-quick-nav');"><?php echo Yii::t('app', 'Quick Navigation'); ?></a></h3>

    <div class="info-block" id="project-quick-nav">
        <?php foreach ($quickTargets as $qTarget): ?>
            <div class="project-quick-nav">
                <div class="target">
                    <a href="<?php echo $this->createUrl("project/target", array("id" => $project->id, "target" => $qTarget->id)); ?>"><?php echo CHtml::encode($qTarget->host); ?></a>
                </div>

                <?php if ($qTarget->categories): ?>
                    <div class="categories">
                        <?php foreach ($qTarget->categories as $cat): ?>
                            <?php
                                $catName = $cat->localizedName;
                                $shortened = false;

                                if (mb_strlen($catName) > 45) {
                                    $catName = mb_substr($catName, 0, 45) . "...";
                                    $shortened = true;
                                }

                                $catName = CHtml::encode($catName);
                            ?>

                            <a href="<?php echo $this->createUrl("project/checks", array("id" => $project->id, "target" => $qTarget->id, "category" => $cat->id)); ?>" <?php if ($category && $target && $target->id == $qTarget->id && $cat->id == $category->check_category_id) echo "class=\"selected\""; ?>><?php echo $catName; ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="clearfix"></div>
    </div>
<?php endif; ?>

<div id="project-info-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#project-info');"><i class="icon-chevron-up"></i></div>
<h3><a href="#toggle" onclick="system.toggleBlock('#project-info');"><?php echo Yii::t('app', 'Project Information'); ?></a></h3>

<div class="info-block" id="project-info">
    <table class="table client-details">
        <tbody>
            <?php if (!User::checkRole(User::ROLE_CLIENT)): ?>
                <tr>
                    <th>
                        <?php echo Yii::t('app', 'Client'); ?>
                    </th>
                    <td>
                        <a href="<?php echo $this->createUrl('client/view', array( 'id' => $client->id )); ?>"><?php echo CHtml::encode($client->name); ?></a>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>
                    <?php echo Yii::t('app', 'Year'); ?>
                </th>
                <td>
                    <?php echo CHtml::encode($project->year); ?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo Yii::t('app', 'Deadline'); ?>
                </th>
                <td>
                    <?php echo CHtml::encode($project->deadline); ?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo Yii::t('app', 'Status'); ?>
                </th>
                <td>
                    <?php echo $statuses[$project->status]; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php if ($project->details): ?>
    <div id="project-details-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#project-details');"><i class="icon-chevron-up"></i></div>
    <h3><a href="#toggle" onclick="system.toggleBlock('#project-details');"><?php echo Yii::t('app', 'Project Details'); ?></a></h3>

    <div class="info-block" id="project-details">
        <?php
            $counter = 0;
            foreach ($project->details as $detail):
        ?>
            <div class="project-detail <?php if (!$counter) echo 'borderless'; ?>">
                <div class="subject"><?php echo CHtml::encode($detail->subject); ?></div>
                <div class="content"><?php echo CHtml::encode($detail->content); ?></div>
            </div>
        <?php
                $counter++;
            endforeach;
        ?>
    </div>
<?php endif; ?>

<?php if (!User::checkRole(User::ROLE_CLIENT)): ?>
    <?php if ($client->hasDetails): ?>
        <div id="client-address-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#client-address');"><i class="icon-chevron-up"></i></div>
        <h3><a href="#toggle" onclick="system.toggleBlock('#client-address');"><?php echo Yii::t('app', 'Client Address'); ?></a></h3>

        <div class="info-block" id="client-address">
            <table class="table client-details">
                <tbody>
                    <?php if ($client->country): ?>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Country'); ?>
                            </th>
                            <td>
                                <?php echo CHtml::encode($client->country); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($client->state): ?>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'State'); ?>
                            </th>
                            <td>
                                <?php echo CHtml::encode($client->state); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($client->city): ?>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'City'); ?>
                            </th>
                            <td>
                                <?php echo CHtml::encode($client->city); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($client->address): ?>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Address'); ?>
                            </th>
                            <td>
                                <?php echo CHtml::encode($client->address); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($client->postcode): ?>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'P.C.'); ?>
                            </th>
                            <td>
                                <?php echo CHtml::encode($client->postcode); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($client->website): ?>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Website'); ?>
                            </th>
                            <td>
                                <a href="<?php echo CHtml::encode($client->website); ?>"><?php echo CHtml::encode($client->website); ?></a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php if ($client->hasContact): ?>
        <div id="client-contact-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#client-contact');"><i class="icon-chevron-up"></i></div>
        <h3><a href="#toggle" onclick="system.toggleBlock('#client-contact');"><?php echo Yii::t('app', 'Client Contact'); ?></a></h3>

        <div class="info-block" id="client-contact">
            <table class="table client-details">
                <tbody>
                    <?php if ($client->contact_name): ?>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Name'); ?>
                            </th>
                            <td>
                                <?php echo CHtml::encode($client->contact_name); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($client->contact_email): ?>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'E-mail'); ?>
                            </th>
                            <td>
                                <a href="mailto:<?php echo CHtml::encode($client->contact_email); ?>"><?php echo CHtml::encode($client->contact_email); ?></a>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($client->contact_phone): ?>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Phone'); ?>
                            </th>
                            <td>
                                <?php echo CHtml::encode($client->contact_phone); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($client->contact_fax): ?>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Fax'); ?>
                            </th>
                            <td>
                                <?php echo CHtml::encode($client->contact_fax); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>