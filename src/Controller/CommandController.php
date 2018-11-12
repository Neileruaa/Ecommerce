<?php

namespace App\Controller;

use App\Entity\Commande;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Form\CommentType;
use App\Form\ProduitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')")
 * Class CommandController
 * @package App\Controller
 */
class CommandController extends AbstractController {
	/**
	 * @Route("/commands/show", name="Command.show")
	 * @param ObjectManager $manager
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showCommand(ObjectManager $manager) {
//		$commands = $manager->getRepository(Commande::class)->findAll();
//		return $this->render('command/Command_show.html.twig',[
//			'command' => $commands
//		]);
	}

    /**
     * @Route("/commande/creee",name="commande.create")
     */
    public function createCommande(){
        $em=$this->getDoctrine()->getManager();
        $user=$this->getUser();
        $new_command=new Commande();
        $produits=$user->getPanier()->getPanierProduits();
        $montant=0;
        foreach ($produits as $produit){
            $montant=$montant+($produit->getProduit()->getPrix())*($produit->getQuantity());
            $new_command->addPanierCommande($produit);
        }
        $new_command->setUser($user);
        $time=new \DateTime();
        $time->format('H:i:s \O\n d-m-Y');
        $new_command->setDateCommande($time);
        $new_command->setEtat("En cours");
        $new_command->setMontant($montant);
        $user->addCommande($new_command);
        foreach ($user->getPanier()->getPanierProduits() as $panierProd){
            $user->getPanier()->removePanierProduit($panierProd);
        }
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute("Produit.show", [
            "commande"=>$new_command
        ]);
    }

    /**
     *
     */
    public function removeCommande(){

    }
}
