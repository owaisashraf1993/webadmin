<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\MatchEntity;
use AppBundle\Entity\Info;
use AppBundle\Entity\Event;

use MediaBundle\Entity\Media;
use AppBundle\Form\MatchType;
use AppBundle\Form\MatchStatisticType;
use AppBundle\Form\MatchMiniType;
use AppBundle\Form\EventType;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\Common\Collections\ArrayCollection;

class MatchController extends Controller
{
    public function addAction(Request $request)
    {
        $match= new MatchEntity();
        $form = $this->createForm(MatchType::class,$match);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($match);
                $em->flush();
                $this->addFlash('success', 'Operation has been done successfully');
                return $this->redirect($this->generateUrl('app_match_index'));
       }
       $imagineCacheManager = $this->get('liip_imagine.cache.manager');
       $clubs=$em->getRepository('AppBundle:Club')->findAll();
       $club_images = "";
       foreach ($clubs as $key => $club) {
          $club_images.=',"'.$club->getId().'":"'. $imagineCacheManager->getBrowserPath( $club->getMedia()->getLink(), 'club_thumb').'"';
       }
       $club_images = "'{".trim($club_images,",")."}'";
       return $this->render("AppBundle:Match:add.html.twig",array("club_images"=>$club_images,"form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $match=$em->getRepository("AppBundle:Match")->find($id);
        if ($match==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(MatchType::class,$match);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($match);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_match_index'));
        }
       $imagineCacheManager = $this->get('liip_imagine.cache.manager');
       $clubs=$em->getRepository('AppBundle:Club')->findAll();
       $club_images = "";
       foreach ($clubs as $key => $club) {
          $club_images.=',"'.$club->getId().'":"'. $imagineCacheManager->getBrowserPath( $club->getMedia()->getLink(), 'club_thumb').'"';
       }
       $club_images = "'{".trim($club_images,",")."}'";
       return $this->render("AppBundle:Match:edit.html.twig",array("club_images"=>$club_images,"form"=>$form->createView()));
    }
    public function sendNotifResult($em,$selected_match){


            $imagineCacheManager = $this->get('liip_imagine.cache.manager');
            $homesubresult = "";
            $awaysubresult = "";
            if($selected_match->getHomesubresult() != null && $selected_match->getHomesubresult() != ""){
                $homesubresult =  "(".$selected_match->getHomesubresult().")";
            }
            if($selected_match->getAwaysubresult() != null && $selected_match->getAwaysubresult() != ""){
                $awaysubresult =  "(".$selected_match->getAwaysubresult().")";
            }
            $icon= $imagineCacheManager->getBrowserPath("img/approved.png", 'user_image');
            $message = array(
                    "type"=> "match",
                    "id"=> $selected_match->getId(),
                    "title"=>$selected_match->getHomeclub()->getAbbreviation() . " [".$selected_match->getHomeresult()."]" .$homesubresult. " vs " .$awaysubresult.  "[".$selected_match->getAwayresult()."] ".$selected_match->getAwayclub()->getAbbreviation() ,
                    "message"=>$selected_match->getHomeclub()->getName() . " [".$selected_match->getHomeresult()."]" .$homesubresult. " vs " .$awaysubresult.  "[".$selected_match->getAwayresult()."] ".$selected_match->getAwayclub()->getName() ,
                    "body"=>$selected_match->getHomeclub()->getName() . " [".$selected_match->getHomeresult()."]" .$homesubresult. " vs " .$awaysubresult.  "[".$selected_match->getAwayresult()."] ".$selected_match->getAwayclub()->getName() ,
                    "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                    "club_home_result"=> $selected_match->getHomeresult(),
                    "club_away_result"=> $selected_match->getAwayresult(),
                    "club_away_sub_result"=> $selected_match->getAwaysubresult(),
                    "club_home_sub_result"=> $selected_match->getHomesubresult(),
                    "icon"=> $icon,
                    'status'=>"done"
                );

             $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
             $key=$setting->getFirebasekey();
             $message_image = $this->send_notificationToken( $message,$key); 
    }
    function send_notificationToken ( $message,$key)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'to'  => '/topics/matches',
            'notification' =>   $message,
            'data' =>   $message,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
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
    public function notificationAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $match=$em->getRepository("AppBundle:Match")->find($id);
        if ($match==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $this->sendNotifResult($em,$match);
        return $this->redirect($request->headers->get('referer'));
    }
    public function notification_eventAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $event=$em->getRepository("AppBundle:Event")->find($id);
        if ($event==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $this->sendNotifEvent($em,$event);
        return $this->redirect($request->headers->get('referer'));
    }
    public function sendNotifEvent($em,$event){


            $imagineCacheManager = $this->get('liip_imagine.cache.manager');

            $icon= $imagineCacheManager->getBrowserPath($event->getAction()->getMedia()->getLink(), 'user_image');
            $tite = "";

            $homesubresult = "";
            $awaysubresult = "";
            if($event->getMatch()->getHomesubresult() != null && $event->getMatch()->getHomesubresult() != ""){
                $homesubresult =  "(".$event->getMatch()->getHomesubresult().")";
            }
            if($event->getMatch()->getAwaysubresult() != null && $event->getMatch()->getAwaysubresult() != ""){
                $awaysubresult =  "(".$event->getMatch()->getAwaysubresult().")";
            }

            if($event->getType() == "home"){
              $title =   $event->getAction()->getTitle(). " (".$event->getTime().") : " . $event->getMatch()->getHomeclub()->getName() ;
            }elseif ($event->getType() == "away"){
              $title =   $event->getAction()->getTitle(). " (".$event->getTime().") : " .$event->getMatch()->getAwayclub()->getName();
            }else{
              $title =  $event->getMatch()->getHomeclub()->getName() . " [".$event->getMatch()->getHomeresult()."]" .$homesubresult. " vs " .$awaysubresult.  "[".$event->getMatch()->getAwayresult()."] ".$event->getMatch()->getAwayclub()->getName();
            }



            $comp = ($event->getSubtitle() != null && $event->getSubtitle() != "")? " - ".$event->getSubtitle():"";
            $subtitle = $event->getTitle() . $comp;
            $message = array(
                    "type"=> "match",
                    "event"=> "yes",
                    "id"=> $event->getMatch()->getId(),
                    "title"=>  $title,
                    "message"=>$subtitle,
                    "body"=>$subtitle,
                    "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                    "icon"=> $icon,
                    "event_id"=>$event->getId(),
                    "event_image"=>$icon,
                    "event_name"=>$event->getAction()->getTitle(),
                    "event_time"=>$event->getTime(),
                    "event_title"=>$event->getTitle(),
                    "event_subtitle"=>$event->getSubtitle(),
                    "event_type"=>$event->getType(),
                    'status'=>"done"
                );  

             $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
             $key=$setting->getFirebasekey();
             $message_image = $this->send_notificationToken( $message,$key); 
    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $match = $em->getRepository("AppBundle:Match")->find($id);
        if($match==null){
            throw new NotFoundHttpException("Page not found");
        }

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($match);
            $em->flush();

            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_match_index'));
        }
        return $this->render('AppBundle:Match:delete.html.twig',array("match"=>$match,"form"=>$form->createView()));
    }
    public function statisticsAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $match=$em->getRepository("AppBundle:Match")->find($id);
        if ($match==null) {
            throw new NotFoundHttpException("Page not found");
        }
        if(sizeof($match->getInfos()) == 0){
            $info1 = new Info();
            $info1->setName('');
            $match->getInfos()->add($info1);
        }

        $originalInfos = new ArrayCollection();
        // Create an ArrayCollection of the current Answer objects in the database
        foreach ($match->getInfos() as $info) {
            $originalInfos->add($info);
            if ($info->getHome()==null) {
               $info->setHome(0);
            }
            if ($info->getAway()==null) {
               $info->setAway(0);
            }
            if ($info->getName()!=null) {
                $originalInfos->add($info);
            }
                    
        }


        $form = $this->createForm(MatchStatisticType::class,$match);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // remove the relationship between the info and the Question
            foreach ($originalInfos as $info) {

                if (false === $match->getInfos()->contains($info)) {
                   // $info->setQuestion(null);
                    $match->removeInfo($info);
                    $em->persist($info);

                    // if you wanted to delete the Answer entirely, you can also do that
                    $em->remove($info);
                }
            }
               foreach ($match->getInfos() as $info) {
                    if ($info->getHome()==null) {
                       $info->setHome(0);
                    }
                    if ($info->getAway()==null) {
                       $info->setAway(0);
                    }
                    if ($info->getName()!=null) {
                        $match->AddInfo($info);
                    }
                }
                $em->persist($match);
                $em->flush();
                $this->addFlash('success', 'Operation has been done successfully');
                #return $this->redirect($this->generateUrl('app_match_index'));
        }
       return $this->render("AppBundle:Match:statistics.html.twig",array("match"=>$match,"form"=>$form->createView()));
    }
    public function eventsAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $match=$em->getRepository("AppBundle:Match")->find($id);
        if ($match==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(MatchMiniType::class,$match);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        $event =new Event();
        $event->setMatch($match);
        $form_event = $this->get('form.factory')->create(EventType::class,$event,array("home"=>$match->getHomeClub()->getName(),"away"=>$match->getAwayClub()->getName(),));

        $form_event->handleRequest($request);
        if ($form_event->isSubmitted() && $form_event->isValid()) {

            $max=0;
            $events=$em->getRepository('AppBundle:Event')->findBy(array("match"=>$match));
            foreach ($events as $key => $value) {
                if ($value->getPosition()>$max) {
                    $max=$value->getPosition();
                }
            }
            $event->setPosition($max+1);
            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }

        return $this->render('AppBundle:Match:events.html.twig',array("match"=>$match,"form_event"=>$form_event->createView(),"form"=>$form->createView()));
    }
    public function upAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $event=$em->getRepository("AppBundle:Event")->find($id);
        if ($event==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $match=$event->getMatch();

        $rout =  'app_match_events';
        if ($event->getPosition()>1) {
                $p=$event->getPosition();
                $events=$em->getRepository('AppBundle:Event')->findBy(array("match"=>$match),array("position"=>"asc"));
                foreach ($events as $key => $value) {
                    if ($value->getPosition()==$p-1) {
                        $value->setPosition($p);  
                    }
                }
                $event->setPosition($event->getPosition()-1);
                $em->flush(); 
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$match->getId())));

    }
    public function downAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $event=$em->getRepository("AppBundle:Event")->find($id);
        if ($event==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $match=$event->getMatch();

        $rout =  'app_match_events';

        $max=0;
        $events=$em->getRepository('AppBundle:Event')->findBy(array("match"=>$match),array("position"=>"asc"));
        foreach ($events  as $key => $value) {
            $max=$value->getPosition();  
        }
        if ($event->getPosition()<$max) {
            $p=$event->getPosition();
            foreach ($events as $key => $value) {
                if ($value->getPosition()==$p+1) {
                    $value->setPosition($p);  
                }
            }
            $event->setPosition($event->getPosition()+1);
            $em->flush();  
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$match->getId())));    

    }
    public function deleteEventAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $event = $em->getRepository("AppBundle:Event")->find($id);
        if($event==null){
            throw new NotFoundHttpException("Page not found");
        }
        
        $match=$event->getMatch();


        $em->remove($event);
        $em->flush();


        $events =  $em->getRepository("AppBundle:Event")->findBy(array("match"=>$match),array("position"=>"asc"));
        $event = 0;
        foreach ($events as $key => $sea) {
            $event ++;
            $sea->setPosition($event);
            $em->flush();
        }
         $this->addFlash('success', 'Operation has been done successfully');
        return $this->redirect($this->generateUrl("app_match_events",array("id"=>$match->getId())));    



    }
    public function indexAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $q = " ";
        if ($request->query->has("q") and $request->query->get("q") != "") {
            $q .= " WHERE  p.title like '%" . $request->query->get("q") . "%'";
        }
        $dql = "SELECT p FROM AppBundle:Match p   " . $q . " ORDER BY p.datetime desc ";
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $matchs = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            16
        );
        $matchs_count = $em->getRepository('AppBundle:Match')->count();
        return $this->render('AppBundle:Match:index.html.twig', array("matchs_count" => $matchs_count, "matchs" => $matchs));
    }
    public function api_by_idAction(Request $request,$token,$id)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $match=$em->getRepository("AppBundle:Match")->findOneBy(array("id"=>$id,"enabled"=>true));
        if($match==null){
            throw new NotFoundHttpException("Page not found");
        }
        $match_object["id"]=$match->getId();
        $match_object["title"]=$match->getTitle();
        $match_object["homeresult"]=$match->getHomeresult();
        $match_object["awayresult"]=$match->getAwayresult();
        $match_object["homesubresult"]=$match->getHomesubresult();
        $match_object["awaysubresult"]=$match->getAwaysubresult();
        $match_object["state"]=$match->getState();
        $match_object["stadium"]=$match->getStadium();
        $match_object["highlights"]=$match->getHighlights();
        $match_object["time"]=$match->getDateTime()->format("H:i");


        $diff = date_diff(new  \DateTime(), \DateTime::createFromFormat('d/m/Y',$match->getDateTime()->format("d/m/Y")));
        if($diff->days == 0){
            $match_object["date"]="Today";
        }elseif($diff->days ==  1){
           if($diff->invert){
                $match_object["date"]="Yesterday";
           }else{
                $match_object["date"]="Tomorrow";
           }
        }else{
            $match_object["date"]=$match->getDateTime()->format(" F d,Y"); 
        }

        $homeclub["id"] =  $match->getHomeClub()->getId();
        $homeclub["name"] =  $match->getHomeClub()->getName();
        $homeclub["image"]=$imagineCacheManager->getBrowserPath($match->getHomeClub()->getMedia()->getLink(), 'club_thumb');

        $match_object["homeclub"] = $homeclub;

        $awayclub["id"] =  $match->getAwayClub()->getId();
        $awayclub["name"] =  $match->getAwayClub()->getName();
        $awayclub["image"] =  $match->getAwayClub()->getMedia()->getLink();
        $awayclub["image"]=$imagineCacheManager->getBrowserPath($match->getAwayClub()->getMedia()->getLink(), 'club_thumb');

        $match_object["awayclub"] = $awayclub;

        $competition_obj["id"] =  $match->getCompetition()->getId();
        $competition_obj["name"] =  $match->getCompetition()->getName();
        $competition_obj["image"] =  $match->getCompetition()->getMedia()->getLink();
        $competition_obj["image"]=$imagineCacheManager->getBrowserPath($match->getCompetition()->getMedia()->getLink(), 'competition_thumb');

        $match_object["competition"] = $competition_obj;


        

        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($match_object, 'json');
        return new Response($jsonContent);
    } 
    public function api_by_competitionAction(Request $request,$page,$token,$id)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $nombre = 15;
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $competition=$em->getRepository("AppBundle:Competition")->findOneBy(array("id"=>$id,"enabled"=>true));
        if ($id != 0) {
            if ($competition==null) {
                throw new NotFoundHttpException("Page not found");
            }

            $repository = $em->getRepository('AppBundle:Match');
            $query = $repository->createQueryBuilder('w')
                ->leftJoin('w.competition', 'c')
                ->where("w.enabled = true","c.id = ".$competition->getId())
                ->addOrderBy('w.datetime', 'desc')
                ->addOrderBy('w.id', 'asc')
                ->setFirstResult($nombre * $page)
                ->setMaxResults($nombre)
                ->getQuery();
        }else{
            
            $repository = $em->getRepository('AppBundle:Match');
            $query = $repository->createQueryBuilder('w')
                ->where("w.enabled = true")
                ->addOrderBy('w.datetime', 'desc')
                ->addOrderBy('w.id', 'asc')
                ->setFirstResult($nombre * $page)
                ->setMaxResults($nombre)
                ->getQuery();
        }
        $matches = $query->getResult();

        $list=array();
        foreach ($matches as $key => $match) {
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

        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($list, 'json');
        return new Response($jsonContent);
    } 
    public function api_statistics_by_idAction(Request $request,$token,$id)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $match=$em->getRepository("AppBundle:Match")->findOneBy(array("id"=>$id,"enabled"=>true));
        if ($match==null ) {
            throw new NotFoundHttpException("Page not found");
        }


        $list=array();
        foreach ($match->getInfos() as $key => $statistic) {
            $s["id"] = $statistic->getId();
            $s["name"] = $statistic->getName();
            $s["home"] = $statistic->getHome();
            $s["away"] = $statistic->getAway();
            $list[]=$s;
        }

        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($list, 'json');
        return new Response($jsonContent);
    } 
    public function api_events_by_idAction(Request $request,$token,$id)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $match=$em->getRepository("AppBundle:Match")->findOneBy(array("id"=>$id,"enabled"=>true));
        if ($match==null ) {
            throw new NotFoundHttpException("Page not found");
        }


        $list=array();
        foreach ($match->getEvents() as $key => $event) {
            $s["id"] = $event->getId();
            $s["type"] = $event->getType();
            $s["time"] = $event->getTime();
            $s["title"] = $event->getTitle();
            $s["subtitle"] = $event->getSubtitle();
            $s["image"] =  $imagineCacheManager->getBrowserPath($event->getAction()->getMedia()->getLink(), 'action_thumb');

            $s["name"] = $event->getAction()->getTitle();
            $list[]=$s;
        }

        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($list, 'json');
        return new Response($jsonContent);
    } 
    public function api_by_clubsAction(Request $request,$token,$home,$away)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $homeclub_obj=$em->getRepository("AppBundle:Club")->findOneBy(array("id"=>$home));
        $awayclub_obj=$em->getRepository("AppBundle:Club")->findOneBy(array("id"=>$away));
        if ($homeclub_obj==null or $awayclub_obj == null) {
            throw new NotFoundHttpException("Page not found");
        }

        $repository = $em->getRepository('AppBundle:Match');

        $query = $repository->createQueryBuilder('m')
            ->leftJoin('m.homeclub', 'hc')
            ->leftJoin('m.awayclub', 'ac')
            ->where("(((hc.id = ".$home . ") and (ac.id = ".$away . ") ) or ((ac.id = ".$home . ") and (hc.id = ".$away . ") )) and m.enabled = true and m.state not like 'progammed' ")
            ->addOrderBy('m.datetime', 'DESC')
            ->getQuery();
        $matches = $query->getResult();

        $list=array();
        foreach ($matches as $key => $match) {
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

        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($list, 'json');
        return new Response($jsonContent);
    } 
}
?>