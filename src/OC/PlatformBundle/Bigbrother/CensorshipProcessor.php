<?php

// src/OC/PlatformBundle/Bigbrother/CensorshipProcessor.php


namespace OC\PlatformBundle\Bigbrother;


use Symfony\Component\Security\Core\User\UserInterface;


class CensorshipProcessor

{

  protected $mailer;


  public function __construct(\Swift_Mailer $mailer)

  {

    $this->mailer = $mailer;

  }


  // M�thode pour notifier par e-mail un administrateur

  public function notifyEmail($message, UserInterface $user)

  {

    $message = \Swift_Message::newInstance()

      ->setSubject("Nouveau message d'un utilisateur surveill�")

      ->setFrom('admin@votresite.com')

      ->setTo('admin@votresite.com')

      ->setBody("L'utilisateur surveill� '".$user->getUsername()."' a post� le message suivant : '".$message."'");


    $this->mailer->send($message);

  }


  // M�thode pour censurer un message (supprimer les mots interdits)

  public function censorMessage($message)

  {

    $message = str_replace(array('top secret', 'mot interdit'), '', $message);


    return $message;

  }

}