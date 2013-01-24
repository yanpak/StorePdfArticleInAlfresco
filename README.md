ABOUT
Extension for MediaWiki.
	Perfom convertation wiki's article into Adobe PDF and storing it in the Alfresco repository whenever it was created or edited. Also, extension fixes some unfinished (or bugs) ToDos in the library Alfresco-php-library.
	Extension uses MPDF54 for converting wiki's articles.
	Developed by Yan Pak (c) 2013.
	
	It is a free software. You can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation.
	
INSTALLATION
	Please note: StorePdfArticleInAlfresco extension depends on the alfresco-php-library (alfresco-php-sdk).
	
	1. Be confirmed that you have installed alfresco-php-library (http://code.google.com/p/alfresco-php-sdk/wiki/AlfrescoPHPLibraryInstallationInstructions) and it was set up to work with mediawiki (http://code.google.com/p/alfresco-php-sdk/wiki/AlfrescoMediaWikiInstallationInstructions).
	2. Copy/merge contents of folder "extensions" to the <mediawiki folder>/extensions.
	3. Copy/merge folder "Alfresco" into alfresco-php-library's folder.
	4. Add thoose lines to the end of LocalSettings.php of mediawiki
		#StorePdfArticleInAlfresco (extension by Yan Pak)
		require_once("extensions/StorePdfArticleInAlfresco/StorePdfArticleInAlfresco.php");
	5. Thats all.
	
	
Расширение для MediaWiki.

	Выполняет конвертацию сохраняемой (вновь/ранее созданной) статьи в файл формата PDF и сохраняет его в репозитории Alfresco сервера.
	акже, выполняет удаление сконвертированного PDF файла при удалении соответствующей статьи в MediaWiki.
	Разработчик: Ян Пак (с) 2013

Зависимости: 
	Alfresco-PHP-Library (mediawiki integration)

Установка:
	1.Убедитесь, что у Вас установлена библиотека alfresco-php-lib (http://code.google.com/p/alfresco-php-sdk/wiki/AlfrescoPHPLibraryInstallationInstructions) и настроена интеграция с mediawiki (http://code.google.com/p/alfresco-php-sdk/wiki/AlfrescoMediaWikiInstallationInstructions) 
	2.Скопируйте содержимое папки extensions в <папка MediaWiki>/extensions/
	3.Скопируйте папку Alfresco в папку alfresco-php-library установленную на системе.
	4.Добавьте в конце файла LocalSettings.php следующие строчки:
		
		#StorePdfArticleInAlfresco (extension by Yan Pak)
		require_once("extensions/StorePdfArticleInAlfresco/StorePdfArticleInAlfresco.php");
	5.Всё готово!



Принцип работы расширения.

Расширение состоит из трех частей:

Store - модуль отвечающий за конвертацию вновь созданной или редактируемой статьи в формат pdf и сохранении ее в репозитории Alfresco.

Delete - модуль выполняющий удалении статьи из репозитория alfresco при удалении статьи из wiki

Каждая из частей подключена к вики как обработчик определенных событий. Поэтому они срабатывают самостоятельно и выполняют определенную работу на фоне.

Подробнее о модулях

StoreArticleInPdf. Модуль использует результаты работы расширения ExternalStoreAlfresco. Во время срабатывания события ArticleSaveComplete, модуль берет из сессии (глобальная переменная) значение нода и записывает в него сконвертированную в пдф статью только что сохраненую на вики. Для получения конвертированного контента потребовалось внести изменения в класс конвертера MPdfConverter.php расширения PdfExport. Для получения ссылки на нод потребовалось также внести изменения в класс ExternalStoreAlfresco.php.

Модуль должен срабатывать после расширения PdfExport, поэтому он должен быть прописан в LocalSettings.php после ExternalStoreAlfresco. Модуль сохраняет файл с таким же именем как и статья, но без расширения .pdf

SafeDeletePdf. Модуль состоит из двух обработчиков событий, ArticleConfirmDelete и ArticleDeleteComplete. В первом обработчике добывается ссылка на нод на пдф файл удаляемой статьи. Во втором обработчике непосредственно удаляется нод в репозитории alfresco, чего, надо сказать не делало расширение ExternalStoreAlfresco, что приводило к конфликту версий и возникновению необрабатываемого исключения при попытке вновь создать ранее удаленную статью. Для выполнения удаления потребовалось привлечь часть стороней библиотеки ifresco-php, в частности, классы отвественные за использование alfresco rest api.

Для стабильной работы вики был произведен тюнинг расширения ExternalStoreAlfresco.
