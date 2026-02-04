<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Version;
use AppBundle\Form\VersionType;
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

class VersionController extends Controller
{

    public function addAction(Request $request)
    {
        $version= new Version();
        $form = $this->createForm(VersionType::class,$version);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($version);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_version_index'));
        }
        return $this->render("AppBundle:Version:add.html.twig",array("form"=>$form->createView()));
    }

    public function indexAction()
    {
	    $em=$this->getDoctrine()->getManager();
        $versions=$em->getRepository('AppBundle:Version')->findBy(array(),array("code"=>"asc"));
	    return $this->render('AppBundle:Version:index.html.twig',array("versions"=>$versions));    
	}
  

    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $version = $em->getRepository("AppBundle:Version")->find($id);
        if($version==null){
            throw new NotFoundHttpException("Page not found");
        }

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            
            //if (sizeof($version->getAlbums())==0) {
                $em->remove($version);
                $em->flush();


                $this->addFlash('success', 'Operation has been done successfully');
            //}else{
             //   $this->addFlash('danger', 'Operation has been cancelled ,Your album not empty');   
            //}
            return $this->redirect($this->generateUrl('app_version_index'));
        }
        return $this->render('AppBundle:Version:delete.html.twig',array("form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $version=$em->getRepository("AppBundle:Version")->find($id);
        if ($version==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(VersionType::class,$version);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($version);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_version_index'));
 
        }
        return $this->render("AppBundle:Version:edit.html.twig",array("form"=>$form->createView()));
    }
}
?>