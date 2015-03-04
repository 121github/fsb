<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareFsbDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the deployment for fsb on the production environment';
    }

    public function run()
    {
        $commandList = array(
            'mv app/config/parameters.yml.dist_fsb app/config/parameters.yml',
            'mv web/app.php.dist_fsb web/app.php',
            'mv src/Fsb/BackendBundle/Resources/public/css/colors.css.dist_fsb src/Fsb/BackendBundle/Resources/public/css/colors.css',
            'mv src/Fsb/BackendBundle/Resources/public/images/logo.png.dist_fsb src/Fsb/BackendBundle/Resources/public/images/logo.png',
            'mv web/favicon.ico.dist_fsb web/favicon.ico'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}