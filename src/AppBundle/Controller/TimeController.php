<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TimeController extends Controller {

	/**
	 * @Route("/timeDelete/{id}", name="time_delete", requirements={"id" = "\d+"})
	 */
	public function timeDeleteAction(Request $request, int $id) {

		if ($id != 0) {
			//**********************
			// On essaie de supprimer supprimer
			//**********************
			// Tentative de suppression de la Bdd
			try {
				$em = $this->getDoctrine()->getEntityManager();
				// On recherche d'abord le projet lié au temps pour savoir
				// si l'on peut le supprimer.
				$time = $em->getRepository('AppBundle:Time')->find($id);
				
				$project = $em->getRepository('AppBundle:Project')->find($time->getProject());
				$employee = $em->getRepository('AppBundle:Employee')->find($time->getEmployee());
				
				$url = $this->generateUrl('employee_time', ['id' => $time->getEmployee()->getId()]);
				
				// Si le projet n'est pas livré et l'employé n'est pas archivé, on peut supprimer
				if (!$project->getLivre() || !$employee->getStatus()) {
					$em->remove($time);
					$em->flush();
					// Message flash
					$this->get('session')->getFlashBag()->add('success', "Temps supprimé avec succès.");
	
				} else {
					$this->get('session')->getFlashBag()->add('error', "Le projet est livré, pas de suppression possible.");
					
				}
				return $this->redirect($url);

			// On intercepte l'erreur si on tente de supprimer une entrée liée
			// à des temps ou des employés
			} catch (\Doctrine\DBAL\DBALException $e) {
				// Message flash
				$this->get('session')->getFlashBag()->add('error', "Erreur. La suppression n'a pas pu s'effectuer correctement.");
			}

			return $this->redirect($url);
		}
	}
}
