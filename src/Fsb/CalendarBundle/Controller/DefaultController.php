<?php

namespace Fsb\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalendarBundle:Default:index.html.twig', array('name' => $name));
    }
}
