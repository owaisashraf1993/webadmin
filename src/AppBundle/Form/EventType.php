<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Event;
class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('time');
        $builder->add('type');
        $builder->add('type' ,ChoiceType::class, array(
        'choices' => array(
            "Match"=> "match",
            $options["home"]=> "home",
            $options["away"]=> "away"
        )));  
        $builder->add('title');
        $builder->add('subtitle');
        $builder->add('action');
        $builder->add('save', SubmitType::class,array("label"=>"Add"));
    }
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Event::class,
			'home' => "home",
			'away' => "away"
		]);
	}

    public function getName()
    {
        return 'Event';
    }
}
?>