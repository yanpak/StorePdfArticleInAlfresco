###ABOUT

Extension for MediaWiki platform.

Perfoms convertation mediawiki's article into Adobe PDF file and storing it in the Alfresco repository whenever ones was created or edited in the Mediawiki. Also, extension enchances some abilities of the Alfresco-php-sdk library. Avoid of  rising "soap call" exception in the front of the user's eyes.
	
For goal of convertation articles into PDF, extension uses MPDF54 converter (supports UTF-8)
	
Also, it uses part of the 'ifresco-phplib' library extended by Dominik Danninger.

<b>Must have extension for the corporate level usage and others needs!</b><br/>
<b>Developed by Yan Pak (c) 2013.</b>


<i>It is a free software. You can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation.</i>

	
###FEATURES
* Stores wiki's articles in PDF. This allows search articles by Alfresco share means
* Allows to create early deleted articles
* Rename PDF article after wiki's article title was changed
* Remove PDF article from repository after one was deleted in the Mediawiki
* Don't arise soap exception to Mediawiki pages unlike it does ExternalStoreAlfresco (part of alfresco-php-sdk)
* Enchance alfresco-php-sdk library

###DEPENDENCIES

* alfresco-php-sdk library (http://code.google.com/p/alfresco-php-sdk/wiki/AlfrescoPHPLibraryInstallationInstructions)

###INSTALLATION
1. Be sure that integration of MediaWiki with Alfresco was properly set up (http://code.google.com/p/alfresco-php-sdk/wiki/AlfrescoMediaWikiInstallationInstructions).
2. Copy/merge folder "extensions" into mediawiki installation folder.
3. Copy/merge folder "Alfresco" into alfresco-php-library's folder.
4. Add these lines at the end of the LocalSettings.php:	

```
#StorePdfArticleInAlfresco
require_once("extensions/StorePdfArticleInAlfresco/StorePdfArticleInAlfresco.php");
```
that's all! Have a nice usage!

<hr/>

### О
	
Расширение для MediaWiki. Выполняет конвертацию сохраняемой (вновь/ранее созданной) статьи в файл формата PDF и сохраняет его в репозитории Alfresco. Также, выполняет удаление/переименовывание сконвертированного PDF файла при удалении/переименовывании соответствующей статьи в MediaWiki. Исключает выброс soap exception на страницу mediawiki. Созданные ПДФ статьи легко ищутся в Alfresco share. В общем, must have расширение для корпоративного уровня.

Для конвертации статей в пдф формат используется конвертер MPDF54 c поддержкой кирилицы.
В коде использована часть библиотеки ifresco-phplib расширенной Домиником Даннинжером.

### ЗАВИСИМОСТИ 
* Alfresco-PHP-Library (mediawiki integration)


### УСТАНОВКА
1. Убедитесь, что у Вас установлена библиотека alfresco-php-lib (http://code.google.com/p/alfresco-php-sdk/wiki/AlfrescoPHPLibraryInstallationInstructions) и настроена интеграция с alfresco (http://code.google.com/p/alfresco-php-sdk/wiki/AlfrescoMediaWikiInstallationInstructions)
2. Скопируйте/Заместите папку extensions в папку MediaWiki
3. Скопируйте/Заместите папку Alfresco в папку alfresco-php-library
4. Добавьте в конце файла LocalSettings.php следующие строчки:

```		
#StorePdfArticleInAlfresco
require_once("extensions/StorePdfArticleInAlfresco/StorePdfArticleInAlfresco.php");
```
Это всё! Приятного использования!
	
#####Разработчик: Ян Пак (с) 2013


##Принцип работы расширения.

Расширение состоит из четырех частей:

<b>Store</b> - модуль отвечающий за конвертацию вновь созданной или редактируемой статьи в формат pdf и сохранении ее в репозитории Alfresco.

<b>Delete</b> - модуль выполняющий удалении статьи из репозитория alfresco при удалении статьи из wiki.

<b>Rename</b> - модуль отвечающий за переименовывание статьи в репозитории вслед за статьей в mediawiki.

<b>Fix</b> - модуль для помощи расширению ExternalStoreAlfresco библиотеки alfresco-php-lib. Исключает баги связанные с конфликтом нодов в репозитории. К примеру, когда ExternalStoreAlfresco пытается создать нод с помощью createChild, а нод на самом деле уже существует.

Каждый из модулей подключен к вики как обработчик определенных событий.

###Подробнее о модулях

<b>Store</b> Модуль использует результаты работы расширения ExternalStoreAlfresco. Во время срабатывания события ArticleSaveComplete, модуль берет из сессии (глобальная переменная) значение нода и записывает в него сконвертированную в пдф статью только что сохраненую на вики. Для получения конвертированного контента потребовалось внести изменения в класс конвертера MPdfConverter.php расширения PdfExport, а также отпаять его от расширения в отдельное решение. Для получения ссылки на нод потребовалось также внести изменения в класс ExternalStoreAlfresco.php.

Модуль должен срабатывать после расширения PdfExport, поэтому он должен быть прописан в LocalSettings.php после ExternalStoreAlfresco. Модуль сохраняет файл с таким же именем как и статья, но без расширения .pdf

<b>Delete</b> Модуль состоит из двух обработчиков событий: ArticleConfirmDelete и ArticleDeleteComplete. В первом обработчике добывается ссылка на нод на пдф файл удаляемой статьи. Во втором обработчике непосредственно удаляется нод в репозитории alfresco, чего, надо сказать не делало расширение ExternalStoreAlfresco, что приводило к конфликту версий и возникновению необрабатываемого исключения при попытке вновь создать ранее удаленную статью. Для выполнения удаления потребовалось привлечь часть стороней библиотеки ifresco-php, в частности, классы отвественные за использование alfresco rest api. Спасибо Доминику!

<b>Rename</b> Модуль состоит из обработчика событий TitleMoveComplete. Когда статья переименована, выполняет последующие переимеоновывание нода в репозитории.

<b>Fix</b> Модуль является обработчиком события ArticleFromTitle. Добывает ссылку на нод статьи с которой работает пользователь в данный момент. Это сделано для стабильной работы ExternalStoreAlfresco.
