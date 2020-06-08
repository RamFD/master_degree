Для корректной работы проекта необходимо пройти следующие шаги:


1) Скопировать проект себе на компьютер

2) Установить Composer

3) Запустить команду composer install внутри директории проекта

4) Создать базу данных

5) Создать папку для проектов пользователей

6) Выдать права используемому серверу на чтение и запись для папки проекта и папки для проектов пользователей ОБЯЗАТЕЛЬНО

7) Отредактировать файл env

Все строки начинающиеся с BD_ отвечают за подключение к базе данных

PATH_TO_PROJECTS = папка, созданная в пункте 5
остальные являются путями к исполнительным файлам пифагора(обязательно поменять)

8) Настроить выбранный сервер (apache/nginx) 
Настройки для apache: 

httpd.conf

DocumentRoot "путь/к/проекту"
<Directory "путь/к/проекту">
    RewriteEngine on
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . index.php
    #
    # Possible values for the Options directive are "None", "All",
    # or any combination of:
    #   Indexes Includes FollowSymLinks SymLinksifOwnerMatch ExecCGI MultiViews
    #
    # Note that "MultiViews" must be named *explicitly* --- "Options All"
    # doesn't give it to you.
    #
    # The Options directive is both complicated and important.  Please see
    # http://httpd.apache.org/docs/2.4/mod/core.html#options
    # for more information.
    #
    Options Indexes FollowSymLinks
    Options All -Indexes
    #
    # AllowOverride controls what directives may be placed in .htaccess files.
    # It can be "All", "None", or any combination of the keywords:
    #   AllowOverride FileInfo AuthConfig Limit
    #
    AllowOverride All

    #
    # Controls who can get stuff from this server.
    #
    Require all granted
</Directory>


sites-available/localhost.conf

<VirtualHost *:80>
        DocumentRoot "путь/к/проекту"
        ServerName localhost
        ServerAdmin you@example.com
        ErrorLog "/путь/к/логам/ошибок"
        TransferLog "/путь/к/логам/"
 
<Directory />
    Options +Indexes +FollowSymLinks +ExecCGI
    AllowOverride All
    Order deny,allow
    Allow from all
Require all granted
</Directory>



9) перейти в папку проекта и вызвать в командной строке следующие команды
php artisan config:cache
php artisan migrate

Если появятся ошибки при миграции - неправильно поменяли файл .env, строки начинающиеся с BD_

10) Перезапустить сервер после изменения конфигурации

11) Открыть браузер и перейти на выбранный при настройке сервера url
