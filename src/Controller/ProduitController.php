<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Produit;
use App\Form\CommentType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProduitController
 * @Security("is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')")
 * @package App\Controller
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/produits/show",name="Produit.show")
     */
    public function showProduits(){
        $produits=$this->getDoctrine()->getRepository(Produit::class)->findAll();
		$panier = $this->getUser()->getPanier();
        $montant=0;
        foreach ($panier->getPanierProduits() as $produit){
            $montant=$montant+($produit->getProduit()->getPrix())*($produit->getQuantity());
        }

        return $this->render("produit/showProduits.html.twig", [
        	"produits" => $produits,
	        'panier' => $panier,
            'montant'=>$montant
        ]);
    }

	/**
	 * @Route("/produits/viewDetails/{id}", name="Produit.viewDetails", requirements={"id"="\d+"})
	 * @param Produit $produit
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function detailsProduits(Produit $produit) {
		$comment = new Comment();
		$form = $this->createForm(CommentType::class, $comment);

		return $this->render('produit/viewDetails.html.twig',[
			'produit' => $produit,
			'form' => $form->createView()
		]);
    }

	/**
	 * @Route("/produits/remove/{id}", name="Produit.remove", requirements={"id"="\d+"})
	 * @IsGranted("ROLE_ADMIN")
	 * @param Produit $produit
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function removeProduit(ObjectManager $manager, Produit $produit) {

		$manager->remove($produit);
		$manager->flush();
		return $this->redirectToRoute('Produit.show');
    }
}
