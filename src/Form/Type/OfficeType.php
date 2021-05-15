<?php

namespace App\Form\Type;

use App\Entity\Office;
use Symfony\Component\Form\AbstractType;
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'data_class' => Office::class,
        ]);
    }
}