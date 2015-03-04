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
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}