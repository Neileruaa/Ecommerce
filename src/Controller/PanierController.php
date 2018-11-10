<?php
/**
 * Created by PhpStorm.
 * User: aurelien
 * Date: 10/11/18
 * Time: 12:00
 */

namespace App\Controller;


use App\Entity\Produit;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends Controller{
	/**
	 * @Route("/Panier/add/{id}",name="Panier.addItem", methods={"GET"})
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
	 * @Route("/Panier/remove/{id}", name="Panier.removeItem", methods={"GET"})
	 * @param Produit $produit
	 * @param ObjectManager $manager
	 * @param Produit $produit
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function supprimerItemDuPanier(ObjectManager $manager, Produit $produit) {
		$panier=$this->getUser()->getPanier();
		$panier->removeListeProduit($produit);
		$manager->persist($panier);
		$manager->flush();
		return $this->redirectToRoute("produit.show");
	}
}