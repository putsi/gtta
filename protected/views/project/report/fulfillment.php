<div class="active-header">
    <div class="pull-right">
        <?php echo $this->renderPartial("//project/partial/submenu", ["page" => "fulfillment", "project" => $project]); ?>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form id="object-selection-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group" id="target-list">
            <label class="control-label"><?php echo Yii::t("app", "Targets"); ?></label>
            <div class="controls">
                <ul class="report-target-list">
                    <?php foreach ($project->targets as $target): ?>
                        <li>
                            <label>
                                <input
                                    checked
                                    type="checkbox"
                                    id="FulfillmentDegreeForm_targetIds_<?= $target->id; ?>"
                                    name="FulfillmentDegreeForm[targetIds][]"
                                    value="<?= $target->id; ?>">

                                <?= $target->host; ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError("fontSize")) echo "error"; ?>">
            <label class="control-label" for="FulfillmentDegreeForm_fontSize"><?php echo Yii::t("app", "Font Size"); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="FulfillmentDegreeForm_fontSize" name="FulfillmentDegreeForm[fontSize]" value="<?php echo $model->fontSize ? CHtml::encode($model->fontSize) : Yii::app()->params["reports"]["fontSize"]; ?>">
                <?php if ($model->getError("fontSize")): ?>
                    <p class="help-block"><?php echo $model->getError("fontSize"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError("fontFamily")) echo "error"; ?>">
            <label class="control-label" for="FulfillmentDegreeForm_fontFamily"><?php echo Yii::t("app", "Font Family"); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="FulfillmentDegreeForm_fontFamily" name="FulfillmentDegreeForm[fontFamily]">
                    <?php foreach (Yii::app()->params["reports"]["fonts"] as $font): ?>
                        <option value="<?php echo $font; ?>"<?php if ($font == Yii::app()->params["reports"]["font"]) echo "selected"; ?>><?php echo $font; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError("fontFamily")): ?>
                    <p class="help-block"><?php echo $model->getError("fontFamily"); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError("pageMargin")) echo "error"; ?>">
            <label class="control-label" for="FulfillmentDegreeForm_pageMargin"><?php echo Yii::t("app", "Page Margin"); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="FulfillmentDegreeForm_pageMargin" name="FulfillmentDegreeForm[pageMargin]" value="<?php echo $model->pageMargin ? CHtml::encode($model->pageMargin) : Yii::app()->params["reports"]["pageMargin"]; ?>">
                <?php if ($model->getError("pageMargin")): ?>
                    <p class="help-block"><?php echo $model->getError("pageMargin"); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError("cellPadding")) echo "error"; ?>">
            <label class="control-label" for="FulfillmentDegreeForm_cellPadding"><?php echo Yii::t("app", "Cell Padding"); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="FulfillmentDegreeForm_cellPadding" name="FulfillmentDegreeForm[cellPadding]" value="<?php echo $model->cellPadding ? CHtml::encode($model->cellPadding) : Yii::app()->params["reports"]["cellPadding"]; ?>">
                <?php if ($model->getError("cellPadding")): ?>
                    <p class="help-block"><?php echo $model->getError("cellPadding"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Generate"); ?></button>
        </div>
    </fieldset>
</form>