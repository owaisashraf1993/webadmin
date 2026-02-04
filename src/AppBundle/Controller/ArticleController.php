<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Article as Article;
use AppBundle\Form\ArticleType;
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

class ArticleController extends Controller
{

     public function api_by_teamAction(Request $request,$token,$id)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $dateTimeFormatter= new DateTimeFormatter($this->get('translator'));
        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $team=$em->getRepository("AppBundle:Team")->findOneBy(array("id"=>$id,"type"=>"articles"));
        if ($team==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $articles=$em->getRepository("AppBundle:Article")->findBy(array("team"=>$team,"enabled"=>true));

        $list=array();
        foreach ($articles as $key => $article) {

            $s["id"]=$article->getId();
            $s["title"]=$article->getTitle();
            $s["content"]=$article->getContent();
            $s["date"]=$article->getContent();
            $agoTime = $dateTimeFormatter->formatDiff($article->getCreated(), new \DateTime()) ;
            $s["created"]=$agoTime;
            $s["image"]=$imagineCacheManager->getBrowserPath( $article->getMedia()->getLink(), 'post_thumb');

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
        $team = $em->getRepository("AppBundle:Team")->findOneBy(array("id"=>$id,"type"=>"articles"));
        if($team==null){
            throw new NotFoundHttpException("Page not found");
        }
        $article= new Article();
        $form = $this->createForm(ArticleType::class,$article);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                if( $article->getFile()!=null ){
                    $media= new Media();
                    $media->setFile($article->getFile());
                    $media->upload($this->container->getParameter('files_directory'));
                    $em->persist($media);
                    $em->flush();

                    $article->setTeam($team);
                    $article->setMedia($media);

                    $max=0;
                    $articles=$em->getRepository('AppBundle:Article')->findBy(array("team"=>$team));
                    foreach ($articles as $key => $value) {
                        if ($value->getPosition()>$max) {
                            $max=$value->getPosition();
                        }
                    }
                    $article->setPosition($max+1);

                    $em->persist($article);
                    $em->flush();
                    $this->addFlash('success', 'Operation has been done successfully');
                    return $this->redirect($this->generateUrl('app_team_articles',array("id"=>$team->getId())));
                }else{
                    $error = new FormError("Required image file");
                    $form->get('file')->addError($error);
                }
       }
       return $this->render("AppBundle:Article:add.html.twig",array("team"=>$team,"form"=>$form->createView()));
    }
    public function upAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $article=$em->getRepository("AppBundle:Article")->find($id);
        if ($article==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $team=$article->getTeam();

        $rout =  'app_team_articles';
        if ($article->getPosition()>1) {
                $p=$article->getPosition();
                $articles=$em->getRepository('AppBundle:Article')->findBy(array("team"=>$team),array("position"=>"asc"));
                foreach ($articles as $key => $value) {
                    if ($value->getPosition()==$p-1) {
                        $value->setPosition($p);  
                    }
                }
                $article->setPosition($article->getPosition()-1);
                $em->flush(); 
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$team->getId())));

    }
    public function downAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $article=$em->getRepository("AppBundle:Article")->find($id);
        if ($article==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $team=$article->getTeam();

        $rout =  'app_team_articles';

        $max=0;
        $articles=$em->getRepository('AppBundle:Article')->findBy(array("team"=>$team),array("position"=>"asc"));
        foreach ($articles  as $key => $value) {
            $max=$value->getPosition();  
        }
        if ($article->getPosition()<$max) {
            $p=$article->getPosition();
            foreach ($articles as $key => $value) {
                if ($value->getPosition()==$p+1) {
                    $value->setPosition($p);  
                }
            }
            $article->setPosition($article->getPosition()+1);
            $em->flush();  
        }
        return $this->redirect($this->generateUrl($rout,array("id"=>$team->getId())));    

    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $article = $em->getRepository("AppBundle:Article")->find($id);
        if($article==null){
            throw new NotFoundHttpException("Page not found");
        }
        
        $team=$article->getTeam();

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $media_old = $article->getMedia();
            $em->remove($article);
            $em->flush();
            if( $media_old!=null ){
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $articles =  $em->getRepository("AppBundle:Article")->findBy(array("team"=>$team),array("position"=>"asc"));
            $p = 0;
            foreach ($articles as $key => $value) {
                $p ++;
                $value->setPosition($p);
                $em->flush();
            }
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_team_articles',array("id"=>$team->getId())));
        }
        return $this->render('AppBundle:Article:delete.html.twig',array("article"=>$article,"form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $article=$em->getRepository("AppBundle:Article")->find($id);
        if ($article==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if( $article->getFile()!=null ){
                $media= new Media();
                $media_old=$article->getMedia();
                $media->setFile($article->getFile());
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $article->setMedia($media);

                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_team_articles',array("id"=>$article->getTeam()->getId())));
 
        }
        return $this->render("AppBundle:Article:edit.html.twig",array("article"=>$article,"form"=>$form->createView()));
    }
}
?>