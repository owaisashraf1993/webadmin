<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Player;
use MediaBundle\Entity\Media;
use AppBundle\Form\PlayerType;
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
use AppBundle\Entity\Statistic;
use AppBundle\Entity\Social;
use AppBundle\Entity\Position;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Knp\Bundle\TimeBundle\DateTimeFormatter;

class PlayerController extends Controller
{

     public function api_by_teamAction(Request $request,$token,$id)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $dateTimeFormatter= new DateTimeFormatter($this->get('translator'));
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $team=$em->getRepository("AppBundle:Team")->findOneBy(array("id"=>$id,"enabled"=>true));
        if ($team==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $list=array();
        foreach ($team->getPositions() as $key => $position) {
            $s["id"]=$position->getId();
            $s["title"]=$position->getTitle();
            $players=array();
            foreach ($position->getPlayers() as $key => $player) {
                    $p["id"]=$player->getId();
                    $p["fname"]=$player->getFname();
                    $p["lname"]=$player->getLname();
                    $p["number"]=$player->getNumber();
                    $p["position"]=$player->getPost()->getTitle();
                    $agoTime = $dateTimeFormatter->formatDiff($player->getBorn(), new \DateTime()) ;
                    $agoTime= preg_replace('/\W\w+\s*(\W*)$/', '$1', $agoTime);

                    $p["age"]=$agoTime;
                    $p["height"]=$player->getheight();
                    $p["weight"]=$player->getWeight();   
                    $p["country"]=$player->getCountry()->getTitle();
                    $p["country_image"]=$imagineCacheManager->getBrowserPath( $player->getCountry()->getMedia()->getLink(), 'country_thumb');
                    $p["image"]=$imagineCacheManager->getBrowserPath( $player->getMedia()->getLink(), 'player_thumb');
                    $statistics=array();

                    foreach ($player->getStatistics() as  $statistic) {
                            $st["id"] = $statistic->getId();
                            $st["name"] = $statistic->getStatistic();
                            $st["value"] = $statistic->getValue()."";
                            $statistics[] = $st;
                    }
                    $p["statistics"]=$statistics;
                    $socials=array();

                    foreach ($player->getSocials() as  $social) {
                            $st["id"] = $social->getId();
                            $st["name"] = $social->getSocial();
                            $st["value"] = $social->getValue()."";
                            $socials[] = $st;
                    }
                    $p["socials"]=$socials;

                    $players[]=$p;
            }
            $s["players"]=$players;
            $list[]=$s;
        }

        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($list, 'json');
        return new Response($jsonContent);
    }   
    public function addAction(Request $request,$id)
    {
   
        $em=$this->getDoctrine()->getManager();
        $post = $em->getRepository("AppBundle:Position")->findOneBy(array("id"=>$id));
        if($post==null){
            throw new NotFoundHttpException("Page not found");
        }
        $player= new Player();


        $statistic1 = new Statistic();
        $statistic1->setStatistic('Games');
        $player->getStatistics()->add($statistic1);
        $statistic2 = new Statistic();
        $statistic2->setStatistic('Goals');
        $player->getStatistics()->add($statistic2);

        $statistic3 = new Statistic();
        $statistic3->setStatistic('Wins');
        $player->getStatistics()->add($statistic3);

        $social1 = new Social();
        $social1->setSocial('facebook');
        $player->getSocials()->add($social1);
        $social2 = new Social();
        $social2->setSocial('instagram');
        $player->getSocials()->add($social2);


        $social3 = new Social();
        $social3->setSocial('twitter');
        $player->getSocials()->add($social3);

        $form = $this->createForm(PlayerType::class,$player);
        $form->handleRequest($request);
        foreach ($player->getStatistics() as $statistic) {
            if ($statistic->getValue()==null) {
               $statistic->setValue(0);
            }
         }

        foreach ($player->getSocials() as $social) {
            if ($social->getValue()==null) {
               $social->setValue(0);
            }
         }

        if ($form->isSubmitted() && $form->isValid()) {

                if( $player->getFile()!=null ){
                    foreach ($player->getStatistics() as $statistic) {
                        if ($statistic->getValue()==null) {
                           $statistic->setValue(0);
                        }
                        if ($statistic->getStatistic()!=null) {
                            $player->addStatistic($statistic);
                        }
                    }
                    foreach ($player->getSocials() as $social) {
                        if ($social->getValue()==null) {
                           $social->setValue(0);
                        }
                        if ($social->getSocial()!=null) {
                            $player->addSocial($social);
                        }
                    }
                    $media= new Media();
                    $media->setFile($player->getFile());
                    $media->upload($this->container->getParameter('files_directory'));
                    $em->persist($media);
                    $em->flush();
                    $player->setMedia($media);


                    $max=0;
                    $players=$em->getRepository('AppBundle:Player')->findBy(array("post"=>$post));
                    foreach ($players as $key => $value) {
                        if ($value->getPosition()>$max) {
                            $max=$value->getPosition();
                        }
                    }
                    $player->setPosition($max+1);
                    $player->setPost($post);
                    $em->persist($player);
                    $em->flush();
                    $this->addFlash('success', 'Operation has been done successfully');
                    return $this->redirect($this->generateUrl('app_team_players',array("id"=>$post->getTeam()->getId())));
                }else{
                    $error = new FormError("Required image file");
                    $form->get('file')->addError($error);
                }
       }
       return $this->render("AppBundle:Player:add.html.twig",array("team"=>$post->getTeam(),"form"=>$form->createView()));
    }
    public function transferAction(Request $request,$id)
    {

        $em=$this->getDoctrine()->getManager();
        $player = $em->getRepository("AppBundle:Player")->find($id);
        $teams = $em->getRepository("AppBundle:Team")->findBy(array("type"=>"players"));

        $selected_team = $player->getPost()->getTeam();
        $selected_team_id = 0;
         $selected_team_id =  $player->getPost()->getTeam()->getId();
        if(isset($_GET["selected_team_id"])){
           $selected_team_id  =  $_GET["selected_team_id"];
           $selected_team = $em->getRepository("AppBundle:Team")->findBy(array("id"=>$selected_team_id));
        }
        $positions = $em->getRepository("AppBundle:Position")->findBy(array("team"=>$selected_team));
        if($player==null){
            throw new NotFoundHttpException("Page not found");
        }
        
        $em=$this->getDoctrine()->getManager();
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('mypost', EntityType::class, array('class' => Position::class, 'choices' => $positions))           
            ->add('send', SubmitType::class,array("label"=>"Transfer now"))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->get("mypost")->getData();
            $player->setPost($data);
                        $em->flush();

            return $this->redirect($this->generateUrl('app_team_players',array("id"=>$player->getPost()->getTeam()->getId())));
            $this->addFlash('success', 'Operation has been done successfully ');
        }


       return $this->render("AppBundle:Player:transfer.html.twig",array("selected_team_id"=>$selected_team_id,"teams"=>$teams,"player"=>$player,"form"=>$form->createView()));
    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $player = $em->getRepository("AppBundle:Player")->find($id);
        if($player==null){
            throw new NotFoundHttpException("Page not found");
        }
        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $post = $player->getPost();
            $media_old = $player->getMedia();
            $em->remove($player);
            $em->flush();
            if( $media_old!=null ){
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $players =  $em->getRepository("AppBundle:Player")->findBy(array("post"=>$post),array("position"=>"asc"));
            $pos = 0;
            foreach ($players as $key => $player) {
                $pos ++;
                $player->setPosition($pos);
                $em->flush();
            }

            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_team_players',array("id"=>$post->getTeam()->getId())));
        }
        return $this->render('AppBundle:Player:delete.html.twig',array("player"=>$player,"form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $player=$em->getRepository("AppBundle:Player")->find($id);
        if ($player==null) {
            throw new NotFoundHttpException("Page not found");
        }


        $originalStatistics = new ArrayCollection();
        // Create an ArrayCollection of the current Statistic objects in the database
        foreach ($player->getStatistics() as $statistic) {
            $originalStatistics->add($statistic);
            if ($statistic->getValue()==null) {
               $statistic->setValue(0);
            }
            if ($statistic->getStatistic()!=null) {
                $originalStatistics->add($statistic);
            }
                    
        }

        $form = $this->createForm(PlayerType::class, $player);

        $form->handleRequest($request);
        foreach ($player->getStatistics() as $statistic) {
                if ($statistic->getValue()==null) {
                   $statistic->setValue(0);
                }
                if ($statistic->getStatistic()==null) {
                    $statistic->setStatistic("No Name");
                }
                    
        }


        $originalSocials = new ArrayCollection();
        // Create an ArrayCollection of the current Social objects in the database
        foreach ($player->getSocials() as $social) {
            $originalSocials->add($social);
            if ($social->getValue()==null) {
               $social->setValue(0);
            }
            if ($social->getSocial()!=null) {
                $originalSocials->add($social);
            }
                    
        }

        $form = $this->createForm(PlayerType::class, $player);

        $form->handleRequest($request);
        foreach ($player->getSocials() as $social) {
                if ($social->getValue()==null) {
                   $social->setValue(0);
                }
                if ($social->getSocial()==null) {
                    $social->setSocial("No Name");
                }
                    
        }


        $form = $this->createForm(PlayerType::class,$player);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if( $player->getFile()!=null ){
                $media= new Media();
                $media_old=$player->getMedia();
                $media->setFile($player->getFile());
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $player->setMedia($media);
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }

            // remove the relationship between the statistic and the Player
            foreach ($originalStatistics as $statistic) {

                if (false === $player->getStatistics()->contains($statistic)) {
                   // $statistic->setPlayer(null);
                    $player->removeStatistic($statistic);
                    $em->persist($statistic);

                    // if you wanted to delete the Statistic entirely, you can also do that
                    $em->remove($statistic);
                }
            }
            foreach ($player->getStatistics() as $statistic) {
                if ($statistic->getValue()==null) {
                   $statistic->setValue(0);
                }
                if ($statistic->getStatistic()!=null) {
                    $player->addStatistic($statistic);
                }
            }


            // remove the relationship between the social and the Player
            foreach ($originalSocials as $social) {

                if (false === $player->getSocials()->contains($social)) {
                   // $social->setPlayer(null);
                    $player->removeSocial($social);
                    $em->persist($social);

                    // if you wanted to delete the Social entirely, you can also do that
                    $em->remove($social);
                }
            }
            foreach ($player->getSocials() as $social) {
                if ($social->getValue()==null) {
                   $social->setValue(0);
                }
                if ($social->getSocial()!=null) {
                    $player->addSocial($social);
                }
            }
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_team_players',array("id"=>$player->getPost()->getTeam()->getId())));
 
        }
        return $this->render("AppBundle:Player:edit.html.twig",array("player"=>$player,"form"=>$form->createView()));
    }
    public function upAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $player=$em->getRepository("AppBundle:Player")->find($id);
        if ($player==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $post=$player->getPost();

        $rout =  'app_team_players';
        if ($player->getPosition()>1) {
                $p=$player->getPosition();
                $players=$em->getRepository('AppBundle:Player')->findBy(array("post"=>$post),array("position"=>"asc"));
                foreach ($players as $key => $value) {
                    if ($value->getPosition()==$p-1) {
                        $value->setPosition($p);  
                    }
                }
                $player->setPosition($player->getPosition()-1);
                $em->flush(); 
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$post->getTeam()->getId())));

    }
    public function downAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $player=$em->getRepository("AppBundle:Player")->find($id);
        if ($player==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $post=$player->getPost();

        $rout =  'app_team_players';

        $max=0;
        $players=$em->getRepository('AppBundle:Player')->findBy(array("post"=>$post),array("position"=>"asc"));
        foreach ($players  as $key => $value) {
            $max=$value->getPosition();  
        }
        if ($player->getPosition()<$max) {
            $p=$player->getPosition();
            foreach ($players as $key => $value) {
                if ($value->getPosition()==$p+1) {
                    $value->setPosition($p);  
                }
            }
            $player->setPosition($player->getPosition()+1);
            $em->flush();  
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$post->getTeam()->getId())));    

    }
}
?>