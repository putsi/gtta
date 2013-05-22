<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" id="object-selection-form" data-object-list-url="<?php echo $this->createUrl('app/objectlist'); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <ul class="nav nav-tabs" id="languages-tab">
            <?php foreach ($languages as $language): ?>
                <li<?php if ($language->default) echo ' class="active"'; ?>>
                    <a href="#<?php echo CHtml::encode($language->code); ?>">
                        <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png" alt="<?php echo CHtml::encode($language->name); ?>">
                        <?php echo CHtml::encode($language->name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <?php foreach ($languages as $language): ?>
                <div class="tab-pane<?php if ($language->default) echo ' active'; ?>" id="<?php echo CHtml::encode($language->code); ?>">
                    <div class="control-group">
                        <label class="control-label" for="GtModuleCheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_description"><?php echo Yii::t('app', 'Description'); ?></label>
                        <div class="controls">
                            <textarea class="wysiwyg" id="GtModuleCheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_description" name="GtModuleCheckEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][description]"><?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['description']) : ''; ?></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="GtModuleCheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_targetDescription"><?php echo Yii::t('app', 'Target Description'); ?></label>
                        <div class="controls">
                            <textarea class="wysiwyg" id="GtModuleCheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_targetDescription" name="GtModuleCheckEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][targetDescription]"><?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['targetDescription']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div>
            <hr>
        </div>
        
        <div class="control-group">
            <label class="control-label" for="GtModuleCheckEditForm_controlId"><?php echo Yii::t('app', 'Control'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="GtModuleCheckEditForm_controlId" onchange="admin.check.loadChecks($(this), $('#GtModuleCheckEditForm_checkId'));">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($categories as $cat): ?>
                        <?php foreach ($cat->controls as $ctrl): ?>
                            <option value="<?php echo $ctrl->id; ?>" <?php if ($controlId == $ctrl->id) echo "selected"; ?>><?php echo CHtml::encode($cat->localizedName); ?> / <?php echo CHtml::encode($ctrl->localizedName); ?></option>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('checkId')) echo 'error'; ?>" id="check-selector">
            <label class="control-label" for="GtModuleCheckEditForm_checkId"><?php echo Yii::t('app', 'Check'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="GtModuleCheckEditForm_checkId" name="GtModuleCheckEditForm[checkId]">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($checks as $check): ?>
                        <option value="<?php echo $check->id; ?>" <?php if ($model->checkId == $check->id) echo "selected"; ?>><?php echo CHtml::encode($check->localizedName); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('checkId')): ?>
                    <p class="help-block"><?php echo $model->getError('checkId'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <hr>
        </div>

        <div class="control-group <?php if ($model->getError('sortOrder')) echo 'error'; ?>">
            <label class="control-label" for="GtModuleCheckEditForm_sortOrder"><?php echo Yii::t('app', 'Sort Order'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="GtModuleCheckEditForm_sortOrder" name="GtModuleCheckEditForm[sortOrder]" value="<?php echo $model->sortOrder ? $model->sortOrder : 0; ?>">
                <?php if ($model->getError('sortOrder')): ?>
                    <p class="help-block"><?php echo $model->getError('sortOrder'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>

<script>
    $('#languages-tab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
</script>