<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Device;
use AppBundle\Entity\Item;

use MediaBundle\Entity\Media;
use AppBundle\Form\WebSettingsType;
use AppBundle\Form\SettingsType;
use AppBundle\Form\AdsType;
use AppBundle\Form\AdsIosType;

use AppBundle\Form\NotificationType;
use AppBundle\Form\PaymentType;
use AppBundle\Form\FaqType;
use AppBundle\Form\RefundType;
use AppBundle\Form\PolicyType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class HomeController extends Controller
{
    function send_notificationToken ($tokens, $message,$key)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'registration_ids'  => $tokens,
            'data'   => $message

            );
        $headers = array(
            'Authorization:key = '.$key,
            'Content-Type: application/json'
            );
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
       return $result;
    }
    function send_notification ($message,$key,$topic)
    {

        $notification = $message;
        $notification["image"] = ($message["icon"] == null)? $message["image"] : $message["icon"] ;
        unset($notification["icon"]);

        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'to'  => '/topics/'.$topic,
            'notification' =>   $notification,
            'data' =>   $message,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'android' => array(
                "priority"=>"high",
                "channel_id"=> 'Link_channel',
                "tag"=> "sendmeNotification",
                "notification"=>array(
                    "channel_id"=> 'Link_channel',
                )
            ),
            'priority'=>'high'
             );
        $headers = array(
            'Authorization:key = '.$key,
            'Content-Type: application/json'
            );
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
       return $result;
    }
      public function privacypolicyAction() {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        return $this->render("AppBundle:Home:privacypolicy.html.twig", array("setting" => $setting));
    }


    public function notifMatchAction(Request $request)
    {
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $em=$this->getDoctrine()->getManager();
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)
            ->add('object', EntityType::class, array('class' => 'AppBundle:Match'))           
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $selected_match = $em->getRepository("AppBundle:Match")->find($data["object"]);
            $message = array(
                    "type"=> "match",
                    "id"=> $selected_match->getId(),
                    "title"=> $data["title"],
                    "message"=>$data["message"],
                    "body"=>$data["message"],
                    "image"=> $data["image"],
                    "icon"=>$data["icon"],
                    "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                    'status'=>"done",
                );

            $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();
            $message_image = $this->send_notification( $message,$key,"matches");
            $this->addFlash('success', 'Operation has been done successfully ');
        }
        return $this->render('AppBundle:Home:notif_match.html.twig',array(
          "form"=>$form->createView()
          ));
    }
    public function notifStatusAction(Request $request)
    {
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $em=$this->getDoctrine()->getManager();
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)
            ->add('object', EntityType::class, array('class' => 'AppBundle:Status'))           
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $selected_poster = $em->getRepository("AppBundle:Status")->find($data["object"]);
            $message = array(
                    "type"=> "status",
                    "id"=> $selected_poster->getId(),
                    "title"=> $data["title"],
                    "message"=>$data["message"],
                    "body"=>$data["message"],
                    "image"=> $data["image"],
                    "icon"=>$data["icon"],
                    "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                    'status'=>"done"
                );

            $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();
            $message_image = $this->send_notification( $message,$key,"status"); 
            $this->addFlash('success', 'Operation has been done successfully ');
        }
        return $this->render('AppBundle:Home:notif_status.html.twig',array(
          "form"=>$form->createView()
          ));
    }
    public function notifPostAction(Request $request)
    {
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $em=$this->getDoctrine()->getManager();
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)
            ->add('object', EntityType::class, array('class' => 'AppBundle:Post'))           
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $selected_poster = $em->getRepository("AppBundle:Post")->find($data["object"]);
            $message = array(
                    "type"=> "post",
                    "id"=> $selected_poster->getId(),
                    "title"=> $data["title"],
                    "body"=>$data["message"],
                    "message"=>$data["message"],
                    "image"=> $data["image"],
                    "icon"=>$data["icon"],
                    "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                    'status'=>"done"
                );

            $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();
            $message_image = $this->send_notification( $message,$key,"news"); 
            $this->addFlash('success', 'Operation has been done successfully ');
        }
        return $this->render('AppBundle:Home:notif_post.html.twig',array(
          "form"=>$form->createView()
          ));
    }


   public function notifUrlAction(Request $request)
    {
    
        memory_get_peak_usage();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        $em=$this->getDoctrine()->getManager();

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('GET')
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)      
            ->add('url', UrlType::class,array("label"=>"Url"))
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = array(
                        "type"=>"link",
                        "id"=>rand(1, 10000),
                        "link"=>$data["url"],
                        "title"=> $data["title"],
                        "message"=>$data["message"],
                        "body"=>$data["message"],
                        "image"=> $data["image"],
                        "icon"=>$data["icon"],
                         "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                         'status'=>"done"
                        );
                        $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();
            $message_image = $this->send_notification( $message,$key,"application"); 
           
            $this->addFlash('success', 'Operation has been done successfully ');
          
        }
        return $this->render('AppBundle:Home:notif_url.html.twig',array(
            "form"=>$form->createView()
        ));
    }

    public function api_app_configAction()
    {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        
        $code="200";
        $message="";
        $errors=array();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');


        $response_app_name["name"] = "APP_NAME";
        $response_app_name["value"] = $setting->getAppname();

        $response_sub_app_name["name"] = "APP_SUB_NAME";
        $response_sub_app_name["value"] = $setting->getAppsubname();


        // ios ads

        $response_ads_ios_native_facebook_id["name"] = "ADMIN_IOS_NATIVE_FACEBOOK_ID";
        $response_ads_ios_native_facebook_id["value"] = $setting->getNativefacebookidios();

        $response_ads_ios_publisher_id["name"] = "ADMIN_IOS_PUBLISHER_ID";
        $response_ads_ios_publisher_id["value"] = $setting->getPublisheridios();

        $response_ads_ios_app_id["name"] = "ADMIN_IOS_APP_ID";
        $response_ads_ios_app_id["value"] = $setting->getAppidios();

                

        $response_ads_ios_native_type["name"] = "ADMIN_IOS_NATIVE_TYPE";
        $response_ads_ios_native_type["value"] = $setting->getNativetypeios();

        $response_ads_ios_native_item["name"] = "ADMIN_IOS_NATIVE_ITEM";
        $response_ads_ios_native_item["value"] = $setting->getNativeitemios();







        $response_ads_ios_interstitial_facebook_id["name"] = "ADMIN_IOS_INTERSTITIAL_FACEBOOK_ID";
        $response_ads_ios_interstitial_facebook_id["value"] = $setting->getInterstitialfacebookidios();




        $response_ads_ios_interstitial_admob_id["name"] = "ADMIN_IOS_INTERSTITIAL_ADMOB_ID";
        $response_ads_ios_interstitial_admob_id["value"] = $setting->getInterstitialadmobidios();
                

        $response_ads_ios_interstitial_type["name"] = "ADMIN_IOS_INTERSTITIAL_TYPE";
        $response_ads_ios_interstitial_type["value"] = $setting->getInterstitialtypeios();

        $response_ads_ios_interstitial_click["name"] = "ADMIN_IOS_INTERSTITIAL_CLICKS";
        $response_ads_ios_interstitial_click["value"] = $setting->getInterstitialclickios();

        $response_ads_ios_banner_admob_id["name"] = "ADMIN_IOS_BANNER_ADMOB_ID";
        $response_ads_ios_banner_admob_id["value"] = $setting->getBanneradmobidios();


        $response_ads_ios_banner_facebook_id["name"] = "ADMIN_IOS_BANNER_FACEBOOK_ID";
        $response_ads_ios_banner_facebook_id["value"] = $setting->getBannerfacebookidios();




        $response_ads_ios_banner_type["name"] = "ADMIN_IOS_BANNER_TYPE";
        $response_ads_ios_banner_type["value"] = $setting->getBannertypeios();



        $response_ads_ios_native_admob_id["name"] = "ADMIN_IOS_NATIVE_ADMOB_ID";
        $response_ads_ios_native_admob_id["value"] = $setting->getNativeadmobidios();



        // android ads


        $response_ads_android_native_facebook_id["name"] = "ADMIN_ANDROID_NATIVE_FACEBOOK_ID";
        $response_ads_android_native_facebook_id["value"] = $setting->getNativefacebookid();

        $response_ads_android_publisher_id["name"] = "ADMIN_ANDROID_PUBLISHER_ID";
        $response_ads_android_publisher_id["value"] = $setting->getPublisherid();

        $response_ads_android_app_id["name"] = "ADMIN_ANDROID_APP_ID";
        $response_ads_android_app_id["value"] = $setting->getAppid();



                



        $response_ads_android_native_item["name"] = "ADMIN_ANDROID_NATIVE_ITEM";
        $response_ads_android_native_item["value"] = $setting->getNativeitem();

        $response_ads_android_interstitial_admob_id["name"] = "ADMIN_ANDROID_INTERSTITIAL_ADMOB_ID";
        $response_ads_android_interstitial_admob_id["value"] = $setting->getInterstitialadmobid();
        


        $response_ads_android_interstitial_facebook_id["name"] = "ADMIN_ANDROID_INTERSTITIAL_FACEBOOK_ID";
        $response_ads_android_interstitial_facebook_id["value"] = $setting->getInterstitialfacebookid();
        



        $response_ads_android_interstitial_type["name"] = "ADMIN_ANDROID_INTERSTITIAL_TYPE";
        $response_ads_android_interstitial_type["value"] = $setting->getInterstitialtype();

        $response_ads_android_interstitial_click["name"] = "ADMIN_ANDROID_INTERSTITIAL_CLICKS";
        $response_ads_android_interstitial_click["value"] = $setting->getInterstitialclick();

        $response_ads_android_banner_admob_id["name"] = "ADMIN_ANDROID_BANNER_ADMOB_ID";
        $response_ads_android_banner_admob_id["value"] = $setting->getBanneradmobid();

        $response_ads_android_banner_facebook_id["name"] = "ADMIN_ANDROID_BANNER_FACEBOOK_ID";
        $response_ads_android_banner_facebook_id["value"] = $setting->getBannerfacebookid();



        $response_ads_android_banner_type["name"] = "ADMIN_ANDROID_BANNER_TYPE";
        $response_ads_android_banner_type["value"] = $setting->getBannertype();



        $response_ads_android_native_admob_id["name"] = "ADMIN_ANDROID_NATIVE_ADMOB_ID";
        $response_ads_android_native_admob_id["value"] = $setting->getNativeadmobid();




        $response_ads_android_native_type["name"] = "ADMIN_ANDROID_NATIVE_TYPE";
        $response_ads_android_native_type["value"] = $setting->getNativetype();


        $response_app_logo["name"] = "APP_LOGO";
        $response_app_logo["value"] = $imagineCacheManager->getBrowserPath($setting->getLogo()->getLink(), 'app_logo');

        $response_sponsors_logo["name"] = "APP_SPONSORS";
        $response_sponsors_logo["value"] = $imagineCacheManager->getBrowserPath($setting->getSponsors()->getLink(), 'app_sponsors');


        $response_star_logo["name"] = "APP_STAR";
        $response_star_logo["value"] = $imagineCacheManager->getBrowserPath($setting->getStar()->getLink(), 'app_star');


        $errors[]=$response_app_name;
        $errors[]=$response_sub_app_name;
        $errors[]=$response_app_logo;
        $errors[]=$response_sponsors_logo;
        $errors[]=$response_star_logo;


        $errors[]=$response_ads_android_app_id;
        $errors[]=$response_ads_android_publisher_id;
        $errors[]=$response_ads_android_native_facebook_id;
        $errors[]=$response_ads_android_native_admob_id;
        $errors[]=$response_ads_android_native_type;

        $errors[]=$response_ads_android_interstitial_facebook_id;
        $errors[]=$response_ads_android_interstitial_admob_id;
        $errors[]=$response_ads_android_interstitial_type;
        $errors[]=$response_ads_android_interstitial_click;
        $errors[]=$response_ads_android_banner_facebook_id;
        $errors[]=$response_ads_android_banner_admob_id;
        $errors[]=$response_ads_android_banner_type;
        $errors[]=$response_ads_android_native_item;



        $errors[]=$response_ads_ios_app_id;
        $errors[]=$response_ads_ios_publisher_id;
        $errors[]=$response_ads_ios_native_facebook_id;
        $errors[]=$response_ads_ios_native_admob_id;
        $errors[]=$response_ads_ios_native_type;
        $errors[]=$response_ads_ios_native_item;
        $errors[]=$response_ads_ios_interstitial_facebook_id;
        $errors[]=$response_ads_ios_interstitial_admob_id;
        $errors[]=$response_ads_ios_interstitial_type;
        $errors[]=$response_ads_ios_interstitial_click;
        $errors[]=$response_ads_ios_banner_facebook_id;
        $errors[]=$response_ads_ios_banner_admob_id;
        $errors[]=$response_ads_ios_banner_type;

        $error=array(
            "code"=>$code,
            "message"=>$message,
            "values"=>$errors,
        );
        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($error, 'json');
        return new Response($jsonContent);  
    }

    public function edit_privacypolicyAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(PolicyType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:edit_privacypolicy.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 


    public function ads_iosAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(AdsIosType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:ads_ios.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 
    public function adsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(AdsType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:ads.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 
    public function notificationsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(NotificationType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:notifications.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 
    public function settingsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $admin = $em->getRepository("UserBundle:User")->find(1);
        if($admin == null || $setting == null){
            throw new NotFoundHttpException("Page not found");
        }
        $setting->setAdminname($admin->getName());
        if($setting->getLogo() == null){
            $logo = new Media();
             $logo->setTitre("app logo");
             $logo->setUrl("club.png");
             $logo->setType("image/png");
             $logo->setType("png");
             $logo->setExtension("png");
             $logo->setEnabled(true);
            $em->persist($logo);
            $em->flush();
            $setting->setLogo($logo);
            $em->flush();
        }
        if($setting->getStar() == null){
            $star = new Media();
            $star->setTitre("app star");
            $star->setUrl("star.jpg");
            $star->setType("image/jpg");
            $star->setType("jpg");
             $star->setExtension("jpg");

            $star->setEnabled(true);
            $em->persist($star);
            $em->flush();
            $setting->setStar($star);
            $em->flush();
        }
        if($setting->getSponsors() == null){
            $sponsors = new Media();
            $sponsors->setTitre("app sponsors");
            $sponsors->setUrl("sponsors.jpg");
            $sponsors->setType("image/jpg");
            $sponsors->setType("jpg");
            $sponsors->setExtension("jpg");

            $sponsors->setEnabled(true);
            $em->persist($sponsors);
            $em->flush();
            $setting->setSponsors($sponsors);
            $em->flush();
        }
        if($admin->getMedia() == null){
            $admin_new_media = new Media();
            $admin_new_media->setTitre("app admin_new_media");
            $admin_new_media->setUrl("admin.jpg");
            $admin_new_media->setType("image/jpg");
            $admin_new_media->setType("jpg");
            $admin_new_media->setExtension("jpg");

            $admin_new_media->setEnabled(true);
            $em->persist($admin_new_media);
            $em->flush();
            $admin->setMedia($admin_new_media);
            $em->flush();
        }
        $form = $this->createForm(SettingsType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($setting->getFilestar() != null) {
                $star = $setting->getStar();
                $star->setFile($setting->getFilestar());
                $star->delete($this->container->getParameter('files_directory'));
                $star->upload($this->container->getParameter('files_directory'));
                $em->persist($star);
                $em->flush();
            }
            if ($setting->getFilelogo() != null) {
                $logo = $setting->getLogo();
                $logo->setFile($setting->getFilelogo());
                $logo->delete($this->container->getParameter('files_directory'));
                $logo->upload($this->container->getParameter('files_directory'));
                $em->persist($logo);
                $em->flush();
            }
            if ($setting->getFilesponsors() != null) {
                $sponsors = $setting->getSponsors();
                $sponsors->setFile($setting->getFilesponsors());
                $sponsors->delete($this->container->getParameter('files_directory'));
                $sponsors->upload($this->container->getParameter('files_directory'));
                $em->persist($sponsors);
                $em->flush();
            }
            if ($setting->getFileadmin() != null) {
                $admin_media = $admin->getMedia();
                $admin_media->setFile($setting->getFileadmin());
                $admin_media->delete($this->container->getParameter('files_directory'));
                $admin_media->upload($this->container->getParameter('files_directory'));
                $em->persist($admin_media);
                $em->flush();
            }
            $admin->setName($setting->getAdminname());

            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:settings.html.twig", array("setting" => $setting,"admin"=>$admin, "form" => $form->createView()));
    } 
    public function indexAction(Request $request)
    {   


        $em=$this->getDoctrine()->getManager();
        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array());
        $support_count= $em->getRepository("AppBundle:Support")->count();







        

        $users_count= $em->getRepository("UserBundle:User")->count();
        $matches_count= $em->getRepository("AppBundle:Match")->count();
        $competition_count= $em->getRepository("AppBundle:Competition")->count();
        $players_count= $em->getRepository("AppBundle:Player")->count();
        $staffs_count= $em->getRepository("AppBundle:Staff")->count();
        $country_count= $em->getRepository("AppBundle:Country")->count();

        $post_shares= $em->getRepository("AppBundle:Post")->countShares();
        $post_views= $em->getRepository("AppBundle:Post")->countViews();
        $posts= $em->getRepository("AppBundle:Post")->countAll();

        $status_downloads= $em->getRepository("AppBundle:Status")->countDownloads();
        $status_shares= $em->getRepository("AppBundle:Status")->countShares();
        $status_views= $em->getRepository("AppBundle:Status")->countViews();
        $status_count= $em->getRepository("AppBundle:Status")->countAll();

        $comment_count= $em->getRepository("AppBundle:Comment")->count();
        $club_count= $em->getRepository("AppBundle:Club")->count();
        $question_count= $em->getRepository("AppBundle:Question")->count();


        return $this->render('AppBundle:Home:index.html.twig',array(
            "support_count"=>$support_count,
            "question_count"=>$question_count,
            "club_count"=>$club_count,
            "status_count"=>$status_count,
            "posts"=>$posts,
            "competition_count"=>$competition_count,
            "players_count"=>$players_count,
            "staffs_count"=>$staffs_count,
            "status_shares"=>$status_shares,
            "status_downloads"=>$status_downloads,
            "status_views"=>$status_views,
            "post_views"=>$post_views,
            "post_shares"=>$post_shares,
            "country_count"=>$country_count,
            "comment_count"=>$comment_count,
            "users_count"=>$users_count,
            "matches_count"=>$matches_count,
        ));
    }
    public function api_searchAction(Request $request, $token,$query) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }

        $em = $this->getDoctrine()->getManager();
        $repositoryChannel = $em->getRepository('AppBundle:Channel');
        $queryChannel = $repositoryChannel->createQueryBuilder('p')
                ->where("p.enabled = true","p.title like '%" . $query . "%' or p.tags like '%" . $query . "%'")
                ->addOrderBy('p.id', 'ASC')
                ->getQuery();         
            
        $channels = $queryChannel->getResult();

        $em = $this->getDoctrine()->getManager();
        $repositoryPoster = $em->getRepository('AppBundle:Poster');
        $queryPosters = $repositoryPoster->createQueryBuilder('p')
                ->leftJoin('p.seasons', 's')
                ->leftJoin('s.episodes', 'e')
                ->where("p.enabled = true","p.title like '%" . $query . "%' or e.title like '%" . $query . "%' or p.tags like '%" . $query . "%'")
                ->addOrderBy('p.id', 'ASC')
                ->getQuery();         
            
        $posters = $queryPosters->getResult();

        return $this->render('AppBundle:Home:api_search.html.php', array("channels"=>$channels,"posters"=>$posters));
    }

    public function api_mylistAction(Request $request, $token,$key,$id) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository("UserBundle:User")->findOneBy(array("id"=>$id));
        $nombre = 30;
        $page = 0;
        $channels =array();

        if($user){

            if ($user->isEnabled()) {

                if ($key==sha1($user->getPassword())) {
                  

                  
                    $channels = $em->getRepository("AppBundle:Item")->findBy(array("poster"=>null,"user"=>$user), array("position" => "desc"));
                    $repository = $em->getRepository('AppBundle:Item');

                    $repo_query = $repository->createQueryBuilder('i');

                    $repo_query->leftJoin('i.poster', 'p');
                    $repo_query->where($repo_query->expr()->isNotNull('i.poster'));
                    $repo_query->andWhere("p.enabled = true");
                    $repo_query->andWhere("i.user =".$user->getId());

                    $repo_query->addOrderBy('i.position', "desc");
                    $repo_query->addOrderBy('p.id', 'ASC');

                    $query =  $repo_query->getQuery(); 
                    $query->setFirstResult($nombre * $page);
                    $query->setMaxResults($nombre);
                    $posters = $query->getResult();
                    
                    return $this->render('AppBundle:Home:api_mylist.html.php', array("posters"=>$posters,"channels"=>$channels));

                }
            }
        }
       return new Response("");
    }
    public function api_addlistAction(Request $request,$token)
    {

            $id =$request->request->get('id');
            $type =$request->request->get('type');
            $user =$request->request->get('user');
            $key =$request->request->get('key');
            if ($token != $this->container->getParameter('token_app')) {
                throw new NotFoundHttpException("Page not found");
            }
            $code = 500;
            $em=$this->getDoctrine()->getManager();
            $user_obj = $em->getRepository("UserBundle:User")->findOneBy(array("id"=>$user));

            if ($user_obj!=null){

                if ($type ==  "poster") {
                    $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"enabled"=>true));
                    if ($poster !=null) {
                        $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$user_obj,"poster" => $poster));
                        if ($item == null) {
                            
                            $last_item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$user_obj,"channel" =>null),array("position"=>"desc"));
                            $position=1;
                            if ($last_item!=null) {
                                $position=$last_item->getPosition()+1;
                            }
                            $code = 200;
                            $item = new Item();
                            $item->setPoster($poster);
                            $item->setUser($user_obj);
                            $item->setPosition($position);
                            $em->persist($item);
                            $em->flush();
                        }else{
                            $em->remove($item);
                            $em->flush();
                            $code = 202;
                        }
                    }
                }
                if ($type ==  "channel") {
                    $channel = $em->getRepository("AppBundle:Channel")->findOneBy(array("id"=>$id,"enabled"=>true));
                    if ($channel !=null) {
                        $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$user_obj,"channel" => $channel));
                        if ($item == null) {
                            $last_item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$user_obj,"poster" =>null),array("position"=>"desc"));
                            $position=1;
                            if ($last_item!=null) {
                                $position=$last_item->getPosition()+1;
                            }


                            $code = 200;
                            $item = new Item();
                            $item->setChannel($channel);
                            $item->setUser($user_obj);
                            $item->setPosition($position);
                            $em->persist($item);
                            $em->flush();
                        }else{
                            $em->remove($item);
                            $em->flush();
                            $code = 202;
                        }
                    }
                }
            }
        
        return new Response($code);
    } 
    public function api_checklistAction(Request $request,$token)
    {
            $id =$request->request->get('id');
            $type =$request->request->get('type');
            $user =$request->request->get('user');
            $key =$request->request->get('key');
            if ($token != $this->container->getParameter('token_app')) {
                throw new NotFoundHttpException("Page not found");
            }
            $code = 500;
            $em=$this->getDoctrine()->getManager();
            $user_obj = $em->getRepository("UserBundle:User")->findOneBy(array("id"=>$user));

            if ($user_obj!=null){
                if ($type ==  "poster") {
                    $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"enabled"=>true));
                    if ($poster !=null) {
                        $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$user_obj,"poster" => $poster));
                        if ($item == null) {
                            $code = 202;
                        }else{
                            $code = 200;
                        }
                    }
                }
                if ($type ==  "channel") {
                    $channel = $em->getRepository("AppBundle:Channel")->findOneBy(array("id"=>$id,"enabled"=>true));
                    if ($channel !=null) {
                        $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$user_obj,"channel" => $channel));
                        if ($item == null) {    
                            $code = 202;
                        }else{
                            $code = 200;
                        }
                    }
                }
            }
        
        return new Response($code);
    } 
    public function api_allAction(Request $request, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $em = $this->getDoctrine()->getManager();
        $players = $em->getRepository("AppBundle:Player")->findBy(array(), array("position" => "asc"),10);
        $matches = $em->getRepository("AppBundle:Match")->findBy(array("enabled"=>true,"featured"=>true), array("datetime" => "asc"),10);
        $questions =  $em->getRepository("AppBundle:Question")->findBy(array("enabled"=>true),array("position"=>"asc"));


        $repository_post = $em->getRepository('AppBundle:Post');
        $query_post = $repository_post->createQueryBuilder('a')
            ->addOrderBy('a.created', 'DESC')
            ->addOrderBy('a.id', 'asc')
            ->setMaxResults(30)
            ->getQuery();
        $posts = $query_post->getResult();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        $list = array();
        foreach ($matches as $key => $match) {
             $s = null;
            $s["id"]=$match->getId();
            $s["title"]=$match->getTitle();
            $s["homeresult"]=$match->getHomeresult();
            $s["awayresult"]=$match->getAwayresult();
            $s["homesubresult"]=$match->getHomesubresult();
            $s["awaysubresult"]=$match->getAwaysubresult();
            $s["state"]=$match->getState();
            $s["stadium"]=$match->getStadium();
            $s["highlights"]=$match->getHighlights();
            $s["time"]=$match->getDateTime()->format("H:i");


            $diff = date_diff(new  \DateTime(), \DateTime::createFromFormat('d/m/Y',$match->getDateTime()->format("d/m/Y")));
            if($diff->days == 0){
                $s["date"]="Today";
            }elseif($diff->days ==  1){
               if($diff->invert){
                    $s["date"]="Yesterday";
               }else{
                    $s["date"]="Tomorrow";
               }
            }else{
                $s["date"]=$match->getDateTime()->format(" F d,Y"); 
            }

            $homeclub["id"] =  $match->getHomeClub()->getId();
            $homeclub["name"] =  $match->getHomeClub()->getName();
            $homeclub["image"]=$imagineCacheManager->getBrowserPath($match->getHomeClub()->getMedia()->getLink(), 'club_thumb');

            $s["homeclub"] = $homeclub;

            $awayclub["id"] =  $match->getAwayClub()->getId();
            $awayclub["name"] =  $match->getAwayClub()->getName();
            $awayclub["image"] =  $match->getAwayClub()->getMedia()->getLink();
            $awayclub["image"]=$imagineCacheManager->getBrowserPath($match->getAwayClub()->getMedia()->getLink(), 'club_thumb');

            $s["awayclub"] = $awayclub;

            $competition_obj["id"] =  $match->getCompetition()->getId();
            $competition_obj["name"] =  $match->getCompetition()->getName();
            $competition_obj["image"] =  $match->getCompetition()->getMedia()->getLink();
            $competition_obj["image"]=$imagineCacheManager->getBrowserPath($match->getCompetition()->getMedia()->getLink(), 'competition_thumb');

            $s["competition"] = $competition_obj;

            $list[]=$s;
        }

        return $this->render('AppBundle:Home:api_all.html.php', array("posts"=>$posts,"questions"=>$questions,"matches"=>$list,"players"=>$players));
    }
    public function api_deviceAction($tkn,$token){
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $code="200";
        $message="";
        $errors=array();
        $em = $this->getDoctrine()->getManager();
        $d=$em->getRepository('AppBundle:Device')->findOneBy(array("token"=>$tkn));
        if ($d==null) {
            $device = new Device();
            $device->setToken($tkn);
            $em->persist($device);
            $em->flush();
            $message="Deivce added";
        }else{
            $message="Deivce Exist";
        }

        $error=array(
            "code"=>$code,
            "message"=>$message,
            "values"=>$errors
        );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($error, 'json');
        return new Response($jsonContent);
    }

    



}