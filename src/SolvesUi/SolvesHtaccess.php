<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 10/12/2019
 */ 
namespace SolvesUi;


class SolvesHtaccess {

    public static function getContent(){
        $content = '<files ~ "^.*\.([Hh][Tt][Aa])">
          order allow,deny
          deny from all
          satisfy all
          </files>
          <ifmodule mod_gzip.c="">
          mod_gzip_on       Yes
          mod_gzip_dechunk  Yes
          mod_gzip_item_include file      \.(html?|css|js|php|pl)$
          mod_gzip_item_include handler   ^cgi-script$
          mod_gzip_item_include mime      ^text/.*
          mod_gzip_item_include mime      ^application/x-javascript.*
          mod_gzip_item_exclude mime      ^image/.*
          mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*
          </ifmodule>
          <IfModule mod_headers.c>
           
              Header always  set Access-Control-Allow-Credentials "true"
            Header always  set Access-Control-Allow-Methods "POST, GET, OPTIONS, DELETE, PUT, HEAD"
            Header always  set Access-Control-Allow-Headers "Cache-Control, Pragma, Authorization, Key, Access-Control-Allow-Headers, Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, HTTP_X_USER_LOGIN, HTTP_X_AUTH_TOKEN, X_USER_LOGIN, X_AUTH_TOKEN, client-security-token"
            
          </IfModule>
          <IfModule mod_rewrite.c>
          RewriteEngine On
';
        
        if(\Solves\SolvesConf::getSolvesConfUrls()->getSolvesConfUrl()->isUseHttps()){
          $content .= '
            # Sempre usar HTTPS
            RewriteCond %{HTTPS} !on
            RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
          ';
        }

        $content .= '
          #Urls amigáveis
          RewriteBase '.\Solves\Solves::getRootPathOrModule().'
          RewriteCond %{REQUEST_FILENAME} !-d
          RewriteCond %{REQUEST_FILENAME} !-f
          RewriteRule ^((?!admin/).*)$ index.php?p=$1 [QSA]

          </IfModule>
        ';

        if(\Solves\Solves::isProdMode()){
          $content .= '
            # Use PHP71 as default
            AddHandler application/x-httpd-php71 .php
            <IfModule mod_suphp.c>
                suPHP_ConfigPath /opt/php71/lib
            </IfModule>
            ';
        }

          return $content;
    }
}