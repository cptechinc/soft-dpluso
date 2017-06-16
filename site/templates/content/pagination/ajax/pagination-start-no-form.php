<?php
	if ($input->get->display) {
		$config->showonpage = $input->get->text('display');
		$session->display = $config->showonpage;
	} elseif ($session->display){
		$config->showonpage = $session->display;
	}

	$results_page_link = $page->httpUrl.querystring_replace($querystring, array('display'), array(false));
	if (strpos($results_page_link, '?') !== FALSE) {
		$symbol = '&';
	} else {
		$symbol = '?';
	}
?>
