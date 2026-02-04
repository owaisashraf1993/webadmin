<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Staff as Staff;
use AppBundle\Form\StaffType;
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

class StaffController extends Controller
{

     public function api_by_teamAction(Request $request,$token,$id)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $dateTimeFormatter= new DateTimeFormatter($this->get('translator'));
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $team=$em->getRepository("AppBundle:Team")->findOneBy(array("id"=>$id,"type"=>"staffs"));
        if ($team==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $list=array();

        foreach ($team->getStaffs() as $key => $staff) {
            $s["id"]=$staff->getId();
            $s["name"]=$staff->getName();
            $s["bio"]=$staff->getBio();
            $s["status"]=$staff->getStatus();
            $s["image"]=$imagineCacheManager->getBrowserPath( $staff->getMedia()->getLink(), 'staff_thumb');
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
        $team = $em->getRepository("AppBundle:Team")->findOneBy(array("id"=>$id,"type"=>"staffs"));
        if($team==null){
            throw new NotFoundHttpException("Page not found");
        }
        $staff= new Staff();
        $form = $this->createForm(StaffType::class,$staff);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                if( $staff->getFile()!=null ){
                    $media= new Media();
                    $media->setFile($staff->getFile());
                    $media->upload($this->container->getParameter('files_directory'));
                    $em->persist($media);
                    $em->flush();

                    $staff->setTeam($team);
                    $staff->setMedia($media);

                    $max=0;
                    $staffs=$em->getRepository('AppBundle:Staff')->findBy(array("team"=>$team));
                    foreach ($staffs as $key => $value) {
                        if ($value->getPosition()>$max) {
                            $max=$value->getPosition();
                        }
                    }
                    $staff->setPosition($max+1);

                    $em->persist($staff);
                    $em->flush();
                    $this->addFlash('success', 'Operation has been done successfully');
                    return $this->redirect($this->generateUrl('app_team_staffs',array("id"=>$team->getId())));
                }else{
                    $error = new FormError("Required image file");
                    $form->get('file')->addError($error);
                }
       }
       return $this->render("AppBundle:Staff:add.html.twig",array("team"=>$team,"form"=>$form->createView()));
    }
    public function upAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $staff=$em->getRepository("AppBundle:Staff")->find($id);
        if ($staff==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $team=$staff->getTeam();

        $rout =  'app_team_staffs';
        if ($staff->getPosition()>1) {
                $p=$staff->getPosition();
                $staffs=$em->getRepository('AppBundle:Staff')->findBy(array("team"=>$team),array("position"=>"asc"));
                foreach ($staffs as $key => $value) {
                    if ($value->getPosition()==$p-1) {
                        $value->setPosition($p);  
                    }
                }
                $staff->setPosition($staff->getPosition()-1);
                $em->flush(); 
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$team->getId())));

    }
    public function downAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $staff=$em->getRepository("AppBundle:Staff")->find($id);
        if ($staff==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $team=$staff->getTeam();

        $rout =  'app_team_staffs';

        $max=0;
        $staffs=$em->getRepository('AppBundle:Staff')->findBy(array("team"=>$team),array("position"=>"asc"));
        foreach ($staffs  as $key => $value) {
            $max=$value->getPosition();  
        }
        if ($staff->getPosition()<$max) {
            $p=$staff->getPosition();
            foreach ($staffs as $key => $value) {
                if ($value->getPosition()==$p+1) {
                    $value->setPosition($p);  
                }
            }
            $staff->setPosition($staff->getPosition()+1);
            $em->flush();  
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$team->getId())));    

    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $staff = $em->getRepository("AppBundle:Staff")->find($id);
        if($staff==null){
            throw new NotFoundHttpException("Page not found");
        }
        
        $team=$staff->getTeam();

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $media_old = $staff->getMedia();
            $em->remove($staff);
            $em->flush();
            if( $media_old!=null ){
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $staffs =  $em->getRepository("AppBundle:Staff")->findBy(array("team"=>$team),array("position"=>"asc"));
            $p = 0;
            foreach ($staffs as $key => $value) {
                $p ++;
                $value->setPosition($p);
                $em->flush();
            }
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_team_staffs',array("id"=>$team->getId())));
        }
        return $this->render('AppBundle:Staff:delete.html.twig',array("staff"=>$staff,"form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $staff=$em->getRepository("AppBundle:Staff")->find($id);
        if ($staff==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(StaffType::class,$staff);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if( $staff->getFile()!=null ){
                $media= new Media();
                $media_old=$staff->getMedia();
                $media->setFile($staff->getFile());
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $staff->setMedia($media);

                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $em->persist($staff);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_team_staffs',array("id"=>$staff->getTeam()->getId())));
 
        }
        return $this->render("AppBundle:Staff:edit.html.twig",array("staff"=>$staff,"form"=>$form->createView()));
    }
}
?>