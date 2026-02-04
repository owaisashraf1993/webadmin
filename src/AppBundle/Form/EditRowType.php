<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('color' ,ChoiceType::class, array(
                'choices' => array(
                    "Default"=> null ,
                    "Red"=> "FF0000" ,
                    "Green"=> "00FF00",
                    "Blue"=> "0000FF",
                    "Orange"=> "FFA500",
                )));  
        $builder->add('prefix');
        $builder->add('club',null,['required' => true]);
        $builder->add('row1');
        $builder->add('row2');
        $builder->add('row3');
        $builder->add('row4');
        $builder->add('row5');
        $builder->add('row6');
        $builder->add('row7');
        $builder->add('row8');

        $builder->add('save', SubmitType::class,array("label"=>"SAVE"));

    }
    public function getBlockPrefix()
    {
        return 'EditRow';
    }
}
?>