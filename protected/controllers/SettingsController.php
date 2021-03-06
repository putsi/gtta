<?php

/**
 * Settings controller.
 */
class SettingsController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            "https",
			"checkAuth",
            "checkAdmin",
            "idle",
            "ajaxOnly + controllogo, integrationkey",
            "postOnly + controllogo, integrationkey",
		);
	}

    /**
     * Edit settings
     */
	public function actionEdit() {
        $form = new SettingsEditForm();

        /** @var System $system  */
        $system = System::model()->findByPk(1);

        $form->fromModel($system);
        $form->languageId = $system->language->id;

        // collect form input data
		if (isset($_POST["SettingsEditForm"])) {
			$form->attributes = $_POST["SettingsEditForm"];
            $form->communityAllowUnverified = isset($_POST["SettingsEditForm"]["communityAllowUnverified"]);
            $form->mailEncryption = isset($_POST["SettingsEditForm"]["mailEncryption"]);
            $form->scriptsVerbosity = isset($_POST["SettingsEditForm"]["scriptsVerbosity"]);
            $form->hostResolve = isset($_POST["SettingsEditForm"]["hostResolve"]);

			if ($form->validate()) {
                $langId = (int) $form->languageId;
                $lang = Language::model()->findByPk($langId);

                if (!$lang) {
                    throw new CHttpException(404, Yii::t("app", "Language not found."));
                }

                $lang->setUserDefault();
                $system->fromForm($form, array("git_username", "git_password"));

                if ($form->gitProto == System::GIT_PROTO_HTTPS) {
                    $system->git_username = $form->gitUsername;
                    $system->git_password = $form->gitPassword ? $form->gitPassword : $system->git_password;
                } elseif ($form->gitProto == System::GIT_PROTO_SSH) {
                    $form->gitKey = CUploadedFile::getInstanceByName("SettingsEditForm[gitKey]");
                    $form->gitKey->saveAs(Yii::app()->params["system"]["filesPath"] . DS . Yii::app()->params["packages"]["git"]["key"]);
                }

                $system->save();
                $this->_system->refresh();

                Yii::app()->user->setFlash("success", Yii::t("app", "Settings saved."));
            } else {
                Yii::app()->user->setFlash("error", Yii::t("app", "Please fix the errors below."));
            }
		}

        $languages = Language::model()->findAll();
        $this->breadcrumbs[] = array(Yii::t("app", "Settings"), "");

		// display the page
        $this->pageTitle = Yii::t("app", "Settings");
		$this->render("edit", array(
            "form" => $form,
            "system" => $system,
            "languages" => $languages,
        ));
    }

    /**
     * Upload logo.
     */
    public function actionUploadLogo() {
        $response = new AjaxResponse();

        try {
            $model = new SystemLogoUploadForm();
            $model->image = CUploadedFile::getInstanceByName("SystemLogoUploadForm[image]");

            if (!$model->validate()) {
                $errorText = "";

                foreach ($model->getErrors() as $error) {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $model->image->saveAs(Yii::app()->params["systemLogo"]["path"]);
            $this->_system->logo_type = $model->image->type;
            $this->_system->save();

            $response->addData("url", $this->createUrl("app/logo"));
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Control logo.
     */
    public function actionControlLogo() {
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

            if (!@file_exists(Yii::app()->params["systemLogo"]["path"])) {
                throw new CHttpException(404, Yii::t("app", "Logo not found."));
            }

            switch ($model->operation) {
                case "delete":
                    @unlink(Yii::app()->params["systemLogo"]["path"]);
                    $this->_system->logo_type = null;
                    $this->_system->save();

                    $response->addData("url", $this->createUrl("app/logo"));

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
     * Generate integration key logo.
     */
    public function actionIntegrationKey() {
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

            switch ($model->operation) {
                case "generate":
                    $this->_system->integration_key = strtoupper(hash("sha256",
                        rand() . time() . ($this->_system->workstation_id ? $this->_system->workstation_id : "N/A")
                    ));
                    $this->_system->save();

                    $response->addData("integrationKey", $this->_system->integration_key);

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
}