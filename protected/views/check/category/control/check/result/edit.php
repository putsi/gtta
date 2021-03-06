<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
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
                    <div class="control-group <?php if ($model->getError('title')) echo 'error'; ?>">
                        <label class="control-label" for="CheckResultEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_title"><?php echo Yii::t('app', 'Title'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckResultEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_title" name="CheckResultEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][title]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['title']) : ''; ?>">
                            <?php if ($model->getError('title')): ?>
                                <p class="help-block"><?php echo $model->getError('title'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="control-group <?php if ($model->getError('result')) echo 'error'; ?>">
                        <label class="control-label" for="CheckResultEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_result"><?php echo Yii::t('app', 'Result'); ?></label>
                        <div class="controls">
                            <textarea class="wysiwyg <?php if (isset($model->localizedItems[$language->id])) echo (Utils::isHtml($model->localizedItems[$language->id]['result']) ? 'html_content' : ''); ?>" id="CheckResultEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_result" name="CheckResultEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][result]"><?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['result']) : ''; ?></textarea>

                            <?php if ($model->getError('result')): ?>
                                <p class="help-block"><?php echo $model->getError('result'); ?></p>
                            <?php endif; ?>

                            <p class="help-block">
                                <a class="btn btn-default" href="#editor" onclick="user.check.toggleEditor('CheckResultEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_result');">
                                    <span class="glyphicon glyphicon-edit"></span>
                                    <?php echo Yii::t("app", "WYSIWYG"); ?>
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div>
            <hr>
        </div>

        <div class="control-group <?php if ($model->getError('sortOrder')) echo 'error'; ?>">
            <label class="control-label" for="CheckResultEditForm_sortOrder"><?php echo Yii::t('app', 'Sort Order'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="CheckResultEditForm_sortOrder" name="CheckResultEditForm[sortOrder]" value="<?php echo $model->sortOrder ? $model->sortOrder : 0; ?>">
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

    /**
     * Enabling ckeditor for wysiwyg elements, which contain HTML
     */
    $.each($('.wysiwyg.html_content'), function (key, value) {
        user.check.toggleEditor($(value).attr('id'));
    });

    /**
     * Binding change event for wysiwyg elements on HTML containing
     */
    $('.wysiwyg').bind('input propertychange', function () {
        if ($(this).val().isHTML()) {
            if (!user.check.getEditor($(this).attr('id'))) {
                user.check.enableEditor($(this).attr('id'));
            }
        }
    });
</script>