<?php

// src/OC/PlatformBundle/Beta/BetaListener.php


namespace OC\PlatformBundle\Beta;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;


class BetaListener

{

  // Notre processeur

  protected $betaHTML;


  // La date de fin de la version b�ta :

  // - Avant cette date, on affichera un compte � rebours (J-3 par exemple)

  // - Apr�s cette date, on n'affichera plus le � b�ta �

  protected $endDate;


  public function __construct(BetaHTML $betaHTML, $endDate)

  {

    $this->betaHTML = $betaHTML;

    $this->endDate  = new \Datetime($endDate);

  }


 public function processBeta(FilterResponseEvent $event)

  {

    if (!$event->isMasterRequest()) {

      return;

    }


    $remainingDays = $this->endDate->diff(new \Datetime())->format('%d');


    // Si la date est d�pass�e, on ne fait rien

    if ($remainingDays <= 0) {

      return;

    }


    // On utilise notre BetaHRML

    $response = $this->betaHTML->displayBeta($event->getResponse(), $remainingDays);

    // On met � jour la r�ponse avec la nouvelle valeur

    $event->setResponse($response);

  }

}