<?php

require_once('magpierss/rss_fetch.inc');

class VerseOfTheDay
{	
	var $verse_title, $verse_link, $verse;
	
	
	function VerseOfTheDay($rsslink) {
  		$verse = @fetch_rss($rsslink);		
		if ($verse) {			
			$verse = $verse->items[0];

    		$this->verse = trim($verse['content']['encoded']);
    		$this->verse_title = $verse['title'];
    		$this->verse_link = $verse['guid'];
		}	
 		else {
    		$this->verse_title = 'RSS Error ' . $rsslink;
  		}
	}
}
?>
