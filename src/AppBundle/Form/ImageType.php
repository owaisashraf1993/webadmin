<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class ImageType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder->add('description',null,array("label"=>"description"));
         $builder->add('enabled',null,array("label"=>"Enabled"));
         $builder->add('comment',null,array("label"=>"Enabled comments"));


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $article = $event->getData();
            $form = $event->getForm();
            if ($article and null !== $article->getId()) {
                 $form->add("file",null,array("label"=>"","required"=>false));
            }else{
                 $form->add("file",null,array("label"=>"","required"=>true));
            }
        });
        $builder->add('save', SubmitType::class,array("label"=>"save"));
      }
      public function getName()
      {
          return 'Image';
      }
}
?>