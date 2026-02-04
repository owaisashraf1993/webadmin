<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdsIosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

       # $builder->add('rewardedadmobidios',null,array("label"=>"AdMob Rewarded ID "));
        $builder->add('publisheridios',null,array("label"=>"Admob publisher ID "));
        $builder->add('appidios',null,array("label"=>"AdMob app ID "));
        $builder->add('banneradmobidios',null,array("label"=>"AdMob Banner ID "));
        $builder->add('bannerfacebookidios',null,array("label"=>"Facebook Banner ID "));
        $builder->add('bannertypeios',ChoiceType::class, array(
                "label"=>"Banner Ad Type",
                'choices' => array(
                    "Disable Ad Banner" => "FALSE",
                    "Google AdMob Ad Banner" => "ADMOB",
                    "Facebook Network Audience Ad Banner"  =>  "FACEBOOK",
                    "Both" => "BOTH"
                )));
        $builder->add('nativeadmobidios',null,array("label"=>"AdMob Native ID "));
        $builder->add('nativefacebookidios',null,array("label"=>"Facebook Native ID "));
       $builder->add('nativetypeios',ChoiceType::class, array(
                "label"=>"Native Ad Type",
                'choices' => array(
                    "Disable Ad Native" => "FALSE",
                     "Google AdMob Ad Native"=> "ADMOB",
                     "Facebook Network Audience Ad Native" =>  "FACEBOOK",
                     "Both"=> "BOTH"
                )));
        $builder->add('nativeitemios',null,array("label"=>"Native Ad Type "));
        $builder->add('interstitialadmobidios',null,array("label"=>"AdMob Interstitial ID "));
        $builder->add('interstitialfacebookidios',null,array("label"=>"Facebook Interstitial ID "));
        $builder->add('interstitialtypeios',ChoiceType::class, array(
                "label"=>"Interstitial Ad Type",
                'choices' => array(
                    "Disable Ad Interstitial"=> "FALSE",
                     "Google AdMob Ad Interstitial"=> "ADMOB",
                    "Facebook Network Audience Ad Interstitial"=>  "FACEBOOK",
                    "Both"=> "BOTH"
                )));    
        $builder->add('interstitialclickios',null,array("label"=>"Interstitial Ad Type "));

        $builder->add('save', SubmitType::class,array("label"=>"SAVE"));
    }
    public function getName()
    {
        return 'AdsIos';
    }
}
?>