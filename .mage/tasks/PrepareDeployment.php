<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the deployment';
    }

    public function run()
    {
        $commandList = array(
            'setfacl -R -m u:www-data:rwx -m u:\`whoami\`:rwx app/cache app/logs',
            'setfacl -dR -m u:www-data:rwx -m u:\`whoami\`:rwx app/cache app/logs',
            'rm -rf web/app.php.*',
            'rm -rf app/config/parameters.yml.*',
            'rm -rf src/Fsb/BackendBundle/Resources/public/css/colors.css.dist*',
            'rm -rf src/Fsb/BackendBundle/Resources/public/images/logo.png.dist*',
            'rm -rf web/favicon.ico.dist*'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}