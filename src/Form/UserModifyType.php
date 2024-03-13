<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserModifyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('siteId', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
            ])
            ->add('password', RepeatedType::class,
                [
                    'type' =>
                    PasswordType::class, 'first_options' => ['label' => 'Mot de passe'], 'second_options' => ['label' => 'Confirmer le mot de passe']]) //, PasswordType::class, [PasswordType::class])



        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
