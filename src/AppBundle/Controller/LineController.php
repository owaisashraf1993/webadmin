<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Line ;
use AppBundle\Form\LineType;
use AppBundle\Form\HeaderType;
use AppBundle\Form\RowType;
use AppBundle\Form\EditRowType;
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

class LineController extends Controller
{

    public function upAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $line=$em->getRepository("AppBundle:Line")->find($id);
        if ($line==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $table=$line->getTable();

        $rout =  'app_table_rows';
        if ($line->getPosition()>1) {
                $p=$line->getPosition();
                $lines=$em->getRepository('AppBundle:Line')->findBy(array("table"=>$table),array("position"=>"asc"));
                foreach ($lines as $key => $value) {
                    if ($value->getPosition()==$p-1) {
                        $value->setPosition($p);  
                    }
                }
                $line->setPosition($line->getPosition()-1);
                $em->flush(); 
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$table->getId())));

    }
    public function downAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $line=$em->getRepository("AppBundle:Line")->find($id);
        if ($line==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $table=$line->getTable();

        $rout =  'app_table_rows';

        $max=0;
        $lines=$em->getRepository('AppBundle:Line')->findBy(array("table"=>$table),array("position"=>"asc"));
        foreach ($lines  as $key => $value) {
            $max=$value->getPosition();  
        }
        if ($line->getPosition()<$max) {
            $p=$line->getPosition();
            foreach ($lines as $key => $value) {
                if ($value->getPosition()==$p+1) {
                    $value->setPosition($p);  
                }
            }
            $line->setPosition($line->getPosition()+1);
            $em->flush();  
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$table->getId())));    

    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $line = $em->getRepository("AppBundle:Line")->find($id);
        if($line==null){
            throw new NotFoundHttpException("Page not found");
        }
        
        $table=$line->getTable();

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($line);
            $em->flush();
            $lines =  $em->getRepository("AppBundle:Line")->findBy(array("table"=>$table),array("position"=>"asc"));
            $p = 0;
            foreach ($lines as $key => $value) {
                $p ++;
                $value->setPosition($p);
                $em->flush();
            }
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_table_rows',array("id"=>$table->getId())));
        }
        return $this->render('AppBundle:Line:delete.html.twig',array("line"=>$line,"form"=>$form->createView()));
    }

}
?>