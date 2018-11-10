<?php

namespace App\DataFixtures;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\TypeProduit;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use MongoDB\Driver\Manager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->addProduits($manager);
        $this->addUsers($manager);
        //$this->addUsers($manager);
    }
    public function addProduits(ObjectManager $manager){
        $vetement=new TypeProduit();
        $vetement->setLibelle("vetement");
        $bureautique=new TypeProduit();
        $bureautique->setLibelle("bureautique");
        $meuble=new TypeProduit();
        $meuble->setLibelle("meuble");
        $produits=[
            ["nom"=>"Tshirt", "prix"=>29.99, "photo"=>"products.jpg", "disponible"=>true, "stock"=>12, "typeProduit_id"=>$vetement],
            ["nom"=>"Stylo", "prix"=>29.99, "photo"=>"products.jpg", "disponible"=>true, "stock"=>3, "typeProduit_id"=>$bureautique],
            ["nom"=>"Armoire", "prix"=>29.99, "photo"=>"products.jpg", "disponible"=>false, "stock"=>0, "typeProduit_id"=>$meuble]
        ];
        $manager->persist($vetement);
        $manager->persist($bureautique);
        $manager->persist($meuble);
        foreach ($produits as $produit){
            $new_produit=new Produit();
            $new_produit->setNom($produit["nom"]);
            $new_produit->setPrix($produit["prix"]);
            $new_produit->setPhoto($produit['photo']);
            $new_produit->setDisponible($produit["disponible"]);
            $new_produit->setStock($produit["stock"]);
            $new_produit->setTypeProduitId($produit["typeProduit_id"]);

            $manager->persist($new_produit);
        }
        $manager->flush();
    }
    public function addUsers(ObjectManager $manager){
        $user=new User();
        $admin=new User();
        $panier_user=new Panier();
        $panier_admin=new Panier();

        $user->setUserName("antoine");
        $user->setPassword("test");
        $user->setEmail("test@gmail.com");
        $user->setRoles("ROLE_USER");
        $user->setActive(true);
        $user->setPanier($panier_user);

        $admin->setUserName("admin");
        $admin->setPassword("admin");
        $admin->setEmail("admin@gmail.com");
        $admin->setRoles("ROLE_ADMIN");
        $admin->setActive(true);
        $admin->setPanier($panier_admin);

        $manager->persist($user);
        $manager->persist($admin);
        $manager->flush();

    }
}
