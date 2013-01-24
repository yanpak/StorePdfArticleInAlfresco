<?php

// Загружаем необходимые библиотеки
require_once ("extensions/AlfrescoConfig.php");
if (isset($_SERVER["ALF_AVAILABLE"]) == false) {
	require_once ("Alfresco/Service/Session.php");
	require_once ("Alfresco/Service/SpacesStore.php");
	require_once ("Alfresco/Service/Node.php");
	require_once ("Alfresco/Service/Version.php");
}

// Непосредственно обработчик события
function onPageContentSave($article) {

	// Получаем название статьи
	$article = $article -> getTitle() -> getDBkey();

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

	// Проверяем на наличие изменений в статье. Если их не было, то предыдущее расширение (ExternalStoreAlfresco) не изменило значения данной переменной.
	if ($_SESSION["lastNode"] === "")
		return true;

	// Создаем нод
	$id = substr($_SESSION["lastNode"], strrpos($_SESSION["lastNode"], "/") + 1);
	$node = $session -> getNode($store, $id);

	// Обновляем содержимое нода и сохраняем.
	$node -> cm_name = str_replace("_", " ", $article);
	$node -> addAspect("cm_versionable", null);
	$node -> cm_initialVersion = false;
	$node -> cm_autoVersion = false;
	$node -> updateContent("cm_content", "application/pdf", "UTF-8", $data);
	$session -> save();

	// Обнуляем переменную хранящую нод
	$_SESSION["lastNode"] = "";
	
	return true;
}
?>
