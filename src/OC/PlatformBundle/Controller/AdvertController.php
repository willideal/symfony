<?php
// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use Symfony\Component\HttpFoundation\RedirectResponse; 

class AdvertController extends Controller

{
	 public function indexAction($page)

  {

    // On ne sait pas combien de pages il y a

    // Mais on sait qu'une page doit être supérieure ou égale à 1

  /*  if ($page < 1) {

      // On déclenche une exception NotFoundHttpException, cela va afficher

      // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)

      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');

    }
*/

    // Ici, on récupérera la liste des annonces, puis on la passera au template
	// Notre liste d'annonce en dur

    $listAdverts = array(

      array(

        'title'   => 'Recherche développpeur Symfony2',

        'id'      => 1,

        'author'  => 'Alexandre',

        'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',

        'date'    => new \Datetime()),

      array(

        'title'   => 'Mission de webmaster',

        'id'      => 2,

        'author'  => 'Hugo',

        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',

        'date'    => new \Datetime()),

      array(

        'title'   => 'Offre de stage webdesigner',

        'id'      => 3,

        'author'  => 'Mathieu',

        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',

        'date'    => new \Datetime())

    );

    // Mais pour l'instant, on ne fait qu'appeler le template
	return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
	'listAdverts' =>  $listAdverts
));

   // return $this->render('OCPlatformBundle:Advert:index.html.twig');

  }
  /* gerer spam
  public function addsAction(Request $request)

  {

    // On récupère le service

    $antispam = $this->container->get('oc_platform.antispam');


    // Je pars du principe que $text contient le texte d'un message quelconque

    $text = '...';

    if ($antispam->isSpam($text)) {

      throw new \Exception('Votre message a été détecté comme spam !');

    }
   

    // Ici le message n'est pas un spam
	

  }*/
  public function viewAction($id)

  {
/*
    $advert = array(

      'title'   => 'Recherche développpeur Symfony2',

      'id'      => $id,

      'author'  => 'Alexandre',

      'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',

      'date'    => new \Datetime()

    );


    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(

      'advert' => $advert

    ));*/
	//
	// On récupère le repository
	/*
    $repository = $this->getDoctrine()

      ->getManager()

      ->getRepository('OCPlatformBundle:Advert')

    ;


    // On récupère l'entité correspondante à l'id $id

    $advert = $repository->find($id);


    // $advert est donc une instance de OC\PlatformBundle\Entity\Advert

    // ou null si l'id $id  n'existe pas, d'où ce if :

    if (null === $advert) {

      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");

    }


    // Le render ne change pas, on passait avant un tableau, maintenant un objet

    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(

      'advert' => $advert

    ));*/
	
	$em = $this->getDoctrine()->getManager();
    // On récupère l'annonce $id
    $advert = $em
      ->getRepository('OCPlatformBundle:Advert')
      ->find($id);
    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }
    // On récupère la liste des candidatures de cette annonce
    $listApplications = $em
      ->getRepository('OCPlatformBundle:Application')
      ->findBy(array('advert' => $advert));
    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
      'advert'           => $advert,
      'listApplications' => $listApplications
    ));
  }
  
  public function addAction(Request $request)

  {
	// Création de l'entité
    $advert = new Advert();
    $advert->setTitle('Recherche développeur Symfony2.');
    $advert->setAuthor('Alexandre');
    $advert->setContent("Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…");
	// Création de l'entité Image

    $image = new Image();

    $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');

    $image->setAlt('Job de rêve');


    // On lie l'image à l'annonce

    $advert->setImage($image);
	
	// Création d'une première candidature

    $application1 = new Application();

    $application1->setAuthor('Marine');

    $application1->setContent("J'ai toutes les qualites requises.");


    // Création d'une deuxième candidature par exemple

    $application2 = new Application();

    $application2->setAuthor('Pierre');

    $application2->setContent("Je suis très motivé.");


    // On lie les candidatures à l'annonce

    $application1->setAdvert($advert);

    $application2->setAdvert($advert);
	
    // On récupère l'EntityManager
    $em = $this->getDoctrine()->getManager();
    // Étape 1 : On « persiste » l'entité et application
    $em->persist($advert);
	$em->persist($application1);
    $em->persist($application2);
    // Étape 2 : On « flush » tout ce qui a été persisté avant
    $em->flush();
    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
    }
    return $this->render('OCPlatformBundle:Advert:add.html.twig');
  }
  
  
  public function deleteAction($id)

  {

    // Ici, on récupérera l'annonce correspondant à $id


    // Ici, on gérera la suppression de l'annonce en question


    return $this->render('OCPlatformBundle:Advert:delete.html.twig');

  }
  public function editAction($id, Request $request)

  {
    $advert = array(

      'title'   => 'Recherche développpeur Symfony2',

      'id'      => $id,

      'author'  => 'Alexandre',

      'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',

      'date'    => new \Datetime()

    );
    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(

      'advert' => $advert

    ));

  }
  public function menuAction($limit)

  {

    // On fixe en dur une liste ici, bien entendu par la suite

    // on la récupérera depuis la BDD !

    $listAdverts = array(

      array('id' => 2, 'title' => 'Recherche développeur Symfony2'),

      array('id' => 5, 'title' => 'Mission de webmaster'),

      array('id' => 9, 'title' => 'Offre de stage webdesigner')

    );


    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(

      // Tout l'intérêt est ici : le contrôleur passe

      // les variables nécessaires au template !

      'listAdverts' => $listAdverts

    ));

  }
}
