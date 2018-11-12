<?php

namespace App\Controller;

use App\Entity\Commande;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')")
 * Class CommandController
 * @package App\Controller
 */
class CommandController extends AbstractController {
	/**
	 * @Route("/commands/show", name="Command.show")
	 * @param ObjectManager $manager
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showCommand(ObjectManager $manager) {
//		$commands = $manager->getRepository(Commande::class)->findAll();
//		return $this->render('command/Command_show.html.twig',[
//			'command' => $commands
//		]);
	}
}
