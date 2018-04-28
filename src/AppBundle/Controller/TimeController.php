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

				//$url = $this->generateUrl('employee_time', ['id' => $time->getEmployee()->getId()]);
				// Si le projet n'est pas livré ou l'employé n'est pas archivé, on peut supprimer
				
				if ($project->getLivre()){
					$this->get('session')->getFlashBag()->add('error', "Le projet est livré, pas de suppression possible.");
				} elseif(!$employee->getStatus()) {
					$this->get('session')->getFlashBag()->add('error', "L'utilisateur en archivé, pas de suppression possible.");
				} else {
					$em->remove($time);
					$em->flush();
					// Message flash
					$this->get('session')->getFlashBag()->add('success', "Temps supprimé avec succès.");
				}

				//return $this->redirect($url);

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

				return $this->redirectToRoute('/');

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
