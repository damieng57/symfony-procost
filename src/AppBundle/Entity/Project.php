<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Projects
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 */
class Project {

	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="intitule", type="string", length=255)
	 */
	private $intitule;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="text", nullable=true)
	 */
	private $description;

	/**
	 * @var enum
	 *
	 * @ORM\Column(name="type", type="string", columnDefinition="enum('OPEX', 'CAPEX')")
	 */
	private $type;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_creation", type="datetime")
	 */
	private $dateCreation;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="livre", type="boolean")
	 */
	private $livre;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Time", cascade={"persist", "merge", "remove"}, mappedBy="project")
	 */
	private $days;

	public function __construct() {
		$this->products = new ArrayCollection();
	}

	public function addDay(\AppBundle\Entity\Project $days) {
		$this->days[] = $days;
		return $this;
	}

	public function removeDay(\AppBundle\Entity\Project $days) {
		$this->days->removeElement($days);
	}
	
	public function getDays() {
		return $this->days;
	}

	/**
	 * Set id
	 *
	 * @param int $id
	 *
	 * @return int
	 */
	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set intitule
	 *
	 * @param string $intitule
	 *
	 * @return string
	 */
	public function setIntitule($intitule) {
		$this->intitule = $intitule;

		return $this;
	}

	/**
	 * Get intitule
	 *
	 * @return string
	 */
	public function getIntitule() {
		return $this->intitule;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 *
	 * @return string
	 */
	public function setDescription($description) {
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	public function setType($type) {
		$this->type = $type;

		return $this;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Set dateCreation
	 *
	 * @param \DateTime $dateCreation
	 *
	 * @return \DateTime
	 */
	public function setDateCreation($dateCreation) {
		$this->dateCreation = $dateCreation;

		return $this;
	}

	/**
	 * Get dateCreation
	 *
	 * @return \DateTime
	 */
	public function getDateCreation() {
		return $this->dateCreation;
	}

	/**
	 * Set livre
	 *
	 * @param boolean $livre
	 *
	 * @return bool
	 */
	public function setLivre($livre) {
		$this->livre = $livre;

		return $this;
	}

	/**
	 * Get livre
	 *
	 * @return bool
	 */
	public function getLivre() {
		return $this->livre;
	}

	public function __toString() {
		return $this->getIntitule();
	}
}
