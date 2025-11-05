<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Product Name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Product Description',
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => [
                    'Electronics-Computers' => 'Electronics_Computers',
                    'Electronics-Mobile' => 'Electronics_Mobile',
                    'Electronics-Tv' => 'Electronics_Tv',
                    'Electronics-Cameras' => 'Electronics_Cameras',
                    'Electronics-Gaming' => 'Electronics_Gaming',
                    'Clothes-Shirts' => 'Clothes_Shirts',
                    'Clothes-Pants' => 'Clothes_Pants',
                    'Clothes-Jackets' => 'Clothes_Jackets',
                    'Clothes-Shoes' => 'Clothes_Shoes',
                    'Accessories-Bags' => 'Accessories_Bags',
                    'Accessories-Watches' => 'Accessories_Watches',
                    'Accessories-Hats' => 'Accessories_Hats',
                    'Accessories-Jewelry' => 'Accessories_Jewelry',
                    'Books-Science' => 'Books_Science',
                    'Books-Fantasy' => 'Books_Fantasy',
                    'Books-Historical' => 'Books_Historical',
                    'Books-Horror' => 'Books_Horror',
                ],
                'placeholder' => 'Choose a category',
            ])
            ->add('price', TextType::class, [
                'label' => 'Price',
            ])
            ->add('image', FileType::class, [
                'label' => 'Product Image',
                'mapped' => false, 
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
