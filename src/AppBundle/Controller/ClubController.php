<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Club;
use MediaBundle\Entity\Media;
use AppBundle\Form\ClubType;
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

class ClubController extends Controller
{

    public function indexAction(Request $request)
    {

        $em=$this->getDoctrine()->getManager();
        $clubs=$em->getRepository('AppBundle:Club')->findAll();
        return $this->render('AppBundle:Club:index.html.twig', array( "clubs" => $clubs));
    }
    
    public function addAction(Request $request)
    {
        $club= new Club();
        $form = $this->createForm(ClubType::class,$club);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                if( $club->getFile()!=null ){
                    $media= new Media();
                    $media->setFile($club->getFile());
                    $media->upload($this->container->getParameter('files_directory'));
                    $em->persist($media);
                    $em->flush();
                    $club->setMedia($media);
                    $em->persist($club);
                    $em->flush();
                    $this->addFlash('success', 'Operation has been done successfully');
                    return $this->redirect($this->generateUrl('app_club_index'));
                }else{
                    $error = new FormError("Required image file");
                    $form->get('file')->addError($error);
                }
       }
       return $this->render("AppBundle:Club:add.html.twig",array("form"=>$form->createView()));
    }

    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $club = $em->getRepository("AppBundle:Club")->find($id);
        if($club==null){
            throw new NotFoundHttpException("Page not found");
        }
        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $media_old = $club->getMedia();
            $em->remove($club);
            $em->flush();
            if( $media_old!=null ){
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_club_index'));
        }
        return $this->render('AppBundle:Club:delete.html.twig',array("form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $club=$em->getRepository("AppBundle:Club")->find($id);
        if ($club==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(ClubType::class,$club);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if( $club->getFile()!=null ){
                $media= new Media();
                $media_old=$club->getMedia();
                $media->setFile($club->getFile());
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $club->setMedia($media);
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $em->persist($club);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_club_index'));
 
        }
        return $this->render("AppBundle:Club:edit.html.twig",array("club"=>$club,"form"=>$form->createView()));
    }
}
?>