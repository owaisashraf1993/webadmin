<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Competition;
use MediaBundle\Entity\Media;
use AppBundle\Form\CompetitionType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CompetitionController extends Controller
{
    public function api_allAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $competitions=$em->getRepository("AppBundle:Competition")->findBy(array("enabled"=>true),array("position"=>"asc"));
        $list=array();
        foreach ($competitions as $key => $competition) {

            $s =  null;
            $s["id"]=$competition->getId();
            $s["name"]=$competition->getName();
            $s["season"]=$competition->getSeason();
            $s["image"]=$imagineCacheManager->getBrowserPath( $competition->getMedia()->getLink(), 'competition_thumb');
            $list[]=$s;
            
        }
        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($list, 'json');
        return new Response($jsonContent);
    }
    public function api_tablesAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $competitions=$em->getRepository("AppBundle:Competition")->findBy(array("enabled"=>true),array("position"=>"asc"));
        $list=array();
        foreach ($competitions as $key => $competition) {
            if (sizeof($competition->getTables())>0) {
                # code...
            
            $s =  null;
            $s["id"]=$competition->getId();
            $s["name"]=$competition->getName();
            $s["season"]=$competition->getSeason();
            $s["image"]=$imagineCacheManager->getBrowserPath( $competition->getMedia()->getLink(), 'competition_thumb');
            $tables = array();
            foreach ($competition->getTables() as $key => $table) {
                $t = null;
                $t["id"]=$table->getId();
                $t["title"]=$table->getTitle();
                $t["columns"]=$table->getColumns();
                $lines = array();
                $header =  null;
                foreach ($table->getLines() as $key => $line) {
                    if($line->getType() == "row"){
                        $l = null;
                        $l["id"]=$line->getId();
                        $l["prefix"]=$line->getPrefix();
                        $l["label"]=$line->getClub()->getName();
                        $l["image"]=$imagineCacheManager->getBrowserPath($line->getClub()->getMedia()->getLink(), 'club_thumb');
                        $l["row1"]=$line->getRow1();
                        $l["row2"]=$line->getRow2();
                        $l["row3"]=$line->getRow3();
                        $l["row4"]=$line->getRow4();
                        $l["row5"]=$line->getRow5();
                        $l["row6"]=$line->getRow6();
                        $l["row7"]=$line->getRow7();
                        $l["row8"]=$line->getRow8();
                        $l["color"]=$line->getColor();
                        $lines[]=$l;
                    }else{
                        $l = null;
                        $h["id"]=$line->getId();
                        $h["prefix"]=$line->getPrefix();
                        $h["label"]=$line->getLabel();
                        $h["row1"]=$line->getRow1();
                        $h["row2"]=$line->getRow2();
                        $h["row3"]=$line->getRow3();
                        $h["row4"]=$line->getRow4();
                        $h["row5"]=$line->getRow5();
                        $h["row6"]=$line->getRow6();
                        $h["row7"]=$line->getRow7();
                        $h["row8"]=$line->getRow8();
                        $header=  $h; 
                    }
                }
                $t["lines"]  = $lines;
                if($header != null){
                    $t["header"]  = $header;
                }
                $tables[]=$t;
            }
            $s["tables"] = $tables;
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
    public function addAction(Request $request)
    {
        $competition= new Competition();
        $form = $this->createForm(CompetitionType::class,$competition);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if( $competition->getFile()!=null ){
                $media= new Media();
                $media->setFile($competition->getFile());
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $competition->setMedia($media);
                $max=0;
                $competitions=$em->getRepository('AppBundle:Competition')->findAll();
                foreach ($competitions as $key => $value) {
                    if ($value->getPosition()>$max) {
                        $max=$value->getPosition();
                    }
                }
                $competition->setPosition($max+1);
                $em->persist($competition);
                $em->flush();
                $this->addFlash('success', 'Operation has been done successfully');
                return $this->redirect($this->generateUrl('app_competition_index'));
            }else{
                $error = new FormError("Required image file");
                $form->get('file')->addError($error);
            }
        }
        return $this->render("AppBundle:Competition:add.html.twig",array("form"=>$form->createView()));
    }
    public function indexAction()
    {
        $em=$this->getDoctrine()->getManager();
        $competitions=$em->getRepository('AppBundle:Competition')->findBy(array(),array("position"=>"asc"));
        return $this->render('AppBundle:Competition:index.html.twig',array("competitions"=>$competitions));    
    }

    public function upAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $competition=$em->getRepository("AppBundle:Competition")->find($id);
        if ($competition==null) {
            throw new NotFoundHttpException("Page not found");
        }
        if ($competition->getPosition()>1) {
            $p=$competition->getPosition();
            $competitions=$em->getRepository('AppBundle:Competition')->findAll();
            foreach ($competitions as $key => $value) {
                if ($value->getPosition()==$p-1) {
                    $value->setPosition($p);  
                }
            }
            $competition->setPosition($competition->getPosition()-1);
            $em->flush(); 
        }
        return $this->redirect($this->generateUrl('app_competition_index'));
    }
    public function downAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $competition=$em->getRepository("AppBundle:Competition")->find($id);
        if ($competition==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $max=0;
        $competitions=$em->getRepository('AppBundle:Competition')->findBy(array(),array("position"=>"asc"));
        foreach ($competitions  as $key => $value) {
            $max=$value->getPosition();  
        }
        if ($competition->getPosition()<$max) {
            $p=$competition->getPosition();
            foreach ($competitions as $key => $value) {
                if ($value->getPosition()==$p+1) {
                    $value->setPosition($p);  
                }
            }
            $competition->setPosition($competition->getPosition()+1);
            $em->flush();  
        }
        return $this->redirect($this->generateUrl('app_competition_index'));
    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $competition = $em->getRepository("AppBundle:Competition")->find($id);
        if($competition==null){
            throw new NotFoundHttpException("Page not found");
        }

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

                

                $media_old = $competition->getMedia();
                $em->remove($competition);
                $em->flush();
                if( $media_old!=null ){
                    $media_old->delete($this->container->getParameter('files_directory'));
                    $em->remove($media_old);
                    $em->flush();
                }
                $competitions=$em->getRepository('AppBundle:Competition')->findBy(array(),array("position"=>"asc"));
                $p=1;
                foreach ($competitions as $key => $value) {
                    $value->setPosition($p); 
                    $p++; 
                }
                $em->flush();

                $this->addFlash('success', 'Operation has been done successfully');

            return $this->redirect($this->generateUrl('app_competition_index'));
        }
        return $this->render('AppBundle:Competition:delete.html.twig',array("form"=>$form->createView()));
    }
    public function tablesAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $competition=$em->getRepository("AppBundle:Competition")->find($id);
        if ($competition==null) {
            throw new NotFoundHttpException("Page not found");
        }
    
        return $this->render("AppBundle:Competition:tables.html.twig",array("competition"=>$competition));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $competition=$em->getRepository("AppBundle:Competition")->find($id);
        if ($competition==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(CompetitionType::class,$competition);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             if( $competition->getFile()!=null ){
                $media= new Media();
                $media_old=$competition->getMedia();
                $media->setFile($competition->getFile());
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $competition->setMedia($media);
                $em->flush();
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $em->persist($competition);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_competition_index'));
        }
        return $this->render("AppBundle:Competition:edit.html.twig",array("form"=>$form->createView()));
    }

}
?>