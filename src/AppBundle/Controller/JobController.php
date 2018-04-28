<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Job;
use AppBundle\Form\JobType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use DataDog\PagerBundle\Pagination;

class JobController extends Controller {

	/**
	 * @Route("/jobEdit/{id}", name="job_edit", requirements={"id" = "\d+"})
	 */
	public function jobEditAction(Request $request, int $id) {

		if ($id == 0) {
			//**********************
			// Création d'un métier
			//**********************
			$job = new Job();
			$form = $this->createForm(JobType::class, $job);

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {

				// Enregistrement du job en base de données
				$em = $this->getDoctrine()->getManager();

				// Tentative d'ajout à la Bdd
				try {
					$em->persist($job);
					$em->flush();
					// Message flash
					$this->get('session')->getFlashBag()->add('success', "Métier ajouté avec succès.");
					// On intercepte l'erreur en cas de duplication
				} catch (UniqueConstraintViolationException $e) {
					// Message flash
					$this->get('session')->getFlashBag()->add('error', "Erreur. L'ajout du métier n'a pas pu s'effectuer correctement.");
				}

				return $this->redirectToRoute('jobs');
			}
		} else {
			//**********************
			// Modication du métier
			//**********************
			$job = new Job();
			$form = $this->createForm(JobType::class, $job);

			$form->handleRequest($request);

			// Si le formulaire est déjà soumis et validé
			if ($form->isSubmitted() && $form->isValid()) {
				// Modification de du métier en base de données
				$em = $this->getDoctrine()->getManager();

				$em->merge($job);
				$em->flush();

				// Message flash
				$this->get('session')->getFlashBag()->add('success', "Métier modifié avec succès.");

				return $this->redirectToRoute('jobs');
			} else {
				// Sinon, on prérempli le formulaire
				// Récupération du métier
				$em = $this->getDoctrine()->getManager();
				$job = $em->getRepository('AppBundle:Job')->find($id);

				$form = $this->createForm(JobType::class, $job);

				$form->handleRequest($request);
			}
		}

		// Rendu du formulaire
		return $this->render('app/jobs/job_edit.html.twig', [
					'job' => $job,
					'form' => $form->createView(),
					'id' => $id,
		]);
	}

	/**
	 * @Route("/jobDelete/{id}", name="job_delete", requirements={"id" = "\d+"})
	 */
	public function jobDeleteAction(Request $request, int $id) {

		$em = $this->getDoctrine()->getManager();
		$job = $em->getRepository('AppBundle:Job')->find($id);


		try {
			$em->remove($job);
			$em->flush();
			// Message flash
			$this->get('session')->getFlashBag()->add('success', "Le métier a été supprimer.");
		} catch (\Doctrine\DBAL\DBALException $e) {
			// Message flash
			$this->get('session')->getFlashBag()->add('error', "Le métier est toujours associé à"
					. " un utilisateur, modifier ou supprimer l'utilisateur avant de supprimer"
					. " le métier.");
		}


		// replace this example code with whatever you need
		return $this->redirectToRoute('jobs');
	}

}
