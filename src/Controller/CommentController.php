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
 * @Security("is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')")
 * @package App\Controller
 */
class CommentController extends AbstractController
{
	/**
	 * id = id du produit pas du comment
	 * @Route("/comment/add/{id}", name="Comment.add")
	 * @param ObjectManager $manager
	 * @param Request $request
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

	/**
	 * @Route("/comment/remove/{productid}/{id}", name="Comment.remove")
	 * @isGranted("ROLE_ADMIN")
	 */
	public function removeComment(ObjectManager $manager, Request $request, $id, $productid) {
		$comment=$manager->getRepository(Comment::class)->find($id);
		$produit=$manager->getRepository(Produit::class)->find($productid);
		$this->getUser()->removeComment($comment);
		$produit->removeComment($comment);
		$manager->remove($comment);
		$manager->flush();
		return $this->redirectToRoute('Produit.viewDetails',['id'=>$produit->getId()]);
    }
}
