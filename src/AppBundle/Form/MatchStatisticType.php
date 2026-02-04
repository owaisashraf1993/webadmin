<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class MatchStatisticType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('homeresult');
        $builder->add('awayresult');
        $builder->add('homesubresult');
        $builder->add('awaysubresult');
        $builder->add('state' ,ChoiceType::class, array(
                'choices' => array(
                    "Programmed"=> "programmed" ,
                    "Ended"=> "ended" ,
                    "Playing"=> "playing",
                    "Canceled" => "canceled",
                    "Postponed" => "postponed"
                ))); 
       $builder->add('infos', CollectionType::class, array(
            'entry_type' => InfoType::class,
            'entry_options' => array('label' => false),
            'allow_add' => true,
            'by_reference' => false,
            'allow_delete' => true,
        )); 
        $builder->add('save', SubmitType::class,array("label"=>"SAVE"));
    }
    public function getName()
    {
        return 'match';
    }
}
?>