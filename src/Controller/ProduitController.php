<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produits/show",name="produit.show")
     * @Security("is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')")
     */
    public function showProduitsUser(){
        $produits=$this->getDoctrine()->getRepository(Produit::class)->findAll();
		$panier = $this->getUser()->getPanier();
        return $this->render("produit/showProduits.html.twig", [
        	"produits" => $produits,
	        'panier' => $panier
        ]);
    }

	/**
	 * @Route("/produits/add/{id}",name="produit.add", methods={"GET"})
	 * @param ObjectManager $manager
	 * @param Produit $produit
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
    public function ajouterItemDansPanier(ObjectManager $manager, Produit $produit){
        $panier=$this->getUser()->getPanier();
        $panier->addListeProduit($produit);
        $manager->persist($panier);
        $manager->flush();
        return $this->redirectToRoute("produit.show");
    }

	/**
	 * @Route("/produits/remove/{id}", name="produit.remove", methods={"GET"})
	 * @param Produit $produit
	 * @param ObjectManager $manager
	 * @param Produit $produit
	 */
	public function supprimerItemDuPanier(ObjectManager $manager, Produit $produit) {
		$panier=$this->getUser()->getPanier();
		$panier->removeListeProduit($produit);
		$manager->persist($panier);
		$manager->flush();
		return $this->redirectToRoute("produit.show");
	}
}
