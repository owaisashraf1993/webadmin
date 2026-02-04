<?php 

$a = array();



	$a["id"]=$post->getId();
	$a["title"]=$post->getTitle();
	$a["type"]=$post->getType();
	$a["content"]=$post->getContent();
	$a["comment"]=$post->getComment();
	$a["comments"]=sizeof($post->getComments());
	$a["views"]=$post->getViews();
	$a["image"]= $this['imagine']->filter($view['assets']->getUrl($post->getMedia()->getLink()), 'post_thumb');
	if ($post->getType()=="video") {
		$a["video"]= str_replace("/media/cache/resolve/post_thumb/", "/",$this['imagine']->filter($view['assets']->getUrl($post->getLocalvideo()->getLink()), 'post_thumb'));
	}
	if ($post->getType()=="youtube") {
		$a["video"]= $post->getYoutube();
	}
	$a["created"]=$view['time']->diff($post->getCreated());
	$a["date"]="Posted at ".$post->getCreated()->format("h:i A, F d,Y");
	$a["tags"]=$post->getTags();
	$a["shares"]=$post->getShares();

echo json_encode($a, JSON_UNESCAPED_UNICODE);?>