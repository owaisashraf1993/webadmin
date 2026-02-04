<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('appname',null,array("label"=>"Application name"));
        $builder->add('appsubname',null,array("label"=>"Application sub-name"));
        $builder->add('appdescription',null,array("label"=>"Application description"));
        $builder->add('appstore',null,array("label"=>"AppStore url"));
        $builder->add('googleplay',null,array("label"=>"Google Play url"));
        $builder->add('adminname',null,array("label"=>"Admin name"));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $article = $event->getData();
            $form = $event->getForm();
            if ($article and null !== $article->getId()) {
                 $form->add("filelogo",null,array("label"=>"","required"=>false));
            }else{
                 $form->add("filelogo",null,array("label"=>"","required"=>true));
            }
        });

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $article = $event->getData();
            $form = $event->getForm();
            if ($article and null !== $article->getId()) {
                 $form->add("fileadmin",null,array("label"=>"","required"=>false));
            }else{
                 $form->add("fileadmin",null,array("label"=>"","required"=>true));
            }
        });
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $article = $event->getData();
            $form = $event->getForm();
            if ($article and null !== $article->getId()) {
                 $form->add("filestar",null,array("label"=>"","required"=>false));
            }else{
                 $form->add("filestar",null,array("label"=>"","required"=>true));
            }
        });
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $article = $event->getData();
            $form = $event->getForm();
            if ($article and null !== $article->getId()) {
                 $form->add("filesponsors",null,array("label"=>"","required"=>false));
            }else{
                 $form->add("filesponsors",null,array("label"=>"","required"=>true));
            }
        });
        $builder->add('save', SubmitType::class,array("label"=>"SAVE"));
    }
    public function getName()
    {
        return 'Settings';
    }
}
?>