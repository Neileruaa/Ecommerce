<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
	/**
	 * @Route("/registration/{typeCompte}", name="Security.registration")
	 * @param Request $request
	 * @param ObjectManager $manager
	 * @param UserPasswordEncoderInterface $encoder
	 * @param $typeCompte
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function registration(Request $request,
	                             ObjectManager $manager,
	                             UserPasswordEncoderInterface $encoder,
								 $typeCompte) {
		$recaptcha = new ReCaptcha('6Ld153kUAAAAAHSMD1u2Vc55NXhkEg1hXFMzYiGP');
		$resp = $recaptcha->verify($request->request->get('g-recaptcha-response'), $request->getClientIp());
		$errors = array();
		$user = new User();
		$form = $this->createForm(RegistrationType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() ){
			if ($resp->isSuccess()) {
				$hash = $encoder->encodePassword($user, $user->getPassword());
				$user->setPassword($hash);
				$user->setActive(true);
				$panier = new Panier();
				$user->setPanier($panier);
				$user->setRoles('ROLE_USER');
				if ($typeCompte == 'admin') {
					$user->setRoles('ROLE_ADMIN');
				}
				$manager->persist($panier);
				$manager->persist($user);
				$manager->flush();
				return $this->redirectToRoute('Security.login');
			}else{
				$errors = $resp->getErrorCodes();
			}
		}
		return $this->render('security/registration.html.twig',[
			'form'=>$form->createView(),
			'errors'=>$errors
		]);
    }

	/**
	 * @Route("/login", name="Security.login")
	 * @param AuthenticationUtils $authenticationUtils
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function login(AuthenticationUtils $authenticationUtils) {
		$lastUsername = $authenticationUtils->getLastUsername();
		$errors = $authenticationUtils->getLastAuthenticationError();
		return $this->render('security/login.html.twig',[
			'last_username' => $lastUsername,
			'errors' => $errors
		]);
    }

    /**
     * @Route("/logout", name="Security.logout")
     */
	public function logout() {}
}
