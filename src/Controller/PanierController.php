<?php
/**
 * Created by PhpStorm.
 * User: aurelien
 * Date: 10/11/18
 * Time: 12:00
 */

namespace App\Controller;


use App\Entity\PanierProduits;
use App\Entity\Produit;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PanierController
 * @Security("is_granted('ROLE_USER')")
 * @package App\Controller
 */
class PanierController extends Controller{
//	FIXME: Refaire ajouter et supprimé -> bugs
	/**
	 * @Route("/Panier/add/{id}",name="Panier.addItem", methods={"GET"})
	 * @param ObjectManager $manager
	 * @param Produit $produit
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function ajouterItemDansPanier(ObjectManager $manager, Produit $produit){
		$panier=$this->getUser()->getPanier();
		foreach ($panier->getPanierProduits() as $new_produit){
            if ($new_produit->getProduit()->getId()==$produit->getId()){
                $new_produit->setQuantity($new_produit->getQuantity()+1);
                //$panier->addPanierProduit($new_produit);
                $manager->persist($new_produit);
                $manager->flush();
                return $this->redirectToRoute("Produit.show");
            }
        }
        $new_produit=new PanierProduits();
		$new_produit->setProduit($produit);
		$new_produit->setPanier($panier);
		$new_produit->setQuantity(1);

		$panier->addPanierProduit($new_produit);
		$manager->persist($panier);
		$manager->flush();
		return $this->redirectToRoute("Produit.show");
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
        foreach ($panier->getPanierProduits() as $new_produit){
            if ($new_produit->getProduit()->getId()==$produit->getId()){
                if ($new_produit->getQuantity()<=1){
                    $panier->removePanierProduit($new_produit);
                    $manager->flush();
                    return $this->redirectToRoute("Produit.show");
                }
                $new_produit->setQuantity($new_produit->getQuantity()-1);

                $panier->addPanierProduit($new_produit);
                $manager->persist($panier);
                $manager->flush();
                return $this->redirectToRoute("Produit.show");
            }
        }
		return $this->redirectToRoute("Produit.show");
	}
}