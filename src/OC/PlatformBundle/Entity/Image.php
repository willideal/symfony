<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\ImageRepository")
 */
class Image
{
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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;
	private $file;
	// On ajoute cet attribut pour y stocker le nom du fichier temporairement
	private $tempFilename;

  

  public function getFile()
  {
    return $this->file;
  }

  public function setFile($file = null)
  {
    //$this->file = $file;
	$this->file = $file;


    // On vérifie si on avait déjà un fichier pour cette entité

    if (null !== $this->url) {

      // On sauvegarde l'extension du fichier pour le supprimer plus tard

      $this->tempFilename = $this->url;


      // On réinitialise les valeurs des attributs url et alt

      $this->url = null;

      $this->alt = null;

    }
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
     * Set url
     *
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }
	
	public function upload()

  {

    // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien

    if (null === $this->file) {

      return;

    }


    // On récupère le nom original du fichier de l'internaute

    $name = $this->file->getClientOriginalName();


    // On déplace le fichier envoyé dans le répertoire de notre choix

    $this->file->move($this->getUploadRootDir(), $name);


    // On sauvegarde le nom de fichier dans notre attribut $url

    $this->url = $name;


    // On crée également le futur attribut alt de notre balise <img>

    $this->alt = $name;

  }


  public function getUploadDir()

  {

    // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)

    return 'uploads/img';

  }


  protected function getUploadRootDir()

  {

    // On retourne le chemin relatif vers l'image pour notre code PHP

    return __DIR__.'/../../../../web/'.$this->getUploadDir();

  }
}

