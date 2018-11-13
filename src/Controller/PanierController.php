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
                if ($produit->getStock()>0){
                    $new_produit->setQuantity($new_produit->getQuantity()+1);
                    $produit->setStock($produit->getStock()-1);
                    //$panier->addPanierProduit($new_produit);
                    $manager->persist($new_produit);
                    $manager->flush();
                    return $this->redirectToRoute("Produit.show");
                }
                return $this->redirectToRoute("Produit.show");
            }
        }
        if ($produit->getStock()>0){
            $produit->setStock($produit->getStock()-1);
            $new_produit=new PanierProduits();
            $new_produit->setProduit($produit);
            $new_produit->setPanier($panier);
            $new_produit->setQuantity(1);

            $panier->addPanierProduit($new_produit);
            $manager->persist($panier);
            $manager->flush();
        }
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
                    $produit->setStock($produit->getStock()+1);
                    $panier->removePanierProduit($new_produit);
                    $manager->flush();
                    return $this->redirectToRoute("Produit.show");
                }
                $produit->setStock($produit->getStock()+1);
                $new_produit->setQuantity($new_produit->getQuantity()-1);

                $panier->addPanierProduit($new_produit);
                $manager->persist($panier);
                $manager->flush();
                return $this->redirectToRoute("Produit.show");
            }
        }
		return $this->redirectToRoute("Produit.show");
	}

    /**
     * @Route("/panier/vider",name="panier.vider")
     */
	public function viderPanier(){
	    $em=$this->getDoctrine()->getManager();
	    $user=$this->getUser();
        foreach ($user->getPanier()->getPanierProduits() as $panierProd){
            $oldProd=$panierProd->getProduit();
            $oldProd->setStock($oldProd->getStock()+$panierProd->getQuantity());
            $user->getPanier()->removePanierProduit($panierProd);
        }
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute("Produit.show");
    }

	/**
	 * @Route("/Panier/set/quantity/{id}",name="Panier.setQuantityOfItem", methods={"POST"})
	 * @param ObjectManager $manager
	 * @param Produit $produit
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function setQuantityOfItem(ObjectManager $manager, Produit $produit){
		$panier=$this->getUser()->getPanier();
		$quantityProduit = $_POST['quantityProduit'];
		foreach ($panier->getPanierProduits() as $new_produit){
			if ($new_produit->getProduit()->getId()==$produit->getId()){
				if ($produit->getStock()>0 && $produit->getStock()> $quantityProduit){
					$new_produit->setQuantity($quantityProduit);
					$produit->setStock($produit->getStock()-$quantityProduit);
					//$panier->addPanierProduit($new_produit);
					$manager->persist($new_produit);
					$manager->flush();
					return $this->redirectToRoute("Produit.show");
				}
				return $this->redirectToRoute("Produit.show");
			}
		}
		if ($produit->getStock()>0 && $produit->getStock()>$quantityProduit){
			$produit->setStock($produit->getStock()-$quantityProduit);
			$new_produit=new PanierProduits();
			$new_produit->setProduit($produit);
			$new_produit->setPanier($panier);
			$new_produit->setQuantity($quantityProduit);

			$panier->addPanierProduit($new_produit);
			$manager->persist($panier);
			$manager->flush();
		}
		return $this->redirectToRoute("Produit.show");
	}
}