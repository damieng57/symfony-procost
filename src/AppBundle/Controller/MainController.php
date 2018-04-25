<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller {

	/**
	 * @Route("/", name="homepage")
	 */
	public function indexAction(Request $request) {

		$em = $this->getDoctrine()->getManager();

		// On récupère le nombre de projets total
		$nbProject = $em->getRepository('AppBundle:Project')->countProject();

		// On récupère le nombre de projets livrés
		$nbProjectFinished = $em->getRepository('AppBundle:Project')->countProjectFinished();

		// On récupère le nombre de projets non livrés
		$nbProjectNotFinished = $em->getRepository('AppBundle:Project')->countProjectNotFinished();

		// On récupère le nombre d'employés
		$nbEmployee = $em->getRepository('AppBundle:Employee')->countEmployee();
		
		// On récupère le nombre d'employés
		$nbJours = $em->getRepository('AppBundle:Time')->countProductionDays();

		// On récupère les 5 derniers projets
		$lastprojects = $em->getRepository('AppBundle:Project')->globalCostByProject();

		// On récupère le nombre de projets CAPEX
		$nbProjectCAPEX = $em->getRepository('AppBundle:Project')->countProjectCAPEX();

		// On récupère le nombre de projets OPEX
		$nbProjectOPEX = $em->getRepository('AppBundle:Project')->countProjectOPEX();
		
		// Récupération du meilleur employé
		$topEmployee = $em->getRepository('AppBundle:Employee')->topEmployee();
		
		// Récupération des temps de production
		$tempsProduction = $em->getRepository('AppBundle:Time')->productionTime();

		
		// replace this example code with whatever you need
		return $this->render('app/index.html.twig', [
					'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
					'nbProject' => $nbProject,
					'nbProjectFinished' => $nbProjectFinished,
					'nbProjectNotFinished' => $nbProjectNotFinished,
					'nbProjectCAPEX' => $nbProjectCAPEX,
					'nbProjectOPEX' => $nbProjectOPEX,
					'nbEmployee' => $nbEmployee,
					'lastProjects' => $lastprojects,
					'nbJours' => $nbJours,
					'topEmployee' => $topEmployee,
					'tempsProduction'=> $tempsProduction,
		]);
	}

	/**
	 * @Route("/projects", name="projects")
	 */
	public function projectsAction(Request $request) {
		// Récupération de la liste des projets
		$em = $this->getDoctrine()->getManager();
		$projects = $em->getRepository('AppBundle:Project')->findAll();

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$projects, $request->query->getInt('page', 1), 10);


		// replace this example code with whatever you need
		return $this->render('app/projects/projects.html.twig', [
					'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
					'projects' => $projects,
					'pagination' => $pagination,
		]);
	}

	/**
	 * @Route("/employees", name="employees")
	 */
	public function employeesAction(Request $request) {
		// Récupération de la liste des employes
		$em = $this->getDoctrine()->getManager();
		$employees = $em->getRepository('AppBundle:Employee')->findAll();

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$employees, $request->query->getInt('page', 1), 10);


		// replace this example code with whatever you need
		return $this->render('app/employees/employees.html.twig', [
					'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
					'employees' => $employees,
					'pagination' => $pagination,
		]);
	}

	/**
	 * @Route("/jobs", name="jobs")
	 */
	public function jobsAction(Request $request) {
		// Récupération de la liste des jobs
		$em = $this->getDoctrine()->getManager();
		$jobs = $em->getRepository('AppBundle:Job')->findAll();

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$jobs, $request->query->getInt('page', 1), 10);

		// replace this example code with whatever you need
		return $this->render('app/jobs/jobs.html.twig', [
					'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
					'jobs' => $jobs,
					'pagination' => $pagination,
		]);
	}

	/**
	 * @Route("/search", name="search")
	 */
	public function searchAction(Request $request) {
		
		// Méthode en utilisant les paramètres de la fonction ou un objet Request
		$slug = $request->query->get('search');
		
		// Récupération de la liste des projets
		$em = $this->getDoctrine()->getManager();
		$projects = $em->getRepository('AppBundle:Project')->searchProject($slug);

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$projects, $request->query->getInt('page', 1), 10);


		// replace this example code with whatever you need
		return $this->render('app/search/search.html.twig', [
					'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
					'projects' => $projects,
					'pagination' => $pagination,
		]);
	}

}
