<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class MatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('enabled');
        $builder->add('featured');
        $builder->add("homeclub",EntityType::class,
                  array(
                        'class' => 'AppBundle:Club',
                        "required"=> false,
                      )
                  );
        $builder->add("awayclub",EntityType::class,
                  array(
                        'class' => 'AppBundle:Club',
                        "required"=> false,
                      )
                  );
        $builder->add('competition');
        $builder->add('highlights');
        $builder->add('stadium');
        $builder->add('datetime');
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
        $builder->add('save', SubmitType::class,array("label"=>"SAVE"));
    }
    public function getName()
    {
        return 'match';
    }
}
?>