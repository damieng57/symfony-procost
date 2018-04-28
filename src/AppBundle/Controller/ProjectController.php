<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Project;
use AppBundle\Entity\Time;
use AppBundle\Form\ProjectType;
use AppBundle\Form\TimeType;
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
				// Modification du projet en base de données
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
				
				// Si le projet n'est pas livré, on peut modifier
				if ($project->getLivre() ) {
					$this->get('session')->getFlashBag()->add('error', "Le projet est livré, pas de modification possible.");
					return $this->redirectToRoute('projects');
				}
					
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
				}

				// On intercepte l'erreur si on tente de supprimer une entrée liée
				// à des temps ou des employés
			} catch (\Doctrine\DBAL\DBALException $e) {
				// Message flash
				$this->get('session')->getFlashBag()->add('error', "Erreur. La "
						. "suppression n'a pas pu s'effectuer correctement. "
						. "Ce projet a déjà des temps et utilisateurs affectés");
			}

			return $this->redirectToRoute('projects');
		}
	}

	/**
	 * @Route("/projectTime/{id}", name="project_time", requirements={"id" = "\d+"})
	 */
	public function projectTimeAction(Request $request, int $id) {
		//**********************
		// Génération du formulaire pour l'ajout de temps
		//**********************
		$time = new Time();
		$form = $this->createForm(TimeType::class, $time, ['id' => $id]);

		$form->handleRequest($request);

		// Si le formulaire est déjà soumis et validé
		if ($form->isSubmitted() && $form->isValid()) {

			$em = $this->getDoctrine()->getManager();

			// On vérifie que le projet n'est pas déjà livré
			$temp = $em->getRepository('AppBundle:Project')->find($time->getProject()->getId());

			// Si oui, on ne peut pas ajouter du temps
			if ($temp->getLivre()) {
				// Message flash
				$this->get('session')->getFlashBag()->add('error', "Le projet est déjà livré, pas d'ajout possible.");
			} else {
				// Sinon, on ajoute du temps sur le projet selectionné 
				// pour l'utilisateur en cours
				$temp = $em->getRepository('AppBundle:Project')->find($id);
				$time->setProject($temp);
				$em->merge($time);
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', "Temps ajouté");
			}

			return $this->redirect($this->generateUrl('project_time', ['id' => $id]));
		} else {
			// Sinon, on affiche le formulaire vide
			$form = $this->createForm(TimeType::class, $time);

			$form->handleRequest($request);
		}

		// Récupération de l'employé
		$em = $this->getDoctrine()->getManager();
		$project = $em->getRepository('AppBundle:Project')->find($id);

		$cost = $em->getRepository('AppBundle:Project')->projectCost($id);

		// Récupération de la liste des projets pour les temps
		$projects = $em->getRepository('AppBundle:Project')->findAll();

		// Récupération de l'historique
		$history = $em->getRepository('AppBundle:Project')->historyProjectByEmployees($id);

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$history, $request->query->getInt('page', 1), 10);

		// replace this example code with whatever you need
		return $this->render('app/projects/project_timetracking.html.twig', [
					'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
					'project' => $project,
					'projects' => $projects,
					'pagination' => $pagination,
					'history' => $history,
					'cost' => $cost,
					'form' => $form->createView(),
		]);
	}

}
