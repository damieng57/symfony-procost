<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Employee;
use AppBundle\Form\EmployeeType;
use AppBundle\Entity\Time;
use AppBundle\Form\TimeType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;

class EmployeeController extends Controller {

	/**
	 * @Route("/employeeEdit/{id}", name="employee_edit", requirements={"id" = "\d+"})
	 */
	public function employeeEditAction(Request $request, int $id) {

		if ($id == 0) {
			//**********************
			// Création d'un employé
			//**********************
			$employee = new Employee();
			$form = $this->createForm(EmployeeType::class, $employee);

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {

				// Enregistrement de l'employé en base de données
				$em = $this->getDoctrine()->getManager();

				// Ajout du champ status non renseigné dans le formulaire
				// Etant donné qu'on ne peut modifier un utilisateur archivé
				// le status sera à 1

				$employee->setStatus(1);

				//************************************************ */
				// $file stores the uploaded image file
				/** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
				$file = $employee->getPicture();

				$fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

				// moves the file to the directory where pictures are stored
				$file->move(
						$this->getParameter('pictures_directory'), $fileName
				);

				// updates the 'picture' property to store the PDF file name
				// instead of its contents
				$employee->setPicture($fileName);
				//********************************************** */

				try {
					$em->persist($employee);
					$em->flush();
					// Message flash
					$this->get('session')->getFlashBag()->add('success', "Utilisateur ajouté avec succès.");
				} catch (UniqueConstraintViolationException $e) {
					// Message flash
					$this->get('session')->getFlashBag()->add('error', "Erreur. Attention, un nouvel utilisateur ne peut"
							. " avoir le même email qu'un utilisateur existant. Editez ou supprimez l'existant.");
				}

				return $this->redirectToRoute('employees');
			}
		} else {
			//**********************
			// Modication de l'employé
			//**********************
			$employee = new Employee();
			$form = $this->createForm(EmployeeType::class, $employee);

			$form->handleRequest($request);

			// Si le formulaire est déjà soumis et validé
			if ($form->isSubmitted() && $form->isValid()) {
				// Modification de l'employé en base de données
				$em = $this->getDoctrine()->getManager();

				// Ajout du champ status non renseigné dans le formulaire
				// Etant donné qu'on ne peut modifier un utilisateur archivé
				// le status sera à 1
				$employee->setStatus(1);
				
				// Update de l'image
				$file = $employee->getPicture();
				$fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

				// moves the file to the directory where pictures are stored
				$file->move(
						$this->getParameter('pictures_directory'), $fileName
				);
				
				$employee->setPicture($fileName);

				$em->merge($employee);
				$em->flush();

				// Message flash
				$this->get('session')->getFlashBag()->add('success', "Utilisateur modifié avec succès.");

				return $this->redirectToRoute('employees');
			} else {
				// Sinon, on prérempli le formulaire
				// Récupération de l'employé
				$em = $this->getDoctrine()->getManager();
				$employee = $em->getRepository('AppBundle:Employee')->find($id);
				
				// Le formulaire attend un objet de type file pour l'image
				// on effectue la transformation grâce à la ligne ci-dessous
				$employee->setPicture(new File($this->getParameter('pictures_directory').'/'.$employee->getPicture()));
				
				$form = $this->createForm(EmployeeType::class, $employee);

				$form->handleRequest($request);
			}
		}

		// Rendu du formulaire
		return $this->render('app/employees/employee_edit.html.twig', [
					'employee' => $employee,
					'form' => $form->createView(),
					'id' => $id,
		]);
	}

	/**
	 * @Route("/employeeTime/{id}", name="employee_time", requirements={"id" = "\d+"})
	 */
	public function employeeTimeAction(Request $request, int $id) {
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
				$temp = $em->getRepository('AppBundle:Employee')->find($id);
				$time->setEmployee($temp);
				$em->merge($time);
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', "Temps ajouté");
			}

			return $this->redirect($this->generateUrl('employee_time', ['id' => $id]));
		} else {
			// Sinon, on affiche le formulaire vide
			$form = $this->createForm(TimeType::class, $time);

			$form->handleRequest($request);
		}

		// Récupération de l'employé
		$em = $this->getDoctrine()->getManager();
		$employee = $em->getRepository('AppBundle:Employee')->find($id);

		// Récupération de la liste des projets pour les temps
		$projects = $em->getRepository('AppBundle:Project')->findAll();

		// Récupération de l'historique
		$history = $em->getRepository('AppBundle:Project')->historyProjectsByEmployee($id);

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$history, $request->query->getInt('page', 1), 10);

		// replace this example code with whatever you need
		return $this->render('app/employees/employee_timetracking.html.twig', [
					'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
					'employee' => $employee,
					'projects' => $projects,
					'pagination' => $pagination,
					'history' => $history,
					'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/employeeArchive/{id}", name="employee_archive", requirements={"id" = "\d+"})
	 */
	public function employeeArchiveAction(Request $request, int $id) {

		// Récupération de l'employé
		$em = $this->getDoctrine()->getManager();
		$employee = $em->getRepository('AppBundle:Employee')->find($id);

		// Changement du status de l'employé en "archivé"
		if (($employee->getStatus() == 1) ?
						$employee->setStatus(0) : $employee->setStatus(1)
		);

		$em->persist($employee);
		$em->flush();

		// Permet de connaître la page d'origine afin d'être redirigé
		// vers celle-ci après l'action.
		// NOTA : L'adresse de la page (si elle existe) qui a conduit le client à la page courante.
		// Cette valeur est affectée par le client, et tous les clients ne le font pas.
		// Certains navigateurs permettent même de modifier la valeur de HTTP_REFERER,
		// sous forme de fonctionnalité. En bref, ce n'est pas une valeur de confiance.
		$currentRoute = $request->server->get('HTTP_REFERER');

		if ($currentRoute != null) {
			// On redirige vers la liste des utilisateurs
			return $this->redirect($currentRoute, 302);
		}

		return $this->redirectToRoute('employees');
	}

	/**
	 * @return string
	 */
	private function generateUniqueFileName() {
		// md5() reduces the similarity of the file names generated by
		// uniqid(), which is based on timestamps
		return md5(uniqid());
	}

}
