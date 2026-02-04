<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title',null,array("label"=>"Table Title"));
        $builder->add('columns',null,array("label"=>"Table Columns"));
        $builder->add('columns' ,ChoiceType::class, array(
                'choices' => array(
                    "1 Clumn" => 1,
                    "2 Clumns" => 2,
                    "3 Clumns" => 3,
                    "5 Clumns" => 4,
                    "6 Clumns" => 6,
                    "7 Clumns" => 7,
                    "8 Clumns" => 8
                )));
        $builder->add('save', SubmitType::class,array("label"=>"SAVE Table"));

    }
    public function getName()
    {
        return 'Table';
    }
}
?>