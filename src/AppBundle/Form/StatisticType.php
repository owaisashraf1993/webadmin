<?php 
namespace AppBundle\Form;

use AppBundle\Entity\Statistic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatisticType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('statistic',null,array("attr"=>array("required"=>true,    'empty_data' => 'John Doe',"placeholder"=>"Statistic")));
        $builder->add('value',null,array("attr"=>array("placeholder"=>"Value",'min' => 0)));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Statistic::class,
        ));
    }
}