<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Table as Table;
use AppBundle\Entity\Line ;
use AppBundle\Form\TableType;
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

class TableController extends Controller
{


    public function addAction(Request $request,$id)
    {

        $em=$this->getDoctrine()->getManager();
        $competition = $em->getRepository("AppBundle:Competition")->findOneBy(array("id"=>$id));
        if($competition==null){
            throw new NotFoundHttpException("Page not found");
        }
        $table= new Table();
        $form = $this->createForm(TableType::class,$table);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

                    $max=0;
                    $tables=$em->getRepository('AppBundle:Table')->findBy(array("competition"=>$competition));
                    foreach ($tables as $key => $value) {
                        if ($value->getPosition()>$max) {
                            $max=$value->getPosition();
                        }
                    }
                    $table->setPosition($max+1);
                    $table->setCompetition($competition);

                    $em->persist($table);
                    $em->flush();

                    $header = new Line();
                    $header->setPrefix("#");
                    $header->setTable($table);
                    $header->setPosition(0);
                    $header->setLabel("Team");
                    $header->setType("header");
                    $em->persist($header);
                    $em->flush();
                    
                    $this->addFlash('success', 'Operation has been done successfully');
                    return $this->redirect($this->generateUrl('app_table_rows',array("id"=>$table->getId())));

       }
       return $this->render("AppBundle:Table:add.html.twig",array("competition"=>$competition,"form"=>$form->createView()));
    }
    public function rowsAction(Request $request,$id)
    {

        $em=$this->getDoctrine()->getManager();
        $table=$em->getRepository("AppBundle:Table")->find($id);

        if ($table==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $header=$em->getRepository('AppBundle:Line')->findOneBy(array("table"=>$table,"type"=>"header"));
        if ($header==null) {
                $header = new Line();
                $header->setPrefix("#");
                $header->setTable($table);
                $header->setPosition(0);
                $header->setLabel("Team");
                $header->setType("header");
                $em->persist($header);
                $em->flush();
        }
        $form = $this->createForm(HeaderType::class,$header);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                    $em->flush();
                    $this->addFlash('success', 'Operation has been done successfully');
       }
        $row = new Line();
        $form_row = $this->createForm(RowType::class,$row);
        $form_row->handleRequest($request);
        if ($form_row->isSubmitted() && $form_row->isValid()) {
                $max=0;
                $lines=$em->getRepository('AppBundle:Line')->findBy(array("table"=>$table));
                foreach ($lines as $key => $value) {
                    if ($value->getPosition()>$max) {
                        $max=$value->getPosition();
                    }
                }
                $row->setPosition($max+1);
                $row->setType("row");
                $row->setTable($table);
                $em->persist($row);
                $em->flush();
                $this->addFlash('success', 'Operation has been done successfully');
                return $this->redirect($this->generateUrl("app_table_rows",array("id"=>$table->getId())));

       }

        $row_id = $request->get("row");
        $edit_row =$em->getRepository('AppBundle:Line')->findOneBy(array("id"=>$row_id));
        $form_edit_row_view = null;
        if($edit_row != null){
            $form_edit_row = $this->createForm(EditRowType::class,$edit_row);
            $form_edit_row->handleRequest($request);
            if ($form_edit_row->isSubmitted() && $form_edit_row->isValid()) {
                    $em->flush();
                    $this->addFlash('success', 'Operation has been done successfully');
                    return $this->redirect($this->generateUrl("app_table_rows",array("id"=>$table->getId())));

           }
            $form_edit_row_view = $form_edit_row->createView();
       }
       return $this->render("AppBundle:Table:rows.html.twig",array("form_edit_row_view"=>$form_edit_row_view,"table"=>$table,"form_row"=>$form_row->createView(),"form"=>$form->createView()));
    }
    public function upAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $table=$em->getRepository("AppBundle:Table")->find($id);
        if ($table==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $competition=$table->getCompetition();

        $rout =  'app_competition_tables';
        if ($table->getPosition()>1) {
                $p=$table->getPosition();
                $tables=$em->getRepository('AppBundle:Table')->findBy(array("competition"=>$competition),array("position"=>"asc"));
                foreach ($tables as $key => $value) {
                    if ($value->getPosition()==$p-1) {
                        $value->setPosition($p);  
                    }
                }
                $table->setPosition($table->getPosition()-1);
                $em->flush(); 
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$competition->getId())));

    }
    public function downAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $table=$em->getRepository("AppBundle:Table")->find($id);
        if ($table==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $competition=$table->getCompetition();

        $rout =  'app_competition_tables';

        $max=0;
        $tables=$em->getRepository('AppBundle:Table')->findBy(array("competition"=>$competition),array("position"=>"asc"));
        foreach ($tables  as $key => $value) {
            $max=$value->getPosition();  
        }
        if ($table->getPosition()<$max) {
            $p=$table->getPosition();
            foreach ($tables as $key => $value) {
                if ($value->getPosition()==$p+1) {
                    $value->setPosition($p);  
                }
            }
            $table->setPosition($table->getPosition()+1);
            $em->flush();  
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$competition->getId())));    

    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $table = $em->getRepository("AppBundle:Table")->find($id);
        if($table==null){
            throw new NotFoundHttpException("Page not found");
        }
        
        $competition=$table->getCompetition();

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($table);
            $em->flush();
            $tables =  $em->getRepository("AppBundle:Table")->findBy(array("competition"=>$competition),array("position"=>"asc"));
            $p = 0;
            foreach ($tables as $key => $value) {
                $p ++;
                $value->setPosition($p);
                $em->flush();
            }
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_competition_tables',array("id"=>$competition->getId())));
        }
        return $this->render('AppBundle:Table:delete.html.twig',array("table"=>$table,"form"=>$form->createView()));
    }
    public function api_by_competitionAction(Request $request,$token,$id)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $competition=$em->getRepository("AppBundle:Competition")->findOneBy(array("id"=>$id));
            $tables = array();
            if (sizeof($competition->getTables())>0) {
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
        }
        
        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($tables, 'json');
        return new Response($jsonContent);
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $table=$em->getRepository("AppBundle:Table")->find($id);
        if ($table==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(TableType::class,$table);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($table);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_competition_tables',array("id"=>$table->getCompetition()->getId())));
 
        }
        return $this->render("AppBundle:Table:edit.html.twig",array("table"=>$table,"form"=>$form->createView()));
    }
}
?>