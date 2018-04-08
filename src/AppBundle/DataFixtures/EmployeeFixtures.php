<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Employee;
use AppBundle\Entity\Project;
use AppBundle\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class EmployeeFixtures extends Fixture {

	public function load(ObjectManager $manager) {

		$faker = \Faker\Factory::create();
		$metier = array(
			'Web designer',
			'SEO Manager',
			'Community Manager',
			'Project Manager',
			'Web developer',
			'Human Resources',
			'Accountant',
			'Director',
			'Tester',
			'Engineer'
		);
		$limit_employe = 20;
		$limit_projet = 15;


		// Génération de la table des métiers
		foreach ($metier as $value) {
			$job = new Job();
			$job->setJob($value);

			$manager->persist($job);
			$manager->flush();
		}

		// Génération de la table des employés
		for ($i = 0; $i < $limit_employe; $i++) {

			$employee = new Employee();
			// On choisi un métier au hasard
			$employee->setJob($manager->getRepository('AppBundle:Job')->findAll(array())[mt_rand(0, 9)]);
			// Génère un status aléatoire (booléen)
			$employee->setStatus(mt_rand(0, 1) == 1);
			$employee->setPrenom($faker->lastName());
			$employee->setNom($faker->firstName());
			$employee->setEmail($faker->email());
			$employee->setCoutJour(mt_rand(10000, 50000) / 100);
			$employee->setDateEmbauche($faker->dateTime);

			$manager->persist($employee);
		}

		$type_projet = array('OPEX', 'CAPEX');

		// Génération de la table des projets
		for ($i = 0; $i < $limit_projet; $i++) {

			$project = new Project();

			$project->setIntitule($faker->text(20));
			$project->setDescription($faker->text(120));
			$project->setType($type_projet[random_int(0, 1)]);
			$project->setLivre(mt_rand(0, 1) == 1);
			$project->setDateCreation($faker->dateTime);

			$manager->persist($project);
		}



		$manager->flush();
	}

}
