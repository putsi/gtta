<?php

/**
 * Report template controller.
 */
class ReporttemplateController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            "https",
			"checkAuth",
            "checkAdmin",
            "ajaxOnly + controlheaderimage, controlsummary, controlsection, controlfile",
            "postOnly + uploadheaderimage, uploadfile, controlheaderimage, controlfile, controlsummary, controlsection",
            "idle",
		);
	}

    /**
     * Display a list of report templates.
     */
	public function actionIndex($page=1) {
        $page = (int) $page;

        if ($page < 1) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $criteria = new CDbCriteria();
        $criteria->limit = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
        $criteria->order = "COALESCE(l10n.name, t.name) ASC";
        $criteria->together = true;

        $templates = ReportTemplate::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array("language_id" => $language)
            )
        ))->findAll($criteria);

        $templateCount = ReportTemplate::model()->count($criteria);
        $paginator = new Paginator($templateCount, $page);

        $this->breadcrumbs[] = array(Yii::t("app", "Report Templates"), "");

        // display the page
        $this->pageTitle = Yii::t("app", "Report Templates");
		$this->render('index', array(
            "templates" => $templates,
            "p" => $paginator,
            "types" => ReportTemplate::getValidTypeNames(),
        ));
	}

    /**
     * Template edit page.
     */
	public function actionEdit($id=0) {
        $id = (int) $id;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        if ($id) {
            $template = ReportTemplate::model()->with(array(
                "l10n" => array(
                    "joinType" => "LEFT JOIN",
                    "on" => "language_id = :language_id",
                    "params" => array("language_id" => $language)
                )
            ))->findByPk($id);
        } else {
            $template = new ReportTemplate();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();
        $form = new ReportTemplateEditForm();
        $form->localizedItems = array();

        if (!$newRecord) {
            $form->fromModel($template);

            $templateL10n = ReportTemplateL10n::model()->findAllByAttributes(array(
                "report_template_id" => $template->id
            ));

            foreach ($templateL10n as $tl) {
                $form->localizedItems[$tl->language_id]["name"] = $tl->name;
                $form->localizedItems[$tl->language_id]["footer"] = $tl->footer;
                $form->localizedItems[$tl->language_id]["highDescription"] = $tl->high_description;
                $form->localizedItems[$tl->language_id]["medDescription"] = $tl->med_description;
                $form->localizedItems[$tl->language_id]["lowDescription"] = $tl->low_description;
                $form->localizedItems[$tl->language_id]["noneDescription"] = $tl->none_description;
                $form->localizedItems[$tl->language_id]["noVulnDescription"] = $tl->no_vuln_description;
                $form->localizedItems[$tl->language_id]["infoDescription"] = $tl->info_description;
            }
        }

        // collect user input data
        if (isset($_POST["ReportTemplateEditForm"])) {
            $form->attributes = $_POST["ReportTemplateEditForm"];
            $form->name = $form->defaultL10n($languages, "name");
            $form->footer = $form->defaultL10n($languages, "footer");
            $form->highDescription = $form->defaultL10n($languages, "highDescription");
            $form->medDescription = $form->defaultL10n($languages, "medDescription");
            $form->lowDescription = $form->defaultL10n($languages, "lowDescription");
            $form->noneDescription = $form->defaultL10n($languages, "noneDescription");
            $form->noVulnDescription = $form->defaultL10n($languages, "noVulnDescription");
            $form->infoDescription = $form->defaultL10n($languages, "infoDescription");

            if ($form->validate()) {
                $template->fromForm($form);
                $template->save();

                foreach ($form->localizedItems as $languageId => $value) {
                    $templateL10n = ReportTemplateL10n::model()->findByAttributes(array(
                        "report_template_id" => $template->id,
                        "language_id" => $languageId
                    ));

                    if (!$templateL10n) {
                        $templateL10n = new ReportTemplateL10n();
                        $templateL10n->report_template_id = $template->id;
                        $templateL10n->language_id = $languageId;
                    }

                    if ($value["name"] == "") {
                        $value["name"] = NULL;
                    }

                    if ($value["highDescription"] == "") {
                        $value["highDescription"] = NULL;
                    }

                    if ($value["medDescription"] == "") {
                        $value["medDescription"] = NULL;
                    }

                    if ($value["lowDescription"] == "") {
                        $value["lowDescription"] = NULL;
                    }

                    if ($value["noneDescription"] == "") {
                        $value["noneDescription"] = NULL;
                    }

                    if ($value["noVulnDescription"] == "") {
                        $value["noVulnDescription"] = NULL;
                    }

                    if ($value["infoDescription"] == "") {
                        $value["infoDescription"] = NULL;
                    }

                    if ($value["footer"] == "") {
                        $value["footer"] = NULL;
                    }

                    $templateL10n->name = $value["name"];
                    $templateL10n->high_description = $value["highDescription"];
                    $templateL10n->med_description = $value["medDescription"];
                    $templateL10n->low_description = $value["lowDescription"];
                    $templateL10n->none_description = $value["noneDescription"];
                    $templateL10n->no_vuln_description = $value["noVulnDescription"];
                    $templateL10n->info_description = $value["infoDescription"];
                    $templateL10n->footer = $value["footer"];
                    $templateL10n->save();
                }

                Yii::app()->user->setFlash("success", Yii::t("app", "Template saved."));

                $template->refresh();

                if ($newRecord) {
                    $this->redirect(array("reporttemplate/edit", "id" => $template->id));
                }

                // refresh the template
                $template = ReportTemplate::model()->with(array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    )
                ))->findByPk($id);
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
        }

        $this->breadcrumbs[] = array(Yii::t("app", "Report Templates"), $this->createUrl("reporttemplate/index"));

        if ($newRecord) {
            $this->breadcrumbs[] = array(Yii::t("app", "New Template"), "");
        } else {
            $this->breadcrumbs[] = array($template->localizedName, "");
        }

        // display the page
        $this->pageTitle = $newRecord ? Yii::t("app", "New Template") : $template->localizedName;
        $this->render("edit", array(
            "model" => $form,
            "template" => $template,
            "languages" => $languages,
        ));
	}

    /**
     * Control report template.
     */
    public function actionControl()
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

            $id = $model->id;
            $template = ReportTemplate::model()->findByPk($id);

            if ($template === null)
                throw new CHttpException(404, Yii::t('app', 'Template not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $template->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Upload header image function.
     */
    function actionUploadHeaderImage($id) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;

            $template = ReportTemplate::model()->findByPk($id);

            if (!$template) {
                throw new CHttpException(404, Yii::t("app", "Template not found."));
            }

            $model = new ReportTemplateHeaderImageUploadForm();
            $model->image = CUploadedFile::getInstanceByName("ReportTemplateHeaderImageUploadForm[image]");

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            // delete the old image
            if ($template->header_image_path) {
                @unlink(Yii::app()->params["reports"]["headerImages"]["path"] . "/" . $template->header_image_path);
            }

            $template->header_image_type = $model->image->type;
            $template->header_image_path = hash("sha256", $model->image->name . rand() . time());
            $template->save();
            $model->image->saveAs(Yii::app()->params["reports"]["headerImages"]["path"] . "/" . $template->header_image_path);

            $response->addData("url", $this->createUrl("reporttemplate/headerimage", array( "id" => $template->id )));
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control header image.
     */
    public function actionControlHeaderImage() {
        $response = new AjaxResponse();

        try {
            $model = new EntryControlForm();
            $model->attributes = $_POST["EntryControlForm"];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $template = ReportTemplate::model()->findByPk($model->id);

            if ($template === null) {
                throw new CHttpException(404, Yii::t("app", "Template not found."));
            }

            if (!$template->header_image_path) {
                throw new CHttpException(404, Yii::t("app", "Header image not found."));
            }

            switch ($model->operation) {
                case "delete":
                    @unlink(Yii::app()->params["reports"]["headerImages"]["path"] . "/" . $template->header_image_path);
                    $template->header_image_path = NULL;
                    $template->header_image_type = NULL;
                    $template->save();

                    break;

                default:
                    throw new CHttpException(403, Yii::t("app", "Unknown operation."));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Get header image.
     */
    public function actionHeaderImage($id) {
        $id = (int) $id;

        $template = ReportTemplate::model()->findByPk($id);

        if ($template === null) {
            throw new CHttpException(404, Yii::t("app", "Template not found."));
        }

        if (!$template->header_image_path) {
            throw new CHttpException(404, Yii::t("app", "Header image not found."));
        }

        $filePath = Yii::app()->params["reports"]["headerImages"]["path"] . "/" . $template->header_image_path;

        if (!file_exists($filePath)) {
            throw new CHttpException(404, Yii::t("app", "Header image not found."));
        }

        $extension = "jpg";

        if ($template->header_image_type == "image/png") {
            $extension = "png";
        }

        // give user a file
        header("Content-Description: File Transfer");
        header("Content-Type: " . $template->header_image_type);
        header("Content-Disposition: attachment; filename=\"header-image." . $extension . "\"");
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");
        header("Content-Length: " . filesize($filePath));

        ob_clean();
        flush();
        readfile($filePath);

        exit();
    }

    /**
     * Get rating image
     * @param $id
     * @param $rating
     * @throws CHttpException
     */
    function actionRatingImage($id, $rating) {
        $id = (int) $id;

        $template = ReportTemplate::model()->findByPk($id);

        if ($template === null) {
            throw new CHttpException(404, Yii::t("app", "Template not found."));
        }

        $image = $template->getRatingImage($rating);

        if (!$image) {
            throw new CHttpException(404, Yii::t("app", "Rating image not found."));
        }

        $filePath = Yii::app()->params["reports"]["ratingImages"]["path"] . "/" . $image->path;

        if (!file_exists($filePath)) {
            throw new CHttpException(404, Yii::t("app", "Rating image not found."));
        }

        $extension = "jpg";

        if ($image->type == "image/png") {
            $extension = "png";
        }

        // give user a file
        header("Content-Description: File Transfer");
        header("Content-Type: " . $image->type);
        header("Content-Disposition: attachment; filename=\"header-image." . $extension . "\"");
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");
        header("Content-Length: " . filesize($filePath));

        ob_clean();
        flush();
        readfile($filePath);

        exit();
    }

    /**
     * Control header image.
     */
    public function actionControlRatingImage($id) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;

            $model = new EntryControlForm();
            $model->attributes = $_POST["EntryControlForm"];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $template = ReportTemplate::model()->findByPk($id);

            if ($template === null) {
                throw new CHttpException(404, Yii::t("app", "Template not found."));
            }

            $image = $template->getRatingImage($model->id);

            if (!$image) {
                throw new CHttpException(404, Yii::t("app", "Rating image not found."));
            }

            switch ($model->operation) {
                case "delete":
                    @unlink(Yii::app()->params["reports"]["headerImages"]["path"] . "/" . $image->path);

                    $criteria = new CDbCriteria();
                    $criteria->addCondition('report_template_id = :report_template_id');
                    $criteria->addCondition('rating_id = :rating_id');
                    $criteria->params = array(
                        'report_template_id' => $template->id,
                        'rating_id' => $model->id,
                    );

                    ReportTemplateRatingImage::model()->deleteAll($criteria);
                    break;

                default:
                    throw new CHttpException(403, Yii::t("app", "Unknown operation."));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Upload rating image function
     * @param $id
     * @param $rating
     */
    function actionUploadRatingImage($id, $rating) {
        $response = new AjaxResponse();

        try {
            $tId = (int) $id;
            $rId = (int) $rating;

            $template = ReportTemplate::model()->findByPk($tId);

            if (!$template) {
                throw new CHttpException(404, Yii::t("app", "Template not found."));
            }

            $uploadForm = new ReportTemplateRatingImageUploadForm();
            $uploadForm->image = CUploadedFile::getInstanceByName("ReportTemplateRatingImageUploadForm[image]");

            if (!$uploadForm->validate()) {
                $errorText = "";

                foreach ($uploadForm->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $criteria = new CDbCriteria();
            $criteria->addCondition('report_template_id = :report_template_id');
            $criteria->addCondition('rating_id = :rating_id');
            $criteria->params = array(
                'report_template_id' => $id,
                'rating_id' => $rId,
            );

            $image = ReportTemplateRatingImage::model()->find($criteria);

            // delete the old image
            if ($image) {
                @unlink(Yii::app()->params["reports"]["headerImages"]["path"] . "/" . $image->path);
            }

            ReportTemplateRatingImage::model()->deleteAll($criteria);

            $newImage = new ReportTemplateRatingImage();
            $newImage->report_template_id = $template->id;
            $newImage->rating_id = $rId;
            $newImage->type = $uploadForm->image->type;
            $newImage->path = hash("sha256", $uploadForm->image->name . rand() . time());
            $newImage->save();
            $uploadForm->image->saveAs(Yii::app()->params["reports"]["ratingImages"]["path"] . "/" . $newImage->path);

            $response->addData("url", $this->createUrl("reporttemplate/ratingimage", array( "id" => $template->id, "rating" => $rId )));
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Display a list of summary blocks.
     */
	public function actionSummary($id, $page=1) {
        $id = (int) $id;
        $page = (int) $page;

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $template = ReportTemplate::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array("language_id" => $language)
            )
        ))->findByPk($id);

        if (!$template || $template->type != ReportTemplate::TYPE_RTF) {
            throw new CHttpException(404, Yii::t("app", "Template not found."));
        }

        if ($page < 1) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

        $criteria = new CDbCriteria();
        $criteria->limit = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
        $criteria->order = "t.rating_from ASC";
        $criteria->addColumnCondition(array("report_template_id" => $template->id));

        $summary_blocks = ReportTemplateSummary::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array("language_id" => $language)
            )
        ))->findAll($criteria);

        $blockCount = ReportTemplateSummary::model()->count($criteria);
        $paginator = new Paginator($blockCount, $page);

        $this->breadcrumbs[] = array(Yii::t("app", "Report Templates"), $this->createUrl("reporttemplate/index"));
        $this->breadcrumbs[] = array($template->localizedName, $this->createUrl("reporttemplate/edit", array("id" => $template->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Summary Blocks"), "");

        // display the page
        $this->pageTitle = $template->localizedName;
		$this->render("summary/index", array(
            "summaryBlocks" => $summary_blocks,
            "p" => $paginator,
            "template" => $template,
        ));
	}

    /**
     * Summary block edit page.
     */
	public function actionEditSummary($id, $summary=0) {
        $id = (int) $id;
        $summary = (int) $summary;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $template = ReportTemplate::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array( "language_id" => $language )
            )
        ))->findByPk($id);

        if (!$template || $template->type != ReportTemplate::TYPE_RTF) {
            throw new CHttpException(404, Yii::t("app", "Template not found."));
        }

        if ($summary) {
            $summary = ReportTemplateSummary::model()->with(array(
                "l10n" => array(
                    "joinType" => "LEFT JOIN",
                    "on" => "language_id = :language_id",
                    "params" => array( "language_id" => $language )
                )
            ))->findByAttributes(array(
                "id" => $summary,
                "report_template_id" => $template->id
            ));

            if (!$summary)
                throw new CHttpException(404, Yii::t("app", "Summary block not found."));
        } else {
            $summary = new ReportTemplateSummary();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new ReportTemplateSummaryEditForm();
        $model->localizedItems = array();

        if (!$newRecord) {
            $model->title = $summary->title;
            $model->summary = $summary->summary;
            $model->ratingFrom = $summary->rating_from;
            $model->ratingTo = $summary->rating_to;

            $reportTemplateSummaryL10n = ReportTemplateSummaryL10n::model()->findAllByAttributes(array(
                "report_template_summary_id" => $summary->id
            ));

            foreach ($reportTemplateSummaryL10n as $rtsl) {
                $model->localizedItems[$rtsl->language_id]["title"]   = $rtsl->title;
                $model->localizedItems[$rtsl->language_id]["summary"] = $rtsl->summary;
            }
        }

		// collect user input data
		if (isset($_POST["ReportTemplateSummaryEditForm"])) {
			$model->attributes = $_POST["ReportTemplateSummaryEditForm"];
            $model->title = $model->defaultL10n($languages, "title");
            $model->summary = $model->defaultL10n($languages, "summary");

			if ($model->validate()) {
                $summary->report_template_id = $template->id;
                $summary->title = $model->title;
                $summary->summary = $model->summary;
                $summary->rating_from = $model->ratingFrom;
                $summary->rating_to = $model->ratingTo;
                $summary->save();

                foreach ($model->localizedItems as $languageId => $value) {
                    $reportTemplateSummaryL10n = ReportTemplateSummaryL10n::model()->findByAttributes(array(
                        "report_template_summary_id" => $summary->id,
                        "language_id" => $languageId
                    ));

                    if (!$reportTemplateSummaryL10n) {
                        $reportTemplateSummaryL10n = new ReportTemplateSummaryL10n();
                        $reportTemplateSummaryL10n->report_template_summary_id = $summary->id;
                        $reportTemplateSummaryL10n->language_id = $languageId;
                    }

                    if ($value["summary"] == "") {
                        $value["summary"] = NULL;
                    }

                    if ($value["title"] == "") {
                        $value["title"] = NULL;
                    }

                    $reportTemplateSummaryL10n->title = $value["title"];
                    $reportTemplateSummaryL10n->summary = $value["summary"];
                    $reportTemplateSummaryL10n->save();
                }

                Yii::app()->user->setFlash("success", Yii::t("app", "Summary block saved."));
                $summary->refresh();

                if ($newRecord) {
                    $this->redirect(array("reporttemplate/editsummary", "id" => $template->id, "summary" => $summary->id));
                }

                // refresh the summary
                $summary = ReportTemplateSummary::model()->with(array(
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    )
                ))->findByAttributes(array(
                    "id" => $summary->id,
                    "report_template_id" => $template->id
                ));
            }

            if (count($model->getErrors()) > 0) {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

        $this->breadcrumbs[] = array(Yii::t("app", "Report Templates"), $this->createUrl("reporttemplate/index"));
        $this->breadcrumbs[] = array($template->localizedName, $this->createUrl("reporttemplate/edit", array("id" => $template->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Summary Blocks"), $this->createUrl("reporttemplate/summary", array("id" => $template->id)));

        if ($newRecord) {
            $this->breadcrumbs[] = array(Yii::t("app", "New Summary Block"), "");
        } else {
            $this->breadcrumbs[] = array($summary->localizedTitle, "");
        }

		// display the page
        $this->pageTitle = $newRecord ? Yii::t("app", "New Summary Block") : $summary->localizedTitle;
		$this->render("summary/edit", array(
            "model" => $model,
            "template" => $template,
            "summary" => $summary,
            "languages" => $languages,
        ));
	}

    /**
     * Summary block control function.
     */
    public function actionControlSummary()
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

            $id = $model->id;
            $summary = ReportTemplateSummary::model()->findByPk($id);

            if ($summary === null)
                throw new CHttpException(404, Yii::t('app', 'Summary block not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $summary->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Display a list of vulnerability sections.
     */
	public function actionVulnSections($id, $page=1) {
        $id = (int) $id;
        $page = (int) $page;

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $template = ReportTemplate::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array("language_id" => $language)
            )
        ))->findByPk($id);

        if (!$template || $template->type != ReportTemplate::TYPE_RTF) {
            throw new CHttpException(404, Yii::t("app", "Template not found."));
        }

        if ($page < 1) {
            throw new CHttpException(404, Yii::t("app", "Page not found."));
        }

        $criteria = new CDbCriteria();
        $criteria->limit = $this->entriesPerPage;
        $criteria->offset = ($page - 1) * $this->entriesPerPage;
        $criteria->order = "t.sort_order ASC";
        $criteria->addColumnCondition(array("report_template_id" => $template->id));

        $sections = ReportTemplateVulnSection::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array("language_id" => $language)
            )
        ))->findAll($criteria);

        $blockCount = ReportTemplateVulnSection::model()->count($criteria);
        $paginator = new Paginator($blockCount, $page);

        $this->breadcrumbs[] = array(Yii::t("app", "Report Templates"), $this->createUrl("reporttemplate/index"));
        $this->breadcrumbs[] = array($template->localizedName, $this->createUrl("reporttemplate/edit", array("id" => $template->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Vulnerability Sections"), "");

        // display the page
        $this->pageTitle = $template->localizedName;
		$this->render("vulnsection/index", array(
            "sections" => $sections,
            "p" => $paginator,
            "template" => $template,
        ));
	}

    /**
     * Section edit page.
     */
	public function actionEditVulnSection($id, $section=0) {
        $id = (int) $id;
        $section = (int) $section;
        $newRecord = false;

        $language = Language::model()->findByAttributes(array(
            "code" => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $template = ReportTemplate::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array("language_id" => $language)
            )
        ))->findByPk($id);

        if (!$template || $template->type != ReportTemplate::TYPE_RTF) {
            throw new CHttpException(404, Yii::t("app", "Template not found."));
        }

        if ($section) {
            $section = ReportTemplateVulnSection::model()->with(array(
                "l10n" => array(
                    "joinType" => "LEFT JOIN",
                    "on" => "language_id = :language_id",
                    "params" => array("language_id" => $language)
                )
            ))->findByAttributes(array(
                "id" => $section,
                "report_template_id" => $template->id
            ));

            if (!$section) {
                throw new CHttpException(404, Yii::t("app", "Section not found."));
            }
        } else {
            $section = new ReportTemplateVulnSection();
            $newRecord = true;
        }

        $languages = Language::model()->findAll();

		$model = new ReportTemplateVulnSectionEditForm();
        $model->localizedItems = array();

        if (!$newRecord) {
            $model->intro = $section->intro;
            $model->title = $section->title;
            $model->categoryId = $section->check_category_id;
            $model->sortOrder = $section->sort_order;
            $model->categoryId = $section->check_category_id;

            $reportTemplateSectionL10n = ReportTemplateVulnSectionL10n::model()->findAllByAttributes(array(
                "report_template_vuln_section_id" => $section->id
            ));

            foreach ($reportTemplateSectionL10n as $rtsl) {
                $model->localizedItems[$rtsl->language_id]["intro"] = $rtsl->intro;
                $model->localizedItems[$rtsl->language_id]["title"] = $rtsl->title;
            }
        } else {
            // increment last sort_order, if any
            $criteria = new CDbCriteria();
            $criteria->select = "MAX(sort_order) as max_sort_order";
            $criteria->addColumnCondition(array("report_template_id" => $template->id));

            $maxOrder = ReportTemplateVulnSection::model()->find($criteria);

            if ($maxOrder && $maxOrder->max_sort_order !== NULL) {
                $model->sortOrder = $maxOrder->max_sort_order + 1;
            }
        }

		// collect user input data
		if (isset($_POST["ReportTemplateVulnSectionEditForm"])) {
			$model->attributes = $_POST["ReportTemplateVulnSectionEditForm"];
            $model->intro = $model->defaultL10n($languages, "intro");
            $model->title = $model->defaultL10n($languages, "title");

			if ($model->validate()) {
                $check = ReportTemplateVulnSection::model()->findByAttributes(array(
                    "report_template_id" => $template->id,
                    "check_category_id" => $model->categoryId
                ));

                if ($check && $check->id != $section->id) {
                    $model->addError("categoryId", Yii::t("app", "Section with this category already exists."));
                } else {
                    $section->report_template_id = $template->id;
                    $section->intro = $model->intro;
                    $section->title = $model->title;
                    $section->sort_order = $model->sortOrder;
                    $section->check_category_id = $model->categoryId;
                    $section->save();

                    foreach ($model->localizedItems as $languageId => $value) {
                        $reportTemplateSectionL10n = ReportTemplateVulnSectionL10n::model()->findByAttributes(array(
                            "report_template_vuln_section_id" => $section->id,
                            "language_id" => $languageId
                        ));

                        if (!$reportTemplateSectionL10n) {
                            $reportTemplateSectionL10n = new ReportTemplateVulnSectionL10n();
                            $reportTemplateSectionL10n->report_template_vuln_section_id = $section->id;
                            $reportTemplateSectionL10n->language_id = $languageId;
                        }

                        if ($value["title"] == "") {
                            $value["title"] = NULL;
                        }

                        if ($value["intro"] == "") {
                            $value["intro"] = NULL;
                        }

                        $reportTemplateSectionL10n->intro = $value["intro"];
                        $reportTemplateSectionL10n->title = $value["title"];
                        $reportTemplateSectionL10n->save();
                    }

                    Yii::app()->user->setFlash("success", Yii::t("app", "Section saved."));

                    $section->refresh();

                    if ($newRecord) {
                        $this->redirect(array("reporttemplate/editvulnsection", "id" => $template->id, "section" => $section->id));
                    }

                    // refresh the section
                     $section = ReportTemplateVulnSection::model()->with(array(
                        "l10n" => array(
                            "joinType" => "LEFT JOIN",
                            "on" => "language_id = :language_id",
                            "params" => array("language_id" => $language)
                        )
                    ))->findByAttributes(array(
                        "id" => $section->id,
                        "report_template_id" => $template->id
                    ));
                }
            }

            if (count($model->getErrors()) > 0) {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

        $criteria = new CDbCriteria();
        $criteria->order = "COALESCE(l10n.name, t.name) ASC";
        $criteria->together = true;

        $categories = CheckCategory::model()->with(array(
            "l10n" => array(
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => array("language_id" => $language)
            )
        ))->findAll($criteria);

        $this->breadcrumbs[] = array(Yii::t("app", "Report Templates"), $this->createUrl("reporttemplate/index"));
        $this->breadcrumbs[] = array($template->localizedName, $this->createUrl("reporttemplate/edit", array("id" => $template->id)));
        $this->breadcrumbs[] = array(Yii::t("app", "Vulnerability Sections"), $this->createUrl("reporttemplate/vulnsections", array("id" => $template->id)));

        if ($newRecord) {
            $this->breadcrumbs[] = array(Yii::t("app", "New Section"), "");
        } else {
            $this->breadcrumbs[] = array($section->localizedTitle, "");
        }

		// display the page
        $this->pageTitle = $newRecord ? Yii::t("app", "New Section") : $section->localizedTitle;
		$this->render("vulnsection/edit", array(
            "model" => $model,
            "template" => $template,
            "section" => $section,
            "languages" => $languages,
            "categories" => $categories,
        ));
	}

    /**
     * Section control function.
     */
    public function actionControlVulnSection()
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

            $id = $model->id;
            $section = ReportTemplateVulnSection::model()->findByPk($id);

            if ($section === null)
                throw new CHttpException(404, Yii::t('app', 'Section not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $section->delete();
                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Upload report file.
     */
    function actionUploadFile($id) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            /** @var ReportTemplate $template */
            $template = ReportTemplate::model()->findByPk($id);

            if (!$template) {
                throw new CHttpException(404, Yii::t("app", "Template not found."));
            }

            $model = new ReportTemplateFileUploadForm();
            $model->file = CUploadedFile::getInstanceByName("ReportTemplateFileUploadForm[file]");

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            // delete the old file, if any
            if ($template->file_path) {
                @unlink(Yii::app()->params["reports"]["file"]["path"] . "/" . $template->file_path);
            }

            $template->file_path = hash("sha256", $model->file->name . rand() . time());
            $template->save();
            $model->file->saveAs(Yii::app()->params["reports"]["file"]["path"] . "/" . $template->file_path);

            $response->addData("url", $this->createUrl("reporttemplate/file", array("id" => $template->id)));
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control report file.
     */
    public function actionControlFile() {
        $response = new AjaxResponse();

        try {
            $model = new EntryControlForm();
            $model->attributes = $_POST["EntryControlForm"];

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            /** @var ReportTemplate $template */
            $template = ReportTemplate::model()->findByPk($model->id);

            if (!$template) {
                throw new CHttpException(404, Yii::t("app", "Template not found."));
            }

            if (!$template->file_path) {
                throw new CHttpException(404, Yii::t("app", "File not found."));
            }

            switch ($model->operation) {
                case "delete":
                    @unlink(Yii::app()->params["reports"]["file"]["path"] . "/" . $template->file_path);
                    $template->file_path = NULL;
                    $template->save();

                    break;

                default:
                    throw new CHttpException(403, Yii::t("app", "Unknown operation."));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Get report template file.
     */
    public function actionFile($id) {
        $id = (int) $id;
        $template = ReportTemplate::model()->findByPk($id);

        if (!$template) {
            throw new CHttpException(404, Yii::t("app", "Template not found."));
        }

        if (!$template->file_path) {
            throw new CHttpException(404, Yii::t("app", "File not found."));
        }

        $filePath = Yii::app()->params["reports"]["file"]["path"] . "/" . $template->file_path;

        if (!file_exists($filePath)) {
            throw new CHttpException(404, Yii::t("app", "File not found."));
        }

        // give user a file
        header("Content-Description: File Transfer");
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        header("Content-Disposition: attachment; filename=\"template.docx\"");
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");
        header("Content-Length: " . filesize($filePath));

        ob_clean();
        flush();
        readfile($filePath);

        exit();
    }

    /**
     * Sections edit page
     * @param $id
     * @throws CHttpException
     */
    public function actionSections($id) {
        $id = (int) $id;
        $template = ReportTemplate::model()->findByPk($id);

        if (!$template) {
            throw new CHttpException(404, Yii::t("app", "Template not found."));
        }

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(["report_template_id" => $template->id]);
        $criteria->order = "t.sort_order ASC";
        $sections = ReportTemplateSection::model()->findAll($criteria);

        $this->breadcrumbs[] = [Yii::t("app", "Report Templates"), $this->createUrl("reporttemplate/index")];
        $this->breadcrumbs[] = [$template->localizedName, $this->createUrl("reporttemplate/edit", ["id" => $template->id])];
        $this->breadcrumbs[] = [Yii::t("app", "Sections"), ""];

        // display the page
        $this->pageTitle = Yii::t("app", "Sections");

        $this->render("section/index", array(
            "template" => $template,
            "sections" => $sections,
            "languages" => Language::model()->findAll(),
            "system" => System::model()->findByPk(1)
        ));
    }

    /**
     * Section save
     * @param $id
     */
    public function actionSaveSection($id) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $template = ReportTemplate::model()->findByPk($id);

            if (!$template) {
                throw new Exception(Yii::t("app", "Template not found."));
            }

            $form = new ReportTemplateSectionEditForm(ReportTemplateSectionEditForm::SCENARIO_SECTION);
            $form->attributes = $_POST["ReportTemplateSectionEditForm"];

            if (!$form->validate()) {
                $errorText = "";

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            /** @var ReportTemplateSection $section */
            $section = null;

            if ($form->id) {
                $section = ReportTemplateSection::model()->findByAttributes([
                    "id" => $form->id,
                    "report_template_id" => $template->id,
                ]);
            } else {
                $form->id = null;
                $section = new ReportTemplateSection();
                $section->report_template_id = $template->id;
                $section->sort_order = 0;
            }

            if (!$section) {
                throw new Exception(Yii::t("app", "Section not found."));
            }

            $section->fromForm($form);
            $section->sort_order = array_search($section->id ? $section->id : null, $form->order);

            if (!$section->sort_order) {
                $section->sort_order = 0;
            }

            $section->save();

            // save orders
            foreach ($form->order as $order => $s) {
                ReportTemplateSection::model()->updateAll(
                    ["sort_order" => $order],
                    "id = :id AND report_template_id = :rt",
                    [
                        ":id" => $s,
                        ":rt" => $template->id,
                    ]
                );
            }

            $response->addData("id", $section->id);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Save section order
     * @param $id
     */
    public function actionSaveSectionOrder($id) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $template = ReportTemplate::model()->findByPk($id);

            if (!$template) {
                throw new Exception(Yii::t("app", "Template not found."));
            }

            $form = new ReportTemplateSectionEditForm();
            $form->attributes = $_POST["ReportTemplateSectionEditForm"];

            if (!$form->validate()) {
                $errorText = "";

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            // save orders
            foreach ($form->order as $order => $s) {
                ReportTemplateSection::model()->updateAll(
                    ["sort_order" => $order],
                    "id = :id AND report_template_id = :rt",
                    [
                        ":id" => $s,
                        ":rt" => $template->id,
                    ]
                );
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control report section.
     * @param $id
     */
    public function actionControlSection($id) {
        $response = new AjaxResponse();

        try {
            $id = (int) $id;
            $template = ReportTemplate::model()->findByPk($id);

            if (!$template) {
                throw new Exception(Yii::t("app", "Template not found."));
            }

            $form = new EntryControlForm();
            $form->attributes = $_POST["EntryControlForm"];

            if (!$form->validate()) {
                $errorText = "";

                foreach ($form->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            /** @var ReportTemplateSection $section */
            $section = ReportTemplateSection::model()->findByAttributes([
                "id" => $form->id,
                "report_template_id" => $template->id,
            ]);

            if (!$section) {
                throw new Exception(Yii::t("app", "Section not found."));
            }

            switch ($form->operation) {
                case "delete":
                    $section->delete();
                    break;

                default:
                    throw new Exception(Yii::t("app", "Unknown operation."));
                    break;
            }
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}