<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends Controller
{
	/**
	 * @Route("/registration", name="Security.registration")
	 * @param Request $request
	 * @param ObjectManager $manager
	 * @param UserPasswordEncoderInterface $encoder
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function registration(Request $request,
	                             ObjectManager $manager,
	                             UserPasswordEncoderInterface $encoder) {
		$user = new User();
		$form = $this->createForm(RegistrationType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() ){
			$hash = $encoder->encodePassword($user, $user->getPassword());
			$user->setPassword($hash);
			$user->setRoles('ROLE_USER')->setActive(true);
			$manager->persist($user);
			$manager->flush();

			$this->redirectToRoute('Security.login');
		}
		return $this->render('security/registration.html.twig',[
			'form'=>$form->createView()
		]);
    }

	/**
	 * @Route("/login", name="Security.login")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function login() {
		return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout", name="Security.logout")
     */
	public function logout() {}
}
