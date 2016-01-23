<?php
// src/OC/PlatformBundle/Entity/Advert.php

namespace OC\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Advert
 *
 * @ORM\Table(name="advert")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Advert
{
	/**

   * @ORM\Column(name="id", type="integer")

   * @ORM\Id

   * @ORM\GeneratedValue(strategy="AUTO")

   */

  private $id;


  /**

   * @ORM\Column(name="date", type="datetime")

   */

  private $date;


  /**

   * @ORM\Column(name="title", type="string", length=255, unique=false)

   */

  private $title;


  /**

   * @ORM\Column(name="author", type="string", length=255)

   */

  private $author;


  /**

   * @ORM\Column(name="content", type="text")

   */

  private $content;


  /**

   * @ORM\Column(name="published", type="boolean")

   */

  private $published = true;


  /**

   * @ORM\OneToOne(targetEntity="OC\PlatformBundle\Entity\Image", cascade={"persist", "remove"})

   */

  private $image;


  /**

   * @ORM\ManyToMany(targetEntity="OC\PlatformBundle\Entity\Category", cascade={"persist"})

   */

  private $categories;


  /**

   * @ORM\OneToMany(targetEntity="OC\PlatformBundle\Entity\Application", mappedBy="advert")

   */

  private $applications; //  une annonce est liée à plusieurs candidatures


  /**

   * @ORM\Column(name="updated_at", type="datetime", nullable=true)

   */

  private $updatedAt;


  /**

   * @ORM\Column(name="nb_applications", type="integer")

   */

  private $nbApplications = 0;


  /**

   * @Gedmo\Slug(fields={"title"})

   * @ORM\Column(length=128, unique=false, nullable=true)

   */

  private $slug;
  
   //ajout du constructeur
	public function __construct()
    {
	$this->date         = new \Datetime();
    $this->categories   = new ArrayCollection();
    $this->applications = new ArrayCollection();
      
    }
	
	//incrementer et de decrementer le nombre d'appli sur l'advert
	  public function increaseApplication()

	  {

		$this->nbApplications++;

	  }


	  public function decreaseApplication()

	  {

		$this->nbApplications--;

	  }
	// on ajoute une seule catégorie à la fois

  public function addCategory(Category $category)

  {

    // Ici, on utilise l'ArrayCollection vraiment comme un tableau

    $this->categories[] = $category;


    return $this;

  }


  public function removeCategory(Category $category)

  {

    // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument

    $this->categories->removeElement($category);

  }


  // Notez le pluriel, on récupère une liste de catégories ici !

  public function getCategories()

  {

    return $this->categories;

  }
	

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Advert
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Advert
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Advert
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Advert
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set image
     *
     * @param \OC\PlatformBundle\Entity\Image $image
     *
     * @return Advert
     */
    public function setImage(\OC\PlatformBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \OC\PlatformBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set nbApplications
     *
     * @param integer $nbApplications
     *
     * @return Advert
     */
    public function setNbApplications($nbApplications)
    {
        $this->nbApplications = $nbApplications;

        return $this;
    }

    /**
     * Get nbApplications
     *
     * @return integer
     */
    public function getNbApplications()
    {
        return $this->nbApplications;
    }

    

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Advert
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
	/**

   * @param Application $application

   * @return Advert

   */

  public function addApplication(Application $application)

  {

    $this->applications[] = $application;


    // On lie l'annonce à la candidature

    $application->setAdvert($this);


    return $this;

  }


  /**

   * @param Application $application

   */

  public function removeApplication(Application $application)

  {

    $this->applications->removeElement($application);

    // $application->setAdvert(null);

  }
  /**

   * @ORM\PreUpdate

   */

  public function updateDate()

  {

    $this->setUpdatedAt(new \Datetime());

  }



    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Advert
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplications()
    {
        return $this->applications;
    }
}
