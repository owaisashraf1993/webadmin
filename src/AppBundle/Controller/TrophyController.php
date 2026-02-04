<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Trophy as Trophy;
use AppBundle\Form\TrophyType;
use MediaBundle\Entity\Media;
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
use Knp\Bundle\TimeBundle\DateTimeFormatter;

class TrophyController extends Controller
{

     public function api_by_teamAction(Request $request,$token,$id)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $dateTimeFormatter= new DateTimeFormatter($this->get('translator'));
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $team=$em->getRepository("AppBundle:Team")->findOneBy(array("id"=>$id,"enabled"=>true,"type"=>"trophies"));
        if ($team==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $list=array();

        foreach ($team->getTrophies() as $key => $trophy) {
            if($trophy->getEnabled() == true){
                $s["id"]=$trophy->getId();
                $s["title"]=$trophy->getTitle();
                $s["number"]=$trophy->getNumber();
                $s["content"]=$trophy->getContent();
                $s["description"]=$trophy->getDescription();
                $s["date"]=$trophy->getContent();
                $agoTime = $dateTimeFormatter->formatDiff($trophy->getCreated(), new \DateTime()) ;
                $s["created"]=$agoTime;
                $s["image"]=$imagineCacheManager->getBrowserPath( $trophy->getMedia()->getLink(), 'trophy_thumb');
                $s["icon"]=$imagineCacheManager->getBrowserPath( $trophy->getIcon()->getLink(), 'trophy_icon');

                $list[]=$s;
            }
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
        $team = $em->getRepository("AppBundle:Team")->findOneBy(array("id"=>$id,"type"=>"trophies"));
        if($team==null){
            throw new NotFoundHttpException("Page not found");
        }
        $trophy= new Trophy();
        $form = $this->createForm(TrophyType::class,$trophy);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                if( $trophy->getFile()!=null && $trophy->getFileicon()!=null ){
                    $media= new Media();
                    $media->setFile($trophy->getFile());
                    $media->upload($this->container->getParameter('files_directory'));
                    $em->persist($media);
                    $em->flush();

                    $trophy->setMedia($media);

                    $icon= new Media();
                    $icon->setFile($trophy->getFileicon());
                    $icon->upload($this->container->getParameter('files_directory'));
                    $em->persist($icon);
                    $em->flush();
                    $trophy->setIcon($icon);

                    $trophy->setTeam($team);

                    $max=0;
                    $trophies=$em->getRepository('AppBundle:Trophy')->findBy(array("team"=>$team));
                    foreach ($trophies as $key => $value) {
                        if ($value->getPosition()>$max) {
                            $max=$value->getPosition();
                        }
                    }
                    $trophy->setPosition($max+1);

                    $em->persist($trophy);
                    $em->flush();
                    $this->addFlash('success', 'Operation has been done successfully');
                    return $this->redirect($this->generateUrl('app_team_trophies',array("id"=>$team->getId())));
                }else{
                    $error = new FormError("Required image file");
                    $form->get('file')->addError($error);
                }
       }
       return $this->render("AppBundle:Trophy:add.html.twig",array("team"=>$team,"form"=>$form->createView()));
    }
    public function upAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $trophy=$em->getRepository("AppBundle:Trophy")->find($id);
        if ($trophy==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $team=$trophy->getTeam();

        $rout =  'app_team_trophies';
        if ($trophy->getPosition()>1) {
                $p=$trophy->getPosition();
                $trophies=$em->getRepository('AppBundle:Trophy')->findBy(array("team"=>$team),array("position"=>"asc"));
                foreach ($trophies as $key => $value) {
                    if ($value->getPosition()==$p-1) {
                        $value->setPosition($p);  
                    }
                }
                $trophy->setPosition($trophy->getPosition()-1);
                $em->flush(); 
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$team->getId())));

    }
    public function downAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $trophy=$em->getRepository("AppBundle:Trophy")->find($id);
        if ($trophy==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $team=$trophy->getTeam();

        $rout =  'app_team_trophies';

        $max=0;
        $trophies=$em->getRepository('AppBundle:Trophy')->findBy(array("team"=>$team),array("position"=>"asc"));
        foreach ($trophies  as $key => $value) {
            $max=$value->getPosition();  
        }
        if ($trophy->getPosition()<$max) {
            $p=$trophy->getPosition();
            foreach ($trophies as $key => $value) {
                if ($value->getPosition()==$p+1) {
                    $value->setPosition($p);  
                }
            }
            $trophy->setPosition($trophy->getPosition()+1);
            $em->flush();  
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$team->getId())));    

    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $trophy = $em->getRepository("AppBundle:Trophy")->find($id);
        if($trophy==null){
            throw new NotFoundHttpException("Page not found");
        }
        
        $team=$trophy->getTeam();

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
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

            $trophies =  $em->getRepository("AppBundle:Trophy")->findBy(array("team"=>$team),array("position"=>"asc"));
            $p = 0;
            foreach ($trophies as $key => $value) {
                $p ++;
                $value->setPosition($p);
                $em->flush();
            }
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_team_trophies',array("id"=>$team->getId())));
        }
        return $this->render('AppBundle:Trophy:delete.html.twig',array("trophy"=>$trophy,"form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $trophy=$em->getRepository("AppBundle:Trophy")->find($id);
        if ($trophy==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(TrophyType::class,$trophy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if( $trophy->getFile()!=null ){
                $media= new Media();
                $media_old=$trophy->getMedia();
                $media->setFile($trophy->getFile());
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $trophy->setMedia($media);

                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
           if( $trophy->getFileicon()!=null ){
                $icon= new Media();
                $icon_old=$trophy->getIcon();
                $icon->setFile($trophy->getFileicon());
                $icon->upload($this->container->getParameter('files_directory'));
                $em->persist($icon);
                $em->flush();
                $trophy->setIcon($icon);
                $em->flush();
                $icon_old->delete($this->container->getParameter('files_directory'));
                $em->remove($icon_old);
                $em->flush();
            }
            $em->persist($trophy);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_team_trophies',array("id"=>$trophy->getTeam()->getId())));
 
        }
        return $this->render("AppBundle:Trophy:edit.html.twig",array("trophy"=>$trophy,"form"=>$form->createView()));
    }
}
?>