<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Project;
use AppBundle\Form\ProjectType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class ProjectController extends Controller {

	/**
	 * @Route("/projectEdit/{id}", name="project_edit", requirements={"id" = "\d+"})
	 */
	public function projectEditAction(Request $request, int $id) {

		if ($id == 0) {
			//**********************
			// Création d'un projet
			//**********************
			$project = new Project();
			$form = $this->createForm(ProjectType::class, $project);

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {

				// Enregistrement du job en base de données
				$em = $this->getDoctrine()->getManager();

				// Tentative d'ajout à la Bdd
				try {
					$em->persist($project);
					$em->flush();
					// Message flash
					$this->get('session')->getFlashBag()->add('success', "Projet ajouté avec succès.");
					// On intercepte l'erreur en cas de duplication
				} catch (UniqueConstraintViolationException $e) {
					// Message flash
					$this->get('session')->getFlashBag()->add('error', "Erreur. L'ajout du projet n'a pas pu s'effectuer correctement.");
				}

				return $this->redirectToRoute('projects');
			}
		} else {
			//**********************
			// Modication du projet
			//**********************
			$project = new Project();
			$form = $this->createForm(ProjectType::class, $project);

			$form->handleRequest($request);

			// Si le formulaire est déjà soumis et validé
			if ($form->isSubmitted() && $form->isValid()) {
				// Modification de du projet en base de données
				$em = $this->getDoctrine()->getManager();

				$em->merge($project);
				$em->flush();

				// Message flash
				$this->get('session')->getFlashBag()->add('success', "Projet modifié avec succès.");

				return $this->redirectToRoute('projects');
			} else {
				// Sinon, on prérempli le formulaire
				// Récupération du projet
				$em = $this->getDoctrine()->getManager();
				$project = $em->getRepository('AppBundle:Project')->find($id);

				$form = $this->createForm(ProjectType::class, $project);

				$form->handleRequest($request);
			}
		}

		// Rendu du formulaire
		return $this->render('app/projects/project_edit.html.twig', [
					'project' => $project,
					'form' => $form->createView(),
					'id' => $id,
		]);
	}

	/**
	 * @Route("/projectDelete/{id}", name="project_delete", requirements={"id" = "\d+"})
	 */
	public function projectDeleteAction(Request $request, int $id) {

		if ($id != 0) {
			//**********************
			// On essaie de supprimer supprimer
			//**********************
			// Tentative de suppression de la Bdd
			try {
				$em = $this->getDoctrine()->getEntityManager();
				$project = $em->getRepository('AppBundle:Project')->find($id);

				// Si le projet n'est pas livré, on peut supprimer
				if (!$project->getLivre()) {
					$em->remove($project);
					$em->flush();
					// Message flash
					$this->get('session')->getFlashBag()->add('success', "Projet supprimé avec succès.");
				} else {
					$this->get('session')->getFlashBag()->add('error', "Le projet est livré, pas de suppression possible.");
					return null;
				}

			// On intercepte l'erreur si on tente de supprimer une entrée liée
			// à des temps ou des employés
			} catch (\Doctrine\DBAL\DBALException $e) {
				// Message flash
				$this->get('session')->getFlashBag()->add('error', "Erreur. La suppression n'a pas pu s'effectuer correctement.");
			}

			return $this->redirectToRoute('projects');
		}
	}
}
