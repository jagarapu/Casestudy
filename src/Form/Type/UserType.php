<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class,[
                'required'=> true,
                'attr' => ['class' => 'form-control',
                ],
            ])
            ->add(
                'firstName', TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'lastName', TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'employeeId', TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add('email',EmailType::class,[
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => User::$getRoles,
                    'required' => true,
                    'multiple' => true,
                    'attr' => [
                        'class' => 'roles_list radio_multiselect',
                    ],
                    'label_attr' => ['style' => 'float:left;', 'class' => ''],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function getName(){
        return 'user';
    }
}
