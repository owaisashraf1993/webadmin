<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Social;
use MediaBundle\Entity\Media;
use AppBundle\Form\SocialTeamType;
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

class SocialController extends Controller
{

    public function api_allAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $socials=$em->getRepository("AppBundle:Social")->findBy(array("player"=>null),array("position"=>"asc"));
        $list=array();
        $s["id"]=0;
        $s["social"]="All socials";
        $s["image"]=$imagineCacheManager->getBrowserPath("/img/global.png", 'social_thumb_api');
        $list[]=$s;
        foreach ($socials as $key => $social) {
            $s["id"]=$social->getId();
            $s["social"]=$social->getSocial();
            $s["image"]=$imagineCacheManager->getBrowserPath( $social->getMedia()->getLink(), 'social_thumb_api');
            $list[]=$s;
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
        $social= new Social();
        $form = $this->createForm(SocialTeamType::class,$social);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if( $social->getFile()!=null ){
                $media= new Media();
                $media->setFile($social->getFile());
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $social->setMedia($media);
                $max=0;
                $socials=$em->getRepository("AppBundle:Social")->findBy(array("player"=>null),array("position"=>"asc"));
                foreach ($socials as $key => $value) {
                    if ($value->getPosition()>$max) {
                        $max=$value->getPosition();
                    }
                }
                $social->setPosition($max+1);
                $em->persist($social);
                $em->flush();
                $this->addFlash('success', 'Operation has been done successfully');
                return $this->redirect($this->generateUrl('app_team_index'));
            }else{
                $error = new FormError("Required image file");
                $form->get('file')->addError($error);
            }
        }
        return $this->render("AppBundle:Social:add.html.twig",array("form"=>$form->createView()));
    }

    public function upAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $social=$em->getRepository("AppBundle:Social")->findOneBy(array("id"=>$id,"player"=>null));
        if ($social==null) {
            throw new NotFoundHttpException("Page not found");
        }
        if ($social->getPosition()>1) {
            $p=$social->getPosition();
            $socials=$em->getRepository("AppBundle:Social")->findBy(array("player"=>null),array("position"=>"asc"));
            foreach ($socials as $key => $value) {
                if ($value->getPosition()==$p-1) {
                    $value->setPosition($p);  
                }
            }
            $social->setPosition($social->getPosition()-1);
            $em->flush(); 
        }
        return $this->redirect($this->generateUrl('app_team_index'));
    }
    public function downAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $social=$em->getRepository("AppBundle:Social")->findOneBy(array("id"=>$id,"player"=>null));
        if ($social==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $max=0;
        $socials=$em->getRepository("AppBundle:Social")->findBy(array("player"=>null),array("position"=>"asc"));
        foreach ($socials  as $key => $value) {
            $max=$value->getPosition();  
        }
        if ($social->getPosition()<$max) {
            $p=$social->getPosition();
            foreach ($socials as $key => $value) {
                if ($value->getPosition()==$p+1) {
                    $value->setPosition($p);  
                }
            }
            $social->setPosition($social->getPosition()+1);
            $em->flush();  
        }
        return $this->redirect($this->generateUrl('app_team_index'));
    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $social=$em->getRepository("AppBundle:Social")->findOneBy(array("id"=>$id,"player"=>null));
        if($social==null){
            throw new NotFoundHttpException("Page not found");
        }

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
                $media_old = $social->getMedia();
                $em->remove($social);
                $em->flush();
                if( $media_old!=null ){
                    $media_old->delete($this->container->getParameter('files_directory'));
                    $em->remove($media_old);
                    $em->flush();
                }
                $socials=$em->getRepository("AppBundle:Social")->findBy(array("player"=>null),array("position"=>"asc"));

                $p=1;
                foreach ($socials as $key => $value) {
                    $value->setPosition($p); 
                    $p++; 
                }
                $em->flush();

                $this->addFlash('success', 'Operation has been done successfully');

            return $this->redirect($this->generateUrl('app_team_index'));
        }
        return $this->render('AppBundle:Social:delete.html.twig',array("form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $social=$em->getRepository("AppBundle:Social")->findOneBy(array("id"=>$id,"player"=>null));
        if ($social==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(SocialTeamType::class,$social);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             if( $social->getFile()!=null ){
                $media= new Media();
                $media_old=$social->getMedia();
                $media->setFile($social->getFile());
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $social->setMedia($media);
                $em->flush();
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $em->persist($social);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_team_index'));
        }
        return $this->render("AppBundle:Social:edit.html.twig",array("form"=>$form->createView()));
    }

}
?>