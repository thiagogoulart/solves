<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 20/07/2019
 */ 
namespace SolvesUi;


class SolvesCabecalho {

    private static $AUTHOR = 'Thiago G.S. Goulart';
    private static $TWITTER_CREATOR = '@solves';
    private static $SCRIPT_ANALYTICS='';
    private static $THEME_COLOR='#FFFFFF';


    public static function config($scriptAnalytics){SolvesCabecalho::setScriptAnalytics($scriptAnalytics);}

    public static function getScriptAnalytics(){return SolvesCabecalho::$SCRIPT_ANALYTICS;}
    public static function setScriptAnalytics($p){SolvesCabecalho::$SCRIPT_ANALYTICS=$p;}
    public static function getThemeColor(){return SolvesCabecalho::$THEME_COLOR;}
    public static function setThemeColor($p){SolvesCabecalho::$THEME_COLOR=$p;}

    public static function getHtml($completeUrl, $pageTitle, $pageDescr, $themeColor=null){
        if(\Solves\Solves::isNotBlank($themeColor)){
            SolvesCabecalho::setThemeColor($themeColor);
        }
        $CANNONICAL = \Solves\Solves::getSiteUrl().$completeUrl;
        $SITE_TITULO = \Solves\Solves::getSiteTitulo().(\Solves\Solves::isNotBlank($pageTitle) ? ' - '.$pageTitle : '');
        $SITE_DESCRIPTION =(\Solves\Solves::isNotBlank($pageTitle) ?  $pageDescr.' ' : '').(\Solves\Solves::getSiteDescr());
        $html = '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="'.SolvesCabecalho::$AUTHOR.'">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>'.$SITE_TITULO.'</title>
    <meta name="application-name" content="'.$SITE_TITULO.'">
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="'.SolvesCabecalho::getThemeColor().'"/>
    <meta name="msapplication-navbutton-color" content="'.SolvesCabecalho::getThemeColor().'">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="'.SolvesCabecalho::getThemeColor().'">

    <link rel="apple-touch-icon-precomposed" sizes="60x60" href="'.(\Solves\Solves::getCompleteImgPathLogo()).'apple-touch-icon-60x60.png" />
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="'.(\Solves\Solves::getCompleteImgPathLogo()).'apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="'.(\Solves\Solves::getCompleteImgPathLogo()).'apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="'.(\Solves\Solves::getCompleteImgPathLogo()).'apple-touch-icon-152x152.png" />
    <link rel="apple-touch-icon-precomposed" sizes="167x167" href="'.(\Solves\Solves::getCompleteImgPathLogo()).'apple-touch-icon-167x167.png" />    
    <link rel="apple-touch-icon-precomposed" sizes="180x180" href="'.(\Solves\Solves::getCompleteImgPathLogo()).'apple-touch-icon-180x180.png" />
    <meta name="msapplication-TileColor" content="'.SolvesCabecalho::getThemeColor().'" />
    <meta name="msapplication-square70x70logo" content="'.(\Solves\Solves::getCompleteImgPathLogo()).'tile70x70.png" />
    <meta name="msapplication-TileImage" content="'.(\Solves\Solves::getCompleteImgPathLogo()).'mstile-144x144.png" />
    <meta name="msapplication-square150x150logo" content="'.(\Solves\Solves::getCompleteImgPathLogo()).'tile150x150.png" />
    <meta name="msapplication-square310x310logo" content="'.(\Solves\Solves::getCompleteImgPathLogo()).'tile310x310.png" />
    <meta name="msapplication-wide310x150logo" content="'.(\Solves\Solves::getCompleteImgPathLogo()).'tile310x150.png" />
    
    <link rel="shortcut icon" href="'.(\Solves\Solves::getCompleteImgPathLogo()).'favicon.ico" type="image/x-icon">
    <link rel="icon" href="'.(\Solves\Solves::getCompleteImgPathLogo()).'favicon.ico" type="image/x-icon">
    <link rel="icon" href="'.(\Solves\Solves::getCompleteImgPathLogo()).'favicon-16x16.png" sizes="16x16">
    <link rel="icon" href="'.(\Solves\Solves::getCompleteImgPathLogo()).'favicon-32x32.png" sizes="32x32">
    <link rel="icon" href="'.(\Solves\Solves::getCompleteImgPathLogo()).'favicon-96x96.png" sizes="96x96">

    <link rel="canonical" href="'.$CANNONICAL.'" />
    <meta name="twitter:site" content="'.$SITE_TITULO.'">
    <meta name="twitter:creator" content="'.SolvesCabecalho::$TWITTER_CREATOR.'">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="'.$SITE_TITULO.'">
    <meta name="twitter:description" content="'.$SITE_DESCRIPTION.'">
    <meta name="twitter:image" content="'.(\Solves\Solves::getCompleteImgPathLogo()).'pwa-192x192.png">
    <meta property="og:site_name" content="'.$SITE_TITULO.'" />
    <meta property="og:title" content="'.$SITE_TITULO.'">
    <meta property="og:image" content="'.(\Solves\Solves::getCompleteImgPathLogo()).'pwa-192x192.png">
    <meta property="og:locale" content="pt_BR" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="'.$CANNONICAL.'" />
    <meta property="og:description" content="'.$SITE_DESCRIPTION.'">
    <meta name="keywords" content="'.(\Solves\Solves::getSiteKeys()).'" />
    <meta name="description" content="'.$SITE_DESCRIPTION.'" />';

/*CSS*/
    $cssFilePaths = \SolvesUi\SolvesUi::getCssFilePaths();
    foreach($cssFilePaths as $cssFilePath){
        $html .='<link rel="stylesheet" href="'.$cssFilePath.'">';
    }

/*SCRIPT ANALYTCS*/
    if(\Solves\Solves::isProdMode()){
        $html .= SolvesCabecalho::getScriptAnalytics();
    }

    $html .='
</head>
<body>
<div style="display:none;">'.$SITE_TITULO.' '.$SITE_DESCRIPTION.' '.\Solves\Solves::getSiteKeys().' '.\Solves\SolvesTime::getTimestampAtual().'</div>
<div id="modalSmall" class="modal fade" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content bd-0 tx-14">
      <div class="modal-header pd-x-20">
        <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold" id="modalSmall_title"></h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body pd-20" id="modalSmall_body"></div>
      <div class="modal-footer justify-content-center" id="modalSmall_footer"></div>
    </div>
  </div><!-- modal-dialog -->
</div>

<div class="overlay" id="overlay" style="display:none;"></div>
<div id="overlay_loading" style="display:none;">
    '.\SolvesUi\SolvesUi::getLoadingElementHtml().'
    Carregando
</div>
<div id="overlay_loaded" style="display:none;"></div>
<div id="preloader" style="">
  <div class="preloader-container">
    <h4 class="preload-logo" style="visibility: visible;">
      <span class="navbar-brand">'.$SITE_TITULO.'</span>
    </h4>
    <img src="'.\Solves\Solves::getImgPath().'preload.gif" alt="Loading" style="visibility: visible;">
  </div>
</div>

<div class="solves_notification" id="notification_new_version" style="display:none;">
  <div class="solves_notification-container">
    <h4 class="preload-logo" style="visibility: visible;">
      <span class="navbar-brand">'.$SITE_TITULO.'</span>
    </h4>
    <div class="p-2">
        <button class="btn btn-md" id="reload_new_version"><img src="'.\Solves\Solves::getImgPath().'preload.gif" alt="Loading" style="visibility: visible;">
        <br>Nova versão disponível.<br>Atualizando...<br>São apenas alguns instantes.</button>
    </div>
  </div>
</div>

<div id="firebase_login"></div>';

return $html;
    }

   
}