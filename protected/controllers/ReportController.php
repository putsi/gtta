<?php

/**
 * Report controller.
 */
class ReportController extends Controller
{
    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'checkAuth',
            'checkUser',
            'ajaxOnly + projectlist, targetlist',
            'postOnly + projectlist, targetlist',
		);
	}

    /**
     * Normalize X coordinate.
     */
    private function _normalizeCoord($coord, $min, $max)
    {
        if ($coord < $min)
            $coord = $min;
        elseif ($coord > $max)
            $coord = $max;

        return $coord;
    }

    /**
     * Rating image.
     */
    private function _generateRatingImage($rating)
    {
        $image     = imagecreatefrompng(Yii::app()->basePath . '/../images/rating-stripe.png');
        $lineCoord = round($rating * 40);
        $color     = imagecolorallocate($image, 0, 0, 0);

        imageline($image, $lineCoord, 0, $lineCoord, 30, $color);

        $topArrow = array(
            $this->_normalizeCoord($lineCoord - 5, 0, 200), 0,
            $this->_normalizeCoord($lineCoord + 5, 0, 200), 0,
            $lineCoord, 5
        );

        $bottomArrow = array(
            $this->_normalizeCoord($lineCoord - 5, 0, 200), 29,
            $this->_normalizeCoord($lineCoord + 5, 0, 200), 29,
            $lineCoord, 24
        );

        imagefilledpolygon($image, $topArrow, count($topArrow) / 2, $color);
        imagefilledpolygon($image, $bottomArrow, count($bottomArrow) / 2, $color);

        $hashName = hash('sha256', rand() . time() . rand());
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName . '.png';

        imagepng($image, $filePath, 0);
        imagedestroy($image);

        return $filePath;
    }

    /**
     * Generate project function.
     */
    private function _generateProjectReport($clientId, $projectId, $targetIds)
    {
        $project = Project::model()->findByAttributes(array(
            'client_id' => $clientId,
            'id'        => $projectId
        ));

        if ($project === null)
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Project doesn\\\'t exist.'));
            return;
        }

        if (!$targetIds || !count($targetIds))
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Please select at least 1 target.'));
            return;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $targetIds);
        $criteria->addColumnCondition(array( 'project_id' => $project->id ));
        $criteria->order = 't.host ASC';
        $targets = Target::model()->findAll($criteria);

        $data     = array();
        $infoData = array();

        $totalRating     = 0.0;
        $totalCheckCount = 0;

        $ratings = array(
            TargetCheck::RATING_HIDDEN    => Yii::t('app', 'Hidden'),
            TargetCheck::RATING_INFO      => Yii::t('app', 'Info'),
            TargetCheck::RATING_LOW_RISK  => Yii::t('app', 'Low Risk'),
            TargetCheck::RATING_MED_RISK  => Yii::t('app', 'Med Risk'),
            TargetCheck::RATING_HIGH_RISK => Yii::t('app', 'High Risk'),
        );

        foreach ($targets as $target)
        {
            $targetData = array(
                'host'       => $target->host,
                'rating'     => 0.0,
                'checkCount' => 0,
                'categories' => array(),
            );

            $infoTargetData = array(
                'host'       => $target->host,
                'categories' => array()
            );

            // get all references (they are the same across all target categories)
            $referenceIds = array();

            $references = TargetReference::model()->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            foreach ($references as $reference)
                $referenceIds[] = $reference->reference_id;

            // get all categories
            $categories = TargetCheckCategory::model()->with(array(
                'category' => array(
                    'with' => array(
                        'l10n' => array(
                            'joinType' => 'LEFT JOIN',
                            'on'       => 'language_id = :language_id',
                            'params'   => array( 'language_id' => $language )
                        )
                    )
                )
            ))->findAllByAttributes(
                array( 'target_id' => $target->id  ),
                array( 'order'     => 't.name ASC' )
            );

            foreach ($categories as $category)
            {
                $categoryData = array(
                    'name'       => $category->category->localizedName,
                    'rating'     => 0.0,
                    'checkCount' => 0,
                    'controls'   => array()
                );

                $infoCategoryData = array(
                    'name'     => $category->category->localizedName,
                    'controls' => array()
                );

                // get all controls
                $controls = CheckControl::model()->with(array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    )
                ))->findAllByAttributes(
                    array( 'check_category_id' => $category->check_category_id ),
                    array( 'order'             => 't.name ASC' )
                );

                if (!$controls)
                    continue;

                foreach ($controls as $control)
                {
                    $controlData = array(
                        'name'       => $control->localizedName,
                        'rating'     => 0.0,
                        'checkCount' => 0,
                        'checks'     => array()
                    );

                    $infoControlData = array(
                        'name'   => $control->localizedName,
                        'checks' => array()
                    );

                    $criteria = new CDbCriteria();

                    $criteria->order = 't.name ASC';
                    $criteria->addInCondition('t.reference_id', $referenceIds);
                    $criteria->addColumnCondition(array(
                        't.check_control_id' => $control->id
                    ));

                    if (!$category->advanced)
                        $criteria->addCondition('t.advanced = FALSE');

                    $checks = Check::model()->with(array(
                        'l10n' => array(
                            'joinType' => 'LEFT JOIN',
                            'on'       => 'l10n.language_id = :language_id',
                            'params'   => array( 'language_id' => $language )
                        ),
                        'targetChecks' => array(
                            'alias'    => 'tcs',
                            'joinType' => 'INNER JOIN',
                            'on'       => 'tcs.target_id = :target_id AND tcs.status = :status AND tcs.rating != :hidden',
                            'params'   => array(
                                'target_id' => $target->id,
                                'status'    => TargetCheck::STATUS_FINISHED,
                                'hidden'    => TargetCheck::RATING_HIDDEN,
                            ),
                        ),
                        'targetCheckSolutions' => array(
                            'alias'    => 'tss',
                            'joinType' => 'LEFT JOIN',
                            'on'       => 'tss.target_id = :target_id',
                            'params'   => array( 'target_id' => $target->id ),
                            'with'     => array(
                                'solution' => array(
                                    'alias'    => 'tss_s',
                                    'joinType' => 'LEFT JOIN',
                                    'with'     => array(
                                        'l10n' => array(
                                            'alias'  => 'tss_s_l10n',
                                            'on'     => 'tss_s_l10n.language_id = :language_id',
                                            'params' => array( 'language_id' => $language )
                                        )
                                    )
                                )
                            )
                        ),
                        'targetCheckAttachments' => array(
                            'alias'    => 'tas',
                            'joinType' => 'LEFT JOIN',
                            'on'       => 'tas.target_id = :target_id',
                            'params'   => array( 'target_id' => $target->id )
                        ),
                        '_reference'
                    ))->findAll($criteria);

                    if (!$checks)
                        continue;

                    foreach ($checks as $check)
                    {
                        $checkData = array(
                            'name'             => $check->localizedName,
                            'background'       => $check->localizedBackgroundInfo,
                            'question'         => $check->localizedQuestion,
                            'result'           => $check->targetChecks[0]->result,
                            'rating'           => 0,
                            'ratingName'       => $ratings[$check->targetChecks[0]->rating],
                            'ratingColor'      => '#999999',
                            'solutions'        => array(),
                            'images'           => array(),
                            'reference'        => $check->_reference->name,
                            'referenceUrl'     => $check->_reference->url,
                            'referenceCode'    => $check->reference_code,
                            'referenceCodeUrl' => $check->reference_url,
                        );

                        switch ($check->targetChecks[0]->rating)
                        {
                            case TargetCheck::RATING_HIDDEN:
                                $checkData['rating']      = 0;
                                $checkData['ratingColor'] = '#999999';
                                break;

                            case TargetCheck::RATING_INFO:
                                $checkData['rating'] = 1;
                                $checkData['ratingColor'] = '#3A87AD';
                                break;

                            case TargetCheck::RATING_LOW_RISK:
                                $checkData['rating'] = 2;
                                $checkData['ratingColor'] = '#53A254';
                                break;

                            case TargetCheck::RATING_MED_RISK:
                                $checkData['rating'] = 3;
                                $checkData['ratingColor'] = '#DACE2F';
                                break;

                            case TargetCheck::RATING_HIGH_RISK:
                                $checkData['rating'] = 4;
                                $checkData['ratingColor'] = '#D63515';
                                break;
                        }

                        if ($check->targetCheckSolutions)
                            foreach ($check->targetCheckSolutions as $solution)
                                $checkData['solutions'][] = $solution->solution->localizedSolution;

                        if ($check->targetCheckAttachments)
                            foreach ($check->targetCheckAttachments as $attachment)
                                if (in_array($attachment->type, array( 'image/jpeg', 'image/png', 'image/gif' )))
                                    $checkData['images'][] = Yii::app()->params['attachments']['path'] . '/' . $attachment->path;

                        $controlData['rating']  += $checkData['rating'];
                        $categoryData['rating'] += $checkData['rating'];
                        $targetData['rating']   += $checkData['rating'];
                        $totalRating            += $checkData['rating'];

                        // put checks with RATING_INFO rating to a separate category
                        if ($check->targetChecks[0]->rating == TargetCheck::RATING_INFO)
                            $infoControlData['checks'][] = $checkData;
                        else
                            $controlData['checks'][] = $checkData;
                    }

                    $controlData['checkCount'] = count($checks);
                    $controlData['rating']    /= $controlData['checkCount'];

                    $categoryData['checkCount'] += $controlData['checkCount'];
                    $targetData['checkCount']   += $controlData['checkCount'];
                    $totalCheckCount            += $controlData['checkCount'];

                    if ($controlData['checks'])
                        $categoryData['controls'][]  = $controlData;

                    if ($infoControlData['checks'])
                        $infoCategoryData['controls'][] = $infoControlData;
                }

                if ($categoryData['controls'])
                {
                    $categoryData['rating'] /= $categoryData['checkCount'];
                    $targetData['categories'][] = $categoryData;
                }

                if ($infoCategoryData['controls'])
                    $infoTargetData['categories'][] = $infoCategoryData;
            }

            if ($targetData['categories'])
                $targetData['rating'] /= $targetData['checkCount'];

            $data[] = $targetData;

            if ($infoTargetData['categories'])
                $infoData[] = $infoTargetData;
        }

        if ($totalCheckCount)
            $totalRating /= $totalCheckCount;

        // include all PHPRtfLite libraries
        Yii::setPathOfAlias('rtf', Yii::app()->basePath . '/extensions/PHPRtfLite/PHPRtfLite');
        Yii::import('rtf.Autoloader', true);
        PHPRtfLite_Autoloader::setBaseDir(Yii::app()->basePath . '/extensions/PHPRtfLite');
        Yii::registerAutoloader(array( 'PHPRtfLite_Autoloader', 'autoload' ), true);

        $rtf = new PHPRtfLite();
        $rtf->setCharset('UTF-8');
        $rtf->setMargins(1.5, 1, 1.5, 1);

        // borders
        $thinBorder = new PHPRtfLite_Border(
            $rtf,
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090')
        );

        // fonts
        $h1Font = new PHPRtfLite_Font(24, 'Helvetica');
        $h1Font->setBold();

        $h2Font = new PHPRtfLite_Font(20, 'Helvetica');
        $h2Font->setBold();

        $h3Font = new PHPRtfLite_Font(16, 'Helvetica');
        $h3Font->setBold();

        $textFont = new PHPRtfLite_Font(12, 'Helvetica');

        $boldFont = new PHPRtfLite_Font(12, 'Helvetica');
        $boldFont->setBold();

        $linkFont = new PHPRtfLite_Font(12, 'Helvetica', '#0088CC');
        $linkFont->setUnderline();

        // paragraphs
        $titlePar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $titlePar->setSpaceBefore(0);

        $projectPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $projectPar->setSpaceAfter(20);

        $h3Par = new PHPRtfLite_ParFormat();
        $h3Par->setSpaceAfter(10);

        $centerPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $centerPar->setSpaceAfter(20);

        $leftPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT);
        $leftPar->setSpaceAfter(20);

        $noPar = new PHPRtfLite_ParFormat();
        $noPar->setSpaceBefore(0);
        $noPar->setSpaceAfter(0);

        // title
        $section = $rtf->addSection();
        $section->writeText(Yii::t('app', 'Project Report'), $h1Font, $titlePar);
        $section->writeText($project->name . ' (' . $project->year . ')', $h2Font, $projectPar);

        // overall summary
        $section->writeText(Yii::t('app', 'Overall Summary'), $h3Font, $h3Par);
        $image = $section->addImage($this->_generateRatingImage($totalRating), $centerPar);

        // detailed summary
        $section->writeText(Yii::t('app', 'Detailed Summary') . '<br>', $h3Font, $noPar);
        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);

        $table->addRows(count($data) + 1);
        $table->addColumnsList(array( 8, 7, 3 ));
        $table->mergeCellRange(1, 2, 1, 3);
        $table->setFontForCellRange($boldFont, 1, 1, 1, 2);
        $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 2);
        $table->setFontForCellRange($textFont, 2, 1, count($data) + 1, 3);
        $table->setBorderForCellRange($thinBorder, 1, 1, count($data) + 1, 3);
        $table->setFirstRowAsHeader();

        // set paddings
        for ($row = 1; $row <= count($data) + 1; $row++)
            for ($col = 1; $col <= 3; $col++)
            {
                $table->getCell($row, $col)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            }

        $table->writeToCell(1, 1, Yii::t('app', 'Target'));
        $table->writeToCell(1, 2, Yii::t('app', 'Rating'));

        $row = 2;

        foreach ($data as $target)
        {
            $table->writeToCell($row, 1, $target['host']);
            $table->addImageToCell($row, 2, $this->_generateRatingImage($target['rating']));
            $table->writeToCell($row, 3, sprintf('%.2f', $target['rating']));

            $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

            $row++;
        }

        // detailed report with vulnerabilities
        $section->writeText(Yii::t('app', 'Detailed Report'), $h3Font, $h3Par);

        foreach ($data as $target)
        {
            $section->writeText($target['host'] . '<br>', $boldFont, $noPar);

            if (!count($target['categories']))
            {
                $section->writeText(Yii::t('app', 'No vulnerabilities found.') . '<br>', $textFont, $noPar);
                continue;
            }

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addColumnsList(array( 5, 13 ));

            $row = 1;

            foreach ($target['categories'] as $category)
            {
                $table->addRow();
                $table->mergeCellRange($row, 1, $row, 2);

                $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                $table->getCell($row, 1)->setBorder($thinBorder);
                $table->setFontForCellRange($boldFont, $row, 1, $row, 1);
                $table->setBackgroundForCellRange('#B0B0B0', $row, 1, $row, 1);
                $table->writeToCell($row, 1, $category['name']);

                $row++;

                foreach ($category['controls'] as $control)
                {
                    $table->addRow();
                    $table->mergeCellRange($row, 1, $row, 2);

                    $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                    $table->getCell($row, 1)->setBorder($thinBorder);
                    $table->setFontForCellRange($boldFont, $row, 1, $row, 1);
                    $table->setBackgroundForCellRange('#D0D0D0', $row, 1, $row, 1);
                    $table->writeToCell($row, 1, $control['name']);

                    $row++;

                    foreach ($control['checks'] as $check)
                    {
                        $table->addRow();
                        $table->mergeCellRange($row, 1, $row, 2);

                        $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 1)->setBorder($thinBorder);
                        $table->setFontForCellRange($boldFont, $row, 1, $row, 1);
                        $table->setBackgroundForCellRange('#F0F0F0', $row, 1, $row, 1);
                        $table->writeToCell($row, 1, $check['name']);

                        $row++;

                        // reference info
                        $table->addRow();
                        $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                        $table->getCell($row, 1)->setBorder($thinBorder);
                        $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 2)->setBorder($thinBorder);

                        $table->writeToCell($row, 1, Yii::t('app', 'Reference'));

                        $reference    = $check['reference'] . ( $check['referenceCode'] ? '-' . $check['referenceCode'] : '' );
                        $referenceUrl = '';

                        if ($check['referenceCode'] && $check['referenceCodeUrl'])
                            $referenceUrl = $check['referenceCodeUrl'];
                        else if ($check['referenceUrl'])
                            $referenceUrl = $check['referenceUrl'];

                        if ($referenceUrl)
                            $table->getCell($row, 2)->writeHyperLink($referenceUrl, $reference, $linkFont);
                        else
                            $table->writeToCell($row, 2, $reference);

                        $row++;

                        if ($check['background'])
                        {
                            $table->addRow();
                            $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                            $table->getCell($row, 1)->setBorder($thinBorder);
                            $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 2)->setBorder($thinBorder);

                            $table->writeToCell($row, 1, Yii::t('app', 'Background Info'));
                            $table->writeToCell($row, 2, $check['background']);

                            $row++;
                        }

                        if ($check['question'])
                        {
                            $table->addRow();
                            $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                            $table->getCell($row, 1)->setBorder($thinBorder);
                            $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 2)->setBorder($thinBorder);

                            $table->writeToCell($row, 1, Yii::t('app', 'Question'));
                            $table->writeToCell($row, 2, $check['question']);

                            $row++;
                        }

                        if ($check['result'])
                        {
                            $table->addRow();
                            $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                            $table->getCell($row, 1)->setBorder($thinBorder);
                            $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 2)->setBorder($thinBorder);

                            $table->writeToCell($row, 1, Yii::t('app', 'Result'));
                            $table->writeToCell($row, 2, $check['result']);

                            $row++;
                        }

                        if ($check['solutions'])
                        {
                            $table->addRows(count($check['solutions']));

                            $table->mergeCellRange($row, 1, $row + count($check['solutions']) - 1, 1);

                            $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                            $table->getCell($row, 1)->setBorder($thinBorder);
                            $table->writeToCell($row, 1, Yii::t('app', 'Solutions'));

                            foreach ($check['solutions'] as $solution)
                            {
                                $table->getCell($row, 1)->setBorder($thinBorder);
                                $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                                $table->getCell($row, 2)->setBorder($thinBorder);
                                $table->writeToCell($row, 2, $solution);

                                $row++;
                            }
                        }

                        if ($check['images'])
                        {
                            $table->addRows(count($check['images']));

                            $table->mergeCellRange($row, 1, $row + count($check['images']) - 1, 1);

                            $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                            $table->getCell($row, 1)->setBorder($thinBorder);
                            $table->writeToCell($row, 1, Yii::t('app', 'Attachments'));

                            foreach ($check['images'] as $image)
                            {
                                $table->getCell($row, 1)->setBorder($thinBorder);
                                $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                                $table->getCell($row, 2)->setBorder($thinBorder);

                                list($imageWidth, $imageHeight) = getimagesize($image);

                                $ratio       = $imageWidth / $imageHeight;
                                $imageWidth  = 12; // cm
                                $imageHeight = $imageWidth / $ratio;

                                $table->addImageToCell($row, 2, $image, new PHPRtfLite_ParFormat(), $imageWidth, $imageHeight);

                                $row++;
                            }
                        }

                        $table->addRow();

                        // rating
                        $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 1)->setBorder($thinBorder);
                        $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 2)->setBorder($thinBorder);

                        $table->writeToCell($row, 1, Yii::t('app', 'Rating'));
                        $table->writeToCell($row, 2, '■ ', new PHPRtfLite_Font(12, null, $check['ratingColor']));
                        $table->writeToCell($row, 2, $check['ratingName'] . ' (' . $check['rating'] . ')');

                        $row++;
                    }
                }
            }
        }

        // list of checks with information
        if ($infoData)
        {
            $section->writeText(Yii::t('app', 'Additional Information'), $h3Font, $h3Par);

            foreach ($infoData as $target)
            {
                $section->writeText($target['host'] . '<br>', $boldFont, $noPar);

                $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
                $table->addColumnsList(array( 5, 13 ));

                $row = 1;

                foreach ($target['categories'] as $category)
                {
                    $table->addRow();
                    $table->mergeCellRange($row, 1, $row, 2);

                    $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                    $table->getCell($row, 1)->setBorder($thinBorder);
                    $table->setFontForCellRange($boldFont, $row, 1, $row, 1);
                    $table->setBackgroundForCellRange('#B0B0B0', $row, 1, $row, 1);
                    $table->writeToCell($row, 1, $category['name']);

                    $row++;

                    foreach ($category['controls'] as $control)
                    {
                        $table->addRow();
                        $table->mergeCellRange($row, 1, $row, 2);

                        $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 1)->setBorder($thinBorder);
                        $table->setFontForCellRange($boldFont, $row, 1, $row, 1);
                        $table->setBackgroundForCellRange('#D0D0D0', $row, 1, $row, 1);
                        $table->writeToCell($row, 1, $control['name']);

                        $row++;

                        foreach ($control['checks'] as $check)
                        {
                            $table->addRow();
                            $table->mergeCellRange($row, 1, $row, 2);

                            $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 1)->setBorder($thinBorder);
                            $table->setFontForCellRange($boldFont, $row, 1, $row, 1);
                            $table->setBackgroundForCellRange('#F0F0F0', $row, 1, $row, 1);
                            $table->writeToCell($row, 1, $check['name']);

                            $row++;

                            // reference info
                            $table->addRow();
                            $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                            $table->getCell($row, 1)->setBorder($thinBorder);
                            $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 2)->setBorder($thinBorder);

                            $table->writeToCell($row, 1, Yii::t('app', 'Reference'));

                            $reference    = $check['reference'] . ( $check['referenceCode'] ? '-' . $check['referenceCode'] : '' );
                            $referenceUrl = '';

                            if ($check['referenceCode'] && $check['referenceCodeUrl'])
                                $referenceUrl = $check['referenceCodeUrl'];
                            else if ($check['referenceUrl'])
                                $referenceUrl = $check['referenceUrl'];

                            if ($referenceUrl)
                                $table->getCell($row, 2)->writeHyperLink($referenceUrl, $reference, $linkFont);
                            else
                                $table->writeToCell($row, 2, $reference);

                            $row++;

                            if ($check['background'])
                            {
                                $table->addRow();
                                $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($thinBorder);
                                $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                                $table->getCell($row, 2)->setBorder($thinBorder);

                                $table->writeToCell($row, 1, Yii::t('app', 'Background Info'));
                                $table->writeToCell($row, 2, $check['background']);

                                $row++;
                            }

                            if ($check['question'])
                            {
                                $table->addRow();
                                $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($thinBorder);
                                $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                                $table->getCell($row, 2)->setBorder($thinBorder);

                                $table->writeToCell($row, 1, Yii::t('app', 'Question'));
                                $table->writeToCell($row, 2, $check['question']);

                                $row++;
                            }

                            // result
                            $table->addRow();
                            $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                            $table->getCell($row, 1)->setBorder($thinBorder);
                            $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 2)->setBorder($thinBorder);

                            $table->writeToCell($row, 1, Yii::t('app', 'Result'));
                            $table->writeToCell($row, 2, $check['result']);

                            $row++;

                            if ($check['solutions'])
                            {
                                $table->addRows(count($check['solutions']));

                                $table->mergeCellRange($row, 1, $row + count($check['solutions']) - 1, 1);

                                $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($thinBorder);
                                $table->writeToCell($row, 1, Yii::t('app', 'Solutions'));

                                foreach ($check['solutions'] as $solution)
                                {
                                    $table->getCell($row, 1)->setBorder($thinBorder);
                                    $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                                    $table->getCell($row, 2)->setBorder($thinBorder);
                                    $table->writeToCell($row, 2, $solution);

                                    $row++;
                                }
                            }

                            if ($check['images'])
                            {
                                $table->addRows(count($check['images']));

                                $table->mergeCellRange($row, 1, $row + count($check['images']) - 1, 1);

                                $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($thinBorder);
                                $table->writeToCell($row, 1, Yii::t('app', 'Attachments'));

                                foreach ($check['images'] as $image)
                                {
                                    $table->getCell($row, 1)->setBorder($thinBorder);
                                    $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                                    $table->getCell($row, 2)->setBorder($thinBorder);

                                    list($imageWidth, $imageHeight) = getimagesize($image);

                                    $ratio       = $imageWidth / $imageHeight;
                                    $imageWidth  = 12; // cm
                                    $imageHeight = $imageWidth / $ratio;

                                    $table->addImageToCell($row, 2, $image, new PHPRtfLite_ParFormat(), $imageWidth, $imageHeight);

                                    $row++;
                                }
                            }

                            $table->addRow();

                            // rating
                            $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 1)->setBorder($thinBorder);
                            $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 2)->setBorder($thinBorder);

                            $table->writeToCell($row, 1, Yii::t('app', 'Rating'));
                            $table->writeToCell($row, 2, '■ ', new PHPRtfLite_Font(12, null, $check['ratingColor']));
                            $table->writeToCell($row, 2, $check['ratingName'] . ' (' . $check['rating'] . ')');

                            $row++;
                        }
                    }
                }
            }
        }

        $fileName = Yii::t('app', 'Project Report') . ' - ' . $project->name . ' (' . $project->year . ').rtf';
        $hashName = hash('sha256', rand() . time() . $fileName);
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName;

        $rtf->save($filePath);

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }

    /**
     * Show project report form.
     */
    public function actionProject()
    {
        $model = new ProjectReportForm();

        if (isset($_POST['ProjectReportForm']))
        {
            $model->attributes = $_POST['ProjectReportForm'];

            if ($model->validate())
                $this->_generateProjectReport($model->clientId, $model->projectId, $model->targetIds);
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
        }

        $clients = Client::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

        $this->breadcrumbs[] = array(Yii::t('app', 'Project Report'), '');

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Project Report');
		$this->render('project', array(
            'model'   => $model,
            'clients' => $clients
        ));
    }

    /**
     * Generate comparison report.
     */
    private function _generateComparisonReport($clientId, $projectId1, $projectId2)
    {
        $project1 = Project::model()->findByAttributes(array(
            'client_id' => $clientId,
            'id'        => $projectId1
        ));

        if ($project1 === null)
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'First project doesn\\\'t exist.'));
            return;
        }

        $project2 = Project::model()->findByAttributes(array(
            'client_id' => $clientId,
            'id'        => $projectId2
        ));

        if ($project2 === null)
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Second project doesn\\\'t exist.'));
            return;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $targets1 = Target::model()->findAllByAttributes(
            array( 'project_id' => $project1->id ),
            array( 'order'      => 't.name ASC'  )
        );

        $targets2 = Target::model()->findAllByAttributes(
            array( 'project_id' => $project2->id ),
            array( 'order'      => 't.name ASC'  )
        );

        // find corresponding targets
        $data = array();

        foreach ($targets1 as $target1)
            foreach ($targets2 as $target2)
                if ($target2->host == $target1->host)
                {
                    $data[] = array(
                        $target1,
                        $target2
                    );

                    break;
                }

        if (!$data)
            throw new CHttpException(404, Yii::t('app', 'No targets to compare.'));

        $targetsData = array();

        foreach ($data as $targets)
        {
            $targetData = array(
                'host'    => $targets[0]->host,
                'ratings' => array()
            );

            foreach ($targets as $target)
            {
                $rating     = 0;
                $checkCount = 0;

                // get all categories
                $categories = TargetCheckCategory::model()->findAllByAttributes(array(
                    'target_id' => $target->id
                ));

                // get all references (they are the same across all target categories)
                $referenceIds = array();

                $references = TargetReference::model()->findAllByAttributes(array(
                    'target_id' => $target->id
                ));

                foreach ($references as $reference)
                    $referenceIds[] = $reference->reference_id;

                foreach ($categories as $category)
                {
                    $controls = CheckControl::model()->findAllByAttributes(array(
                        'check_category_id' => $category->check_category_id
                    ));

                    $controlIds = array();

                    foreach ($controls as $control)
                        $controlIds[] = $control->id;

                    $criteria = new CDbCriteria();

                    $criteria->addInCondition('reference_id', $referenceIds);
                    $criteria->addInCondition('check_control_id', $controlIds);

                    if (!$category->advanced)
                        $criteria->addCondition('advanced = FALSE');

                    $checks = Check::model()->with(array(
                        'targetChecks' => array(
                            'alias'    => 'tcs',
                            'joinType' => 'INNER JOIN',
                            'on'       => 'tcs.target_id = :target_id AND tcs.status = :status AND tcs.rating != :hidden',
                            'params'   => array(
                                'target_id' => $target->id,
                                'status'    => TargetCheck::STATUS_FINISHED,
                                'hidden'    => TargetCheck::RATING_HIDDEN,
                            ),
                        ),
                    ))->findAll($criteria);

                    if (!$checks)
                        continue;

                    foreach ($checks as $check)
                    {
                        switch ($check->targetChecks[0]->rating)
                        {
                            case TargetCheck::RATING_INFO:
                                $rating += 1;
                                break;

                            case TargetCheck::RATING_LOW_RISK:
                                $rating += 2;
                                break;

                            case TargetCheck::RATING_MED_RISK:
                                $rating += 3;
                                break;

                            case TargetCheck::RATING_HIGH_RISK:
                                $rating += 4;
                                break;
                        }

                        $checkCount++;
                    }
                }

                if ($checkCount)
                    $rating /= $checkCount;

                $targetData['ratings'][] = $rating;
            }

            $targetsData[] = $targetData;
        }

        // include all PHPRtfLite libraries
        Yii::setPathOfAlias('rtf', Yii::app()->basePath . '/extensions/PHPRtfLite/PHPRtfLite');
        Yii::import('rtf.Autoloader', true);
        PHPRtfLite_Autoloader::setBaseDir(Yii::app()->basePath . '/extensions/PHPRtfLite');
        Yii::registerAutoloader(array( 'PHPRtfLite_Autoloader', 'autoload' ), true);

        $rtf = new PHPRtfLite();
        $rtf->setCharset('UTF-8');
        $rtf->setMargins(1.5, 1, 1.5, 1);

        // borders
        $thinBorder = new PHPRtfLite_Border(
            $rtf,
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090')
        );

        // fonts
        $h1Font = new PHPRtfLite_Font(24, 'Helvetica');
        $h1Font->setBold();

        $h2Font = new PHPRtfLite_Font(20, 'Helvetica');
        $h2Font->setBold();

        $h3Font = new PHPRtfLite_Font(16, 'Helvetica');
        $h3Font->setBold();

        $textFont = new PHPRtfLite_Font(12, 'Helvetica');

        $boldFont = new PHPRtfLite_Font(12, 'Helvetica');
        $boldFont->setBold();

        // paragraphs
        $titlePar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $titlePar->setSpaceBefore(0);

        $projectPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $projectPar->setSpaceAfter(20);

        $noPar = new PHPRtfLite_ParFormat();
        $noPar->setSpaceBefore(0);
        $noPar->setSpaceAfter(0);

        // title
        $section = $rtf->addSection();
        $section->writeText(Yii::t('app', 'Projects Comparison'), $h1Font, $titlePar);
        $section->writeText($project1->name . ' (' . $project1->year . ')<br>' . $project2->name . ' (' . $project2->year . ')', $h2Font, $projectPar);

        // detailed summary
        $section->writeText(Yii::t('app', 'Target Comparison') . '<br>', $h3Font, $noPar);
        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);

        $table->addRows(count($targetsData) + 1);
        $table->addColumnsList(array( 6, 6, 6 ));
        $table->setFontForCellRange($boldFont, 1, 1, 1, 3);
        $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
        $table->setFontForCellRange($textFont, 2, 1, count($targetsData) + 1, 3);
        $table->setBorderForCellRange($thinBorder, 1, 1, count($targetsData) + 1, 3);
        $table->setFirstRowAsHeader();

        // set paddings
        for ($row = 1; $row <= count($targetsData) + 1; $row++)
            for ($col = 1; $col <= 3; $col++)
            {
                $table->getCell($row, $col)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            }

        $table->writeToCell(1, 1, Yii::t('app', 'Target'));
        $table->writeToCell(1, 2, $project1->name . ' (' . $project1->year . ')');
        $table->writeToCell(1, 3, $project2->name . ' (' . $project2->year . ')');

        $row = 2;

        foreach ($targetsData as $target)
        {
            $table->writeToCell($row, 1, $target['host']);
            $table->addImageToCell($row, 2, $this->_generateRatingImage($target['ratings'][0]));
            $table->addImageToCell($row, 3, $this->_generateRatingImage($target['ratings'][1]));

            $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
            $table->getCell($row, 3)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

            $row++;
        }

        $fileName = Yii::t('app', 'Projects Comparison') . '.rtf';
        $hashName = hash('sha256', rand() . time() . $fileName);
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName;

        $rtf->save($filePath);

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }

    /**
     * Show comparison report form.
     */
    public function actionComparison()
    {
        $model = new ProjectComparisonForm();

        if (isset($_POST['ProjectComparisonForm']))
        {
            $model->attributes = $_POST['ProjectComparisonForm'];

            if ($model->validate())
                $this->_generateComparisonReport($model->clientId, $model->projectId1, $model->projectId2);
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
        }

        $clients = Client::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

        $this->breadcrumbs[] = array(Yii::t('app', 'Projects Comparison'), '');

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Projects Comparison');
		$this->render('comparison', array(
            'model'   => $model,
            'clients' => $clients
        ));
    }

    /**
     * Project list.
     */
    public function actionProjectList()
    {
        $response = new AjaxResponse();

        try
        {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $client = Client::model()->findByPk($model->id);

            if (!$client)
                throw new CHttpException(404, Yii::t('app', 'Client not found.'));

            $projects = Project::model()->findAllByAttributes(
                array( 'client_id' => $client->id ),
                array( 'order'     => 't.name ASC, t.year ASC' )
            );

            $projectList = array();

            foreach ($projects as $project)
                $projectList[] = array(
                    'id'   => $project->id,
                    'name' => CHtml::encode($project->name) . ' (' . $project->year . ')',
                );

            $response->addData('projects', $projectList);
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Target list.
     */
    public function actionTargetList()
    {
        $response = new AjaxResponse();

        try
        {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $project = Project::model()->findByPk($model->id);

            if (!$project)
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));

            $targets = Target::model()->findAllByAttributes(
                array( 'project_id' => $project->id ),
                array( 'order'      => 't.host ASC' )
            );

            $targetList = array();

            foreach ($targets as $target)
                $targetList[] = array(
                    'id'   => $target->id,
                    'host' => $target->host,
                );

            $response->addData('targets', $targetList);
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Fulfillment degree image.
     */
    private function _generateFulfillmentImage($degree)
    {
        $scale = imagecreatefrompng(Yii::app()->basePath . '/../images/fulfillment-stripe.png');
        imagealphablending($scale, false);
        imagesavealpha($scale, true);
        $image = imagecreatetruecolor(301, 30);
        //imagealphablending($image, false);
        //imagesavealpha($image, true);

        $lineCoord = $degree * 3;
        $white     = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
        $color     = imagecolorallocate($image, 0x3A, 0x87, 0xAD);

        imagefilledrectangle($image, 0, 0, 301, 30, $white);
        imagefilledrectangle($image, 0, 6, $lineCoord, 24, $color);
        imagecopyresampled($image, $scale, 0, 0, 0, 0, 301, 30, 301, 30);

        $hashName = hash('sha256', rand() . time() . rand());
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName . '.png';

        imagepng($image, $filePath, 0);
        imagedestroy($image);

        return $filePath;
    }

    /**
     * Sort controls.
     */
    public static function sortControls($a, $b)
    {
        return $a['degree'] > $b['degree'];
    }

    /**
     * Generate a Degree of Fulfillment report
     */
    private function _generateFulfillmentDegreeReport($clientId, $projectId, $targetIds)
    {
        $project = Project::model()->findByAttributes(array(
            'client_id' => $clientId,
            'id'        => $projectId
        ));

        if ($project === null)
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Project doesn\\\'t exist.'));
            return;
        }

        if (!$targetIds || !count($targetIds))
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Please select at least 1 target.'));
            return;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $targetIds);
        $criteria->addColumnCondition(array( 'project_id' => $project->id ));
        $criteria->order = 't.host ASC';
        $targets = Target::model()->findAll($criteria);

        $data = array();

        foreach ($targets as $target)
        {
            $targetData = array(
                'host'     => $target->host,
                'controls' => array(),
            );

            // get all references (they are the same across all target categories)
            $referenceIds = array();

            $references = TargetReference::model()->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            foreach ($references as $reference)
                $referenceIds[] = $reference->reference_id;

            // get all categories
            $categories = TargetCheckCategory::model()->with(array(
                'category' => array(
                    'with' => array(
                        'l10n' => array(
                            'joinType' => 'LEFT JOIN',
                            'on'       => 'language_id = :language_id',
                            'params'   => array( 'language_id' => $language )
                        )
                    )
                )
            ))->findAllByAttributes(
                array( 'target_id' => $target->id  ),
                array( 'order'     => 't.name ASC' )
            );

            foreach ($categories as $category)
            {
                // get all controls
                $controls = CheckControl::model()->with(array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    )
                ))->findAllByAttributes(
                    array( 'check_category_id' => $category->check_category_id ),
                    array( 'order'             => 't.name ASC' )
                );

                if (!$controls)
                    continue;

                foreach ($controls as $control)
                {
                    $controlData = array(
                        'name'   => $category->category->localizedName . ' / ' . $control->localizedName,
                        'degree' => 0.0,
                    );

                    $criteria = new CDbCriteria();

                    $criteria->addInCondition('t.reference_id', $referenceIds);
                    $criteria->addColumnCondition(array(
                        't.check_control_id' => $control->id
                    ));

                    if (!$category->advanced)
                        $criteria->addCondition('t.advanced = FALSE');

                    $checks = Check::model()->with(array(
                        'targetChecks' => array(
                            'alias'    => 'tcs',
                            'joinType' => 'INNER JOIN',
                            'on'       => 'tcs.target_id = :target_id AND tcs.status = :status',
                            'params'   => array(
                                'target_id' => $target->id,
                                'status'    => TargetCheck::STATUS_FINISHED,
                            ),
                        ),
                    ))->findAll($criteria);

                    if (!$checks)
                        continue;

                    foreach ($checks as $check)
                    {
                        switch ($check->targetChecks[0]->rating)
                        {
                            case TargetCheck::RATING_HIDDEN:
                            case TargetCheck::RATING_INFO:
                                $controlData['degree'] += 0;
                                break;

                            case TargetCheck::RATING_LOW_RISK:
                                $controlData['degree'] += 1;
                                break;

                            case TargetCheck::RATING_MED_RISK:
                                $controlData['degree'] += 2;
                                break;

                            case TargetCheck::RATING_HIGH_RISK:
                                $controlData['degree'] += 3;
                                break;
                        }
                    }

                    $maxDegree             = count($checks) * 3;
                    $controlData['degree'] = round(100 - $controlData['degree'] / $maxDegree * 100);

                    $targetData['controls'][] = $controlData;
                }
            }

            $data[] = $targetData;
        }

        // include all PHPRtfLite libraries
        Yii::setPathOfAlias('rtf', Yii::app()->basePath . '/extensions/PHPRtfLite/PHPRtfLite');
        Yii::import('rtf.Autoloader', true);
        PHPRtfLite_Autoloader::setBaseDir(Yii::app()->basePath . '/extensions/PHPRtfLite');
        Yii::registerAutoloader(array( 'PHPRtfLite_Autoloader', 'autoload' ), true);

        $rtf = new PHPRtfLite();
        $rtf->setCharset('UTF-8');
        $rtf->setMargins(1.5, 1, 1.5, 1);

        // borders
        $thinBorder = new PHPRtfLite_Border(
            $rtf,
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090')
        );

        // fonts
        $h1Font = new PHPRtfLite_Font(24, 'Helvetica');
        $h1Font->setBold();

        $h2Font = new PHPRtfLite_Font(20, 'Helvetica');
        $h2Font->setBold();

        $h3Font = new PHPRtfLite_Font(16, 'Helvetica');
        $h3Font->setBold();

        $textFont = new PHPRtfLite_Font(12, 'Helvetica');

        $boldFont = new PHPRtfLite_Font(12, 'Helvetica');
        $boldFont->setBold();

        $linkFont = new PHPRtfLite_Font(12, 'Helvetica', '#0088CC');
        $linkFont->setUnderline();

        // paragraphs
        $titlePar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $titlePar->setSpaceBefore(0);

        $projectPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $projectPar->setSpaceAfter(20);

        $h3Par = new PHPRtfLite_ParFormat();

        $centerPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $centerPar->setSpaceAfter(20);

        $leftPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT);
        $leftPar->setSpaceAfter(20);

        $noPar = new PHPRtfLite_ParFormat();
        $noPar->setSpaceBefore(0);
        $noPar->setSpaceAfter(0);

        // title
        $section = $rtf->addSection();
        $section->writeText(Yii::t('app', 'Degree of Fulfillment'), $h1Font, $titlePar);
        $section->writeText($project->name . ' (' . $project->year . ')', $h2Font, $projectPar);

        foreach ($data as $target)
        {
            $section->writeText($target['host'] . '<br>', $h3Font, $h3Par);

            if (!count($target['controls']))
            {
                $section->writeText(Yii::t('app', 'No checks.') . '<br>', $textFont, $noPar);
                continue;
            }

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addColumnsList(array( 5, 10, 3 ));

            $row = 1;

            $table->addRow();
            $table->mergeCellRange(1, 2, 1, 3);

            $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

            $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
            $table->getCell($row, 2)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

            $table->setFontForCellRange($boldFont, 1, 1, 1, 3);
            $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
            $table->setBorderForCellRange($thinBorder, 1, 1, 1, 3);
            $table->setFirstRowAsHeader();

            $table->writeToCell($row, 1, Yii::t('app', 'Control'));
            $table->writeToCell($row, 2, Yii::t('app', 'Degree of Fulfillment'));

            $row++;

            usort($target['controls'], array('ReportController', 'sortControls'));

            foreach ($target['controls'] as $control)
            {
                $table->addRow();
                $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                $table->getCell($row, 1)->setBorder($thinBorder);

                $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                $table->getCell($row, 2)->setBorder($thinBorder);
                $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

                $table->getCell($row, 3)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                $table->getCell($row, 3)->setBorder($thinBorder);

                $table->writeToCell($row, 1, $control['name'], $textFont);
                $table->addImageToCell($row, 2, $this->_generateFulfillmentImage($control['degree']));
                $table->writeToCell($row, 3, $control['degree'] . '%');

                $row++;
            }

            $table->setFontForCellRange($textFont, 1, 1, count($target['controls']) + 1, 2);
        }

        $fileName = Yii::t('app', 'Degree of Fulfillment') . ' - ' . $project->name . ' (' . $project->year . ').rtf';
        $hashName = hash('sha256', rand() . time() . $fileName);
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName;

        $rtf->save($filePath);

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }

    /**
     * Show degree of fulfillment report form.
     */
    public function actionFulfillment()
    {
        $model = new FulfillmentDegreeForm();

        if (isset($_POST['FulfillmentDegreeForm']))
        {
            $model->attributes = $_POST['FulfillmentDegreeForm'];

            if ($model->validate())
                $this->_generateFulfillmentDegreeReport($model->clientId, $model->projectId, $model->targetIds);
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
        }

        $clients = Client::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

        $this->breadcrumbs[] = array(Yii::t('app', 'Degree of Fulfillment'), '');

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Degree of Fulfillment');
		$this->render('fulfillment', array(
            'model'   => $model,
            'clients' => $clients
        ));
    }
}