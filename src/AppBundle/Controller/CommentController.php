<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CommentController extends Controller
{
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $dql        = "SELECT c FROM AppBundle:Comment c ORDER BY c.created desc";
        $query      = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
            10
        );
        $comments=$em->getRepository('AppBundle:Comment')->findAll();
        
        return $this->render('AppBundle:Comment:index.html.twig',
            array(
                'pagination' => $pagination,
                'comments' => $comments,
            )
        );
    }
    public function api_by_statusAction($id,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();

        $status=$em->getRepository('AppBundle:Status')->find($id);
        $comments=array();
        if ($status!=null) {
            $comments=$em->getRepository('AppBundle:Comment')->findBy(array("status"=>$status));
        }

        return $this->render('AppBundle:Comment:api_by.html.php',
            array('comments' => $comments)
        );  
    }
    public function api_by_postAction($id,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();

        $post=$em->getRepository('AppBundle:Post')->find($id);
        if ($post==null) {
            throw new NotFoundHttpException("Page not found");  
        }
        $comments=array();
        if ($post!=null) {
            $comments=$em->getRepository('AppBundle:Comment')->findBy(array("post"=>$post));
        }

        return $this->render('AppBundle:Comment:api_by.html.php',
            array('comments' => $comments)
        );  
    }
    public function api_add_statusAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $user=$request->get("user");
        $comment=$request->get("comment");
        $id=$request->get("id");
        $key=$request->get("key");

        $em=$this->getDoctrine()->getManager();
        $user_obj=$em->getRepository("UserBundle:User")->find($user);
        $status_obj=$em->getRepository('AppBundle:Status')->find($id);

        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        $code = 200;
        $message = "";
        $errors = array();
        if ($user_obj != null and $status_obj != null) {
            if (sha1($user_obj->getPassword()) == $key and $user_obj->isEnabled()) {
                $code=200;
                $message="";
                $errors=array();
                    $comment_obj = new Comment();
                    $comment_obj->setContent($comment);
                    $comment_obj->setEnabled(true);
                    $comment_obj->setUser($user_obj);

                    $em=$this->getDoctrine()->getManager();
                    $comment_obj->setStatus($status_obj);
                    
                    $em->persist($comment_obj);
                    $em->flush();  
                    $errors[]=array("name"=>"id","value"=>$comment_obj->getId());
                    $errors[]=array("name"=>"content","value"=>$comment_obj->getContent());
                    $errors[]=array("name"=>"user","value"=>$comment_obj->getUser()->getName());
                    $errors[]=array("name"=>"enabled","value"=>$comment_obj->getEnabled());
                    $icon = "https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg";
                    if($comment_obj->getUser()->getMedia() ==  null ){
                        $errors[]=array("name"=>"image","value"=>"https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg");   
                    }else{
                        if ($comment_obj->getUser()->getMedia()->getType()=="link") {
                            $errors[]=array("name"=>"image","value"=>$comment_obj->getUser()->getMedia()->getUrl());   
                           $icon = $comment_obj->getUser()->getMedia()->getUrl();

                        }else{
                            $errors[]=array("name"=>"image","value"=>$imagineCacheManager->getBrowserPath($comment_obj->getUser()->getMedia()->getLink(), 'user_image')) ;   
                           $icon = $imagineCacheManager->getBrowserPath($comment_obj->getUser()->getMedia()->getLink(), 'user_image');
                            
                        }
                    }
                    $errors[]=array("name"=>"created","value"=>"now");
                    $message="Your comment has been added";

                    if ($user_obj->getId() != $status_obj->getUser()->getId()) {
                            $tokens[] = $status_obj->getUser()->getToken();
                            $messageNotif = array(
                                "type" => "status",
                                "id" => $status_obj->getId(),
                                "title" => $user_obj->getName() . " Commented at your status ",
                                "message" => "Comment : ".base64_decode($comment) ,
                                "body" => "Comment : ".base64_decode($comment) ,
                                "icon"=> $icon
                            );
                            $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());         
                            if($setting->getCommentnotification() == true){         
                                $key=$setting->getFirebasekey();
                                $message_status = $this->send_notificationToken($tokens, $messageNotif, $key);
                            }
                    }
            }else {
                $code = 500;
                $message = "Sorry, Your comment could not be added at this time";

            }
        } else {
            $code = 500;
            $message = "Sorry, your comment could not be added at this time";
        }

        $error=array(
            "code"=>$code,
            "message"=>$message,
            "values"=>$errors
        );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($error, 'json');
        return new Response($jsonContent);
    }
    public function api_add_postAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $user=$request->get("user");
        $comment=$request->get("comment");
        $id=$request->get("id");
        $key=$request->get("key");

        $em=$this->getDoctrine()->getManager();
        $user_obj=$em->getRepository("UserBundle:User")->find($user);
        $post_obj=$em->getRepository('AppBundle:Post')->find($id);

        $imagineCacheManager = $this->get('liip_imagine.cache.manager');


        $code = 200;
        $message = "";
        $errors = array();
        if ($user_obj != null and $post_obj != null) {
            if (sha1($user_obj->getPassword()) == $key and $user_obj->isEnabled()) {
                $code=200;
                $message="";
                $errors=array();
                    $comment_obj = new Comment();
                    $comment_obj->setContent($comment);
                    $comment_obj->setEnabled(true);
                    $comment_obj->setUser($user_obj);

                    $em=$this->getDoctrine()->getManager();
                    $comment_obj->setPost($post_obj);
                    
                    $em->persist($comment_obj);
                    $em->flush();  
                    $errors[]=array("name"=>"id","value"=>$comment_obj->getId());
                    $errors[]=array("name"=>"content","value"=>$comment_obj->getContent());
                    $errors[]=array("name"=>"user","value"=>$comment_obj->getUser()->getName());
                    $errors[]=array("name"=>"enabled","value"=>$comment_obj->getEnabled());
                    if($comment_obj->getUser()->getMedia() ==  null ){
                        $errors[]=array("name"=>"image","value"=>"https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg");   
                    }else{
                        if ($comment_obj->getUser()->getMedia()->getType()=="link") {
                            $errors[]=array("name"=>"image","value"=>$comment_obj->getUser()->getMedia()->getUrl());   
                        }else{
                            $errors[]=array("name"=>"image","value"=>$imagineCacheManager->getBrowserPath($comment_obj->getUser()->getMedia()->getLink(), 'user_image')) ;   
                        }
                    }
                    $errors[]=array("name"=>"created","value"=>"now");
                    $message="Your comment has been added";
                
            }else {
                $code = 500;
                $message = "Sorry, Your comment could not be added at this time";

            }
        } else {
            $code = 500;
            $message = "Sorry, your comment could not be added at this time";
        }

        $error=array(
            "code"=>$code,
            "message"=>$message,
            "values"=>$errors
        );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($error, 'json');
        return new Response($jsonContent);
    }


    function send_notificationToken($tokens, $message, $key) {
        $notification = $message;
        $notification["image"] = ($message["icon"] == null)? $message["image"] : $message["icon"] ;
        unset($notification["icon"]);



        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'registration_ids'  => $tokens,
            'data'   => $message,
            'notification'   => $notification,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'android' => array(
                "priority"=>"high",
                "channel_id"=> 'Link_channel',
                "tag"=> "sendmeNotification",
                "notification"=>array(
                    "channel_id"=> 'Link_channel',
                )
            ),
            'priority'=>'high'
            );
        $headers = array(
            'Authorization:key = ' . $key,
            'Content-Type: application/json',
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
    public function hideAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();
        $comment = $em->getRepository("AppBundle:Comment")->find($id);
        if($comment==null){
            throw new NotFoundHttpException("Page not found");
        }
        $user=$comment->getUser();
    	if ($comment->getEnabled()==true) {
    		$comment->setEnabled(false);
    	}else{
    		$comment->setEnabled(true);
    	}
        $em->flush();
        $this->addFlash('success', 'Operation has been done successfully');
        return  $this->redirect($request->server->get('HTTP_REFERER'));
    }


    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $comment = $em->getRepository("AppBundle:Comment")->find($id);
        if($comment==null){
            throw new NotFoundHttpException("Page not found");
        }
        $post=$comment->getPost();
        $status=$comment->getStatus();
        $user=$comment->getUser();
        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        if ($request->query->has("user")) {
            $url = $this->generateUrl('user_user_edit',array("id"=>$user->getId()));
        }
        else if ($request->query->has("status")) {
                 if ($status==null) {
                    $url = $this->generateUrl('app_comment_index');
                }else{
                    $url = $this->generateUrl('app_status_view',array("id"=>$status->getId()));
                }
        }
        else if ($request->query->has("post")) {
            if ($post==null) {
                $url = $this->generateUrl('app_comment_index');
            }else{
                    $url = $this->generateUrl('app_post_view',array("id"=>$post->getId()));

            }
        }
        else{
            $url = $this->generateUrl('app_comment_index');
        }
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($comment);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($url);
        }
        return $this->render('AppBundle:Comment:delete.html.twig',array("url"=>$url,"form"=>$form->createView()));
    }
}
