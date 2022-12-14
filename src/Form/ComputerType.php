<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Computer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComputerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('model', TextType::class, [
            'label' => "Modèle",
            'attr' => [
                'placeholder' => "Entrez le modèle de l'ordinateur"
        ]])
        ->add('description', TextareaType::class, [
            'label' => "Description",
            'attr' => [
                'placeholder' => "Entrez une courte description de l'ordinateur"
        ]])
        ->add('price', NumberType:: class ,[
            'label' => 'Prix du bien',
            'attr' => [
                'placeholder' => "Entrez le prix"
        ]
        ])
        ->add('year', NumberType:: class ,[
            'label' => 'Année de sortie',
            'attr' => [
                'placeholder' => "Entrez l'année de sortie"
        ]
        ])
        ->add('img', UrlType::class, [
            'label' => "Photo de l'ordinateur",
            'required' => false,
            'attr' => [
                'placeholder' => "Entrez l'URL de l'image"
        ]])
        ->add('brand', EntityType::class, [
            'choice_label'=> 'name',
            'class'=> Brand::class,
            'label'=>'Choix de la marque',
            'multiple' => false,
            'expanded' => false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Computer::class,
        ]);
    }
}
