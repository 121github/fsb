<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareVoicegroupDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the deployment for voicegroup on the production environment';
    }

    public function run()
    {
        $commandList = array(
            'mv app/config/parameters.yml.dist_voicegroup app/config/parameters.yml',
            'mv web/app.php.dist_voicegroup web/app.php',
            'mv src/Fsb/BackendBundle/Resources/public/css/colors.css.dist_voicegroup src/Fsb/BackendBundle/Resources/public/css/colors.css',
            'mv src/Fsb/BackendBundle/Resources/public/images/logo.png.dist_voicegroup src/Fsb/BackendBundle/Resources/public/images/logo.png',
            'mv web/favicon.ico.dist_voicegroup web/favicon.ico'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}