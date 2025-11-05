<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
                'required' => true,
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Phone Number',
                'required' => true,
            ])
            ->add('country', TextType::class, [
                'label' => 'Country',
                'required' => true,
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'required' => true,
            ])
            ->add('address', TextType::class, [
                'label' => 'Address',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
