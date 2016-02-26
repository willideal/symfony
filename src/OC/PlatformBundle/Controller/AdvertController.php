<?php
// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Category;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Form\ImageType;
use OC\PlatformBundle\Form\CategoryType;
use OC\PlatformBundle\Entity\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
//use Symfony\Component\HttpFoundation\RedirectResponse; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class AdvertController extends Controller

{
	 public function indexAction($page)
  {
	if ($page < 1) {

      throw $this->createNotFoundException("La page ".$page." n'existe pas.");

    }

    // Pour récupérer la liste de toutes les annonces : on utilise getAdverts au lieu de findAll()
	//nombre d'annonce par page
	$nbPerPage= 3;
    $listAdverts = $this->getDoctrine()

      ->getManager()

      ->getRepository('OCPlatformBundle:Advert')

      ->getAdverts($page, $nbPerPage);
	  
	 // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts)/$nbPerPage);

    // Si la page n'existe pas, on retourne une 404
    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages'     => $nbPages,
      'page'        => $page
    ));

  }

  public function viewAction(Advert $advert, $id)
  {	
	$em = $this->getDoctrine()->getManager();
    // On récupère l'annonce $id
    $advert = $em
      ->getRepository('OCPlatformBundle:Advert')
      ->find($id);
    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }
    // On récupère la liste des advertskills de cette annonce
    $listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')->findByAdvert($advert);

    // pour prendre en compte les variables :
    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
      'advert'           => $advert,
      'listAdvertSkills' => $listAdvertSkills,
    ));
  }
  
  public function addAction(Request $request)
  {
   /*if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {

      // Sinon on déclenche une exception « Accès interdit »

      throw new AccessDeniedException('Accès limité aux auteurs.');

    }*/
	// On crée un objet Advert
    $advert = new Advert();
	$form = $this->createForm(AdvertType::class, $advert);
	
	$categories1 = new Category();
    $categories1->getName = 'Graphisme';
    $advert->getCategories()->add($categories1);
    /*$tag2 = new Tag();
    $tag2->name = 'tag2';
    $task->getTags()->add($tag2);*/
	
    if ($form->handleRequest($request)->isValid()) {
      // On l'enregistre notre objet $advert dans la base de données, par exemple
	   $advert->getImage()->upload();
      $em = $this->getDoctrine()->getManager();
      $em->persist($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      // On redirige vers la page de visualisation de l'annonce nouvellement créée
      return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));

    }
    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
      'form' => $form->createView(),
    ));
  }
  
  
  
  public function deleteAction($id, Request $request)
  {
	 $em = $this->getDoctrine()->getManager();


    // On récupère l'annonce $id

    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);


    if (null === $advert) {

      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");

    }


    // On crée un formulaire vide, qui ne contiendra que le champ CSRF

    // Cela permet de protéger la suppression d'annonce contre cette faille

    $form = $this->createFormBuilder()->getForm();


    if ($form->handleRequest($request)->isValid()) {

      $em->remove($advert);

      $em->flush();


      $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");


      return $this->redirect($this->generateUrl('oc_platform_home'));

    }


    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer

    return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(

      'advert' => $advert,

      'form'   => $form->createView()

    ));
  }
  
  public function editAction($id)
  {
	// On récupère l'EntityManager
    $em = $this->getDoctrine()->getManager();
	
    // On récupère l'entité correspondant à l'id $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    // Si l'annonce n'existe pas, on affiche une erreur 404
    if (null === $advert) {

      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");

    }

	$advert = new Advert();
	$form = $this->createForm(AdvertEditType::class, $advert);
    
    if ($form->handleRequest($request)->isValid()) {

      // Inutile de persister ici, Doctrine connait déjà notre annonce

      $em->flush();


      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');


      return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));

    }


    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(

      'form'   => $form->createView(),

      'advert' => $advert // Je passe également l'annonce à la vue si jamais elle veut l'afficher

    ));
  }
  
	public function menuAction($limit = 3)
  {
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->findBy(
        array(),                 // Pas de critère
        array('date' => 'desc'), // On trie par date décroissante
        $limit,                  // On sélectionne $limit annonces
        0                        // À partir du premier
    );
    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }
  
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
		$authenticationUtils = $this->get('security.authentication_utils');

    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render(
        'OCPlatformBundle:Advert:login.html.twig',
        array(
            // last username entered by the user
            'last_username' => $lastUsername,
            'error'         => $error,
        )
    );
    }
	
	public function translationAction($name)

  {

    return $this->render('OCPlatformBundle:Blog:translation.html.twig', array(

      'name' => $name

    ));

  }
  /**

   * @ParamConverter("json")

   */

  public function ParamConverterAction($json)

  {

    return new Response(print_r($json, true));

  }
}
