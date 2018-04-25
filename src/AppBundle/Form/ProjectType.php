<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('intitule', TextType::class, array('label' => 'Dénomination'))
            ->add('description', TextType::class, array('label' => 'Description'))
            ->add('id', HiddenType::class);

        $builder->add('type', ChoiceType::class, array(
            'choices' => array(
                'OPEX' => 'OPEX',
                'CAPEX' => 'CAPEX',
            ),
        ));

        $builder->add('dateCreation', DateType::class, array(
            // renders it as a single text box
            'widget' => 'single_text',
        ));

        $builder->add('livre', ChoiceType::class, array(
            'label' => 'Le projet a-t-il été livré ?',
            'choices' => array(
                'Oui' => true,
                'Non' => false,
            ),
            'required' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Project',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_project';
    }

}
