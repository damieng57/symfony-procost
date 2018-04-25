<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Jobs
 *
 * @ORM\Table(name="job")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JobRepository")
 */
class Job {

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
	 * @Assert\NotBlank()
	 * @ORM\Column(name="job", type="string", length=255, unique=false)
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
	 * Set job
	 *
	 * @param string $job
	 *
	 * @return Job
	 */
	public function setJob($job) {
		$this->job = $job;

		return $this;
	}

	/**
	 * Get job
	 *
	 * @return string
	 */
	public function getJob() {
		return $this->job;
	}

	public function __toString() {
		return $this->getJob();
	}

}
