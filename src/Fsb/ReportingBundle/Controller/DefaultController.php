<?php

namespace Fsb\ReportingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ReportingBundle:Default:index.html.twig', array('name' => $name));
    }
}
