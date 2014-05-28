<?php

namespace Fsb\NoteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('NoteBundle:Default:index.html.twig', array('name' => $name));
    }
}
