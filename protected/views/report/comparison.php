<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form id="project-report-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" data-project-url="<?php echo $this->createUrl('report/projectlist'); ?>" data-target-url="<?php echo $this->createUrl('report/targetlist'); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group">
            <label class="control-label" for="ProjectComparisonForm_clientId"><?php echo Yii::t('app', 'Client'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectComparisonForm_clientId" name="ProjectComparisonForm[clientId]" onchange="user.report.comparisonFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client->id; ?>"><?php echo CHtml::encode($client->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="hide control-group" id="project-list-1">
            <label class="control-label" for="ProjectComparisonForm_projectId1"><?php echo Yii::t('app', 'First Project'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectComparisonForm_projectId1" name="ProjectComparisonForm[projectId1]" onchange="user.report.comparisonFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                </select>
            </div>
        </div>

        <div class="hide control-group" id="project-list-2">
            <label class="control-label" for="ProjectComparisonForm_projectId2"><?php echo Yii::t('app', 'Second Project'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectComparisonForm_projectId2" name="ProjectComparisonForm[projectId2]" onchange="user.report.comparisonFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn" disabled><?php echo Yii::t('app', 'Generate'); ?></button>
        </div>
    </fieldset>
</form>