<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Employees
 *
 * @ORM\Table(name="employee")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmployeeRepository")
 */
class Employee {

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
	 * @ORM\Column(name="prenom", type="string", length=255)
	 */
	private $prenom;

	/**
	 * @var string
	 *
	 * @Assert\NotBlank()
	 * @ORM\Column(name="nom", type="string", length=255)
	 */
	private $nom;

	/**
	 * @var string
	 *
	 * @Assert\Email(message="L\'email n\'pas valide.", checkMX=true)
	 * @ORM\Column(name="email", type="string", length=255, unique=true)
	 */
	private $email;

	/**
	 * @var string
	 * 
	 * @Assert\NotBlank()
	 * @ORM\Column(name="cout_jour", type="decimal", precision=7, scale=2)
	 */
	private $coutJour;

	/**
	 * @var \DateTime
	 *
	 * @Assert\NotBlank()
	 * @ORM\Column(name="date_embauche", type="datetime")
	 */
	private $dateEmbauche;
	
	 /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Merci de bien vouloir charger une image (.gif, .jpg ou .png.")
     * @Assert\File(mimeTypes={ "image/gif", "image/jpeg", "image/png" })
     */
    private $picture;
	
	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Time", cascade={"persist", "merge"}, mappedBy="employee")
	 */
	private $days;

	public function __construct() {
		$this->products = new ArrayCollection();
	}

	public function addDay(\AppBundle\Entity\Employee $days) {
		$this->days[] = $days;
		return $this;
	}

	public function removeDay(\AppBundle\Entity\Employee $days) {
		$this->days->removeElement($days);
	}
	
	public function getDays() {
		return $this->days;
	}
	

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }
		
	/**
	 * @var string
	 * 
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Job", cascade={"persist", "merge"})
	 * 
     */
	private $job;
		
	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Set id
	 *
	 * @param string $id
	 *
	 * @return Employees
	 */
	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	/**
	 * Set prenom
	 *
	 * @param string $prenom
	 *
	 * @return Employees
	 */
	public function setPrenom($prenom) {
		$this->prenom = $prenom;

		return $this;
	}

	/**
	 * Get prenom
	 *
	 * @return string
	 */
	public function getPrenom() {
		return $this->prenom;
	}

	/**
	 * Set nom
	 *
	 * @param string $nom
	 *
	 * @return Employees
	 */
	public function setNom($nom) {
		$this->nom = $nom;

		return $this;
	}

	/**
	 * Get nom
	 *
	 * @return string
	 */
	public function getNom() {
		return $this->nom;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 *
	 * @return Employees
	 */
	public function setEmail($email) {
		$this->email = $email;

		return $this;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Set coutJour
	 *
	 * @param string $coutJour
	 *
	 * @return Employees
	 */
	public function setCoutJour($coutJour) {
		$this->coutJour = $coutJour;

		return $this;
	}

	/**
	 * Get coutJour
	 *
	 * @return string
	 */
	public function getCoutJour() {
		return $this->coutJour;
	}

	/**
	 * Set dateEmbauche
	 *
	 * @param \DateTime $dateEmbauche
	 *
	 * @return Employees
	 */
	public function setDateEmbauche($dateEmbauche) {
		$this->dateEmbauche = $dateEmbauche;

		return $this;
	}

	/**
	 * Get dateEmbauche
	 *
	 * @return \DateTime
	 */
	public function getDateEmbauche() {
		return $this->dateEmbauche;
	}
	
	/**
     * Set job
     *
     * @param string $job
     *
     * @return Job
     */
    public function setJob($job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return string
     */
    public function getJob()
    {
        return $this->job;
    }
	
	

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Employee
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }
	
	
	public function __toString() {
		return $this->getNom();
	}
}
