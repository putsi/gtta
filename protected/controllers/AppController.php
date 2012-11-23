<?php

/**
 * Main app controller.
 */
class AppController extends Controller
{
    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
            'https',
			'checkAuth - login, error, maintenance, l10n',
            'postOnly + objectList',
            'ajaxOnly + objectList',
		);
	}

    /**
     * If user is logged in then redirect to a project list, otherwise
     * redirect to a login form.
     */
	public function actionIndex()
	{
        $this->redirect(array( 'project/index' ));
	}

    /**
     * Log the user in and redirect to a project list
     */
	public function actionLogin()
	{
        if (!Yii::app()->user->isGuest)
            $this->redirect(array( 'project/index' ));

		$model = new LoginForm();

		// collect user input data
		if (isset($_POST['LoginForm']))
		{
			$model->attributes = $_POST['LoginForm'];

			if ($model->validate())
            {
                if ($model->login())
				    $this->redirect(Yii::app()->user->returnUrl);
            }
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
		}

		// display the login form
        $this->pageTitle = Yii::t('app', 'Login');
		$this->render('login', array(
            'model' => $model
        ));
	}

    /**
     * Log the user out and redirect to the main page
     */
	public function actionLogout()
	{
        Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	/**
	 * Exception handler
	 */
	public function actionError()
	{
	    $error = Yii::app()->errorHandler->error;
        $this->breadcrumbs[] = array(Yii::t('app', 'Error'), '');

        if ($error)
	    {
	    	if (Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
            {
                $this->pageTitle = Yii::t('app', 'Error {code}', array( '{code}' => $error['code'] ));
	        	$this->render('error', array( 'message' => $error['message'] ));
            }
	    }
	}

    /**
	 * Maintenance handler
	 */
	public function actionMaintenance()
	{
        $this->breadcrumbs[] = array(Yii::t('app', 'Maintenance'), '');
        $this->pageTitle = Yii::t('app', 'Maintenance');
        $this->render('maintenance');
	}

    /**
     * Localization javascript file.
     */
    public function actionL10n()
    {
        header('Content-Type: text/javascript');
        echo $this->renderPartial('l10n');
    }

    /**
     * Object list.
     */
    public function actionObjectList()
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

            $language = Language::model()->findByAttributes(array(
                'code' => Yii::app()->language
            ));

            if ($language)
                $language = $language->id;

            $objects = array();

            switch ($model->operation)
            {
                case 'category-list':
                    $template  = RiskTemplate::model()->findByPk($model->id);

                    if (!$template)
                        throw new CHttpException(404, Yii::t('app', 'Template not found.'));

                    $criteria = new CDbCriteria();
                    $criteria->order = 'COALESCE(l10n.name, t.name) ASC';
                    $criteria->addColumnCondition(array(
                        't.risk_template_id' => $template->id
                    ));
                    $criteria->together = true;

                    $categories = RiskCategory::model()->with(array(
                        'l10n' => array(
                            'joinType' => 'LEFT JOIN',
                            'on'       => 'l10n.language_id = :language_id',
                            'params'   => array(
                                'language_id' => $language,
                            ),
                        ),
                        'checks'
                    ))->findAll($criteria);

                    foreach ($categories as $category)
                    {
                        $checks = array();

                        foreach ($category->checks as $check)
                            $checks[$check->check_id] = array(
                                'likelihood' => $check->likelihood,
                                'damage'     => $check->damage
                            );

                        $objects[] = array(
                            'id'     => $category->id,
                            'name'   => CHtml::encode($category->localizedName),
                            'checks' => $checks
                        );
                    }

                    break;

                case 'project-list':
                    $client = Client::model()->findByPk($model->id);

                    if (!$client)
                        throw new CHttpException(404, Yii::t('app', 'Client not found.'));

                    if (!$client->checkPermission())
                        throw new CHttpException(403, Yii::t('app', 'Access denied.'));

                    $criteria = new CDbCriteria();
                    $criteria->order = 't.name ASC, t.year ASC';
                    $criteria->addColumnCondition(array(
                        't.client_id' => $client->id
                    ));
                    $criteria->together = true;

                    if (User::checkRole(User::ROLE_ADMIN))
                        $projects = Project::model()->findAll($criteria);
                    else
                        $projects = Project::model()->with(array(
                            'project_users' => array(
                                'joinType' => 'INNER JOIN',
                                'on'       => 'project_users.user_id = :user_id',
                                'params'   => array(
                                    'user_id' => Yii::app()->user->id,
                                ),
                            ),
                        ))->findAll($criteria);

                    foreach ($projects as $project)
                        $objects[] = array(
                            'id'   => $project->id,
                            'name' => CHtml::encode($project->name) . ' (' . $project->year . ')',
                        );

                    break;

                case 'target-list':
                    $project = Project::model()->findByPk($model->id);

                    if (!$project)
                        throw new CHttpException(404, Yii::t('app', 'Project not found.'));

                    if (!$project->checkPermission())
                        throw new CHttpException(403, Yii::t('app', 'Access denied.'));

                    $targets = Target::model()->findAllByAttributes(
                        array( 'project_id' => $project->id ),
                        array( 'order'      => 't.host ASC' )
                    );

                    foreach ($targets as $target)
                        $objects[] = array(
                            'id'   => $target->id,
                            'host' => $target->host,
                        );

                    break;

                case 'target-check-list':
                    $targets = explode(',', $model->id);

                    foreach ($targets as $target)
                    {
                        $target = (int) $target;
                        $target = Target::model()->with('project')->findByPk($target);

                        if (!$target)
                            throw new CHttpException(404, Yii::t('app', 'Target not found.'));

                        if (!$target->project->checkPermission())
                            throw new CHttpException(403, Yii::t('app', 'Access denied.'));

                        $checkList    = array();
                        $referenceIds = array();

                        $references = TargetReference::model()->findAllByAttributes(array(
                            'target_id' => $target->id
                        ));

                        foreach ($references as $reference)
                            $referenceIds[] = $reference->reference_id;

                        $categories = TargetCheckCategory::model()->findAllByAttributes(
                            array( 'target_id' => $target->id  )
                        );

                        $ratings = array(
                            TargetCheck::RATING_HIDDEN    => Yii::t('app', 'Hidden'),
                            TargetCheck::RATING_INFO      => Yii::t('app', 'Info'),
                            TargetCheck::RATING_LOW_RISK  => Yii::t('app', 'Low Risk'),
                            TargetCheck::RATING_MED_RISK  => Yii::t('app', 'Med Risk'),
                            TargetCheck::RATING_HIGH_RISK => Yii::t('app', 'High Risk'),
                        );

                        $targetData = array(
                            'id'          => $target->id,
                            'host'        => $target->host,
                            'description' => $target->description,
                            'checks'      => array()
                        );

                        foreach ($categories as $category)
                        {
                            $controlIds = array();

                            $controls = CheckControl::model()->findAllByAttributes(array(
                                'check_category_id' => $category->check_category_id
                            ));

                            foreach ($controls as $control)
                                $controlIds[] = $control->id;

                            $criteria = new CDbCriteria();

                            $criteria->order = 'COALESCE(l10n.name, t.name) ASC';
                            $criteria->addInCondition('t.reference_id', $referenceIds);
                            $criteria->addInCondition('t.check_control_id', $controlIds);
                            $criteria->together = true;

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
                                    'on'       => 'tcs.target_id = :target_id AND tcs.status = :status AND (tcs.rating = :high_risk OR tcs.rating = :med_risk)',
                                    'params'   => array(
                                        'target_id' => $target->id,
                                        'status'    => TargetCheck::STATUS_FINISHED,
                                        'high_risk' => TargetCheck::RATING_HIGH_RISK,
                                        'med_risk'  => TargetCheck::RATING_MED_RISK,
                                    ),
                                )
                            ))->findAll($criteria);

                            foreach ($checks as $check)
                                $targetData['checks'][] = array(
                                    'id'         => $check->id,
                                    'ratingName' => $ratings[$check->targetChecks[0]->rating],
                                    'rating'     => $check->targetChecks[0]->rating,
                                    'name'       => CHtml::encode($check->localizedName),
                                );
                        }

                        $objects[] = $targetData;
                    }

                    break;

                default:
                    throw new CHttpException(403, Yii::t('app', 'Unknown operation.'));
                    break;
            }

            $response->addData('objects', $objects);
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}
