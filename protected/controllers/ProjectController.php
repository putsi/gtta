<?php

/**
 * Project controller.
 */
class ProjectController extends Controller
{
    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'checkAuth',
            'checkUser + edit, target, edittarget, controltarget, uploadattachment, controlattachment, attachment, controlcheck, updatechecks',
            'checkAdmin + control',
            'ajaxOnly + savecheck, controlattachment, controlcheck, updatechecks',
            'postOnly + savecheck, uploadattachment, controlattachment, controlcheck, updatechecks',
		);
	}

    /**
     * Display a list of projects.
     */
	public function actionIndex($page=1)
	{
        $page = (int) $page;

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.deadline ASC, t.name ASC';
        $criteria->addCondition('t.status != :status');
        $criteria->params = array( 'status' => Project::STATUS_FINISHED );

        if (User::checkRole(User::ROLE_CLIENT))
        {
            $user = User::model()->findByPk(Yii::app()->user->id);
            $criteria->addColumnCondition(array( 'client_id' => $user->client_id ));
        }

        $projects = Project::model()->with('client')->findAll($criteria);

        $projectCount = Project::model()->count($criteria);
        $paginator    = new Paginator($projectCount, $page);

        $this->breadcrumbs[Yii::t('app', 'Projects')] = '';

        $projectStats = array();

        foreach ($projects as $project)
        {
            $checkCount    = 0;
            $finishedCount = 0;
            $lowRiskCount  = 0;
            $medRiskCount  = 0;
            $highRiskCount = 0;

            $targets = Target::model()->with(array(
                'checkCount',
                'finishedCount',
                'lowRiskCount',
                'medRiskCount',
                'highRiskCount',
            ))->findAllByAttributes(array(
                'project_id' => $project->id
            ));

            foreach ($targets as $target)
            {
                if ($target->checkCount)
                    $checkCount += $target->checkCount;

                if ($target->finishedCount)
                    $finishedCount += $target->finishedCount;

                if ($target->lowRiskCount)
                    $lowRiskCount += $target->lowRiskCount;

                if ($target->medRiskCount)
                    $medRiskCount += $target->medRiskCount;

                if ($target->highRiskCount)
                    $highRiskCount += $target->highRiskCount;
            }

            $projectStats[$project->id] = array(
                'checkCount'    => $checkCount,
                'finishedCount' => $finishedCount,
                'lowRiskCount'  => $lowRiskCount,
                'medRiskCount'  => $medRiskCount,
                'highRiskCount' => $highRiskCount
            );
        }

        // display the page
        $this->pageTitle = Yii::t('app', 'Projects');
		$this->render('index', array(
            'projects' => $projects,
            'stats'    => $projectStats,
            'p'        => $paginator,
            'statuses' => array(
                Project::STATUS_OPEN        => Yii::t('app', 'Open'),
                Project::STATUS_IN_PROGRESS => Yii::t('app', 'In Progress'),
                Project::STATUS_FINISHED    => Yii::t('app', 'Finished'),
            )
        ));
	}

    /**
     * Export vulnerabilities.
     */
    private function _exportVulns($project)
    {
        $model = new ProjectVulnExportForm();
        $model->attributes = $_POST['ProjectVulnExportForm'];

        $model->header = isset($_POST['ProjectVulnExportForm']['header']);

        if (!$model->validate())
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Error exporting vulnerabilities.'));
            return;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $targets = Target::model()->findAllByAttributes(
            array( 'project_id' => $project->id ),
            array( 'order'      => 't.host ASC' )
        );

        $data = array();

        if ($model->header)
        {
            $row = array();

            if (in_array(TargetCheck::COLUMN_TARGET, $model->columns))
                $row[] = Yii::t('app', 'Target');

            if (in_array(TargetCheck::COLUMN_NAME, $model->columns))
                $row[] = Yii::t('app', 'Name');

            if (in_array(TargetCheck::COLUMN_REFERENCE, $model->columns))
                $row[] = Yii::t('app', 'Reference');

            if (in_array(TargetCheck::COLUMN_BACKGROUND_INFO, $model->columns))
                $row[] = Yii::t('app', 'Background Info');

            if (in_array(TargetCheck::COLUMN_QUESTION, $model->columns))
                $row[] = Yii::t('app', 'Question');

            if (in_array(TargetCheck::COLUMN_RESULT, $model->columns))
                $row[] = Yii::t('app', 'Result');

            if (in_array(TargetCheck::COLUMN_SOLUTION, $model->columns))
                $row[] = Yii::t('app', 'Solution');

            if (in_array(TargetCheck::COLUMN_RATING, $model->columns))
                $row[] = Yii::t('app', 'Rating');

            $data[] = $row;
        }

        $ratings = array(
            TargetCheck::RATING_HIDDEN    => Yii::t('app', 'Hidden'),
            TargetCheck::RATING_INFO      => Yii::t('app', 'Info'),
            TargetCheck::RATING_LOW_RISK  => Yii::t('app', 'Low Risk'),
            TargetCheck::RATING_MED_RISK  => Yii::t('app', 'Med Risk'),
            TargetCheck::RATING_HIGH_RISK => Yii::t('app', 'High Risk'),
        );

        foreach ($targets as $target)
        {
            // get all references (they are the same across all target categories)
            $referenceIds = array();

            $references = TargetReference::model()->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            if (!$references)
                continue;

            foreach ($references as $reference)
                $referenceIds[] = $reference->reference_id;

            // get all categories
            $categories = TargetCheckCategory::model()->findAllByAttributes(
                array( 'target_id' => $target->id  ),
                array( 'order'     => 't.name ASC' )
            );

            if (!$categories)
                continue;

            foreach ($categories as $category)
            {
                // get all controls
                $controls = CheckControl::model()->findAllByAttributes(
                    array( 'check_category_id' => $category->check_category_id ),
                    array( 'order'             => 't.name ASC' )
                );

                if (!$controls)
                    continue;

                foreach ($controls as $control)
                {
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
                        '_reference'
                    ))->findAll($criteria);

                    if (!$checks)
                        continue;

                    foreach ($checks as $check)
                    {
                        if (!in_array($check->targetChecks[0]->rating, $model->ratings))
                            continue;

                        $row = array();

                        if (in_array(TargetCheck::COLUMN_TARGET, $model->columns))
                            $row[] = $target->host;

                        if (in_array(TargetCheck::COLUMN_NAME, $model->columns))
                            $row[] = $check->localizedName;

                        if (in_array(TargetCheck::COLUMN_REFERENCE, $model->columns))
                            $row[] = $check->_reference->name . ( $check->reference_code ? '-' . $check->reference_code : '' );

                        if (in_array(TargetCheck::COLUMN_BACKGROUND_INFO, $model->columns))
                            $row[] = $check->localizedBackgroundInfo;

                        if (in_array(TargetCheck::COLUMN_QUESTION, $model->columns))
                            $row[] = $check->localizedQuestion;

                        if (in_array(TargetCheck::COLUMN_RESULT, $model->columns))
                            $row[] = $check->targetChecks[0]->result;

                        if (in_array(TargetCheck::COLUMN_SOLUTION, $model->columns))
                        {
                            $solutions = array();

                            foreach ($check->targetCheckSolutions as $solution)
                                $solutions[] = $solution->solution->localizedSolution;

                            $row[] = implode("\n", $solutions);
                        }

                        if (in_array(TargetCheck::COLUMN_RATING, $model->columns))
                            $row[] = $ratings[$check->targetChecks[0]->rating];

                        $data[] = $row;
                    }
                }
            }
        }

        $csv = array();

        foreach ($data as $row)
        {
            $columns = array();

            foreach ($row as $column)
            {
                $column    = str_replace(array( "\r\n", "\r", "\n" ), "\r", $column);
                $columns[] = '"' . str_replace('"', '\"', $column) . '"';
            }

            $csv[] = implode(',', $columns);
        }

        $csv = implode("\n", $csv);

        $fileName = Yii::t('app', '{project} Vulnerabilities', array(
            '{project}' => $project->name . ' (' . $project->year . ')'
        )) . ' - ' . date('Y-m-d') .  '.csv';

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . strlen($csv));

        ob_clean();
        flush();

        echo $csv;

        exit();
    }

    /**
     * Display a list of targets.
     */
	public function actionView($id, $page=1)
	{
        $id   = (int) $id;
        $page = (int) $page;

        $project = Project::model()->with(array(
            'details' => array(
                'order' => 'subject ASC'
            )
        ))->findByPk($id);

        if (!$project)
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));

        if (User::checkRole(User::ROLE_CLIENT))
        {
            $user = User::model()->findByPk(Yii::app()->user->id);

            if ($user->client_id != $project->client_id)
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
        }

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        if (isset($_POST['ProjectVulnExportForm']))
            $this->_exportVulns($project);

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.host ASC';
        $criteria->addCondition('t.project_id = :project_id');
        $criteria->params = array( 'project_id' => $project->id );

        $client = Client::model()->findByPk($project->client_id);

        $targets = Target::model()->with(array(
            'checkCount',
            'finishedCount',
            'lowRiskCount',
            'medRiskCount',
            'highRiskCount',
        ))->findAll($criteria);

        $targetCount = Target::model()->count($criteria);
        $paginator   = new Paginator($targetCount, $page);

        $this->breadcrumbs[Yii::t('app', 'Projects')] = $this->createUrl('project/index');
        $this->breadcrumbs[$project->name] = '';

        // display the page
        $this->pageTitle = $project->name;
		$this->render('view', array(
            'project'  => $project,
            'client'   => $client,
            'targets'  => $targets,
            'p'        => $paginator,
            'statuses' => array(
                Project::STATUS_OPEN        => Yii::t('app', 'Open'),
                Project::STATUS_IN_PROGRESS => Yii::t('app', 'In Progress'),
                Project::STATUS_FINISHED    => Yii::t('app', 'Finished'),
            ),
            'ratings' => array(
                TargetCheck::RATING_INFO      => Yii::t('app', 'Info'),
                TargetCheck::RATING_LOW_RISK  => Yii::t('app', 'Low Risk'),
                TargetCheck::RATING_MED_RISK  => Yii::t('app', 'Med Risk'),
                TargetCheck::RATING_HIGH_RISK => Yii::t('app', 'High Risk'),
            ),
            'columns' => array(
                TargetCheck::COLUMN_TARGET          => Yii::t('app', 'Target'),
                TargetCheck::COLUMN_NAME            => Yii::t('app', 'Name'),
                TargetCheck::COLUMN_REFERENCE       => Yii::t('app', 'Reference'),
                TargetCheck::COLUMN_BACKGROUND_INFO => Yii::t('app', 'Background Info'),
                TargetCheck::COLUMN_QUESTION        => Yii::t('app', 'Question'),
                TargetCheck::COLUMN_RESULT          => Yii::t('app', 'Result'),
                TargetCheck::COLUMN_SOLUTION        => Yii::t('app', 'Solution'),
                TargetCheck::COLUMN_RATING          => Yii::t('app', 'Rating'),
            )
        ));
	}

    /**
     * Project edit page.
     */
	public function actionEdit($id=0)
	{
        $id        = (int) $id;
        $newRecord = false;

        if ($id)
            $project = Project::model()->findByPk($id);
        else
        {
            $project  = new Project();
            $newRecord = true;
        }

		$model = new ProjectEditForm(User::checkRole(User::ROLE_ADMIN) ? ProjectEditForm::ADMIN_SCENARIO : ProjectEditForm::USER_SCENARIO);

        if (!$newRecord)
        {
            $model->name     = $project->name;
            $model->year     = $project->year;
            $model->status   = $project->status;
            $model->clientId = $project->client_id;
            $model->deadline = $project->deadline;
        }
        else
            $model->deadline = date('Y-m-d');

		// collect user input data
		if (isset($_POST['ProjectEditForm']))
		{
			$model->attributes = $_POST['ProjectEditForm'];

			if ($model->validate())
            {
                $project->name      = $model->name;
                $project->year      = $model->year;
                $project->status    = $model->status;
                $project->client_id = $model->clientId;
                $project->deadline  = $model->deadline;

                $project->save();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Project saved.'));

                $project->refresh();

                if ($newRecord)
                    $this->redirect(array( 'project/edit', 'id' => $project->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[Yii::t('app', 'Projects')]  = $this->createUrl('project/index');

        if ($newRecord)
            $this->breadcrumbs[Yii::t('app', 'New Project')] = '';
        else
        {
            $this->breadcrumbs[$project->name] = $this->createUrl('project/view', array( 'id' => $project->id ));
            $this->breadcrumbs[Yii::t('app', 'Edit')] = '';
        }

        $clients = Client::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Project') : $project->name;
		$this->render('edit', array(
            'model'    => $model,
            'project'  => $project,
            'clients'  => $clients,
            'statuses' => array(
                Project::STATUS_OPEN        => Yii::t('app', 'Open'),
                Project::STATUS_IN_PROGRESS => Yii::t('app', 'In Progress'),
                Project::STATUS_FINISHED    => Yii::t('app', 'Finished'),
            )
        ));
	}

    /**
     * Display a list of details.
     */
	public function actionDetails($id, $page=1)
	{
        $id   = (int) $id;
        $page = (int) $page;

        if (!User::checkRole(User::ROLE_ADMIN) && !User::checkRole(User::ROLE_CLIENT))
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));

        $project = Project::model()->findByPk($id);

        if (!$project)
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));

        if (User::checkRole(User::ROLE_CLIENT))
        {
            $user = User::model()->findByPk(Yii::app()->user->id);

            if ($user->client_id != $project->client_id)
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
        }

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 't.subject ASC';
        $criteria->addCondition('t.project_id = :project_id');
        $criteria->params = array( 'project_id' => $project->id );

        $details = ProjectDetail::model()->findAll($criteria);

        $detailCount = ProjectDetail::model()->count($criteria);
        $paginator   = new Paginator($detailCount, $page);

        $this->breadcrumbs[Yii::t('app', 'Projects')] = $this->createUrl('project/index');
        $this->breadcrumbs[$project->name]            = $this->createUrl('project/view', array( 'id' => $project->id ));
        $this->breadcrumbs[Yii::t('app', 'Details')]  = '';

        // display the page
        $this->pageTitle = $project->name;
		$this->render('detail/index', array(
            'project'  => $project,
            'details'  => $details,
            'p'        => $paginator,
        ));
	}

    /**
     * Project detail edit page.
     */
	public function actionEditDetail($id, $detail=0)
	{
        $id        = (int) $id;
        $detail    = (int) $detail;
        $newRecord = false;

        if (!User::checkRole(User::ROLE_ADMIN) && !User::checkRole(User::ROLE_CLIENT))
            throw new CHttpException(403, Yii::t('app', 'Access denied.'));

        $project = Project::model()->findByPk($id);

        if (!$project)
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));

        if (User::checkRole(User::ROLE_CLIENT))
        {
            $user = User::model()->findByPk(Yii::app()->user->id);

            if ($user->client_id != $project->client_id)
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));
        }

        if ($detail)
        {
            $detail = ProjectDetail::model()->findByAttributes(array(
                'id'         => $detail,
                'project_id' => $project->id
            ));

            if (!$detail)
                throw new CHttpException(404, Yii::t('app', 'Detail not found.'));
        }
        else
        {
            $detail   = new ProjectDetail();
            $newRecord = true;
        }

		$model = new ProjectDetailEditForm();

        if (!$newRecord)
        {
            $model->subject = $detail->subject;
            $model->content = $detail->content;
        }

		// collect user input data
		if (isset($_POST['ProjectDetailEditForm']))
		{
			$model->attributes = $_POST['ProjectDetailEditForm'];

			if ($model->validate())
            {
                $detail->project_id = $project->id;
                $detail->subject    = $model->subject;
                $detail->content    = $model->content;

                $detail->save();

                Yii::app()->user->setFlash('success', Yii::t('app', 'Detail saved.'));

                $detail->refresh();

                if ($newRecord)
                    $this->redirect(array( 'project/editdetail', 'id' => $project->id, 'detail' => $detail->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[Yii::t('app', 'Projects')] = $this->createUrl('project/index');
        $this->breadcrumbs[$project->name]            = $this->createUrl('project/view', array( 'id' => $project->id ));
        $this->breadcrumbs[Yii::t('app', 'Details')]  = $this->createUrl('project/details', array( 'id' => $project->id ));

        if ($newRecord)
            $this->breadcrumbs[Yii::t('app', 'New Detail')] = '';
        else
            $this->breadcrumbs[$detail->subject] = '';

		// display the page
        $this->pageTitle = $detail->isNewRecord ? Yii::t('app', 'New Detail') : $detail->subject;
		$this->render('detail/edit', array(
            'model'   => $model,
            'project' => $project,
            'detail'  => $detail
        ));
	}

    /**
     * Display a list of check categories.
     */
	public function actionTarget($id, $target, $page=1)
	{
        $id     = (int) $id;
        $target = (int) $target;
        $page   = (int) $page;

        $project = Project::model()->findByPk($id);

        if (!$project)
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));

        $target = Target::model()->findByAttributes(array(
            'id'         => $target,
            'project_id' => $project->id
        ));

        if (!$target)
            throw new CHttpException(404, Yii::t('app', 'Target not found.'));

        if ($page < 1)
            throw new CHttpException(404, Yii::t('app', 'Page not found.'));

        $criteria = new CDbCriteria();
        $criteria->limit  = Yii::app()->params['entriesPerPage'];
        $criteria->offset = ($page - 1) * Yii::app()->params['entriesPerPage'];
        $criteria->order  = 'name ASC';
        $criteria->addCondition('t.target_id = :target_id');
        $criteria->params = array( 'target_id' => $target->id );

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $categories = TargetCheckCategory::model()->with(array(
            'category' => array(
                'with' => array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    ),
                )
            ),
        ))->findAll($criteria);

        $categoryCount = TargetCheckCategory::model()->count($criteria);
        $paginator     = new Paginator($categoryCount, $page);

        $client = Client::model()->findByPk($project->client_id);

        $this->breadcrumbs[Yii::t('app', 'Projects')] = $this->createUrl('project/index');
        $this->breadcrumbs[$project->name]            = $this->createUrl('project/view', array( 'id' => $project->id ));
        $this->breadcrumbs[$target->host]             = '';

        // display the page
        $this->pageTitle = $target->host;
		$this->render('target/index', array(
            'project'    => $project,
            'target'     => $target,
            'client'     => $client,
            'categories' => $categories,
            'p'          => $paginator,
            'statuses'   => array(
                Project::STATUS_OPEN        => Yii::t('app', 'Open'),
                Project::STATUS_IN_PROGRESS => Yii::t('app', 'In Progress'),
                Project::STATUS_FINISHED    => Yii::t('app', 'Finished'),
            )
        ));
	}

    /**
     * Project target edit page.
     */
	public function actionEditTarget($id, $target=0)
	{
        $id        = (int) $id;
        $target    = (int) $target;
        $newRecord = false;

        $project = Project::model()->findByPk($id);

        if (!$project)
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));

        if ($target)
        {
            $target = Target::model()->findByAttributes(array(
                'id'         => $target,
                'project_id' => $project->id
            ));

            if (!$target)
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));
        }
        else
        {
            $target    = new Target();
            $newRecord = true;
        }

		$model = new TargetEditForm();

        $model->categoryIds  = array();
        $model->referenceIds = array();

        if (!$newRecord)
        {
            $model->host = $target->host;

            $categories = TargetCheckCategory::model()->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            foreach ($categories as $category)
                $model->categoryIds[] = $category->check_category_id;

            $references = TargetReference::model()->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            foreach ($references as $reference)
                $model->referenceIds[] = $reference->reference_id;
        }

		// collect user input data
		if (isset($_POST['TargetEditForm']))
		{
            $model->categoryIds  = array();
            $model->referenceIds = array();

			$model->attributes = $_POST['TargetEditForm'];

			if ($model->validate())
            {
                $target->project_id = $project->id;
                $target->host       = $model->host;

                $target->save();

                $addCategories = array();
                $delCategories = array();

                $addReferences = array();
                $delReferences = array();

                if (!$newRecord)
                {
                    $oldCategories = array();
                    $oldReferences = array();

                    // fill in addCategories & delCategories arrays
                    $categories = TargetCheckCategory::model()->findAllByAttributes(array(
                        'target_id' => $target->id
                    ));

                    foreach ($categories as $category)
                        $oldCategories[] = $category->check_category_id;

                    foreach ($oldCategories as $category)
                        if (!in_array($category, $model->categoryIds))
                            $delCategories[] = $category;

                    foreach ($model->categoryIds as $category)
                        if (!in_array($category, $oldCategories))
                            $addCategories[] = $category;

                    // fill in addReferences & delReferences arrays
                    $references = TargetReference::model()->findAllByAttributes(array(
                        'target_id' => $target->id
                    ));

                    foreach ($references as $reference)
                        $oldReferences[] = $reference->reference_id;

                    foreach ($oldReferences as $reference)
                        if (!in_array($reference, $model->referenceIds))
                            $delReferences[] = $reference;

                    foreach ($model->referenceIds as $reference)
                        if (!in_array($reference, $oldReferences))
                            $addReferences[] = $reference;
                }
                else
                {
                    $addCategories = $model->categoryIds;
                    $addReferences = $model->referenceIds;
                }

                // delete categories
                if ($delCategories)
                {
                    $criteria = new CDbCriteria();

                    $criteria->addInCondition('check_category_id', $delCategories);
                    $criteria->addColumnCondition(array(
                        'target_id' => $target->id
                    ));

                    TargetCheckCategory::model()->deleteAll($criteria);
                }

                // add categories
                foreach ($addCategories as $category)
                {
                    $targetCategory = new TargetCheckCategory();
                    $targetCategory->target_id         = $target->id;
                    $targetCategory->check_category_id = $category;
                    $targetCategory->advanced          = true;

                    $targetCategory->save();
                    $targetCategory->updateStats();
                }

                // delete references
                if ($delReferences)
                {
                    $criteria = new CDbCriteria();
                    $criteria->addInCondition('reference_id', $delReferences);

                    TargetReference::model()->deleteAll($criteria);
                }

                // add references
                foreach ($addReferences as $reference)
                {
                    $targetReference = new TargetReference();
                    $targetReference->target_id    = $target->id;
                    $targetReference->reference_id = $reference;
                    $targetReference->save();
                }

                if ($addReferences || $delReferences)
                {
                    $categories = TargetCheckCategory::model()->findAllByAttributes(array(
                        'target_id' => $target->id
                    ));

                    foreach ($categories as $category)
                        $category->updateStats();
                }

                Yii::app()->user->setFlash('success', Yii::t('app', 'Target saved.'));

                $target->refresh();

                if ($newRecord)
                    $this->redirect(array( 'project/edittarget', 'id' => $project->id, 'target' => $target->id ));
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

        $this->breadcrumbs[Yii::t('app', 'Projects')] = $this->createUrl('project/index');
        $this->breadcrumbs[$project->name]            = $this->createUrl('project/view', array( 'id' => $project->id ));

        if ($newRecord)
            $this->breadcrumbs[Yii::t('app', 'New Target')] = '';
        else
        {
            $this->breadcrumbs[$target->host]         = $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id ));
            $this->breadcrumbs[Yii::t('app', 'Edit')] = '';
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $categories = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

        $references = Reference::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

		// display the page
        $this->pageTitle = $newRecord ? Yii::t('app', 'New Target') : $target->host;
		$this->render('target/edit', array(
            'model'      => $model,
            'project'    => $project,
            'target'     => $target,
            'categories' => $categories,
            'references' => $references
        ));
	}

    /**
     * Display a list of checks.
     */
	public function actionChecks($id, $target, $category)
	{
        $id       = (int) $id;
        $target   = (int) $target;
        $category = (int) $category;

        $project = Project::model()->findByPk($id);

        if (!$project)
            throw new CHttpException(404, Yii::t('app', 'Project not found.'));

        $target = Target::model()->findByAttributes(array(
            'id'         => $target,
            'project_id' => $project->id
        ));

        if (!$target)
            throw new CHttpException(404, Yii::t('app', 'Target not found.'));

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $category = TargetCheckCategory::model()->with(array(
            'category' => array(
                'with' => array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    )
                )
            )
        ))->findByAttributes(array(
            'target_id'         => $target->id,
            'check_category_id' => $category
        ));

        if (!$category)
            throw new CHttpException(404, Yii::t('app', 'Category not found.'));

        $controlIds   = array();
        $referenceIds = array();

        $controls = CheckControl::model()->findAllByAttributes(array(
            'check_category_id' => $category->check_category_id
        ));

        foreach ($controls as $control)
            $controlIds[] = $control->id;

        $references = TargetReference::model()->findAllByAttributes(array(
            'target_id' => $target->id
        ));

        foreach ($references as $reference)
            $referenceIds[] = $reference->reference_id;

        $criteria = new CDbCriteria();

        $criteria->addInCondition('t.check_control_id', $controlIds);
        $criteria->addInCondition('t.reference_id', $referenceIds);
        $criteria->order = 'control.name ASC, t.name ASC';

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
                'joinType' => 'LEFT JOIN',
                'on'       => 'tcs.target_id = :target_id',
                'params'   => array( 'target_id' => $target->id )
            ),
            'targetCheckInputs' => array(
                'alias'    => 'tis',
                'joinType' => 'LEFT JOIN',
                'on'       => 'tis.target_id = :target_id',
                'params'   => array( 'target_id' => $target->id )
            ),
            'targetCheckSolutions' => array(
                'alias'    => 'tss',
                'joinType' => 'LEFT JOIN',
                'on'       => 'tss.target_id = :target_id',
                'params'   => array( 'target_id' => $target->id )
            ),
            'targetCheckAttachments' => array(
                'alias'    => 'tas',
                'joinType' => 'LEFT JOIN',
                'on'       => 'tas.target_id = :target_id',
                'params'   => array( 'target_id' => $target->id ),
            ),
            'inputs' => array(
                'joinType' => 'LEFT JOIN',
                'with'     => array(
                    'l10n' => array(
                        'alias'    => 'l10n_i',
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'l10n_i.language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    )
                ),
                'order' => 'inputs.sort_order ASC'
            ),
            'results' => array(
                'joinType' => 'LEFT JOIN',
                'with'     => array(
                    'l10n' => array(
                        'alias'    => 'l10n_r',
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'l10n_r.language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    )
                ),
                'order' => 'results.sort_order ASC'
            ),
            'solutions' => array(
                'joinType' => 'LEFT JOIN',
                'with'     => array(
                    'l10n' => array(
                        'alias'    => 'l10n_s',
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'l10n_s.language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    )
                ),
                'order' => 'solutions.sort_order ASC'
            ),
            'control' => array(
                'joinType' => 'LEFT JOIN',
                'with'     => array(
                    'l10n' => array(
                        'alias'    => 'l10n_c',
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'l10n_c.language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    )
                )
            ),
            '_reference'
        ))->findAll($criteria);

        $client = Client::model()->findByPk($project->client_id);

        $this->breadcrumbs[Yii::t('app', 'Projects')] = $this->createUrl('project/index');
        $this->breadcrumbs[$project->name]            = $this->createUrl('project/view', array( 'id' => $project->id ));
        $this->breadcrumbs[$target->host]             = $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id ));
        $this->breadcrumbs[$category->category->localizedName] = '';

        // display the page
        $this->pageTitle = $category->category->localizedName;
		$this->render('target/check/index', array(
            'project'  => $project,
            'target'   => $target,
            'client'   => $client,
            'category' => $category,
            'checks'   => $checks,
            'statuses' => array(
                Project::STATUS_OPEN        => Yii::t('app', 'Open'),
                Project::STATUS_IN_PROGRESS => Yii::t('app', 'In Progress'),
                Project::STATUS_FINISHED    => Yii::t('app', 'Finished'),
            ),
            'ratings' => array(
                TargetCheck::RATING_HIDDEN    => Yii::t('app', 'Hidden'),
                TargetCheck::RATING_INFO      => Yii::t('app', 'Info'),
                TargetCheck::RATING_LOW_RISK  => Yii::t('app', 'Low Risk'),
                TargetCheck::RATING_MED_RISK  => Yii::t('app', 'Med Risk'),
                TargetCheck::RATING_HIGH_RISK => Yii::t('app', 'High Risk'),
            )
        ));
	}

    /**
     * Save check.
     */
    public function actionSaveCheck($id, $target, $category, $check)
    {
        $response = new AjaxResponse();

        try
        {
            $id       = (int) $id;
            $target   = (int) $target;
            $category = (int) $category;
            $check    = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project)
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));

            $target = Target::model()->findByAttributes(array(
                'id'         => $target,
                'project_id' => $project->id
            ));

            if (!$target)
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));

            $category = TargetCheckCategory::model()->with('category')->findByAttributes(array(
                'target_id'         => $target->id,
                'check_category_id' => $category
            ));

            if (!$category)
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));

            $controls = CheckControl::model()->findAllByAttributes(array(
                'check_category_id' => $category->check_category_id
            ));

            $controlIds = array();

            foreach ($controls as $control)
                $controlIds[] = $control->id;

            $criteria = new CDbCriteria();
            $criteria->addInCondition('check_control_id', $controlIds);
            $criteria->addColumnCondition(array(
                'id' => $check
            ));

            $check = Check::model()->find($criteria);

            if (!$check)
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));

            $model = new TargetCheckEditForm();
            $model->attributes = $_POST['TargetCheckEditForm_' . $check->id];

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

            $targetCheck = TargetCheck::model()->findByAttributes(array(
                'target_id' => $target->id,
                'check_id'  => $check->id
            ));

            if (!$targetCheck)
            {
                $targetCheck = new TargetCheck();
                $targetCheck->target_id = $target->id;
                $targetCheck->check_id  = $check->id;
            }

            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if (!$language)
                $language = Language::model()->findByAttributes(array(
                    'default' => true
                ));

            if (!$model->overrideTarget)
                $model->overrideTarget = NULL;

            if (!$model->protocol)
                $model->protocol = NULL;

            if (!$model->port)
                $model->port = NULL;

            if ($model->result == '')
                $model->result = NULL;

            if ($model->rating == '')
                $model->rating = NULL;

            $targetCheck->user_id         = Yii::app()->user->id;
            $targetCheck->language_id     = $language->id;
            $targetCheck->override_target = $model->overrideTarget;
            $targetCheck->protocol        = $model->protocol;
            $targetCheck->port            = $model->port;
            $targetCheck->result          = $model->result;
            $targetCheck->status          = $model->rating ? TargetCheck::STATUS_FINISHED : $targetCheck->status;
            $targetCheck->rating          = $model->rating;
            $targetCheck->save();

            $category->updateStats();

            // delete old solutions
            TargetCheckSolution::model()->deleteAllByAttributes(array(
                'target_id' => $target->id,
                'check_id'  => $check->id
            ));

            // delete old inputs
            TargetCheckInput::model()->deleteAllByAttributes(array(
                'target_id' => $target->id,
                'check_id'  => $check->id
            ));

            // add solutions
            if ($model->solutions)
                foreach ($model->solutions as $solutionId)
                {
                    $solution = CheckSolution::model()->findByAttributes(array(
                        'id'       => $solutionId,
                        'check_id' => $check->id
                    ));

                    if (!$solution)
                        throw new CHttpException(404, Yii::t('app', 'Solution not found.'));

                    $solution = new TargetCheckSolution();
                    $solution->target_id         = $target->id;
                    $solution->check_solution_id = $solutionId;
                    $solution->check_id          = $check->id;
                    $solution->save();
                }

            // add inputs
            if ($model->inputs && $check->automated)
                foreach ($model->inputs as $inputId => $inputValue)
                {
                    $input = CheckInput::model()->findByAttributes(array(
                        'id'       => $inputId,
                        'check_id' => $check->id
                    ));

                    if (!$input)
                        throw new CHttpException(404, Yii::t('app', 'Input not found.'));

                    if ($inputValue == '')
                        $inputValue = NULL;

                    $input = new TargetCheckInput();
                    $input->target_id      = $target->id;
                    $input->check_input_id = $inputId;
                    $input->check_id       = $check->id;
                    $input->value          = $inputValue;
                    $input->save();
                }

            $response->addData('rating', $targetCheck->rating);

            if ($project->status == Project::STATUS_OPEN)
            {
                $project->status = Project::STATUS_IN_PROGRESS;
                $project->save();
            }
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Save check category.
     */
    public function actionSaveCategory($id, $target, $category)
    {
        $response = new AjaxResponse();

        try
        {
            $id       = (int) $id;
            $target   = (int) $target;
            $category = (int) $category;

            $project = Project::model()->findByPk($id);

            if (!$project)
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));

            $target = Target::model()->findByAttributes(array(
                'id'         => $target,
                'project_id' => $project->id
            ));

            if (!$target)
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));

            $category = TargetCheckCategory::model()->with('category')->findByAttributes(array(
                'target_id'         => $target->id,
                'check_category_id' => $category
            ));

            if (!$category)
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));

            $model = new TargetCheckCategoryEditForm();
            $model->attributes = $_POST['TargetCheckCategoryEditForm'];

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

            $category->advanced = $model->advanced;
            $category->save();

            $category->updateStats();
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control function.
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

            $id      = $model->id;
            $project = Project::model()->findByPk($id);

            if ($project === null)
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $project->delete();
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
     * Control target function.
     */
    public function actionControlTarget()
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

            $id     = $model->id;
            $target = Target::model()->findByPk($id);

            if ($target === null)
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $target->delete();
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
     * Control detail function.
     */
    public function actionControlDetail()
    {
        $response = new AjaxResponse();

        try
        {
            if (!User::checkRole(User::ROLE_ADMIN) && !User::checkRole(User::ROLE_CLIENT))
                throw new CHttpException(403, Yii::t('app', 'Access denied.'));

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

            $id     = $model->id;
            $detail = ProjectDetail::model()->with('project')->findByPk($id);

            if ($detail === null)
                throw new CHttpException(404, Yii::t('app', 'Detail not found.'));

            if (User::checkRole(User::ROLE_CLIENT))
            {
                $user = User::model()->findByPk(Yii::app()->user->id);

                if ($user->client_id != $detail->project->client_id)
                    throw new CHttpException(403, Yii::t('app', 'Access denied.'));
            }

            switch ($model->operation)
            {
                case 'delete':
                    $detail->delete();
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
     * Upload attachment function.
     */
    function actionUploadAttachment($id, $target, $category, $check)
    {
        $response = new AjaxResponse();

        try
        {
            $id       = (int) $id;
            $target   = (int) $target;
            $category = (int) $category;
            $check    = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project)
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));

            $target = Target::model()->findByAttributes(array(
                'id'         => $target,
                'project_id' => $project->id
            ));

            if (!$target)
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));

            $category = TargetCheckCategory::model()->with('category')->findByAttributes(array(
                'target_id'         => $target->id,
                'check_category_id' => $category
            ));

            if (!$category)
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));

            $check = Check::model()->findByAttributes(array(
                'id'                => $check,
                'check_category_id' => $category->check_category_id
            ));

            if (!$check)
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));

            $model = new TargetCheckAttachmentUploadForm();
            $model->attachment = CUploadedFile::getInstanceByName('TargetCheckAttachmentUploadForm[attachment]');

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

            $attachment = new TargetCheckAttachment();
            $attachment->target_id = $target->id;
            $attachment->check_id  = $check->id;
            $attachment->name      = $model->attachment->name;
            $attachment->type      = $model->attachment->type;
            $attachment->size      = $model->attachment->size;
            $attachment->path      = hash('sha256', $attachment->name . rand() . time());
            $attachment->save();

            $model->attachment->saveAs(Yii::app()->params['attachments']['path'] . '/' . $attachment->path);

            $response->addData('name',       CHtml::encode($attachment->name));
            $response->addData('url',        $this->createUrl('project/attachment', array( 'path' => $attachment->path )));
            $response->addData('path',       $attachment->path);
            $response->addData('controlUrl', $this->createUrl('project/controlattachment'));
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control attachment.
     */
    public function actionControlAttachment()
    {
        $response = new AjaxResponse();

        try
        {
            $model = new TargetCheckAttachmentControlForm();
            $model->attributes = $_POST['TargetCheckAttachmentControlForm'];

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

            $path       = $model->path;
            $attachment = TargetCheckAttachment::model()->findByAttributes(array(
                'path' => $path
            ));

            if ($attachment === null)
                throw new CHttpException(404, Yii::t('app', 'Attachment not found.'));

            switch ($model->operation)
            {
                case 'delete':
                    $attachment->delete();
                    @unlink(Yii::app()->params['attachments']['path'] . '/' . $attachment->path);
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
     * Get attachment.
     */
    public function actionAttachment($path)
    {
        $attachment = TargetCheckAttachment::model()->findByAttributes(array(
            'path' => $path
        ));

        if ($attachment === null)
            throw new CHttpException(404, Yii::t('app', 'Attachment not found.'));

        $filePath = Yii::app()->params['attachments']['path'] . '/' . $attachment->path;

        if (!file_exists($filePath))
            throw new CHttpException(404, Yii::t('app', 'Attachment not found.'));

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . str_replace('"', '', $attachment->name) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $attachment->size);

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }

    /**
     * Control check function.
     */
    public function actionControlCheck($id, $target, $category, $check)
    {
        $response = new AjaxResponse();

        try
        {
            $id       = (int) $id;
            $target   = (int) $target;
            $category = (int) $category;
            $check    = (int) $check;

            $project = Project::model()->findByPk($id);

            if (!$project)
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));

            $target = Target::model()->findByAttributes(array(
                'id'         => $target,
                'project_id' => $project->id
            ));

            if (!$target)
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));

            $category = TargetCheckCategory::model()->with('category')->findByAttributes(array(
                'target_id'         => $target->id,
                'check_category_id' => $category
            ));

            if (!$category)
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));

            $controls = CheckControl::model()->findAllByAttributes(array(
                'check_category_id' => $category->check_category_id
            ));

            $controlIds = array();

            foreach ($controls as $control)
                $controlIds[] = $control->id;

            $criteria = new CDbCriteria();
            $criteria->addInCondition('check_control_id', $controlIds);
            $criteria->addColumnCondition(array(
                'id' => $check
            ));

            $check = Check::model()->find($criteria);

            if (!$check)
                throw new CHttpException(404, Yii::t('app', 'Check not found.'));

            $targetCheck = TargetCheck::model()->findByAttributes(array(
                'target_id' => $target->id,
                'check_id'  => $check->id
            ));

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

            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if (!$language)
                $language = Language::model()->findByAttributes(array(
                    'default' => true
                ));

            if (!$targetCheck)
            {
                $targetCheck = new TargetCheck();
                $targetCheck->target_id   = $target->id;
                $targetCheck->check_id    = $check->id;
                $targetCheck->user_id     = Yii::app()->user->id;
                $targetCheck->language_id = $language->id;
            }

            switch ($model->operation)
            {
                case 'start':
                    if (!in_array($targetCheck->status, array( TargetCheck::STATUS_OPEN, TargetCheck::STATUS_FINISHED )))
                        throw new CHttpException(403, Yii::t('app', 'Access denied.'));

                    // delete solutions
                    TargetCheckSolution::model()->deleteAllByAttributes(array(
                        'target_id' => $target->id,
                        'check_id'  => $check->id
                    ));

                    $targetCheck->status  = TargetCheck::STATUS_IN_PROGRESS;
                    $targetCheck->result  = null;
                    $targetCheck->rating  = null;
                    $targetCheck->started = null;
                    $targetCheck->pid     = null;
                    $targetCheck->save();

                    break;

                case 'stop':
                    if ($targetCheck->status != TargetCheck::STATUS_IN_PROGRESS)
                        throw new CHttpException(403, Yii::t('app', 'Access denied.'));

                    $targetCheck->status = TargetCheck::STATUS_STOP;
                    $targetCheck->save();

                    break;

                case 'reset':
                    if (!in_array($targetCheck->status, array( TargetCheck::STATUS_OPEN, TargetCheck::STATUS_FINISHED )))
                        throw new CHttpException(403, Yii::t('app', 'Access denied.'));

                    // delete solutions
                    TargetCheckSolution::model()->deleteAllByAttributes(array(
                        'target_id' => $target->id,
                        'check_id'  => $check->id
                    ));

                    // delete inputs
                    TargetCheckInput::model()->deleteAllByAttributes(array(
                        'target_id' => $target->id,
                        'check_id'  => $check->id
                    ));

                    // delete files
                    TargetCheckAttachment::model()->deleteAllByAttributes(array(
                        'target_id' => $target->id,
                        'check_id'  => $check->id
                    ));

                    $targetCheck->delete();

                    $response->addData('automated', $check->automated);
                    $response->addData('protocol',  $check->protocol);
                    $response->addData('port',      $check->port);

                    $inputValues = array();

                    // get default input values
                    if ($check->automated)
                    {
                        $inputs = CheckInput::model()->with(array(
                            'l10n' => array(
                                'alias'    => 'l10n_i',
                                'joinType' => 'LEFT JOIN',
                                'on'       => 'l10n_i.language_id = :language_id',
                                'params'   => array( 'language_id' => $language->id )
                            )
                        ))->findAllByAttributes(array(
                            'check_id' => $check->id
                        ));

                        foreach ($inputs as $input)
                            $inputValues[] = array(
                                'id'    => 'TargetCheckEditForm_' . $check->id . '_inputs_' . $input->id,
                                'value' => $input->localizedValue
                            );
                    }

                    $response->addData('inputs', $inputValues);

                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }

            $category->updateStats();
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Update checks function.
     */
    public function actionUpdateChecks($id, $target, $category)
    {
        $response = new AjaxResponse();

        try
        {
            $id       = (int) $id;
            $target   = (int) $target;
            $category = (int) $category;

            $project = Project::model()->findByPk($id);

            if (!$project)
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));

            $target = Target::model()->findByAttributes(array(
                'id'         => $target,
                'project_id' => $project->id
            ));

            if (!$target)
                throw new CHttpException(404, Yii::t('app', 'Target not found.'));

            $category = TargetCheckCategory::model()->with('category')->findByAttributes(array(
                'target_id'         => $target->id,
                'check_category_id' => $category
            ));

            if (!$category)
                throw new CHttpException(404, Yii::t('app', 'Category not found.'));

            $controls = CheckControl::model()->findAllByAttributes(array(
                'check_category_id' => $category->check_category_id
            ));

            $controlIds = array();

            foreach ($controls as $control)
                $controlIds[] = $control->id;

            $model = new TargetCheckUpdateForm();
            $model->attributes = $_POST['TargetCheckUpdateForm'];

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

            $checkIds = explode(',', $model->checks);

            $criteria = new CDbCriteria();
            $criteria->params = array(
                'target_id'   => $target->id
            );

            $criteria->addInCondition('check_control_id', $controlIds);
            $criteria->addInCondition('id', $checkIds);

            $checks = Check::model()->with(array(
                'targetChecks' => array(
                    'alias'    => 'tcs',
                    'joinType' => 'INNER JOIN',
                    'on'       => 'tcs.target_id = :target_id',
                    'params'   => array( 'target_id' => $target->id )
                ),
            ))->findAll($criteria);

            $checkData = array();
            date_default_timezone_set(Yii::app()->params['timeZone']);

            foreach ($checks as $check)
            {
                $time = $check->targetChecks[0]->started;

                if ($time)
                    $time = time() - strtotime($time);
                else
                    $time = -1;

                $checkData[] = array(
                    'id'       => $check->id,
                    'result'   => $check->targetChecks[0]->result,
                    'finished' => $check->targetChecks[0]->status == TargetCheck::STATUS_FINISHED,
                    'time'     => $time
                );
            }

            $response->addData('checks', $checkData);
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}
