<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Post;
use AppBundle\Entity\Rate;
use AppBundle\Entity\Tag;
use AppBundle\Form\PostReviewType;
use AppBundle\Form\LocalVideoType;
use AppBundle\Form\VideoType;
use AppBundle\Form\PostType;
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
class PostController extends Controller {
	public function addAction(Request $request) {
		$post = new Post();
		$post->setType("post");
		$form = $this->createForm(PostType::class, $post);
		$em = $this->getDoctrine()->getManager();

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			if ($post->getFile() != null) {
				$media = new Media();
				$media->setFile($post->getFile());
				$media->setEnabled(true);
				$media->upload($this->container->getParameter('files_directory'));

				$post->setMedia($media);

				$em->persist($media);
				$em->flush();

				$em->persist($post);
				$em->flush();
				$this->addFlash('success', 'Operation has been done successfully');
				return $this->redirect($this->generateUrl('app_post_index'));
			} else {
				$photo_error = new FormError("Required image file");
				$form->get('file')->addError($photo_error);
			}
		}
		return $this->render("AppBundle:Post:add.html.twig", array(
			"form" => $form->createView()
		));
	}


	public function api_add_shareAction(Request $request, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}

	        $hash = $request->get("id");
	        $id = base64_decode($hash);
	        $id = $id - 55463938;
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository("AppBundle:Post")->findOneBy(array("id" => $id, "enabled" => true));
		if ($post == null) {
			throw new NotFoundHttpException("Page not found");
		}
		$post->setShares($post->getShares() + 1);
		$em->flush();
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($post->getShares(), 'json');
		return new Response($jsonContent);
	}

	public function api_add_viewAction(Request $request, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
	        $hash = $request->get("id");
	        $id = base64_decode($hash);
	        $id = $id - 55463938;
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository("AppBundle:Post")->findOneBy(array("id" => $id, "enabled" => true));
		if ($post == null) {
			throw new NotFoundHttpException("Page not found");
		}
		$post->setViews($post->getViews() + 1);
		$em->flush();
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($post->getViews(), 'json');
		return new Response($jsonContent);
	}

	public function api_allAction(Request $request, $page, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Post');

			$query = $repository->createQueryBuilder('w')
				->where("w.enabled = true")
				->addOrderBy('w.created', 'DESC')
				->addOrderBy('w.id', 'asc')
				->setFirstResult($nombre * $page)
				->setMaxResults($nombre)
				->getQuery();

		$posts = $query->getResult();
		return $this->render('AppBundle:Post:api_all.html.php', array("posts" => $posts));
	}



	public function api_by_queryAction(Request $request, $order, $page, $query, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Post');
		$query_dql = $repository->createQueryBuilder('w')
			->where("w.enabled = true", "LOWER(w.title) like LOWER('%" . $query . "%') OR LOWER(w.tags) like LOWER('%" . $query . "%') ")
			->addOrderBy('w.' . $order, 'DESC')
			->addOrderBy('w.id', 'asc')
			->setFirstResult($nombre * $page)
			->setMaxResults($nombre)
			->getQuery();

		$posts = $query_dql->getResult();

		return $this->render('AppBundle:Post:api_all.html.php', array("posts" => $posts));
	}



	public function api_by_idAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository("AppBundle:Post")->find($id);
		if ($post == null) {
			throw new NotFoundHttpException("Page not found");
		}
		return $this->render("AppBundle:Post:api_one.html.php", array("post" => $post));
	}


	public function deleteAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();

		$post = $em->getRepository("AppBundle:Post")->find($id);
		if ($post == null) {
			throw new NotFoundHttpException("Page not found");
		}

		$form = $this->createFormBuilder(array('id' => $id))
			->add('id', HiddenType::class)
			->add('Yes', SubmitType::class)
			->getForm();
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {


			$media_old = null;
			if ($post->getMedia() != null) {
				$media_old = $post->getMedia();
			}
			$video_old = null;
			if ($post->getLocalvideo() != null) {
				$video_old = $post->getLocalvideo();
			}
			$em->remove($post);
			$em->flush();
			if ($media_old != null) {
				$media_old->delete($this->container->getParameter('files_directory'));
				$em->remove($media_old);
				$em->flush();
			}
			if ($video_old != null) {
				$video_old->delete($this->container->getParameter('files_directory'));
				$em->remove($video_old);
				$em->flush();
			}
			$this->addFlash('success', 'Operation has been done successfully');
			return $this->redirect($this->generateUrl('app_post_index'));
		}
		return $this->render('AppBundle:Post:delete.html.twig', array("form" => $form->createView()));
	}
	public function viewAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository("AppBundle:Post")->find($id);
		if ($post == null) {
			throw new NotFoundHttpException("Page not found");
		}
		return $this->render("AppBundle:Post:view.html.twig", array("post" => $post));
	}


	public function editAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository("AppBundle:Post")->findOneBy(array("id" => $id));
		if ($post == null) {
			throw new NotFoundHttpException("Page not found");
		}


		$form = $this->createForm(PostType::class, $post);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			
			$em->persist($post);
			$em->flush();

			
			if ($post->getFile() != null) {
				$media = new Media();
				$media_old = $post->getMedia();
				$media->setFile($post->getFile());
				$media->setEnabled(true);
				$media->upload($this->container->getParameter('files_directory'));
				$em->persist($media);
				$em->flush();
				$post->setMedia($media);
				$em->flush();
				$media_old->delete($this->container->getParameter('files_directory'));
				$em->remove($media_old);
				$em->flush();
			}
			$em->persist($post);
			$em->flush();
			$this->addFlash('success', 'Operation has been done successfully');
			return $this->redirect($this->generateUrl('app_post_index'));
		}

		return $this->render("AppBundle:Post:edit.html.twig", array("post" => $post,"form" => $form->createView()));
	}
	    public function api_addAction(Request $request,$question,$choices,$token)
	    {
	        $code="200";
	        $message="";
	        $errors=array();
	        $em=$this->getDoctrine()->getManager();
	        $question = $em->getRepository('AppBundle:Question')->find($question);
	        if ($question==null) {
	            $code="404";
	            $message = "Error : This Questionnaire not found";
	        }elseif(!$question->getEnabled()){
	            $code="403";
	            $message = "Error : This Questionnaire is Disabled";
	        }elseif(!$question->getOpen()){
	            $code="402";
	            $message = "Error : This Questionnaire is Closed";
	        }else{
	            if ($question->getMulti()) {
	                $list = explode("_", $choices);
	                foreach ($list as $key => $value) {
	                    $choice = $em->getRepository('AppBundle:Answer')->find($value);
	                    if ($choice) {
	                        if ($choice->getQuestion()->getId()==$question->getId()) {
	                           $choice->setValue($choice->getValue()+1);          
	                           $em->flush();
	                        }
	                    }
	                    
	                }
	            }else{
	                $choice = $em->getRepository('AppBundle:Answer')->find($choices);
	                if ($choice) {
	                    if ($choice->getQuestion()->getId()==$question->getId()) {
	                        $choice->setValue($choice->getValue()+1);          
	                        $em->flush();
	                    }
	                }
	                
	            }
	            $code = "200";
	            $message = "Thank your your vote has been registered";
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
	public function indexAction(Request $request) {

		$em = $this->getDoctrine()->getManager();
		$q = "  ";
		if ($request->query->has("query") and $request->query->get("query") != "") {
			$q .= " WHERE  i.title like '%" . $request->query->get("query") . "%'";
		}

		$dql = "SELECT i FROM AppBundle:Post i  " . $q . " ORDER BY i.created desc ";
		$query = $em->createQuery($dql);
		$paginator = $this->get('knp_paginator');
		$post_list = $paginator->paginate(
			$query,
			$request->query->getInt('page', 1),
			12
		);
		$post_count = $em->getRepository('AppBundle:Post')->countAll();
		return $this->render('AppBundle:Post:index.html.twig', array("post_list" => $post_list, "post_count" => $post_count));
	}

	public function local_addAction(Request $request) {
		$post = new Post();
		$em = $this->getDoctrine()->getManager();

		$form = $this->createForm(LocalVideoType::class, $post);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			if ($post->getFile() != null || $post->getVideofile() != null) {
				// add media
				$file = $post->getFile();
				$media = new Media();
				$media->setFile($file);
				$media->upload($this->container->getParameter('files_directory'));
				$em->persist($media);
				$em->flush();

				$postfile = $post->getVideofile();
				$postmedia = new Media();
				$postmedia->setFile($postfile);
				$postmedia->upload($this->container->getParameter('files_directory'));
				$em->persist($postmedia);
				$em->flush();

				$url = $this->container->getParameter('files_directory') . $media->getUrl();
				$post->setType("video");
				$post->setMedia($media);
				$post->setLocalvideo($postmedia);





				$em->flush();
				$em->persist($post);
				$em->flush();
				$this->addFlash('success', 'Operation has been done successfully');
				return $this->redirect($this->generateUrl('app_post_index'));
			} else {
				$error = new FormError("Required image file");
				$form->get('file')->addError($error);
				$form->get('localvideo')->addError($error);
			}
		} else {

		}
		return $this->render("AppBundle:Post:local_add.html.twig", array("form" => $form->createView()));
	}

	public function local_editAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository("AppBundle:Post")->findOneBy(array("id" => $id, 'type' => "video"));
		

		if ($post == null) {
			throw new NotFoundHttpException("Page not found");
		}


		$form = $this->createForm(LocalVideoType::class, $post);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			
			$em->persist($post);
			$em->flush();




			if ($post->getFile() != null) {
				$media = new Media();
				$media_old = $post->getMedia();
				$media->setFile($post->getFile());
				$media->setEnabled(true);
				$media->upload($this->container->getParameter('files_directory'));
				$em->persist($media);
				$em->flush();
				$post->setMedia($media);
				$em->flush();
				$media_old->delete($this->container->getParameter('files_directory'));
				$em->remove($media_old);
				$em->flush();

				$url = $this->container->getParameter('files_directory') . $media->getUrl();


			}

			if ($post->getVideofile() != null) {
				$mediavideo = new Media();
				$mediavideo_old = $post->getLocalvideo();
				$mediavideo->setFile($post->getVideofile());
				$mediavideo->setEnabled(true);
				$mediavideo->upload($this->container->getParameter('files_directory'));
				$em->persist($mediavideo);
				$em->flush();
				$post->setLocalvideo($mediavideo);
				$em->flush();
				$mediavideo_old->delete($this->container->getParameter('files_directory'));
				$em->remove($mediavideo_old);
				$em->flush();
			}
			$em->persist($post);
			$em->flush();

			$this->addFlash('success', 'Operation has been done successfully');
			return $this->redirect($this->generateUrl('app_post_index'));
		}
		return $this->render("AppBundle:Post:local_edit.html.twig", array("form" => $form->createView()));
	}






	public function shareAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository("AppBundle:Post")->find($id);
		$setting = $em->getRepository("AppBundle:Settings")->findOneBy(array());
		if ($post == null) {
			throw new NotFoundHttpException("Page not found");
		}

		return $this->render("AppBundle:Post:share.html.twig", array("post" => $post, "setting" => $setting));
	}

	
	public function youtube_addAction(Request $request) {
		$post = new Post();
		$em = $this->getDoctrine()->getManager();
		
		$form = $this->createForm(VideoType::class, $post);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			if ($post->getFile() != null) {
				// add media
				$file = $post->getFile();
				$media = new Media();
				$media->setFile($file);
				$media->upload($this->container->getParameter('files_directory'));
				$em->persist($media);
				$em->flush();

				$post->setType("youtube");
				$post->setMedia($media);

				$em->flush();
				$em->persist($post);
				$em->flush();
				$this->addFlash('success', 'Operation has been done successfully');
				return $this->redirect($this->generateUrl('app_post_index'));
			} else {
				$error = new FormError("Required image file");
				$form->get('file')->addError($error);
			}
		}
		return $this->render("AppBundle:Post:youtube_add.html.twig", array("form" => $form->createView()));
	}

	public function youtube_editAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository("AppBundle:Post")->findOneBy(array("type" => "youtube", "id" => $id));
		
		
		if ($post == null) {
			throw new NotFoundHttpException("Page not found");
		}


		$form = $this->createForm(VideoType::class, $post);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			
			$em->persist($post);
			$em->flush();



			if ($post->getFile() != null) {
				$media = new Media();
				$media_old = $post->getMedia();
				$media->setFile($post->getFile());
				$media->setEnabled(true);
				$media->upload($this->container->getParameter('files_directory'));
				$em->persist($media);
				$em->flush();
				$post->setMedia($media);
				$em->flush();
				$media_old->delete($this->container->getParameter('files_directory'));
				$em->remove($media_old);
				$em->flush();

				$url = $this->container->getParameter('files_directory') . $media->getUrl();


			}

			$em->persist($post);
			$em->flush();

			$this->addFlash('success', 'Operation has been done successfully');
			return $this->redirect($this->generateUrl('app_post_index'));
		}
		return $this->render("AppBundle:Post:youtube_edit.html.twig", array("form" => $form->createView()));
	}
}
?>