<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EmployeeType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {

		$builder->add('prenom', TextType::class, array('label' => 'Prénom'))
				->add('nom')
				->add('email')
				->add('coutJour')
				->add('id', HiddenType::class);

		$builder->add('job', EntityType::class, array(
			'class' => 'AppBundle:Job',
			'label' => 'Métier',
			'multiple' => false,
		));

		$builder->add('dateEmbauche', DateType::class, array(
			// renders it as a single text box
			'widget' => 'single_text',
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Employee'
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'appbundle_employee';
	}

}
