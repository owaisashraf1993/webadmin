<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NotificationType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder->add('firebasekey');
        $builder->add('commentnotification',ChoiceType::class, array(
                'choices' => array(
                    "Enable Comments Notification" =>true,
                    "Disable Comments Notification" => false,
                )));
        $builder->add('reviewnotification',ChoiceType::class, array(
                'choices' => array(
                    "Enable Approved Status Notification" =>true,
                    "Disable Approved Status Notification" => false,
                )));
        $builder->add('save', SubmitType::class,array("label"=>"Save Settings"));
      }
      public function getName()
      {
          return 'NotificationType';
      }
}
?>