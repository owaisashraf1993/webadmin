<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Team;
use AppBundle\Entity\Position;
use MediaBundle\Entity\Media;
use AppBundle\Form\TeamType;
use AppBundle\Form\PositionType;
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
use Symfony\Component\Form\FormError;
use Knp\Bundle\TimeBundle\DateTimeFormatter;

class TeamController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $teams =   $em->getRepository("AppBundle:Team")->findBy(array(),array("position"=>"asc"));
        $socials =   $em->getRepository("AppBundle:Social")->findBy(array("player"=>null),array("position"=>"asc"));
        return $this->render("AppBundle:Team:index.html.twig",array("socials"=>$socials,"teams"=>$teams));
    }



    public function addAction(Request $request)
    {
        $team= new Team();
        $form = $this->createForm(TeamType::class,$team);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                    if( $team->getFile()!=null and $team->getFileicon()!=null ){
                        $media= new Media();
                        $media->setFile($team->getFile());
                        $media->upload($this->container->getParameter('files_directory'));
                        $em->persist($media);
                        $em->flush();
                        $team->setMedia($media);

                        $icon= new Media();
                        $icon->setFile($team->getFileicon());
                        $icon->upload($this->container->getParameter('files_directory'));
                        $em->persist($icon);
                        $em->flush();
                        $team->setIcon($icon);

                        $max=0;
                        $teams=$em->getRepository('AppBundle:Team')->findAll();
                        foreach ($teams as $key => $value) {
                            if ($value->getPosition()>$max) {
                                $max=$value->getPosition();
                            }
                        }
                        $team->setPosition($max+1);
                        $em->persist($team);
                        $em->flush();
                        $this->addFlash('success', 'Operation has been done successfully');
                        return $this->redirect($this->generateUrl('app_team_index'));
                    }else{
                        $error = new FormError("the icon and image required");
                        $form->get('file')->addError($error);
                    }



       }
        return $this->render("AppBundle:Team:add.html.twig",array("form"=>$form->createView()));
    }

    public function upAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $team=$em->getRepository("AppBundle:Team")->find($id);
        if ($team==null) {
            throw new NotFoundHttpException("Page not found");
        }
        if ($team->getPosition()>1) {
            $p=$team->getPosition();
            $teams=$em->getRepository('AppBundle:Team')->findAll();
            foreach ($teams as $key => $value) {
                if ($value->getPosition()==$p-1) {
                    $value->setPosition($p);  
                }
            }
            $team->setPosition($team->getPosition()-1);
            $em->flush(); 
        }
        return $this->redirect($this->generateUrl('app_team_index'));
    }
    public function downAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $team=$em->getRepository("AppBundle:Team")->find($id);
        if ($team==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $max=0;
        $teams=$em->getRepository('AppBundle:Team')->findBy(array(),array("position"=>"asc"));
        foreach ($teams  as $key => $value) {
            $max=$value->getPosition();  
        }
        if ($team->getPosition()<$max) {
            $p=$team->getPosition();
            foreach ($teams as $key => $value) {
                if ($value->getPosition()==$p+1) {
                    $value->setPosition($p);  
                }
            }
            $team->setPosition($team->getPosition()+1);
            $em->flush();  
        }
        return $this->redirect($this->generateUrl('app_team_index'));
    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $team = $em->getRepository("AppBundle:Team")->find($id);
        if($team==null){
            throw new NotFoundHttpException("Page not found");
        }

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            foreach ($team->getStaffs() as $key => $staff) {
                $media_old = $staff->getMedia();
                $em->remove($staff);
                $em->flush();
                if( $media_old!=null ){
                    $media_old->delete($this->container->getParameter('files_directory'));
                    $em->remove($media_old);
                    $em->flush();
                }
            }
            foreach ($team->getArticles() as $key => $article) {
                $media_old = $article->getMedia();
                $em->remove($article);
                $em->flush();
                if( $media_old!=null ){
                    $media_old->delete($this->container->getParameter('files_directory'));
                    $em->remove($media_old);
                    $em->flush();
                }
            }
            foreach ($team->getTrophies() as $key => $trophy) {
                    $media_old = $trophy->getMedia();
                    $logo_old = $trophy->getIcon();
                    $em->remove($trophy);
                    $em->flush();
                    if( $media_old!=null ){
                        $media_old->delete($this->container->getParameter('files_directory'));
                        $em->remove($media_old);
                        $em->flush();
                    }
                    if( $logo_old!=null ){
                        $logo_old->delete($this->container->getParameter('files_directory'));
                        $em->remove($logo_old);
                        $em->flush();
                    }
            }
            foreach ($team->getPositions() as $key => $position) {
                foreach ($position->getPlayers() as $key => $player) {
                    $media_old = $player->getMedia();
                    $em->remove($player);
                    $em->flush();
                    if( $media_old!=null ){
                        $media_old->delete($this->container->getParameter('files_directory'));
                        $em->remove($media_old);
                        $em->flush();
                    }
                 }
                $em->remove($position);
                $em->flush();
            }
            $media = $team->getMedia();
            $icon = $team->getIcon();

            $em->remove($team);
            $em->flush();


            $p=1;            
            $teams=$em->getRepository('AppBundle:Team')->findBy(array(),array("position"=>"asc"));

            foreach ($teams as $key => $value) {
                $value->setPosition($p); 
                $p++; 
            }
            if ($media!=null) {
                $media->delete($this->container->getParameter('files_directory'));
                $em->remove($media);
                $em->flush();
            }

            if ($icon!=null) {
                $icon->delete($this->container->getParameter('files_directory'));
                $em->remove($icon);
                $em->flush();
            }

            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_team_index'));
        }
        return $this->render('AppBundle:Team:delete.html.twig',array("form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $team=$em->getRepository("AppBundle:Team")->find($id);
        if ($team==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(TeamType::class,$team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             if( $team->getFile()!=null ){
                $media= new Media();
                $media_old=$team->getMedia();
                $media->setFile($team->getFile());
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $team->setMedia($media);
                $em->flush();
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
             if( $team->getFileicon()!=null ){
                $icon= new Media();
                $icon_old=$team->getIcon();
                $icon->setFile($team->getFileicon());
                $icon->upload($this->container->getParameter('files_directory'));
                $em->persist($icon);
                $em->flush();
                $team->setIcon($icon);
                $em->flush();
                $icon_old->delete($this->container->getParameter('files_directory'));
                $em->remove($icon_old);
                $em->flush();
            }
            $em->persist($team);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_team_index'));
        }
        return $this->render("AppBundle:Team:edit.html.twig",array("team"=>$team,"form"=>$form->createView()));
    }

    public function staffsAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $team=$em->getRepository("AppBundle:Team")->findOneBy(array("id"=>$id,"type"=>"staffs"));
        if ($team==null) {
            throw new NotFoundHttpException("Page not found");
        }
        return $this->render("AppBundle:Team:staffs.html.twig",array("team"=>$team));
    }
    public function playersAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $team=$em->getRepository("AppBundle:Team")->findOneBy(array("id"=>$id,"type"=>"players"));
        if ($team==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $position = new Position();
        $position_form = $this->createForm(PositionType::class,$position);
        $position_form->handleRequest($request);
        if ($position_form->isSubmitted() && $position_form->isValid()) {
                $max=0;
                $positions=$em->getRepository('AppBundle:Position')->findBy(array("team"=>$team));
                foreach ($positions as $key => $value) {
                    if ($value->getPosition()>$max) {
                        $max=$value->getPosition();
                    }
                }
                $position->setPosition($max+1);
                $position->setTeam($team);
                $em->persist($position);
                $em->flush();  
                $position = new Position();
                $position_form = $this->createForm(PositionType::class,$position);
        }
        return $this->render("AppBundle:Team:players.html.twig",array("position_form"=>$position_form->createView(),"team"=>$team));
    }
    public function trophiesAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $team=$em->getRepository("AppBundle:Team")->findOneBy(array("id"=>$id,"type"=>"trophies"));
        if ($team==null) {
            throw new NotFoundHttpException("Page not found");
        }
        return $this->render("AppBundle:Team:trophies.html.twig",array("team"=>$team));
    }
    public function articlesAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $team=$em->getRepository("AppBundle:Team")->findOneBy(array("id"=>$id,"type"=>"articles"));
        if ($team==null) {
            throw new NotFoundHttpException("Page not found");
        }
        return $this->render("AppBundle:Team:articles.html.twig",array("team"=>$team));
    }
    public function api_players_by_teamAction(Request $request,$token,$id)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $dateTimeFormatter= new DateTimeFormatter($this->get('translator'));
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $team=$em->getRepository("AppBundle:Team")->findOneBy(array("id"=>$id));
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
        $articles=array();
        foreach ($team->getArticles() as $article) {
            $aricle_item["id"]=$article->getId();
            $aricle_item["title"]=$article->getTitle();
            $articles[]=$aricle_item;
        }
        $list["articles"]=$articles;

        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($list, 'json');
        return new Response($jsonContent);
    }
    public function api_allAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $teams=$em->getRepository("AppBundle:Team")->findBy(array("enabled"=>true),array("position"=>"asc"));
        $list=array();
        foreach ($teams as $key => $team) {
            $s["id"]=$team->getId();
            $s["title"]=$team->getTitle();
            $s["subtitle"]=$team->getSubtitle();
            $s["position"]=$team->getPosition();
            $s["type"]=$team->getType();
            $s["image"]=$imagineCacheManager->getBrowserPath( $team->getMedia()->getLink(), 'team_thumb');
            $s["icon"]=$imagineCacheManager->getBrowserPath( $team->getIcon()->getLink(), 'team_icon');
            $list[]=$s;
        }
        $socials=$em->getRepository("AppBundle:Social")->findBy(array("player"=>null),array("position"=>"asc"));
        $social_list=array();
        foreach ($socials as $key => $social) {
            $s_social["id"]=$social->getId();
            $s_social["social"]=$social->getSocial();
            $s_social["value"]=$social->getValue();
            $s_social["username"]=$social->getUsername();
            $s_social["color"]=$social->getColor();
            $s_social["icon"]=$imagineCacheManager->getBrowserPath( $social->getMedia()->getLink(), 'social_icon');
            $social_list[]=$s_social;
        }
        $data["socials"]=$social_list;
        $data["items"]=$list;
        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($data, 'json');
        return new Response($jsonContent);
    }
}
?>