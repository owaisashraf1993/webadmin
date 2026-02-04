<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
class QuoteReviewType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder->add('color',null,array("label"=>"Enabled"));
         $builder->add('comment',null,array("label"=>"Enabled comments"));

        $builder->add('save', SubmitType::class,array("label"=>"Publish"));
      }
      public function getName()
      {
          return 'Quote';
      }
}
?>