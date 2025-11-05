<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Full Name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Phone Number',
            ])
            ->add('address', TextType::class, [
                'label' => 'Address',
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Postal Code',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
