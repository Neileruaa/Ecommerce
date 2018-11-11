<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Produit;
use App\Form\CommentType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentController
 * @Security("is_granted('ROLE_USER')")
 * @package App\Controller
 */
class CommentController extends AbstractController
{
	/**
	 * @Route("/comment/add/{id}", name="Comment.add")
	 * @param $form
	 * @param Produit $produit
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function addComment(ObjectManager $manager, Request $request, Produit $produit) {
		$comment = new Comment();
		$form = $this->createForm(CommentType::class, $comment);
		$form->handleRequest($request);
		if ($form->isValid() && $form->isSubmitted()){
			$comment = $form->getData();
			$comment->setCreatedAt(new \DateTime())->setAuthor($this->getUser())->setProduit($produit);
			$produit->addComment($comment);
			$this->getUser()->addComment($comment);
			$manager->persist($comment);
			$manager->flush();
		}
		return $this->redirectToRoute('Produit.viewDetails', ['id' => $produit->getId()]);
    }
}
