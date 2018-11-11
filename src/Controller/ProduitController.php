<?php

namespace App\Controller;

use App\Entity\Produit;
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
    public function showProduitsUser(){
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
	 * @Route("/produits/remove/{id}", name="Produit.remove", requirements={"page"="\d+"})
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
