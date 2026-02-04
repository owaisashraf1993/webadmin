<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Position;
use MediaBundle\Entity\Media;
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
class PositionController extends Controller
{

    public function upAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $position=$em->getRepository("AppBundle:Position")->find($id);
        if ($position==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $team=$position->getTeam();

        $rout =  'app_team_players';
        if ($position->getPosition()>1) {
                $p=$position->getPosition();
                $positions=$em->getRepository('AppBundle:Position')->findBy(array("team"=>$team),array("position"=>"asc"));
                foreach ($positions as $key => $value) {
                    if ($value->getPosition()==$p-1) {
                        $value->setPosition($p);  
                    }
                }
                $position->setPosition($position->getPosition()-1);
                $em->flush(); 
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$team->getId())));

    }
    public function downAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $position=$em->getRepository("AppBundle:Position")->find($id);
        if ($position==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $team=$position->getTeam();

        $rout =  'app_team_players';

        $max=0;
        $positions=$em->getRepository('AppBundle:Position')->findBy(array("team"=>$team),array("position"=>"asc"));
        foreach ($positions  as $key => $value) {
            $max=$value->getPosition();  
        }
        if ($position->getPosition()<$max) {
            $p=$position->getPosition();
            foreach ($positions as $key => $value) {
                if ($value->getPosition()==$p+1) {
                    $value->setPosition($p);  
                }
            }
            $position->setPosition($position->getPosition()+1);
            $em->flush();  
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$team->getId())));    

    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $position = $em->getRepository("AppBundle:Position")->find($id);
        if($position==null){
            throw new NotFoundHttpException("Page not found");
        }
        
        $team=$position->getTeam();

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

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


            $positions =  $em->getRepository("AppBundle:Position")->findBy(array("team"=>$team),array("position"=>"asc"));
            $position = 0;
            foreach ($positions as $key => $sea) {
                $position ++;
                $sea->setPosition($position);
                $em->flush();
            }


            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_team_players',array("id"=>$team->getId())));
        }
        return $this->render('AppBundle:Position:delete.html.twig',array("position"=>$position,"form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $position=$em->getRepository("AppBundle:Position")->find($id);
        if ($position==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(PositionType::class,$position);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($position);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_position_index'));
 
        }
        return $this->render("AppBundle:Position:edit.html.twig",array("position"=>$position,"form"=>$form->createView()));
    }
}
?>