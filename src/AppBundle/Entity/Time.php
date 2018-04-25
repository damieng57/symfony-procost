<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Time
 *
 * @ORM\Table(name="time")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TimeRepository")
 */
class Time {

	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="day", type="integer")
	 */
	private $day;

	/**
	 * @var string
	 * 
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Employee", cascade={"persist", "merge"}, inversedBy="days")
	 * @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
	 * 
	 */
	private $employee;

	/**
	 * @var string
	 * 
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project", cascade={"persist", "merge"}, inversedBy="days")
	 * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
	 */
	private $project;
	
	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_ajout", type="datetime", nullable=false)
	 */
	private $dateAjout;

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set day
	 *
	 * @param integer $day
	 *
	 * @return Time
	 */
	public function setDay($day) {
		$this->day = $day;

		return $this;
	}

	/**
	 * Get day
	 *
	 * @return int
	 */
	public function getDay() {
		return $this->day;
	}


    /**
     * Set employee
     *
     * @param \AppBundle\Entity\Employee $employee
     *
     * @return Time
     */
    public function setEmployee(\AppBundle\Entity\Employee $employee = null)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \AppBundle\Entity\Employee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set project
     *
     * @param \AppBundle\Entity\Project $project
     *
     * @return Time
     */
    public function setProject(\AppBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \AppBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }
	
	function __construct() {
		$this->dateAjout = new \DateTime("now");
	}

    /**
     * Set dateAjout
     *
     * @param \DateTime $dateAjout
     *
     * @return Time
     */
    public function setDateAjout($dateAjout)
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    /**
     * Get dateAjout
     *
     * @return \DateTime
     */
    public function getDateAjout()
    {
        return $this->dateAjout;
    }
}
