<?php

namespace Fsb\RecordBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('RecordBundle:Default:index.html.twig', array('name' => $name));
    }
}
