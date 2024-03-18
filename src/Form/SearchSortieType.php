<?php

namespace App\Form;

use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('GET')
            ->add('site', EntityType::class,[
                'class'=>Site::class,
                'choice_label' => 'nom'
            ] )
            ->add('nom', TextType::class, [
                'required'=> false
            ])
            ->add('date_one', DateType::class, [
                'label' => 'Entre',
                'widget' => 'single_text',
                'required'=> false
            ])
            ->add('date_two', DateType::class, [
                'label' => 'et',
                'widget' => 'single_text',
                'required'=> false
            ])
            ->add('sorties_orga', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'required' => false,
            ])
            ->add('sorties_inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
            ])
            ->add('sorties_nonInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
            ])
            ->add('sorties_passees', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
            ])
            ->add('searchBtn', SubmitType::class, [
                'label'=>'Rechercher'
            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
