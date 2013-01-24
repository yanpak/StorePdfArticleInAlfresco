<?php

function onArticleFromTitle(Title &$title, &$article) {
	// Подготавливаем необходимые данные для расширения ExternalStoreAlfresco. Код взят из ExternalStoreAlfresco. В общем-то, их баг я уменьшил
	$url = null;
	$fieldName = 'old_text';
	$revision = Revision::newFromTitle($title);
	if (isset($revision)) {
		$dbr = wfGetDB(DB_SLAVE);
		$row = $dbr -> selectRow('text', array('old_text', 'old_flags'), array('old_id' => $revision -> getTextId()), 'ExternalStoreAlfresco::alfArticleSave');
		$url = $row -> $fieldName;
	}

	// Store the details of the article in the session
	$_SESSION["title"] = ExternalStoreAlfresco::getTitle($title);
	$_SESSION["lastVersionUrl"] = $url;

	return TRUE;

}

function onTitleMoveComplete(Title &$title, Title &$newtitle, User &$user, $oldid, $newid) {

	// Получаем название новой статьи
	$article = $newtitle -> getDBkey();

	// Инициализируем конвертер. Берем содержимое статьи в PDF
	$pdfConvertor = PdfConverterFactory::getPdfConverter();
	$pdfConvertor -> initialize();
	$data = $pdfConvertor -> getRawPdf(array($article));

	// Создаем сессию AlfrescoStore
	$repository = new Repository($GLOBALS['alfURL']);
	try {
		$ticket = $repository -> authenticate($GLOBALS['alfUser'], $GLOBALS['alfPassword']);
	} catch (Exception $e) {
		die('Could not authenticate user "' . $GLOBALS['alfUser'] . '"');
	}

	// Создаем сессию
	$session = $repository -> createSession($ticket);

	// Получаем хранилище
	$store = $session -> getStoreFromString($GLOBALS['alfWikiStore']);

	// Получаем пространство
	$results = $session -> query($store, 'PATH:"' . $GLOBALS['alfWikiSpace'] . '"');
	$wikiSpace = $results[0];

	// Создаем нод
	$id = substr($_SESSION["lastNode"], strrpos($_SESSION["lastNode"], "/") + 1);
	$node = $session -> getNode($store, $id);

	// Переименовываем название нода(файла) и сохраняем его.
	$node -> cm_name = str_replace("_", " ", $newtitle -> getText());
	$node -> updateContent("cm_content", "application/pdf", "UTF-8", $data);
	$session -> save();

	// Обнуляем переменную хранящую нод.
	$_SESSION["lastNode"] = "";
	
	return TRUE;
}
?>