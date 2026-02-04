<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Action;
use MediaBundle\Entity\Media;
use AppBundle\Form\ActionType;
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

class ActionController extends Controller
{

    
    public function indexAction()
    {
        $em=$this->getDoctrine()->getManager();
        $actions=$em->getRepository('AppBundle:Action')->findBy(array(),array("position"=>"asc"));
        return $this->render('AppBundle:Action:index.html.twig',array("actions"=>$actions));    
    }
    
    public function addAction(Request $request)
    {
        $action= new Action();
        $form = $this->createForm(ActionType::class,$action);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                if( $action->getFile()!=null ){
                    $max=0;
                    $actions=$em->getRepository('AppBundle:Action')->findAll();
                    foreach ($actions as $key => $value) {
                        if ($value->getPosition()>$max) {
                            $max=$value->getPosition();
                        }
                    }
                    $action->setPosition($max+1);
                    $media= new Media();
                    $media->setFile($action->getFile());
                    $media->upload($this->container->getParameter('files_directory'));
                    $em->persist($media);
                    $em->flush();
                    $action->setMedia($media);
                    $em->persist($action);
                    $em->flush();
                    $this->addFlash('success', 'Operation has been done successfully');
                    return $this->redirect($this->generateUrl('app_action_index'));
                }else{
                    $error = new FormError("Required image file");
                    $form->get('file')->addError($error);
                }
       }
       return $this->render("AppBundle:Action:add.html.twig",array("form"=>$form->createView()));
    }

    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $action = $em->getRepository("AppBundle:Action")->find($id);
        if($action==null){
            throw new NotFoundHttpException("Page not found");
        }
        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $media_old = $action->getMedia();
            $em->remove($action);
            $em->flush();
            if( $media_old!=null ){
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $em->flush();
            $actions=$em->getRepository('AppBundle:Action')->findBy(array(),array("position"=>"asc"));

            $p=1;
            foreach ($actions as $key => $value) {
                $value->setPosition($p); 
                $p++; 
            }
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_action_index'));
        }
        return $this->render('AppBundle:Action:delete.html.twig',array("action"=>$action,"form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $action=$em->getRepository("AppBundle:Action")->find($id);
        if ($action==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(ActionType::class,$action);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if( $action->getFile()!=null ){
                $media= new Media();
                $media_old=$action->getMedia();
                $media->setFile($action->getFile());
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $action->setMedia($media);
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $em->persist($action);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_action_index'));
 
        }
        return $this->render("AppBundle:Action:edit.html.twig",array("action"=>$action,"form"=>$form->createView()));
    }
    public function upAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $action=$em->getRepository("AppBundle:Action")->find($id);
        if ($action==null) {
            throw new NotFoundHttpException("Page not found");
        }
        if ($action->getPosition()>1) {
            $p=$action->getPosition();
            $actions=$em->getRepository('AppBundle:Action')->findAll();
            foreach ($actions as $key => $value) {
                if ($value->getPosition()==$p-1) {
                    $value->setPosition($p);  
                }
            }
            $action->setPosition($action->getPosition()-1);
            $em->flush(); 
        }
        return $this->redirect($this->generateUrl('app_action_index'));
    }
    public function downAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $action=$em->getRepository("AppBundle:Action")->find($id);
        if ($action==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $max=0;
        $actions=$em->getRepository('AppBundle:Action')->findBy(array(),array("position"=>"asc"));
        foreach ($actions  as $key => $value) {
            $max=$value->getPosition();  
        }
        if ($action->getPosition()<$max) {
            $p=$action->getPosition();
            foreach ($actions as $key => $value) {
                if ($value->getPosition()==$p+1) {
                    $value->setPosition($p);  
                }
            }
            $action->setPosition($action->getPosition()+1);
            $em->flush();  
        }
        return $this->redirect($this->generateUrl('app_action_index'));
    }
}
?>