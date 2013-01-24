<?php

function onArticleDelete($article, $output, &$reason) {
	// Подготавливаем необходимые данные для дальнейшей работы. Код взят из ExternalStoreAlfresco
	$url = null;
	$fieldName = 'old_text';
	$revision = Revision::newFromTitle($article -> getTitle());
	if (isset($revision)) {
		$dbr = wfGetDB(DB_SLAVE);
		$row = $dbr -> selectRow('text', array('old_text', 'old_flags'), array('old_id' => $revision -> getTextId()), 'ExternalStoreAlfresco::alfArticleSave');
		$url = $row -> $fieldName;
	}

	$_SESSION["lastVersionUrl"] = $url;
	return TRUE;
}

function onArticleDeleteComplete(&$article, User &$user, $reason, $id) {

	require_once ("REST/RESTContent.php");
	require_once ("REST/HTTP/RESTClient.php");

	$url = $_SESSION["lastVersionUrl"];

	$repository = new Repository($GLOBALS['alfURL']);
	try {
		$ticket = $repository -> authenticate($GLOBALS['alfUser'], $GLOBALS['alfPassword']);
	} catch (Exception $e) {
		die('Could not authenticate user "' . $GLOBALS['alfUser'] . '"');
	}

	$session = $repository -> createSession($ticket);

	// Get the store
	$store = $session -> getStoreFromString($GLOBALS['alfWikiStore']);

	// Get the wiki space
	$results = $session -> query($store, 'PATH:"' . $GLOBALS['alfWikiSpace'] . '"');
	$wikiSpace = $results[0];

	$values = explode('/', substr($url, 11));
	$nodeId = $values[2];
	
	// Удаляем нод
	$RESTContent = new RESTContent($repository, $wikiSpace, $session);
	$RESTContent->DeleteNode($nodeId);
	
	
	return TRUE;
}
?>
