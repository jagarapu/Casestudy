<?php

namespace App\Form\Type;

use App\Entity\Office;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,[
                'required'=> true,
                'attr' => ['class' => 'form-control',
                ],
            ])
            ->add(
                'address', TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'city', TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add('country',CountryType::class,[
                'placeholder' => 'Select Country of residence',
                'label' => 'Country of residence',
                'attr' => [
                    'class' => 'form-control chosen-select',
                ],
            ])
            ->add('state', TextType::class, [
                'label' => 'State/Province',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add(
                'postCode', TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'phoneNumber', TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'officeCapacity', NumberType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add('logo',FileType::class, [
                'label' => 'Select Logo',
                'data_class' => null,
                'required'   => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'data_class' => Office::class,
            'validation_groups' => [ 'office'],
        ]);
    }
}