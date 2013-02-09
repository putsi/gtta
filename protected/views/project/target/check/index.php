<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.ui.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.iframe-transport.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.fileupload.js"></script>

<div class="pull-right buttons">
    <div class="btn-group" data-toggle="buttons-radio">
        <button class="btn <?php if (!$category->advanced) echo 'active'; ?>" onclick="user.check.setAdvanced('<?php echo $this->createUrl('project/savecategory', array( 'id' => $project->id, 'target' => $target->id, 'category' => $category->check_category_id )); ?>', 0);"><?php echo Yii::t('app', 'Basic'); ?></button>
        <button class="btn <?php if ($category->advanced)  echo 'active'; ?>" onclick="user.check.setAdvanced('<?php echo $this->createUrl('project/savecategory', array( 'id' => $project->id, 'target' => $target->id, 'category' => $category->check_category_id )); ?>', 1);"><?php echo Yii::t('app', 'Advanced'); ?></button>
    </div>
</div>

<div class="pull-right buttons">
    <a class="btn" href="#expand-all" onclick="user.check.expandAll();"><i class="icon icon-arrow-down"></i> <?php echo Yii::t('app', 'Expand'); ?></a>&nbsp;
    <a class="btn" href="#collapse-all" onclick="user.check.collapseAll();"><i class="icon icon-arrow-up"></i> <?php echo Yii::t('app', 'Collapse'); ?></a>&nbsp;

    <?php
        $hasAutomated = false;

        foreach ($checks as $check)
            if ($check->automated)
            {
                $hasAutomated = true;
                break;
            }

        if ($hasAutomated):
    ?>
        <a class="btn" href="#start-all" onclick="user.check.startAll();"><i class="icon icon-play"></i> <?php echo Yii::t('app', 'Start'); ?></a>
    <?php endif; ?>
</div>
<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($checks) > 0): ?>
                <div>
                    <table class="table control-header">
                        <tbody>
                            <tr>
                                <th class="name"><?php echo Yii::t('app', 'Category'); ?></th>
                                <th class="stats"><?php echo Yii::t('app', 'Risk Stats'); ?></th>
                                <th class="percent"><?php echo Yii::t('app', 'Completed'); ?></th>
                                <th class="check-count"><?php echo Yii::t('app', 'Checks'); ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php
                    $counter     = 0;
                    $prevControl = 0;

                    $collapseControls = count($checks) >= Yii::app()->params['collapseCheckCount'];

                    foreach ($checks as $check):
                ?>
                    <?php if ($check->control->id != $prevControl): ?>
                        <?php if ($prevControl != 0): ?>
                            </div>
                        <?php endif; ?>

                        <div id="control-<?php echo $check->control->id; ?>" class="control-header" data-id="<?php echo $check->control->id; ?>">
                            <table class="table control-header">
                                <tbody>
                                    <tr>
                                        <td class="name">
                                            <a href="#control-<?php echo $check->control->id; ?>" onclick="user.check.toggleControl(<?php echo $check->control->id; ?>);"><?php echo CHtml::encode($check->control->localizedName); ?></a>
                                        </td>
                                        <td class="stats">
                                            <span class="high-risk"><?php echo $controlStats[$check->control->id]['highRisk']; ?></span> /
                                            <span class="med-risk"><?php echo $controlStats[$check->control->id]['medRisk']; ?></span> /
                                            <span class="low-risk"><?php echo $controlStats[$check->control->id]['lowRisk']; ?></span> /
                                            <span class="info"><?php echo $controlStats[$check->control->id]['info']; ?></span>
                                        </td>
                                        <td class="percent">
                                            <?php echo $controlStats[$check->control->id]['checks'] ? sprintf('%.0f', ($controlStats[$check->control->id]['finished'] / $controlStats[$check->control->id]['checks']) * 100) : '0'; ?>% /
                                            <?php echo $controlStats[$check->control->id]['finished']; ?>
                                        </td>
                                        <td class="check-count">
                                            <?php echo $controlStats[$check->control->id]['checks']; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="control-body<?php if ($collapseControls) echo ' hide'; ?>" data-id="<?php echo $check->control->id; ?>">
                    <?php
                        endif;

                        $prevControl = $check->control->id;
                    ?>
                    <div id="check-<?php echo $check->id; ?>" class="check-header <?php if ($check->isRunning) echo 'in-progress'; ?>" data-id="<?php echo $check->id; ?>" data-control-url="<?php echo $this->createUrl('project/controlcheck', array( 'id' => $project->id, 'target' => $target->id, 'category' => $category->check_category_id, 'check' => $check->id )); ?>" data-type="<?php echo $check->automated ? 'automated' : 'manual'; ?>">
                        <table class="check-header">
                            <tbody>
                                <tr>
                                    <td class="name">
                                        <a href="#check-<?php echo $check->id; ?>" onclick="user.check.toggle(<?php echo $check->id; ?>);"><?php echo CHtml::encode($check->localizedName); ?></a>
                                        <?php if ($check->automated): ?>
                                            <i class="icon-cog" title="<?php echo Yii::t('app', 'Automated'); ?>"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="status">
                                        <?php if ($check->targetChecks && $check->targetChecks[0]->status == TargetCheck::STATUS_FINISHED): ?>
                                            <?php
                                                switch ($check->targetChecks[0]->rating)
                                                {
                                                    case TargetCheck::RATING_HIDDEN:
                                                        echo '<span class="label">' . $ratings[TargetCheck::RATING_HIDDEN] . '</span>';
                                                        break;

                                                    case TargetCheck::RATING_INFO:
                                                        echo '<span class="label label-info">' . $ratings[TargetCheck::RATING_INFO] . '</span>';
                                                        break;

                                                    case TargetChecK::RATING_LOW_RISK:
                                                        echo '<span class="label label-low-risk">' . $ratings[TargetCheck::RATING_LOW_RISK] . '</span>';
                                                        break;

                                                    case TargetChecK::RATING_MED_RISK:
                                                        echo '<span class="label label-med-risk">' . $ratings[TargetCheck::RATING_MED_RISK] . '</span>';
                                                        break;

                                                    case TargetChecK::RATING_HIGH_RISK:
                                                        echo '<span class="label label-high-risk">' . $ratings[TargetCheck::RATING_HIGH_RISK] . '</span>';
                                                        break;
                                                }
                                            ?>
                                        <?php elseif ($check->isRunning): ?>
                                            <?php
                                                $seconds = $check->targetChecks[0]->started;
                                                date_default_timezone_set(Yii::app()->params['timeZone']);

                                                if ($seconds)
                                                {
                                                    $seconds = time() - strtotime($seconds);
                                                    $minutes = 0;

                                                    if ($seconds > 59)
                                                    {
                                                        $minutes = floor($seconds / 60);
                                                        $seconds = $seconds - ($minutes * 60);
                                                    }

                                                    printf('%02d:%02d', $minutes, $seconds);
                                                }
                                                else
                                                    echo '00:00';
                                            ?>
                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions">
                                        <?php if ($check->automated): ?>
                                            <?php if (!$check->targetChecks || $check->targetChecks && in_array($check->targetChecks[0]->status, array( TargetCheck::STATUS_OPEN, TargetCheck::STATUS_FINISHED ))): ?>
                                                <a href="#start" title="<?php echo Yii::t('app', 'Start'); ?>" onclick="user.check.start(<?php echo $check->id; ?>);"><i class="icon icon-play"></i></a>
                                            <?php elseif ($check->targetChecks && $check->targetChecks[0]->status == TargetCheck::STATUS_IN_PROGRESS): ?>
                                                <a href="#stop" title="<?php echo Yii::t('app', 'Stop'); ?>" onclick="user.check.stop(<?php echo $check->id; ?>);"><i class="icon icon-stop"></i></a>
                                            <?php else: ?>
                                                <span class="disabled"><i class="icon icon-stop" title="<?php echo Yii::t('app', 'Stop'); ?>"></i></span>
                                            <?php endif; ?>
                                            &nbsp;
                                        <?php endif; ?>

                                        <?php if ($check->targetChecks && in_array($check->targetChecks[0]->status, array( TargetCheck::STATUS_OPEN, TargetCheck::STATUS_FINISHED ))): ?>
                                            <a href="#reset" title="<?php echo Yii::t('app', 'Reset'); ?>" onclick="user.check.reset(<?php echo $check->id; ?>);"><i class="icon icon-refresh"></i></a>
                                        <?php else: ?>
                                            <span class="disabled"><i class="icon icon-refresh" title="<?php echo Yii::t('app', 'Reset'); ?>"></i></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="check-form hide" data-id="<?php echo $check->id; ?>" data-save-url="<?php echo $this->createUrl('project/savecheck', array( 'id' => $project->id, 'target' => $target->id, 'category' => $category->check_category_id, 'check' => $check->id )); ?>">
                        <table class="table check-form">
                            <tbody>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Reference'); ?>
                                    </th>
                                    <td class="text">
                                        <?php
                                            $reference    = $check->_reference->name . ( $check->reference_code ? '-' . $check->reference_code : '' );
                                            $referenceUrl = '';

                                            if ($check->reference_code && $check->reference_url)
                                                $referenceUrl = $check->reference_url;
                                            else if ($check->_reference->url)
                                                $referenceUrl = $check->_reference->url;

                                            if ($referenceUrl)
                                                $reference = '<a href="' . $referenceUrl . '" target="_blank">' . CHtml::encode($reference) . '</a>';
                                            else
                                                $reference = CHtml::encode($reference);

                                            echo $reference;
                                        ?>
                                    </td>
                                </tr>
                                <?php if ($check->localizedBackgroundInfo): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Background Info'); ?>
                                        </th>
                                        <td class="text">
                                            <div class="limiter"><?php echo $check->localizedBackgroundInfo; ?></div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($check->localizedHints): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Hints'); ?>
                                        </th>
                                        <td class="text">
                                            <div class="limiter"><?php echo $check->localizedHints; ?></div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($check->localizedQuestion): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Question'); ?>
                                        </th>
                                        <td class="text">
                                            <div class="limiter"><?php echo $check->localizedQuestion; ?></div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($check->automated): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Override Target'); ?>
                                        </th>
                                        <td>
                                            <input type="text" class="input-xlarge" name="TargetCheckEditForm_<?php echo $check->id; ?>[overrideTarget]" id="TargetCheckEditForm_<?php echo $check->id; ?>_overrideTarget" value="<?php if ($check->targetChecks) echo CHtml::encode($check->targetChecks[0]->override_target); ?>" <?php if ($check->isRunning) echo 'readonly'; ?>>
                                        </td>
                                    </tr>
                                    <?php if ($check->protocol): ?>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t('app', 'Protocol'); ?>
                                            </th>
                                            <td>
                                                <input type="text" class="input-xlarge" name="TargetCheckEditForm_<?php echo $check->id; ?>[protocol]" id="TargetCheckEditForm_<?php echo $check->id; ?>_protocol" value="<?php echo CHtml::encode($check->targetChecks ? $check->targetChecks[0]->protocol : $check->protocol); ?>" <?php if ($check->isRunning) echo 'readonly'; ?>>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if ($check->port): ?>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t('app', 'Port'); ?>
                                            </th>
                                            <td>
                                                <input type="text" class="input-xlarge" name="TargetCheckEditForm_<?php echo $check->id; ?>[port]" id="TargetCheckEditForm_<?php echo $check->id; ?>_port" value="<?php echo $check->targetChecks ? $check->targetChecks[0]->port : $check->port; ?>" <?php if ($check->isRunning) echo 'readonly'; ?>>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($check->inputs && $check->automated): ?>
                                    <?php foreach ($check->inputs as $input): ?>
                                        <tr>
                                            <th>
                                                <?php echo CHtml::encode($input->localizedName); ?>
                                            </th>
                                            <td>
                                                <?php if ($input->type == CheckInput::TYPE_TEXT): ?>
                                                    <?php
                                                        $value = '';

                                                        if ($check->targetCheckInputs)
                                                            foreach ($check->targetCheckInputs as $inputValue)
                                                                if ($inputValue->check_input_id == $input->id)
                                                                {
                                                                    $value = $inputValue->value;
                                                                    break;
                                                                }

                                                        if ($value == NULL && $input->value != NULL)
                                                            $value = $input->value;

                                                        if ($value != NULL)
                                                            $value = CHtml::encode($value);
                                                    ?>
                                                    <input type="text" name="TargetCheckEditForm_<?php echo $check->id; ?>[inputs][<?php echo $input->id; ?>]" class="max-width" id="TargetCheckEditForm_<?php echo $check->id; ?>_inputs_<?php echo $input->id; ?>" <?php if ($check->isRunning) echo 'readonly'; ?> value="<?php echo $value; ?>">
                                                <?php elseif ($input->type == CheckInput::TYPE_TEXTAREA): ?>
                                                    <?php
                                                        $value = '';

                                                        if ($check->targetCheckInputs)
                                                            foreach ($check->targetCheckInputs as $inputValue)
                                                                if ($inputValue->check_input_id == $input->id)
                                                                {
                                                                    $value = $inputValue->value;
                                                                    break;
                                                                }

                                                        if ($value == NULL && $input->value != NULL)
                                                            $value = $input->value;

                                                        if ($value != NULL)
                                                            $value = CHtml::encode($value);
                                                    ?>
                                                    <textarea wrap="off" name="TargetCheckEditForm_<?php echo $check->id; ?>[inputs][<?php echo $input->id; ?>]" class="max-width" rows="2" id="TargetCheckEditForm_<?php echo $check->id; ?>_inputs_<?php echo $input->id; ?>" <?php if ($check->isRunning) echo 'readonly'; ?>><?php echo $value; ?></textarea>
                                                <?php elseif ($input->type == CheckInput::TYPE_CHECKBOX): ?>
                                                    <?php
                                                        $value = '';

                                                        if ($check->targetCheckInputs)
                                                            foreach ($check->targetCheckInputs as $inputValue)
                                                                if ($inputValue->check_input_id == $input->id)
                                                                {
                                                                    $value = $inputValue->value;
                                                                    break;
                                                                }
                                                    ?>
                                                    <input type="checkbox" name="TargetCheckEditForm_<?php echo $check->id; ?>[inputs][<?php echo $input->id; ?>]" id="TargetCheckEditForm_<?php echo $check->id; ?>_inputs_<?php echo $input->id; ?>" <?php if ($check->isRunning) echo 'readonly'; ?> value="1"<?php if ($value) echo ' checked'; ?>>
                                                <?php elseif ($input->type == CheckInput::TYPE_RADIO): ?>
                                                    <?php
                                                        $value = '';

                                                        if ($check->targetCheckInputs)
                                                            foreach ($check->targetCheckInputs as $inputValue)
                                                                if ($inputValue->check_input_id == $input->id)
                                                                {
                                                                    $value = $inputValue->value;
                                                                    break;
                                                                }

                                                        $radioBoxes = explode("\n", str_replace("\r", '', $input->value));
                                                    ?>

                                                    <ul class="radio-input">
                                                        <?php foreach ($radioBoxes as $radio): ?>
                                                            <li>
                                                                <label class="radio">
                                                                    <input name="TargetCheckEditForm_<?php echo $check->id; ?>[inputs][<?php echo $input->id; ?>]" type="radio" value="<?php echo CHtml::encode($radio); ?>" <?php if ($check->isRunning) echo 'disabled'; ?> <?php if ($value == $radio) echo ' checked'; ?>>
                                                                    <?php echo CHtml::encode($radio); ?>
                                                                </label>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php elseif ($input->type == CheckInput::TYPE_FILE): ?>
                                                    <?php
                                                        $value = '';

                                                        if ($check->targetCheckInputs)
                                                            foreach ($check->targetCheckInputs as $inputValue)
                                                                if ($inputValue->check_input_id == $input->id)
                                                                {
                                                                    $value = $inputValue->value;
                                                                    break;
                                                                }
                                                    ?>
                                                    <input type="checkbox" name="TargetCheckEditForm_<?php echo $check->id; ?>[inputs][<?php echo $input->id; ?>]" id="TargetCheckEditForm_<?php echo $check->id; ?>_inputs_<?php echo $input->id; ?>" <?php if ($check->isRunning) echo 'readonly'; ?> value="1"<?php if ($value) echo ' checked'; ?>>
                                                <?php endif; ?>

                                                <?php if ($input->localizedDescription): ?>
                                                    <p class="help-block">
                                                        <?php echo CHtml::encode($input->localizedDescription); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Result'); ?>
                                    </th>
                                    <td>
                                        <textarea name="TargetCheckEditForm_<?php echo $check->id; ?>[result]" class="max-width result" rows="10" id="TargetCheckEditForm_<?php echo $check->id; ?>_result" <?php if ($check->isRunning) echo 'readonly'; ?>><?php if ($check->targetChecks) echo $check->targetChecks[0]->result; ?></textarea>

                                        <div class="table-result">
                                            <?php
                                                if ($check->targetChecks && $check->targetChecks[0]->table_result)
                                                {
                                                    $table = new ResultTable();
                                                    $table->parse($check->targetChecks[0]->table_result);
                                                    echo $this->renderPartial('/project/target/check/tableresult', array( 'table' => $table ));
                                                }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php if ($check->results): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Insert Result'); ?>
                                        </th>
                                        <td class="text">
                                            <ul class="results">
                                                <?php foreach ($check->results as $result): ?>
                                                    <li>
                                                        <div class="result-header">
                                                            <a href="#insert" onclick="user.check.insertResult(<?php echo $check->id; ?>, $('.result-content[data-id=<?php echo $result->id; ?>]').html());"><?php echo CHtml::encode($result->localizedTitle); ?></a>

                                                            <span class="result-control" data-id="<?php echo $result->id; ?>">
                                                                <a href="#result" onclick="user.check.expandResult(<?php echo $result->id; ?>);"><i class="icon-chevron-down"></i></a>
                                                            </span>
                                                        </div>

                                                        <div class="result-content hide" data-id="<?php echo $result->id; ?>"><?php echo str_replace("\n", '<br>', CHtml::encode($result->localizedResult)); ?></div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($check->solutions): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Solution'); ?>
                                        </th>
                                        <td class="text">
                                            <ul class="solutions">
                                                <?php if (!$check->multiple_solutions): ?>
                                                    <li>
                                                        <div class="solution-header">
                                                            <label class="radio">
                                                                <input name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="radio" value="0" <?php if ($check->isRunning) echo 'disabled'; ?> <?php if (!$check->targetCheckSolutions) echo 'checked'; ?>>
                                                                <?php echo Yii::t('app', 'None'); ?>
                                                            </label>
                                                        </div>
                                                    </li>
                                                <?php endif; ?>
                                                <?php foreach ($check->solutions as $solution): ?>
                                                    <li>
                                                        <div class="solution-header">
                                                            <?php
                                                                $checked = false;

                                                                if ($check->targetCheckSolutions)
                                                                    foreach ($check->targetCheckSolutions as $solutionValue)
                                                                        if ($solutionValue->check_solution_id == $solution->id)
                                                                        {
                                                                            $checked = true;
                                                                            break;
                                                                        }
                                                            ?>
                                                            <?php if ($check->multiple_solutions): ?>
                                                                <label class="checkbox">
                                                                    <input name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="checkbox" value="<?php echo $solution->id; ?>" <?php if ($checked) echo 'checked'; ?> <?php if ($check->isRunning) echo 'disabled'; ?>>
                                                            <?php else: ?>
                                                                <label class="radio">
                                                                    <input name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="radio" value="<?php echo $solution->id; ?>" <?php if ($checked) echo 'checked'; ?> <?php if ($check->isRunning) echo 'disabled'; ?>>
                                                            <?php endif; ?>
                                                                <?php echo CHtml::encode($solution->localizedTitle); ?>

                                                                <span class="solution-control" data-id="<?php echo $solution->id; ?>">
                                                                    <a href="#solution" onclick="user.check.expandSolution(<?php echo $solution->id; ?>);"><i class="icon-chevron-down"></i></a>
                                                                </span>
                                                            </label>
                                                        </div>

                                                        <div class="solution-content hide" data-id="<?php echo $solution->id; ?>">
                                                            <?php echo $solution->localizedSolution; ?>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Attachments'); ?>
                                    </th>
                                    <td class="text">
                                        <div class="file-input" id="upload-link-<?php echo $check->id; ?>">
                                            <a href="#attachment"><?php echo Yii::t('app', 'New Attachment'); ?></a>
                                            <input type="file" name="TargetCheckAttachmentUploadForm[attachment]" data-id="<?php echo $check->id; ?>" data-upload-url="<?php echo $this->createUrl('project/uploadattachment', array( 'id' => $project->id, 'target' => $target->id, 'category' => $category->check_category_id, 'check' => $check->id )); ?>">
                                        </div>

                                        <div class="upload-message hide" id="upload-message-<?php echo $check->id; ?>"><?php echo Yii::t('app', 'Uploading...'); ?></div>

                                        <?php if ($check->targetCheckAttachments): ?>
                                            <table class="table attachment-list">
                                                <tbody>
                                                    <?php foreach ($check->targetCheckAttachments as $attachment): ?>
                                                        <tr data-path="<?php echo $attachment->path; ?>" data-control-url="<?php echo $this->createUrl('project/controlattachment'); ?>">
                                                            <td class="name">
                                                                <a href="<?php echo $this->createUrl('project/attachment', array( 'path' => $attachment->path )); ?>"><?php echo CHtml::encode($attachment->name); ?></a>
                                                            </td>
                                                            <td class="actions">
                                                                <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="user.check.delAttachment('<?php echo $attachment->path; ?>');"><i class="icon icon-remove"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Result Rating'); ?>
                                    </th>
                                    <td class="text">
                                        <ul class="rating">
                                            <?php foreach(array( TargetCheck::RATING_NONE, TargetCheck::RATING_HIDDEN, TargetCheck::RATING_INFO, TargetCheck::RATING_LOW_RISK, TargetCheck::RATING_MED_RISK, TargetCheck::RATING_HIGH_RISK ) as $rating): ?>
                                                <li>
                                                    <label class="radio">
                                                        <input type="radio" name="TargetCheckEditForm_<?php echo $check->id; ?>[rating]" value="<?php echo $rating; ?>" <?php if (($check->targetChecks && $check->targetChecks[0]->rating == $rating) || ($rating == TargetCheck::RATING_NONE && (!$check->targetChecks || !$check->targetChecks[0]->rating))) echo 'checked'; ?> <?php if ($check->isRunning) echo 'disabled'; ?>>
                                                        <?php echo $ratings[$rating]; ?>
                                                    </label>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>
                                        <button class="btn" onclick="user.check.save(<?php echo $check->id; ?>, false);" <?php if ($check->isRunning) echo 'disabled'; ?>><?php echo Yii::t('app', 'Save'); ?></button>&nbsp;
                                        <?php if ($counter < count($checks) - 1): ?>
                                            <button class="btn" onclick="user.check.save(<?php echo $check->id; ?>, true);" <?php if ($check->isRunning) echo 'disabled'; ?>><?php echo Yii::t('app', 'Save & Next'); ?></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php
                        $counter++;
                    endforeach;
                ?>
                </div>
            <?php else: ?>
                <?php echo Yii::t('app', 'No checks in this category.'); ?>
            <?php endif; ?>
        </div>
        <div class="span4">
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
        </div>
    </div>
</div>

<script>
    var ratings = {
        <?php
            $ratingNames = array();

            foreach ($ratings as $k => $v)
            {
                $class = null;

                switch ($k)
                {
                    case TargetCheck::RATING_INFO:
                        $class = 'label-info';
                        break;

                    case TargetCheck::RATING_LOW_RISK:
                        $class = 'label-low-risk';
                        break;

                    case TargetCheck::RATING_MED_RISK:
                        $class = 'label-med-risk';
                        break;

                    case TargetCheck::RATING_HIGH_RISK:
                        $class = 'label-high-risk';
                        break;
                }

                $ratingNames[] = $k . ':' . json_encode(array(
                    'text'   => CHtml::encode($v),
                    'classN' => $class
                ));
            }

            echo implode(',', $ratingNames);
        ?>
    };

    $(function () {
        user.check.initTargetCheckAttachmentUploadForms();

        user.check.runningChecks = [
            <?php
                date_default_timezone_set(Yii::app()->params['timeZone']);
                $runningChecks = array();

                foreach ($checks as $check)
                    if ($check->isRunning)
                    {
                        $runningChecks[] = json_encode(array(
                            'id'   => $check->id,
                            'time' => $check->targetChecks[0]->started != NULL ? time() - strtotime($check->targetChecks[0]->started) : -1,
                        ));
                    }

                echo implode(',', $runningChecks);
            ?>
        ];

        setTimeout(function () {
            user.check.update('<?php echo $this->createUrl('project/updatechecks', array( 'id' => $project->id, 'target' => $target->id, 'category' => $category->check_category_id )); ?>');
        }, 1000);

        var href = window.location.href;

        if (href.indexOf('#check-') >= 0)
        {
            var checkId = href.substring(href.indexOf('#check-') + 7, href.length);
            user.check.expand(parseInt(checkId), function () {
                location.href = '#check-' + checkId;
            });
        }
    });
</script>