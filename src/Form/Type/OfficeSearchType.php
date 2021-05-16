<?php

namespace App\Form\Type;

use App\Entity\Office;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\SearchFilter;
use Doctrine\ORM\EntityManager;

class OfficeSearchType extends AbstractType
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * OfficeSearchType constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $citiesChoices = $this->em->getRepository(Office::class)->findAllCities();
        $builder
                ->add('city',
                    ChoiceType::class,
                    [
                        'placeholder' => 'Select City',
                        'choices' => array_combine($citiesChoices, $citiesChoices),
                        'error_bubbling' => true,
                        'attr' => [
                            'class' => 'form-control chosen-select',
                            'style' => 'min-width:50px',
                        ],
                    ])
                ->add('search',SubmitType::class,[
                    'attr' =>   ['class' => 'btn-primary float-right']
                ])

        ;

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SearchFilter::class,
            'validation_groups' => [],
        ));
    }

}