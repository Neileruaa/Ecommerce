<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\TypeProduit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prix')
            ->add('photo', FileType::class, array(
            	'label'=>'Photo au format PNG'
            ))
            ->add('disponible')
            ->add('stock')
            ->add('typeProduit_id', EntityType::class, array(
            	'class' => TypeProduit::class,
	            'choice_label' => 'libelle',
	            'required' => true,
	            'expanded'=>true,
	            'multiple'=>false,
	            'by_reference'=>false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
