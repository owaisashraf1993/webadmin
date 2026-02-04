<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Status;
use AppBundle\Form\ImageType;
use AppBundle\Form\QuoteType;
use AppBundle\Form\StatusReviewType;
use AppBundle\Form\QuoteReviewType;
use AppBundle\Form\StatusVideoType;
use AppBundle\Form\VideoUrlType;
use MediaBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class StatusController extends Controller {
    function remove_emoji ($string="") {
    $string = str_replace(" ","736489290",$string);
     
     // PREG_REPLACE REMOVE ALL OTHER CHARACTERS THAT NOT AVAIALABLE IN PREG_REPLACE FIRST
     // PARAMETER YOU CANNOT UNDERSTAND FIRST PARAMETER YOU MUST READ PHP REGULAR EXPRESSION!
     $string = preg_replace('/[^A-Za-z0-9]/','',$string);
     
     //STRIP_TAGS REMOVE HTML TAGS
     $string=strip_tags($string,"");
      //HERE WE REMOVE WHITE SPACES AND RETURN IT
    
     $newString =  trim($string);
     $newString = str_replace("736489290"," ",$newString);

     return $newString;
    }
	public function addVideoAction(Request $request) {
		$video = new Status();
		$video->setType("video");
		$form = $this->createForm(StatusVideoType::class, $video);
		$em = $this->getDoctrine()->getManager();

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			if ($video->getFile() != null and $video->getFilevideo() != null) {
				$media = new Media();
				$media->setFile($video->getFile());
				$media->setEnabled(true);
				$media->upload($this->container->getParameter('files_directory'));

				$video->setMedia($media);

				$video_media = new Media();
				$video_media->setFile($video->getFilevideo());
				$video_media->setEnabled(true);
				$video_media->upload($this->container->getParameter('files_directory'));

				$video->setVideo($video_media);

				$video->setUser($this->getUser());
				$video->setReview(false);
				$video->setDownloads(0);
				$em->persist($media);
				$em->flush();

				$em->persist($video_media);
				$em->flush();

				$em->persist($video);
				$em->flush();
				$this->addFlash('success', 'Operation has been done successfully');
				return $this->redirect($this->generateUrl('app_status_index'));
			} else {
				$photo_error = new FormError("Required image file");
				$video_error = new FormError("Required video file");
				$form->get('file')->addError($photo_error);
				$form->get('filevideo')->addError($video_error);
			}
		}
		return $this->render("AppBundle:Status:video_add.html.twig", array("form" => $form->createView()));
	}

    public function api_by_idAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $status = $em->getRepository("AppBundle:Status")->find($id);
        if ($status == null) {
            throw new NotFoundHttpException("Page not found");
        }
        return $this->render("AppBundle:Status:api_one.html.php", array("status" => $status));
    }

    public function editVideoAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository("AppBundle:Status")->findOneBy(array("id" => $id, "review" => false));
        if ($video == null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(StatusVideoType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($video->getFile() != null) {
                $media = new Media();
                $media_old = $video->getMedia();
                $media->setFile($video->getFile());
                $media->setEnabled(true);
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $video->setMedia($media);
                $em->flush();
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }

            if ($video->getFilevideo() != null) {
                $video_media = new Media();
                $video_media_old = $video->getVideo();
                $video_media->setFile($video->getFilevideo());
                $video_media->setEnabled(true);
                $video_media->upload($this->container->getParameter('files_directory'));
                $em->persist($video_media);
                $em->flush();

                $video->setVideo($video_media);
                $em->flush();

                $video_media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($video_media_old);
                $em->flush();
            }

            $em->persist($video);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_status_index'));
        }
        return $this->render("AppBundle:Status:video_edit.html.twig", array("form" => $form->createView()));
    }


    public function addQuoteAction(Request $request) {
        $status = new Status();
        $status->setType("quote");
        $form = $this->createForm(QuoteType::class, $status);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                $status->setQuote(base64_encode($status->getDescription()));
                $status->setDescription($this->remove_emoji($status->getDescription()));
                $status->setUser($this->getUser());
                $status->setReview(false);
                $status->setDownloads(0);
                $em->persist($status);
                $em->flush();
                $this->addFlash('success', 'Operation has been done successfully');
                return $this->redirect($this->generateUrl('app_status_index'));
        }
        return $this->render("AppBundle:Status:quote_add.html.twig", array("form" => $form->createView()));
    }
    public function editQuoteAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $status=$em->getRepository("AppBundle:Status")->findOneBy(array("id"=>$id,"review"=>false));
        if ($status==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $status->setDescription(base64_decode($status->getQuote()));

        $form = $this->createForm(QuoteType::class,$status);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $status->setQuote(base64_encode($status->getDescription()));
            $status->setDescription($this->remove_emoji($status->getDescription()));
            $em->persist($status);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_status_index'));
        }
        return $this->render("AppBundle:Status:quote_edit.html.twig",array("form"=>$form->createView()));
    }
	public function addImageAction(Request $request) {
		$video = new Status();
		$video->setType("image");
		$form = $this->createForm(ImageType::class, $video);
		$em = $this->getDoctrine()->getManager();

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			if ($video->getFile() != null) {
				$media = new Media();
				$media->setFile($video->getFile());
				$media->setEnabled(true);
				$media->upload($this->container->getParameter('files_directory'));

				$video->setMedia($media);

				$video->setUser($this->getUser());
				$video->setReview(false);
				$video->setDownloads(0);
				$em->persist($media);
				$em->flush();

				$em->persist($video);
				$em->flush();
				$this->addFlash('success', 'Operation has been done successfully');
				return $this->redirect($this->generateUrl('app_status_index'));
			} else {
				$photo_error = new FormError("Required image file");
				$form->get('file')->addError($photo_error);
			}
		}
		return $this->render("AppBundle:Status:image_add.html.twig", array("form" => $form->createView()));
	}
    public function editImageAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository("AppBundle:Status")->findOneBy(array("id" => $id, "review" => false));
        if ($video == null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(ImageType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($video->getFile() != null) {
                $media = new Media();
                $media_old = $video->getMedia();
                $media->setFile($video->getFile());
                $media->setEnabled(true);
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $video->setMedia($media);
                $em->flush();
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $em->persist($video);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_status_index'));
        }
        return $this->render("AppBundle:Status:image_edit.html.twig", array("form" => $form->createView()));
    }



	public function api_add_shareAction(Request $request, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $code = 200;

        $hash = $request->get("id");
        $id = base64_decode($hash);
        $id = $id - 55463938;

        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository("AppBundle:Status")->find($id);
        if ($video == null) {
            $code  == 500;
        }
        $video->setShares($video->getShares() + 1);
        $em->flush();
        $value["code"]=$code;
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($value, 'json');
        return new Response($jsonContent);
	}
    public function api_add_viewAction(Request $request, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $code = 200;

        $hash = $request->get("id");
        $id = base64_decode($hash);
        $id = $id - 55463938;

        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository("AppBundle:Status")->find($id);
        if ($video == null) {
            $code  == 500;
        }
        $video->setViews($video->getViews() + 1);
        $em->flush();
        $value["code"]=$code;
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($value, 'json');
        return new Response($jsonContent);
    }
    public function api_add_downloadAction(Request $request, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $code = 200;

        $hash = $request->get("id");
        $id = base64_decode($hash);
        $id = $id - 55463938;

        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository("AppBundle:Status")->find($id);
        if ($video == null) {
            $code  == 500;
        }
        $video->setDownloads($video->getDownloads() + 1);
        $em->flush();
        $value["code"]=$code;
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($value, 'json');
        return new Response($jsonContent);
    }


	public function api_add_likeAction(Request $request, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
        $code = 200;

        $hash = $request->get("id");
        $id = base64_decode($hash);
        $id = $id - 55463938;

		$em = $this->getDoctrine()->getManager();
		$video = $em->getRepository("AppBundle:Status")->find($id);
		if ($video == null) {
			$code  == 500;
		}
		$video->setLikes($video->getLikes() + 1);
		$em->flush();
        $value["code"]=$code;
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($value, 'json');
		return new Response($jsonContent);
	}

	public function api_allAction(Request $request, $page, $order,  $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Status');

			$query = $repository->createQueryBuilder('w')
				->where("w.enabled = true")
				->addOrderBy('w.' . $order, 'DESC')
				->addOrderBy('w.id', 'asc')
				->setFirstResult($nombre * $page)
				->setMaxResults($nombre)
				->getQuery();
		
		$videos = $query->getResult();
		return $this->render('AppBundle:Status:api_all.html.php', array("videos" => $videos));
	}



	public function api_by_meAction(Request $request, $page, $user, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Status');
		$query = $repository->createQueryBuilder('w')
			->where('w.user = :user',"w.type not like  'fullscreen'")
			->setParameter('user', $user)
			->addOrderBy('w.created', 'DESC')
			->addOrderBy('w.id', 'asc')
			->setFirstResult($nombre * $page)
			->setMaxResults($nombre)
			->getQuery();
		$videos = $query->getResult();
		return $this->render('AppBundle:Status:api_all.html.php', array("videos" => $videos));
	}
	public function api_by_queryAction(Request $request, $order,  $page, $query, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Status');
			$query_dql = $repository->createQueryBuilder('w')
				->where("w.enabled = true", "LOWER(w.description) like LOWER('%" . $query . "%') OR LOWER(w.tags) like LOWER('%" . $query . "%')  OR LOWER(w.description) like LOWER('%" . $query . "%') ","w.type not like  'fullscreen'")
				->addOrderBy('w.' . $order, 'DESC')
				->addOrderBy('w.id', 'asc')
				->setFirstResult($nombre * $page)
				->setMaxResults($nombre)
				->getQuery();
		
		$videos = $query_dql->getResult();

		return $this->render('AppBundle:Status:api_all.html.php', array("videos" => $videos));
	}
	public function api_by_userAction(Request $request, $page, $order,  $user, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Status');
			$query = $repository->createQueryBuilder('w')
				->where('w.user = :user', "w.enabled = true","w.type not like 'fullscreen'")
				->setParameter('user', $user)
				->addOrderBy('w.' . $order, 'DESC')
				->addOrderBy('w.id', 'asc')
				->setFirstResult($nombre * $page)
				->setMaxResults($nombre)
				->getQuery();
	
		$videos = $query->getResult();
		return $this->render('AppBundle:Status:api_all.html.php', array("videos" => $videos));
	}





	public function api_delete_likeAction(Request $request, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
        $code = 200;
        $hash = $request->get("id");
        $id = base64_decode($hash);
        $id = $id - 55463938;

		$em = $this->getDoctrine()->getManager();
		$video = $em->getRepository("AppBundle:Status")->find($id);
		if ($video == null) {
			$code= 500;
		}
		$video->setLikes($video->getLikes() - 1);
		$em->flush();
        $value["code"]=$code;
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($value, 'json');
		return new Response($jsonContent);
	}

    public function api_uploadQuoteAction(Request $request, $token) {

        $userId = $request->get("user");
        $userKey = $request->get("key");
        $quote = $request->get("quote");
        $color = $request->get("color");


        $code = 200;
        $message = "Ok";
        $values = array();

        if($userId){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("UserBundle:User")->find($userId);
            if ($user) {
                if (sha1($user->getPassword()) == $userKey) {
                    $w = new Status();
                    $w->setType("quote");
                    $w->setColor($color);
                    $w->setDownloads(0);
                    if (!$user->getTrusted()) {
                        $w->setEnabled(false);
                        $w->setReview(true); 
                    }else{
                        $w->setEnabled(true);
                        $w->setReview(false);         
                    }
                    $w->setComment(true);
                    $w->setDescription($this->remove_emoji(base64_decode($quote)));
                    $w->setQuote($quote);

                    $w->setUser($user);

                    $em->persist($w);
                    $em->flush();

                    $code = 200;
                    if ($user->getTrusted()) {
                        $this->sendNotif($em,$w);
                    }
                }else{
                    $code = 500; 
                    $message = "Operation has been cancelled !" ;
                }
            }else{
                $code = 500; 
                $message = "Operation has been cancelled !" ;
            }
        }else{
            $code = 500; 
            $message = "Operation has been cancelled !" ;
       }
        $error = array(
            "code" => $code,
            "message" => $message,
            "values" => $values,
        );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($error, 'json');
        return new Response($jsonContent);
    }
    public function sendNotif($em,$selected_status){
            $user= $selected_status->getUser();
             if ($user==null) {
                throw new NotFoundHttpException("Page not found");  
            }
            $tokens=array();

            $tokens[]=$user->getToken();
            $original = "";
            $thumbnail = "";
            $type = "";
            $extension = "";
            $color = "";
            $imagineCacheManager = $this->get('liip_imagine.cache.manager');

            $icon= $imagineCacheManager->getBrowserPath("img/approved.png", 'user_image');
            $message = array(
                    "type"=> "status",
                    "id"=> $selected_status->getId(),
                    "title"=>"👍👍 Status approved ❤️❤️",
                    "message"=>"😀😀 Congratulation you status has been approved ❤️❤️",
                    "body"=>"😀😀 Congratulation you status has been approved ❤️❤️",
                    "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                    "icon"=> $icon,
                    'status'=>"done"
                );

             $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());   
             if($setting->getReviewnotification() == true){         
                 $key=$setting->getFirebasekey();
                 $message_image = $this->send_notificationToken($tokens, $message,$key); 
             }
    }
    function send_notificationToken ($tokens, $message,$key)
    {


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
            'Authorization:key = '.$key,
            'Content-Type: application/json'
            );
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
       return $result;
    }
	public function api_uploadAction(Request $request, $token) {
		$id = str_replace('"', '', $request->get("user"));
		$key = str_replace('"', '', $request->get("key"));

		 
		$description = str_replace('"', '', $request->get("description"));



		$code = 200;
		$message = "Ok";
		$values = array();
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('UserBundle:User')->findOneBy(array("id" => $id));
		if ($user == null) {
			throw new NotFoundHttpException("Page not found");
		}
		if (sha1($user->getPassword()) != $key) {
			throw new NotFoundHttpException("Page not found");
		}
		if ($user) {

			if ($request->files->has('uploaded_file') && $request->files->has('uploaded_file_thum')) {
				// $old_media=$user->getMedia();
				$file = $request->files->get('uploaded_file');
				$file_thum = $request->files->get('uploaded_file_thum');

				$media = new Media();
				$media->setFile($file);
				$media->upload($this->container->getParameter('files_directory'));
				$em->persist($media);
				$em->flush();

				$media_thum = new Media();
				$media_thum->setFile($file_thum);
				$media_thum->upload($this->container->getParameter('files_directory'));
				$em->persist($media_thum);
				$em->flush();

				$w = new Status();
				$w->setType("video");
				$w->setDownloads(0);

                if (!$user->getTrusted()) {
                    $w->setEnabled(false);
                    $w->setReview(true); 
                }else{
                    $w->setEnabled(true);
                    $w->setReview(false); 
                }
				$w->setComment(true);
				$w->setDescription($description);
				$w->setUser($user);
				$w->setVideo($media);
				$w->setMedia($media_thum);



				$em->persist($w);
				$em->flush();

                if ($user->getTrusted()) {
                    $this->sendNotif($em,$w);
                }

			}
		}else{
            throw new NotFoundHttpException("Page not found");

        }
		$error = array(
			"code" => $code,
			"message" => $message,
			"values" => $values,
		);
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($error, 'json');
		return new Response($jsonContent);
	}


	public function api_uploadImageAction(Request $request, $token) {

		$id = str_replace('"', '', $request->get("user"));
		$key = str_replace('"', '', $request->get("key"));
		$description = str_replace('"', '', $request->get("description"));



        sleep(1);
		$code = 200;
		$message = "Ok";
		$values = array();
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('UserBundle:User')->findOneBy(array("id" => $id));
		if ($user == null) {
			throw new NotFoundHttpException("Page not found");
		}
		if (sha1($user->getPassword()) != $key) {
			throw new NotFoundHttpException("Page not found");
		}
		if ($user) {

			if ($request->files->has('uploaded_file')) {
				$file_thum = $request->files->get('uploaded_file');
				$media_thum = new Media();
				$media_thum->setFile($file_thum);
				$media_thum->upload($this->container->getParameter('files_directory'));
				$em->persist($media_thum);
				$em->flush();

				$w = new Status();
				$w->setType("image");
				$w->setDownloads(0);

                if (!$user->getTrusted()) {
                    $w->setEnabled(false);
                    $w->setReview(true); 
                }else{
                    $w->setEnabled(true);
                    $w->setReview(false); 
                }
				$w->setComment(true);
				$w->setDescription($description);
				$w->setUser($user);
				$w->setMedia($media_thum);


				$em->persist($w);
				$em->flush();

                if ($user->getTrusted()) {
                    $this->sendNotif($em,$w);
                }

			}
		}
		$error = array(
			"code" => $code,
			"message" => $message,
			"values" => $values,
		);
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($error, 'json');
		return new Response($jsonContent);
	}


    public function api_deleteAction(Request $request, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $code = 200;
        $message = "Ok";
        $values = array();

        $em = $this->getDoctrine()->getManager();
        $id = $request->get("id");
        $userId = $request->get("user");
        $userKey = $request->get("key");

        if($userId){
            $user = $em->getRepository("UserBundle:User")->find($userId);
            if ($user) {
                if (sha1($user->getPassword()) == $userKey) {
                    $video = $em->getRepository("AppBundle:Status")->findOneBy(array("id"=>$id,"user"=>$user));
                    if ($video != null) {
                        $media_old_video = null;
                        $media_old_thumb = null;
                        if ($video->getVideo() != null) {
                            $media_old_video = $video->getVideo();
                        }
                        if ($video->getMedia() != null) {
                            $media_old_thumb = $video->getMedia();
                        }
                        $em->remove($video);
                        $em->flush();
                        if ($media_old_thumb != null) {
                            $media_old_thumb->delete($this->container->getParameter('files_directory'));
                            $em->remove($media_old_thumb);
                            $em->flush();
                        }
                        if ($media_old_video != null) {
                            $media_old_video->delete($this->container->getParameter('files_directory'));
                            $em->remove($media_old_video);
                            $em->flush();
                        }
                    }else{
                        throw new NotFoundHttpException("Page not found");
                    }
                }else{
                     throw new NotFoundHttpException("Page not found");
                }
            }else{
                throw new NotFoundHttpException("Page not found");

            }
        }else{
             throw new NotFoundHttpException("Page not found");
        }
        $error = array(
            "code" => $code,
            "message" => $message,
            "values" => $values,
        );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($error, 'json');
        return new Response($jsonContent);
    }

	public function deleteAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();

		$video = $em->getRepository("AppBundle:Status")->find($id);
		if ($video == null) {
			throw new NotFoundHttpException("Page not found");
		}

		$form = $this->createFormBuilder(array('id' => $id))
			->add('id', HiddenType::class)
			->add('Yes', SubmitType::class)
			->getForm();
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$media_old_video = null;
			$media_old_thumb = null;
			if ($video->getVideo() != null) {
				$media_old_video = $video->getVideo();
			}
			if ($video->getMedia() != null) {
				$media_old_thumb = $video->getMedia();
			}
			$em->remove($video);
			$em->flush();
			if ($media_old_thumb != null) {
				$media_old_thumb->delete($this->container->getParameter('files_directory'));
				$em->remove($media_old_thumb);
				$em->flush();
			}
			if ($media_old_video != null) {
				$media_old_video->delete($this->container->getParameter('files_directory'));
				$em->remove($media_old_video);
				$em->flush();
			}
			$this->addFlash('success', 'Operation has been done successfully');
			return $this->redirect($this->generateUrl('app_status_index'));
		}
		return $this->render('AppBundle:Status:delete.html.twig', array("form" => $form->createView()));
	}


    public function multi_deleteAction(Request $request) {
          if($request->isXmlHttpRequest()) {
            $list =  explode("_",trim($_POST["ids"],"_"));
            foreach ($list as $key => $id) {
                $em = $this->getDoctrine()->getManager();
                $video = $em->getRepository("AppBundle:Status")->findOneBy(array("id"=>$id));
                if ($video != null) {
                    $media_old_video = null;
                    $media_old_thumb = null;
                    if ($video->getVideo() != null) {
                        $media_old_video = $video->getVideo();
                    }
                    if ($video->getMedia() != null) {
                        $media_old_thumb = $video->getMedia();
                    }
                    $em->remove($video);
                    $em->flush();
                    if ($media_old_thumb != null) {
                        $media_old_thumb->delete($this->container->getParameter('files_directory'));
                        $em->remove($media_old_thumb);
                        $em->flush();
                    }
                    if ($media_old_video != null) {
                        $media_old_video->delete($this->container->getParameter('files_directory'));
                        $em->remove($media_old_video);
                        $em->flush();
                    }
                }
            }
            return  new Response("Operation has been completed successfully");
          }else{
                return  new Response("Operation has been cancelled");
          }
    }

    public function multi_reviewAction(Request $request) {
          if($request->isXmlHttpRequest()) {
            $list =  explode("_",trim($_POST["ids"],"_"));
            foreach ($list as $key => $id) {
                $em = $this->getDoctrine()->getManager();
                $status = $em->getRepository("AppBundle:Status")->findOneBy(array("id"=>$id));
                

                $status->setEnabled(true);
                $status->setReview(false);
                $status->setCreated(new \DateTime());

                $em->flush();
            
                $this->sendNotif($em,$status);

            }
            return  new Response("Operation has been completed successfully");
          }else{
                return  new Response("Operation has been cancelled");
          }
    }
	public function indexAction(Request $request) {

		$em = $this->getDoctrine()->getManager();
		$q = "  ";
		if ($request->query->has("q") and $request->query->get("q") != "") {
			$q .= " AND  i.description like '%" . $request->query->get("q") . "%'";
		}

		$dql = "SELECT i FROM AppBundle:Status i  WHERE i.review = false " . $q . " ORDER BY i.created desc ";
		$query = $em->createQuery($dql);
		$paginator = $this->get('knp_paginator');
		$status_list = $paginator->paginate(
			$query,
			$request->query->getInt('page', 1),
			12
		);
		$status_count = $em->getRepository('AppBundle:Status')->countAll();
		return $this->render('AppBundle:Status:index.html.twig', array("status_list" => $status_list, "status_count" => $status_count));
	}

	public function reviewAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$status = $em->getRepository("AppBundle:Status")->findOneBy(array("id" => $id, "review" => true));
		if ($status == null) {
			throw new NotFoundHttpException("Page not found");
		}
		$form = $this->createForm(StatusReviewType::class, $status);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$status->setReview(false);
			$status->setEnabled(true);
			$status->setCreated(new \DateTime());
			$em->persist($status);
			$em->flush();
			$this->addFlash('success', 'Operation has been done successfully');
            $this->sendNotif($em,$status);
			return $this->redirect($this->generateUrl('app_status_reviews'));
		}
		return $this->render("AppBundle:Status:review.html.twig", array("form" => $form->createView()));
	}
    public function reviewQuoteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $status = $em->getRepository("AppBundle:Status")->findOneBy(array("id" => $id, "review" => true));
        if ($status == null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(QuoteReviewType::class,$status);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $status->setReview(false);
            $status->setEnabled(true);
            $status->setCreated(new \DateTime());
            $em->persist($status);
            $em->flush();
           
            $this->sendNotif($em,$status);

            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_status_reviews'));

        }
        return $this->render("AppBundle:Status:quote_review.html.twig", array("status"=>$status,"form" => $form->createView()));
    }
	public function reviewsAction(Request $request) {

		$em = $this->getDoctrine()->getManager();

        $em = $this->getDoctrine()->getManager();
        $q = "  ";
        if ($request->query->has("q") and $request->query->get("q") != "") {
            $q .= " AND  i.description like '%" . $request->query->get("q") . "%'";
        }

        $dql = "SELECT i FROM AppBundle:Status i  WHERE i.review = true " . $q . " ORDER BY i.created desc ";
		$query = $em->createQuery($dql);
		$paginator = $this->get('knp_paginator');
		$videos = $paginator->paginate(
			$query,
			$request->query->getInt('page', 1),
			12
		);
		$video_list = $em->getRepository('AppBundle:Status')->findBy(array("review" => true));
		$videos_count = sizeof($video_list);
		return $this->render('AppBundle:Status:reviews.html.twig', array("videos" => $videos, "videos_count" => $videos_count));
	}

	public function viewAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$status = $em->getRepository("AppBundle:Status")->find($id);
		if ($status == null) {
			throw new NotFoundHttpException("Page not found");
		}
		return $this->render("AppBundle:Status:view.html.twig", array("status" => $status));
	}
}
?>