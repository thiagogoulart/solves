<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 18/07/2019
 */ 
namespace SolvesUi;


class SolvesUi {

    private static $RESTRICTED_URLS = array();

    private static $SCRIPT_FILEPATHS = array();
    private static $CSS_FILEPATHS = array();
    
    private static $SCRIPTS_ONLOAD = '';
    private static $INCLUDE_SCRIPTS_TAGS='';

    private static $THEME_BACKGROUND_COLOR='#FFFFFF';
    private static $THEME_COLOR='#FFFFFF';

    private static $IS_APP=false;
    private static $UI_VERSION=null;

    public static function config($cssFilePaths, $jsFilePaths, $themeBgColor, $themeColor, $uiVersion){
        SolvesUi::setCssFilePaths($cssFilePaths);
        SolvesUi::setScriptFilePaths($jsFilePaths);
        SolvesUi::setThemeBackgroundColor($themeBgColor);
        SolvesUi::setThemeColor($themeColor);
        SolvesUi::setUiVersion($uiVersion);
    }

    public static function setRestrictedUrls($arr){SolvesUi::$RESTRICTED_URLS = $arr;}
    public static function getRestrictedUrls(){return SolvesUi::$RESTRICTED_URLS;}
    public static function isRestrictedUrl($url){
        return in_array($url, SolvesUi::$RESTRICTED_URLS);
    }

    public static function setScriptFilePaths($arr){SolvesUi::$SCRIPT_FILEPATHS = $arr;}
    public static function setCssFilePaths($arr){SolvesUi::$CSS_FILEPATHS = $arr;}
    public static function getScriptFilePaths(){return SolvesUi::$SCRIPT_FILEPATHS;}
    public static function getSingleScriptFilePath($item){return (is_array($item) ? $item[0]: $item);}
    public static function getSingleScriptFilePathProperties($item){return (is_array($item) ? $item[1]:  (strpos($item, '/')==0 ?'':'crossorigin="anonymous"'));}
    public static function getCssFilePaths(){return SolvesUi::$CSS_FILEPATHS;}
    public static function getUiVersion(){return SolvesUi::$UI_VERSION;}
    public static function setUiVersion($p){SolvesUi::$UI_VERSION = $p;}
    public static function getCacheUiVersion(){
        return \Solves\Solves::getSystemName().'_'.\Solves\Solves::getSystemVersion().(\Solves\Solves::isNotBlank(SolvesUi::$UI_VERSION)?'_ui'.SolvesUi::$UI_VERSION:'');
    }

    public static function getThemeBackgroundColor(){return SolvesUi::$THEME_BACKGROUND_COLOR;}
    public static function setThemeBackgroundColor($p){SolvesUi::$THEME_BACKGROUND_COLOR = $p;}
    public static function getThemeColor(){return SolvesUi::$THEME_COLOR;}
    public static function setThemeColor($p){SolvesUi::$THEME_COLOR = $p;}

    public static function getScriptsJS($INCLUDE_SCRIPTS_TAGS, $SCRIPTS, $SCRIPTS_ONLOAD, $ATUAL_URL){
        $s = '';
        $arrScripts = \SolvesUi\SolvesUi::getScriptFilePaths();
        foreach($arrScripts as $scrSource){
            $jsFilePath = \SolvesUi\SolvesUi::getSingleScriptFilePath($scrSource);
            $jsProps = \SolvesUi\SolvesUi::getSingleScriptFilePathProperties($scrSource);
            $s .= '<script type="text/javascript" src="'.$jsFilePath.'" '.$jsProps.'></script>
';
        }

        $s .= $INCLUDE_SCRIPTS_TAGS; 

        $s .= '<script type="text/javascript">';
        $s .= $SCRIPTS; 
        $s .= "$(function(){
            'use strict';
            $.Solves.url = '".$ATUAL_URL."';
                ".$SCRIPTS_ONLOAD."
            });
        </script>";
        return $s;
    }

    public static function getScriptAjusteMetaTags($completeUrl, $titulo, $descr, $img){
        $completeUrl = \Solves\Solves::getSiteUrl().$completeUrl;
        return "
        $('link[rel=\"canonical\"]').attr('href', '".$completeUrl."');
        $('meta[property=\"og:url\"]').attr('content', '".$completeUrl."');
        ".((\Solves\Solves::isNotBlank($titulo) && $titulo!=\Solves\Solves::getSiteTitulo())?"
                            $('title').html('".\Solves\Solves::getSiteTitulo().' - '.$titulo."');
                            $('meta[name=\"twitter:title\"]').attr('content', '".\Solves\Solves::getSiteTitulo().' - '.$titulo."');
                            $('meta[property=\"og:title\"]').attr('content', '".\Solves\Solves::getSiteTitulo().' - '.$titulo."');":"")."
        ".(\Solves\Solves::isNotBlank($img)?"$('meta[property=\"og:image\"]').attr('content', '".\Solves\Solves::getSiteUrl().$img."');
                            $('meta[name=\"twitter:image\"]').attr('content', '".\Solves\Solves::getSiteUrl().$img."');":"")."
        ".(\Solves\Solves::isNotBlank($descr)?"
        $('meta[name=\"description\"]').attr('content', '".$descr."'+$('meta[name=description]').attr('content'));
        $('meta[name=\"twitter:description\"]').attr('content', '".$descr."'+$('meta[name=description]').attr('content'));
        $('meta[property=\"og:description\"]').attr('content', '".$descr."'+$('meta[name=description]').attr('content'));":"")."
        ";
    }
    public static function getHtmlShareButtons($titulo, $completeUrl, $img){
        $completeUrl = \Solves\Solves::getSiteUrl().$completeUrl;
        $linkMsg = 'Olha%20o%20que%20eu%20vi%20no%20site%20'.\Solves\Solves::getSiteTitulo().':%20'.$titulo;
        $linkMsgComUrl = $linkMsg.'%20'.$completeUrl.'';
        $linkMsgComUrlEncoded = $linkMsg.'%20'.urlencode($completeUrl).'';
        return '
        <div class="share_social_box"><span class="share_social_box_title">Compartilhar: </span>
    <a rel="noopener" href="https://api.whatsapp.com/send?text='.$linkMsgComUrlEncoded.'" target="_blank" title="Compartilhar no Whatsapp">
        <i class="fab fa-lg fa-whatsapp"></i>
    </a>
    <a rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u='.$completeUrl.'" target="_blank" title="Compartilhar no Facebook">
        <i class="fab fa-lg fa-facebook"></i>
    </a>
    <a rel="noopener" href="http://twitter.com/share?text='.$linkMsg.'&amp;url='.$completeUrl.'" target="_blank" data-role="shareLink" title="Compartilhar no Twitter">
        <i class="fab fa-lg fa-twitter"></i>
    </a>
    <a rel="noopener" href="http://pinterest.com/pin/create/button/?url='.$completeUrl.(\Solves\Solves::isNotBlank($img)?'&amp;media='.\Solves\Solves::getSiteUrl().$img:'').'" target="_blank" title="Compartilhar no Pinterest">
        <i class="fab fa-lg fa-pinterest"></i>
    </a>
    <!-- a rel="noopener" href="https://plus.google.com/share?url='.$completeUrl.'" target="_blank" title="Compartilhar no Google+ ">
        <i class="fab fa-lg fa-google-plus"></i>
    </a -->
    <a rel="noopener" href="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$completeUrl.'" target="_blank" title="Compartilhar no LinkedIn">
        <i class="fab fa-lg fa-linkedin"></i>
    </a>
    <!-- a rel="noopener" href="mailto:?Subject='.$titulo.'&amp;Body='.$linkMsgComUrl.'" target="_blank" title="Compartilhar por e-mail">
        <i class="fa fa-lg fa-envelope"></i>
    </a -->
    </div>
        ';
    }
    public static function getPaginacaoNav($qtdPaginas, $paginaAtual, $qtdPorPagina, $qtdRows){
        if($qtdPaginas<=1){
            return '';
        }
        $maxQtdButtons = 5;
        $meio = ($maxQtdButtons-1)/2;
        $html = '<div class="pagination_container text-center">';
        $paginaAtual = (isset($paginaAtual) ? $paginaAtual : 1);
        $initButtons = (($paginaAtual>($meio+1))?(($paginaAtual-$meio)<=$maxQtdButtons ? ($paginaAtual-$maxQtdButtons+1) : ($paginaAtual-$meio+1)):1);
        $endButtons = (($qtdPaginas>($paginaAtual+$meio))?($paginaAtual<=($meio+1) ? $maxQtdButtons : ($paginaAtual+$meio)):$qtdPaginas);
        $initButtons = ($initButtons<1 ? 1 : $initButtons);
        $endButtons = ($endButtons>$qtdPaginas ? $qtdPaginas : $endButtons);
        if($paginaAtual>1){
           $i = ($paginaAtual-1);
           $html .= '<a class="btn btn-default pagination_btn" href="?page='.$i.'" alt="Página Anterior" title="Página Anterior">Anterior</a>';
        }    
        for($i=$initButtons;$i<=$endButtons;$i++){
           $html .= '<a class="btn btn-default pagination_btn pagination_btn_number '.($i==$paginaAtual ? 'btn-default-active' : '').'" href="?page='.$i.'" alt="Ir para a Página '.$i.'" title="Ir para a Página '.$i.'">'.$i.'</a>';
        }
        if($qtdPaginas>1 && $paginaAtual<$qtdPaginas){
           $i = ($paginaAtual+1);
           $html .= '<a class="btn btn-default pagination_btn" href="?page='.$i.'" alt="Próxima Página" title="Próxima Página">Próxima</a>';
        }    
        $html .= '</div>';
        return $html;
    }

    public static function getLoadingElementHtml(){
        return '<div class="sk-cube-grid">
          <div class="sk-cube sk-cube1"></div>
          <div class="sk-cube sk-cube2"></div>
          <div class="sk-cube sk-cube3"></div>
          <div class="sk-cube sk-cube4"></div>
          <div class="sk-cube sk-cube5"></div>
          <div class="sk-cube sk-cube6"></div>
          <div class="sk-cube sk-cube7"></div>
          <div class="sk-cube sk-cube8"></div>
          <div class="sk-cube sk-cube9"></div>
        </div>';
    }
    public static function getPublicLoadingContainerHtml(){
        return '<div id="public-loading" class="login-wrapper wd-300 wd-xs-350 pd-25 pd-xs-40 bg-white" style="background-color: #ffffffa1;color: #000000;">
            <div class="signin-logo tx-center tx-24 tx-bold tx-inverse">
              <div><img src="'.\Solves\Solves::getSiteIcone().'" alt="'.\Solves\Solves::getSiteTitulo().'" title="'.\Solves\Solves::getSiteTitulo().'"/></div>
              <div>'.SolvesUi::getLoadingElementHtml().'</div>
            </div>
            <div class="tx-center mg-b-60">Carregando...</div>
          </div>';
    }
    public static function getHtmlExibicao($string){
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    public static function getNumeroExibicaoHtml($n){
        $t='';
        if($n<1000){
            $t = $n;
        }else if($n>=1000 && $n<1000000){
            $n = round($n/1000, 1);
            $t = str_replace('.', ',', $n).'<span style="font-size: 14px;">mil</span>';
        }else if($n>=1000000 && $n<1000000000){
            $n = round($n/1000000, 1);
            $t = str_replace('.', ',', $n).'<span style="font-size: 14px;">Milhões</span>';
        }else if($n>=1000000000 && $n<1000000000000){
            $n = round($n/1000000000, 1);
            $t = str_replace('.', ',', $n).'<span style="font-size: 14px;">Bilhões</span>'; 
        }else if($n>=1000000000000 && $n<1000000000000000){
            $n = round($n/1000000000000, 1);
            $t = str_replace('.', ',', $n).'<span style="font-size: 14px;">Trilhões</span>';
        }else{
            $n = round($n/1000000000000000, 1);
            $t = str_replace('.', ',', $n).'<span style="font-size: 14px;">Quadrilhões</span>';
        }
        return $t;
    }
}