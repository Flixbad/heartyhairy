<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('payment', ChoiceType::class, [
            'label' => 'Mode de paiement',
            'choices' => [
                '💳 Carte Bancaire' => 'carte',
                '🅿️ PayPal' => 'paypal',
                '🏠 Sur place' => 'sur_place'
            ],
            'expanded' => true, // rend les options en boutons radio
        ]);
    }
}

