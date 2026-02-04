<?php 
namespace AppBundle\Form;

use AppBundle\Entity\Info;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('home',null,array("attr"=>array("required"=>true)));
        $builder->add('name',null,array("attr"=>array("required"=>true,"placeholder"=>"Statistic")));
        $builder->add('away',null,array("attr"=>array("required"=>true)));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Info::class,
        ));
    }
}