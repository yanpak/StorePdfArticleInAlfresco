<?php
// Включаем библиотеки расширения
require_once ("main/Store.php");
require_once ("main/Delete.php");
require_once ("main/Rename.php");

/**
 * Цепляем все необходимые хэндлеры
 */
// Сохранение статьи
$wgHooks['PageContentSaveComplete'][] = 'onPageContentSave';
$wgHooks['onArticleInsertComplete'][] = 'onPageContentSave';
$wgHooks['ArticleSaveComplete'][] = 'onPageContentSave';
// Удаление статьи
$wgHooks['ArticleConfirmDelete'][] = 'onArticleDelete';
$wgHooks['ArticleDeleteComplete'][] = 'onArticleDeleteComplete';
// Переименовывание статьи
$wgHooks['ArticleFromTitle'][] = 'onArticleFromTitle';
$wgHooks['TitleMoveComplete'][] = 'onTitleMoveComplete';
?>
