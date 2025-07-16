<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    ->add('email', EmailType::class, ['label' => 'Email'])
    ->add('phone', TextType::class, ['label' => 'Téléphone', 'required' => false])
    ->add('plainPassword', PasswordType::class, [
        'label' => 'Nouveau mot de passe',
        'mapped' => false,
        'required' => false
    ])
    ->add('photo', FileType::class, [
        'label' => 'Photo de profil',
        'mapped' => false,
        'required' => false
    ]);
    }
}
