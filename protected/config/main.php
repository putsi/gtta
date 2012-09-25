<?php

// main Web application configuration
return array(
	'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'     => 'GTTA',
    'charset'  => 'utf-8',

	// preloading components
	'preload' => array( 'log' ),

	// autoloading model and component classes
	'import' => array(
		'application.models.*',
		'application.components.*',
        'application.extensions.PHPRtfLite.*',
        'application.extensions.PHPRtfLite.PHPRtfLite.*',
	),

	// application components
	'components' => array(
        'session' => array(
            'class'                  => 'CDbHttpSession',
            'sessionTableName'       => 'sessions',
            'connectionID'           => 'db',
            'autoCreateSessionTable' => true,
            'timeout'                => 60 * 60, // 1 hour
        ),

        'request' => array(
            'enableCsrfValidation' => true,
        ),

		'user' => array(
            'class'           => 'WebUser',
            'autoUpdateFlash' => true,
		),

		'urlManager' => array(
            'baseUrl'        => GTTA_PRODUCTION ? 'https://gtta.kicks-ass.net' : 'https://gtta.local',
			'urlFormat'      => 'path',
            'showScriptName' => false,
            'rules'          => array(
                // account
                '<action:(login|logout)>' => 'app/<action>',
                'app/l10n.js'             => 'app/l10n',

                // projects
                'projects/<page:\d+>'         => 'project/index',
                'projects'                    => 'project/index',
                'project/<id:\d+>/<page:\d+>' => 'project/view',
                'project/<id:\d+>'            => 'project/view',
                'project/<id:\d+>/edit'       => 'project/edit',
                'project/new'                 => 'project/edit',
                'project/control'             => 'project/control',

                // project details
                'project/<id:\d+>/details/<page:\d+>'       => 'project/details',
                'project/<id:\d+>/details'                  => 'project/details',
                'project/<id:\d+>/detail/<detail:\d+>/edit' => 'project/editdetail',
                'project/<id:\d+>/detail/new'               => 'project/editdetail',
                'project/detail/control'                    => 'project/controldetail',

                // project users
                'project/<id:\d+>/users/<page:\d+>' => 'project/users',
                'project/<id:\d+>/users'            => 'project/users',
                'project/<id:\d+>/user/add'         => 'project/adduser',
                'project/<id:\d+>/user/control'     => 'project/controluser',

                'project/<id:\d+>/target/<target:\d+>/<page:\d+>'                                            => 'project/target',
                'project/<id:\d+>/target/<target:\d+>'                                                       => 'project/target',
                'project/<id:\d+>/target/<target:\d+>/edit'                                                  => 'project/edittarget',
                'project/<id:\d+>/target/new'                                                                => 'project/edittarget',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>'                                  => 'project/checks',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>/save'                             => 'project/savecategory',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>/check/<check:\d+>/save'           => 'project/savecheck',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>/check/<check:\d+>/control'        => 'project/controlcheck',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>/update'                           => 'project/updatechecks',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>/check/<check:\d+>/attachment/new' => 'project/uploadattachment',
                'project/attachment/<path:[a-z\d]+>/download'                                                => 'project/attachment',
                'project/attachment/control'                                                                 => 'project/controlattachment',
                'project/target/control'                                                                     => 'project/controltarget',
                'project/search'                                                                             => 'project/search',

                // reports
                'reports/project'      => 'report/project',
                'reports/comparison'   => 'report/comparison',
                'reports/fulfillment'  => 'report/fulfillment',
                'reports/risk-matrix'  => 'report/riskmatrix',
                'reports/object-list'  => 'report/objectlist',

                // clients
                'clients/<page:\d+>'   => 'client/index',
                'clients'              => 'client/index',
                'client/<id:\d+>'      => 'client/view',
                'client/<id:\d+>/edit' => 'client/edit',
                'client/new'           => 'client/edit',
                'client/control'       => 'client/control',
                'client/search'        => 'client/search',

                // effort
                'effort' => 'effort/index',

                // checks
                'checks/<page:\d+>'         => 'check/index',
                'checks'                    => 'check/index',
                'check/<id:\d+>/<page:\d+>' => 'check/view',
                'check/<id:\d+>'            => 'check/view',
                'check/<id:\d+>/edit'       => 'check/edit',
                'check/new'                 => 'check/edit',
                'check/control'             => 'check/control',
                'check/search'              => 'check/search',

                // check controls
                'check/<id:\d+>/control/<control:\d+>/<page:\d+>' => 'check/viewcontrol',
                'check/<id:\d+>/control/<control:\d+>'            => 'check/viewcontrol',
                'check/<id:\d+>/control/<control:\d+>/edit'       => 'check/editcontrol',
                'check/<id:\d+>/control/new'                      => 'check/editcontrol',
                'check/control/control'                           => 'check/controlcontrol',

                // checks
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/edit' => 'check/editcheck',
                'check/<id:\d+>/control/<control:\d+>/check/new'              => 'check/editcheck',
                'check/control/check/control'                                 => 'check/controlcheck',

                // check results
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/results/<page:\d+>'       => 'check/results',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/results'                  => 'check/results',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/result/<result:\d+>/edit' => 'check/editresult',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/result/new'               => 'check/editresult',
                'check/control/check/result/control'                                              => 'check/controlresult',
                
                // check solutions
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/solutions/<page:\d+>'         => 'check/solutions',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/solutions'                    => 'check/solutions',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/solution/<solution:\d+>/edit' => 'check/editsolution',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/solution/new'                 => 'check/editsolution',
                'check/control/check/solution/control'                                                => 'check/controlsolution',
                
                // check inputs
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/inputs/<page:\d+>'      => 'check/inputs',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/inputs'                 => 'check/inputs',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/input/<input:\d+>/edit' => 'check/editinput',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/input/new'              => 'check/editinput',
                'check/control/check/input/control'                                             => 'check/controlinput',

                // references
                'references/<page:\d+>'   => 'reference/index',
                'references'              => 'reference/index',
                'reference/<id:\d+>/edit' => 'reference/edit',
                'reference/new'           => 'reference/edit',
                'reference/control'       => 'reference/control',

                // risk classification categories (templates)
                'risks/<page:\d+>'         => 'risk/index',
                'risks'                    => 'risk/index',
                'risk/<id:\d+>/<page:\d+>' => 'risk/view',
                'risk/<id:\d+>'            => 'risk/view',
                'risk/<id:\d+>/edit'       => 'risk/edit',
                'risk/new'                 => 'risk/edit',
                'risk/control'             => 'risk/control',

                // risk categories
                'risk/<id:\d+>/category/<category:\d+>/edit' => 'risk/editcategory',
                'risk/<id:\d+>/category/new'                 => 'risk/editcategory',
                'risk/<id:\d+>/category/control'             => 'risk/controlcategory',

                // users
                'users/<page:\d+>'   => 'user/index',
                'users'              => 'user/index',
                'user/<id:\d+>/edit' => 'user/edit',
                'user/new'           => 'user/edit',
                'user/control'       => 'user/control',

                // user projects
                'user/<id:\d+>/projects/<page:\d+>' => 'user/projects',
                'user/<id:\d+>/projects'            => 'user/projects',
                'user/<id:\d+>/project/add'         => 'user/addproject',
                'user/<id:\d+>/project/control'     => 'user/controlproject',
                'user/<id:\d+>/object-list'         => 'user/objectlist',

                // backup
                'backup'  => 'backup/backup',
                'restore' => 'backup/restore',
            ),
		),

        'db' => array(
			'connectionString' => 'pgsql:host=localhost;port=5432;dbname=gtta',
			'username'         => 'gtta',
			'password'         => '123',
			'charset'          => 'utf8',
		),

		'errorHandler' => array(
            'errorAction' => 'app/error',
        ),

		'log' => array(
			'class'  => 'CLogRouter',
			'routes' => array(
				array(
					'class'  => 'CFileLogRoute',
					'levels' => 'error, warning',
				),
                /*array(
                    'class'   => 'CProfileLogRoute',
                    'enabled' => true,
                ),
                array(
                    'class' => 'CWebLogRoute',
                ),*/
			),
		),
	),

    // parameters
	'params' => array(
        'entriesPerPage' => 10,

        'attachments' => array(
            'path'    => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../files/attachments',
            'maxSize' => 100 * 1024 * 1024, // 100 megabytes
        ),

        'backups' => array(
            'maxSize' => 100 * 1024 * 1024, // 100 megabytes
        ),

        'tmpPath'            => '/tmp',
        'timeZone'           => GTTA_PRODUCTION ? 'Europe/Zurich' : 'Europe/Moscow',
        'collapseCheckCount' => 20,
    ),

    // default controller name
    'defaultController' => 'app',

    // maintenance mode
    'catchAllRequest' => file_exists(dirname(__FILE__) . '/.maintenance') ? array( 'app/maintenance' ) : null,

    // language settings
    'sourceLanguage' => 'en',
    'language'       => 'en',
);
