<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	/**
	 * @Route("/", name="Home.index")
	 * @param SessionInterface $session
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function index(SessionInterface $session) {
    	$session->clear();
	    $session->set('categorie', 'All');
        return $this->render('home/index.html.twig');
    }
}
