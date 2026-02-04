<?php 
$obj;


$list_posts=array();
$players_list=array();
$questions_list=array();

foreach ($posts as $key => $post) {
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
	$list_posts[]=$a;
}

 foreach ($questions as $key => $question) {
     $s["id"]=$question->getId();
     $s["question"]=$question->getQuestion();
     $s["multi"]=$question->getMulti();
     $s["open"]=($question->getOpen())?true:false;

     $choices=array();
     foreach ($question->getAnswers() as $key => $answer) {
         $c["id"]=$answer->getId();
         $c["answer"]=$answer->getAnswer();
         $c["votes"]=$answer->getValue();

         $choices[]=$c;
     }
     $s["answers"]=$choices;
     $questions_list[]=$s;
 }

foreach ($players as $key => $player) {
                    $p["id"]=$player->getId();
                    $p["fname"]=$player->getFname();
                    $p["lname"]=$player->getLname();
                    $p["number"]=$player->getNumber();
                    $p["position"]=$player->getPost()->getTitle();
                    $agoTime = $view['time']->diff($player->getBorn()) ;
                    $agoTime= preg_replace('/\W\w+\s*(\W*)$/', '$1', $agoTime);
                    $p["age"]=$agoTime;
                    $p["height"]=$player->getheight();
                    $p["weight"]=$player->getWeight();   
                    $p["country"]=$player->getCountry()->getTitle();
			$p["country_image"]= $this['imagine']->filter($view['assets']->getUrl($player->getCountry()->getMedia()->getLink()), 'country_thumb');
			$p["image"]= $this['imagine']->filter($view['assets']->getUrl($player->getMedia()->getLink()), 'player_thumb');

                    $statistics=array();

                    foreach ($player->getStatistics() as  $statistic) {
                            $st["id"] = $statistic->getId();
                            $st["name"] = $statistic->getStatistic();
                            $st["value"] = $statistic->getValue()."";
                            $statistics[] = $st;
                    }
                    $p["statistics"]=$statistics;
                    $socials=array();

                    foreach ($player->getSocials() as  $social) {
                            $st["id"] = $social->getId();
                            $st["name"] = $social->getSocial();
                            $st["value"] = $social->getValue()."";
                            $socials[] = $st;
                    }
                    $p["socials"]=$socials;

                    $players_list[]=$p;
            

        }


$obj["matches"]=$matches;
$obj["posts"]=$list_posts;
$obj["players"]=$players_list;
$obj["questions"]=$questions_list;
echo json_encode($obj, JSON_UNESCAPED_UNICODE);
?>