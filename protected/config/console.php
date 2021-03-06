<?php

$mainConfig = require(dirname(__FILE__) . "/main.php");

// This is the configuration for yiic console application.
return [
	"basePath" => dirname(__FILE__) . "/..",
	"name" => $mainConfig["name"],
    "runtimePath" => $mainConfig["runtimePath"],

    // preloading components
	"preload" => ["log"],

    // autoloading model and component classes
	"import" => [
        "application.forms.*",
		"application.models.*",
		"application.components.*",
        "application.components.formats.*",
        "application.components.filters.*",
        "application.components.reports.*",
        "application.components.reports.docx.*",
        "application.jobs.*",
        "ext.yii-mail.YiiMailMessage",
	],

	// application components
	"components" => [
		"db" => $mainConfig["components"]["db"],

        "mail" => [
            "class" => "ext.yii-mail.YiiMail",
            "transportType" => "smtp",
            "viewPath" => "application.views.mail",
            "logging" => false,
            "dryRun" => false,
            "transportOptions" => []
        ],

        "urlManager" => $mainConfig["components"]["urlManager"],

        "log" => [
			"class"  => "CLogRouter",
			"routes" => [
				[
					"class"   => "CFileLogRoute",
					"levels"  => "error, warning",
                    "logFile" => "console.log",
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.AutomationJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "automation.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.BackupJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "backup.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.ChainJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "checkchainautomation.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.ClearLogJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "clearlog.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.CommunityInstallJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "communityinstall.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.CommunityShareJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "communityshare.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.EmailJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "email.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.ModifiedPackagesJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "modifiedpackages.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.PackageJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "package.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.RegenerateJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "regenerate.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.RestoreJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "restore.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.StatsJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "stats.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.ReindexJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "reindex.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.UpdateJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "update.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.GitJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "git.log",
                    'maxLogFiles' => 1,
                ],
                [
                    "class"   => "CFileLogRoute",
                    "levels"  => "error",
                    "categories" => "bg.HostResolveJob",
                    "logPath" => $mainConfig["params"]["bgLogsPath"],
                    "logFile" => "hostresolve.log",
                    'maxLogFiles' => 1,
                ],
			],
		],
	],

    // parameters
    "params" => $mainConfig["params"],
    
    "commandMap" => [
        "migrate" => [
            "class" => "system.cli.commands.MigrateCommand",
            "migrationPath" => "application.migrations",
            "migrationTable" => "migrations",
            "connectionID" => "db",
            "templateFile" => "application.migrations.template",
        ],
        "checkupdate" => [
            "class" => "application.commands.CheckUpdateCommand",
        ],
    ],
];