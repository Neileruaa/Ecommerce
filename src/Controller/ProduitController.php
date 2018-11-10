<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produits/show",name="produit.show")
     */
    public function showProduitsUser(){
        $produits=$this->getDoctrine()->getRepository(Produit::class)->findAll();

        return $this->render("produit/showProduits.html.twig", ["produits"=>$produits]);
    }

    /**
     * @Route("/produits/add/{id}",name="produit.add", methods={"GET"})
     */
    public function addProduit(Request $request){
        $em=$this->getDoctrine()->getManager();
        $user=$this->getUser();
        $new_panier=$user->getPanier();
        $new_produit=$this->getDoctrine()->getRepository(Produit::class)->find($request->get("id"));
        dump($new_panier);
        die();
        $panier=$user->getPanier()->addListeProduit($new_produit);
        return $this->redirectToRoute("produit.show");

    }
}
