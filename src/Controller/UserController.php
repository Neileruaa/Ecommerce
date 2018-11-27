<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UserType;
use Doctrine\Common\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Security("is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')")
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
	/**
	 * @Route("/user/edit", name="User.edit")
	 * @param Request $request
	 * @param ObjectManager $manager
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function editUser(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
    	$user = $this->getUser();
		$form = $this->createForm(RegistrationType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){
			$hash = $encoder->encodePassword($user, $user->getPassword());
			$user->setPassword($hash);;
			$manager->persist($user);
			$manager->flush();
			return $this->redirectToRoute('Home.index');
		}elseif ($form->isSubmitted() && !$form->isValid()){
			dump($form);
			die();
		}

        return $this->render('user/edit_user.html.twig', [
            'user' => $user,
	        'form' => $form->createView(),
        ]);
    }

	/**
	 * @Route("/user/remove/{id}", name="User.remove", requirements={"id"="\d+"})
	 * @param ObjectManager $manager
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function removeUser(ObjectManager $manager, $id) {
		$currentUserId= $this->getUser()->getId();
		if ($currentUserId == $id){
			$session = new Session();
			$session->invalidate();
		}
		$user = $manager->getRepository(User::class)->find($id);
		$manager->remove($user);
		$manager->flush();
		return $this->redirectToRoute('Security.login');
	}
}
