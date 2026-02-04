<?php 


	$a = array();

	$a["id"]=$status->getId();
	$a["kind"]=$status->getType();
	$a["description"]=$status->getDescription();
	$a["review"]=$status->getReview();
	$a["comment"]=$status->getComment();
	$a["comments"]=sizeof($status->getComments());
	$a["downloads"]=$status->getDownloads();
	$a["shares"]=$status->getShares();
	$a["views"]=$status->getViews();
	$a["username"]=$status->getUser()->getName();
	$a["userid"]=$status->getUser()->getId();
	$a["trusted"]=$status->getUser()->getTrusted();
	if($status->getUser()->getMedia()->getType() == "link"){
		$a["userimage"]=$status->getUser()->getMedia()->getUrl();
	}else{
		$a["userimage"]= $this['imagine']->filter($view['assets']->getUrl($status->getUser()->getMedia()->getLink()), 'user_image');
	}
	if ($status->getType()!="quote") {
		if ($status->getVideo()) {
			$a["type"]=$status->getVideo()->getType();
			$a["extension"]=$status->getVideo()->getExtension();
		}else{
			$a["type"]=$status->getMedia()->getType();
			$a["extension"]=$status->getMedia()->getExtension();
		}
		$a["thumbnail"]= $this['imagine']->filter($view['assets']->getUrl($status->getMedia()->getLink()), 'status_thumb_api');
		$a["image"]= $this['imagine']->filter($view['assets']->getUrl($status->getMedia()->getLink()), 'status_image_api');
		
		if ($status->getVideo()) {
			$a["original"] = $app->getRequest()->getSchemeAndHttpHost() . "/" .$status->getVideo()->getLink();
		}else{
			$a["original"]=$app->getRequest()->getSchemeAndHttpHost() . "/" . $status->getMedia()->getLink();
		}
	}else{
		$a["color"]=$status->getColor();
		$a["quote"]=$status->getQuote();

	}
	$a["created"]=$view['time']->diff($status->getCreated());
	$a["likes"]=$status->getLikes();


echo json_encode($a, JSON_UNESCAPED_UNICODE);?>