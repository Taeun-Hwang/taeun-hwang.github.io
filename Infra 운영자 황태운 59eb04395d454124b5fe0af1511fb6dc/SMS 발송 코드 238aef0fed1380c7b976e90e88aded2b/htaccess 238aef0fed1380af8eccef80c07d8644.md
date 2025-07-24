# .htaccess

DirectoryIndex send_form.php index.html index.php home.html default.php

RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]