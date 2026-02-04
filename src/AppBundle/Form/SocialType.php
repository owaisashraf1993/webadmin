<?php 
namespace AppBundle\Form;

use AppBundle\Entity\Social;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SocialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('social' ,ChoiceType::class, array(
            'choices' => array(
                "Facebook" => "facebook",
                "Instagram" => "instagram",
                "Twitter" => "twitter",
                "Youtube"=>"youtube",
                "Website"=>"website"
            )));
        $builder->add('value',null,array("attr"=>array("placeholder"=>"Link",'min' => 0)));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Social::class,
        ));
    }
}