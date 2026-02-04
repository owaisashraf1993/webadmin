<?php 
$list=array();
foreach ($comments as $key => $comment) {
	$a["id"]=$comment->getId();
  $a["username"]=$comment->getUser()->getName();
	$a["userid"]=$comment->getUser()->getId();
	$a["content"]=$comment->getContent();
	$a["enabled"]=$comment->getEnabled();
          if($comment->getUser()->getMedia() ==  null ){
              $a["userimage"] = "https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg" ;
          }else{
              if ($comment->getUser()->getMedia()->getType()=="link") {
                  $a["userimage"] = $comment->getUser()->getMedia()->getUrl() ;
              }else{
                  $a["userimage"] = $this['imagine']->filter($comment->getUser()->getMedia()->getLink(), 'user_image') ;
              }
          }
	$a["created"]=$view['time']->diff($comment->getCreated());
	$list[]=$a;
}
echo json_encode($list, JSON_UNESCAPED_UNICODE);
?>