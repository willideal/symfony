<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use OC\PlatformBundle\Form\ImageType;
use Symfony\Component\Form\FormEvent;

use Symfony\Component\Form\FormEvents;


class AdvertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class)
            ->add('title', TextType::class)
			->add('content', TextareaType::class)
			->add('author', TextType::class)
			->add('published', CheckboxType::class)
            ->add('save', SubmitType::class, array('label' => 'Créer Une annonce'));
			
		$builder->add('image', ImageType::class);
		$builder->add('categories', CollectionType::class, array(
            'entry_type' => CategoryType::class
        ));
		$builder->add('categories', EntityType::class, array(

		  'class'    => 'OCPlatformBundle:Category',

		  'choice_label' => 'name',

		  'multiple' => true,
		  


		));
		// On ajoute une fonction qui va écouter l'évènement PRE_SET_DATA

    $builder->addEventListener(

      FormEvents::PRE_SET_DATA,

      function(FormEvent $event) {

        // On récupère notre objet Advert sous-jacent

        $advert = $event->getData();


        if (null === $advert) {

          return;

        }


        if (!$advert->getPublished() || null === $advert->getId()) {

          $event->getForm()->add('published', CheckboxType::class, array('required' => false));

        } else {

          $event->getForm()->remove('published');

        }

      }

    );
		
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Advert'
        ));
    }
}
