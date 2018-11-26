<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\PanierProduits;
use App\Entity\Produit;
use App\Entity\TypeProduit;
use App\Form\CommentType;
use App\Form\ProduitType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function showProduits(SessionInterface $session){
    	if ($session->get('categorie')!= 'All'){
    		dump($session->get('categorie'));
		    $produits=$this->getDoctrine()->getRepository(Produit::class)->findAllByCategorie($session->get('categorie'));
	    }else{
		    $produits=$this->getDoctrine()->getRepository(Produit::class)->findAll();
	    }

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
			'form' => $form->createView(),
            'user'=>$this->getUser()
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

	/**
	 * @Route("/produits/add", name="Produit.add")
	 * @isGranted("ROLE_ADMIN")
	 * @param ObjectManager $manager
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function addProduit(ObjectManager $manager, Request $request) {
		$produit = new Produit();
		$form = $this->createForm(ProduitType::class, $produit);

		$form->handleRequest($request);
		if ($form->isSubmitted() and $form->isValid()){
			/** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
			$file = $form->get('photo')->getData();

			$fileName = $this->generateUniqueFilename().'.'.$file->guessExtension();

			try{
				$file->move(
					$this->getParameter('photos_directory'),
					$fileName
				);
			}catch (FileException $e){
				throw $e;
			}

			$produit->setPhoto($fileName);

			$manager->persist($produit);
			$manager->flush();
			return $this->redirectToRoute('Produit.show');
		}

		return $this->render('produit/addProduit.html.twig', [
			'form'=>$form->createView()
		]);
    }

    private function generateUniqueFilename(){
		return md5(uniqid());
    }

	/**
	 * @Route("/produits/show/by/type", name="Produit.showByType")
	 * @param ObjectManager $manager
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function showProduitsByType(ObjectManager $manager, Request $request, SessionInterface $session){
		$typesProduits = $manager->getRepository(TypeProduit::class)->findAll();
		$produits = $manager->getRepository(Produit::class)->findAll();
		$produits_to_show = $produits;
	    if (isset($_POST['submit'])) {
	    	$produits_to_show=array();
		    if (isset($_POST['typeCategorie'])) {
			    $categorie_actuelle = $_POST['typeCategorie'];
			    if ($categorie_actuelle != -1) {
			    	$cat = $manager->getRepository(TypeProduit::class)->find($categorie_actuelle);
			    	$session->clear();
			    	$session->set('categorie', $categorie_actuelle);
			        foreach ($produits as $produit){
			        	if ($produit->getTypeProduitId()->getId() == $cat->getId()){
			        		$produits_to_show[] = $produit;
				        }
			        }
			    }else{
			    	$produits_to_show = $produits;
				    $session->clear();
				    $session->set('categorie', 'All');
			    }
		    }
	    }
	    return $this->render('produit/filter_produits.html.twig', [
			'typesProduits' => $typesProduits,
		    'produits' =>$produits_to_show
		]);
    }

    /**
     * @Route("/produit/incremente/{id}",name="produit.incremente",  requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     * @param Produit $produit
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     */
    public function incrementeProduit(ObjectManager $manager, Produit $produit){
        $produit->setStock($produit->getStock()+1);
        $manager->persist($produit);
        $manager->flush();
        return $this->redirectToRoute('Produit.show');
    }

    /**
     * @Route("/produit/decremente/{id}",name="produit.decremente",  requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     * @param Produit $produit
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function decrementeProduit(ObjectManager $manager, Produit $produit){
        if ($produit->getStock()>0){
            $produit->setStock($produit->getStock()-1);
            $manager->persist($produit);
            $manager->flush();
        }
        return $this->redirectToRoute('Produit.show');
    }
}
