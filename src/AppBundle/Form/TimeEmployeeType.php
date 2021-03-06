<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class TimeEmployeeType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {

		$builder->add('project', EntityType::class, array(
			'class' => 'AppBundle:Project',
			'label' => 'Projet à imputer',
			'multiple' => false,
		));

		$builder->add('day', TextType::class, array(
			// renders it as a single text box
			'label' => 'Temps en jours',
			'required' => true,
		));

		$builder->add('employee', HiddenType::class, array(
			'data' => $options['id'],
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Time',
			'id' => null,
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'appbundle_time';
	}

}
