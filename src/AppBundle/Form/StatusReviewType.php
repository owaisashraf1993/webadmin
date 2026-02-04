<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class StatusReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('description',null,array("label"=>"description"));
       $builder->add('comment');

       $builder->add('save', SubmitType::class ,array("label"=>"REVIEW"));
    }
    public function getName()
    {
        return 'Status';
    }
}
?>