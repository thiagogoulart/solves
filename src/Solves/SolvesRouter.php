<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 06/09/2019
 */ 
namespace Solves;


class SolvesRouter {
    public $ATUAL_URL='';
    public $CANNONICAL='';
    public $MODO_SOON_ATIVADO = false;
    public $IS_SOON_PAGE = false;
    public $IS_APP = false;
    public $p;

    //Flags to VIEWS identification
    private $incluiTopo = true;
    private $incluiTopoPublic = false;
    private $incluiTopoApp = false;
    private $incluiTopoAppPublic = false;

    //Flags of file type  required
    private $isPageController = false;
    private $isJs = false;
    private $isServiceWorkerFile = false;
    private $isServiceWorkerRegisterFile = false;
    private $isWebManifest = false;
    private $isConfigJsFile = false;

    //RESULT OF PROCESSMENT
    private $requestedPage = ''; 
    private $pagInclude ='';

    //USED BY CONTROLLERS TO RECEIVE REQUEST DATA  
    private $dados = null;   
    private $token = null;   
    private $userData = null;   
    private $perfil = null;   
    private $usuario = null;   

    //HTTP 
    private $HTTP_METHOD;
    private $_HTTPREQUEST_SERVER;
    private $_HTTPREQUEST_POST;   
    private $_HTTPREQUEST_GET;   
    private $_HTTPREQUEST_PUT;   
    private $_HTTPREQUEST_DELETE;

    //REST
    private $restService;
    private $restDetails;


    public function __construct($_HTTPREQUEST_SERVER, $_HTTPREQUEST_POST, $_HTTPREQUEST_GET, $_HTTPREQUEST_PUT, $_HTTPREQUEST_DELETE) {
        $this->_HTTPREQUEST_SERVER = $_HTTPREQUEST_SERVER;
        $this->_HTTPREQUEST_POST = $_HTTPREQUEST_POST;
        $this->_HTTPREQUEST_GET = $_HTTPREQUEST_GET;
        $this->_HTTPREQUEST_PUT = $_HTTPREQUEST_PUT;
        $this->_HTTPREQUEST_DELETE = $_HTTPREQUEST_DELETE;

        $this->MODO_SOON_ATIVADO = \Solves\Solves::isModoSoon();
        $this->requestedPage = $this->_HTTPREQUEST_GET['p'];

        $this->processa();
    } 
    private function processa(){
        if(isset($this->requestedPage) && strlen($this->requestedPage)>1 && strlen($this->requestedPage)<9999){
          if(substr_compare($this->requestedPage, 'processupload.php', -strlen('processupload.php')) === 0){
            //ALIAS
            $this->requestedPage = 'rest/upload';
          }
          $this->isPageController = $this->verifyIfIsPageController();
          $this->IS_APP = $this->verifyIfIsApp();

          if($this->isPageController) {
            $this->processaController();
          }else if(substr($this->requestedPage, 0, 5)=='sw.js'){
            $this->isServiceWorkerFile = true;
            $this->isJs = true;
            $this->pagInclude = '/sw.js';
          }else if(substr($this->requestedPage, 0, 9)=='config.js'){
            $this->isConfigJsFile = true;
            $this->isJs = true;
            $this->pagInclude = '/config.js';
          }else if(substr($this->requestedPage, 0, 14)=='sw_register.js'){
            $this->isServiceWorkerRegisterFile = true;
            $this->isJs = true;
            $this->pagInclude = '/sw_register.js';
          }else if(substr($this->requestedPage, 0, 20)=='manifest.webmanifest'){ 
            $this->isWebManifest = true;
            $this->pagInclude = '/manifest.webmanifest';
          }else if(substr($this->requestedPage, 0, 4)=='soon'){
            //SOON PAGES
            $this->IS_SOON_PAGE = true;
            $this->ATUAL_URL = \Solves\Solves::getUrlName('',$this->requestedPage, false);
            $this->p= \Solves\Solves::getUrlNameViewPath($this->ATUAL_URL);
            if($this->checkIfFileExists($this->p)) {
              $this->pagInclude = $this->p;
            }else{
              $this->pagInclude = 'views/soon.php';
            }
          }else if(substr($this->requestedPage, 0, 3)=='pre'){
            //SOON PAGES
            $this->IS_SOON_PAGE = true;
            $this->ATUAL_URL = \Solves\Solves::getUrlName('',$this->requestedPage, false);
            $this->p= 'views/cadastro.php';
            if($this->checkIfFileExists($this->p)) {
              $this->pagInclude = $this->p;
            }else{
              $this->pagInclude = 'views/soon.php';
            }
          }else{    
            $this->ATUAL_URL = \Solves\Solves::getUrlName('',$this->requestedPage, false);
            $this->isRestricted = \SolvesUi\SolvesUi::isRestrictedUrl($this->ATUAL_URL);
            $this->p = \Solves\Solves::getUrlNameViewPath($this->ATUAL_URL);
            $this->includePage();
          }
        }else{
          $this->pagInclude = 'views/index.php';
        }

        //POR DEFAULT VAI PARA SOON
        if($this->MODO_SOON_ATIVADO && !$this->isPageController && !$this->IS_SOON_PAGE){ 
          $this->IS_SOON_PAGE = true;
          //TODO deixar parametrizado Quais URLS serão abertas durante SOON
          if($this->ATUAL_URL=='meu_perfil' || $this->ATUAL_URL=='termo_uso' || $this->ATUAL_URL=='termo_privacidade'){
            $this->pagInclude = \Solves\Solves::getUrlNameViewPath($this->ATUAL_URL);
            if(!$this->checkIfFileExists($this->pagInclude)) {
              $this->pagInclude= 'views/soon.php';
            }
          }else{
            $this->pagInclude= 'views/soon.php';
          }
        }
        //END SOON

        $this->CANNONICAL =  \Solves\Solves::getSiteUrl().$this->ATUAL_URL;
        $this->configUseOfTopos();
    }
    private function verifyIfIsPageController(){
        return (strpos($this->requestedPage, 'rest/')===0 || strpos($this->requestedPage, 'avatar/')===0 ||  strpos($this->requestedPage, 'thumb/')===0 || strpos($this->requestedPage, 'perfil/')===0 || strpos($this->requestedPage, 'file/')===0 || strpos($this->requestedPage, 'foto/')===0);
    }
    private function verifyIfIsApp(){
        $this->IS_APP = (!$this->isPageController && (strpos($this->requestedPage, 'app/')===0 || strpos($this->requestedPage, 'app_')===0));
        if($this->IS_APP && strpos($this->requestedPage, 'app_')===0){
            $this->requestedPage = str_replace('app_', 'app/', $this->requestedPage);
        }
        return $this->IS_APP;
    }
    private function configUseOfTopos(){
        if($this->IS_SOON_PAGE){
          $this->incluiTopoApp = false;
          $this->incluiTopo = false;
          $this->incluiTopoPublic = false;
        }else if($this->IS_APP){
            if($this->isRestricted){
                $this->incluiTopoApp = true;
                $this->incluiTopoAppPublic = false;
            }else{
                $this->incluiTopoAppPublic = true;
                $this->incluiTopoApp = false;
            }
          $this->incluiTopo = false;
          $this->incluiTopoPublic = false;
        }else if($this->isRestricted){
          $this->incluiTopo = true;
          $this->incluiTopoApp = false;
          $this->incluiTopoPublic = false;
        }else{
          $this->incluiTopoPublic = true;
          $this->incluiTopo = false;
        }
    }
    private function processaController(){
        try{ 
            /*************** REGRA DO CONTROLLER  ************************/
            $this->isRest = false;
            if(isset($this->requestedPage) && strlen($this->requestedPage)>1 && strlen($this->requestedPage)<255){
                if(substr($this->requestedPage, 0, 5)=='rest/'){
                    $this->processaRest();
                }else if(strpos($this->requestedPage, 'avatar/')===0){                
                  $this->processaAvatar();
                }else if(strpos($this->requestedPage, 'foto/')===0){                
                  $this->processaFoto();
                }else{
                  $this->isPageController = false;
                  $this->include404();
                }
            }else{
              $this->isPageController = false;
              $this->include404();
            }
        } catch (Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
        }
    }
    private function includePage(){
        $defined = false;
        if($this->IS_APP){
            if($this->p=='app' || $this->p=='views/app.php'){
                $p= str_replace('app/', '', $this->ATUAL_URL);
                $p = \Solves\Solves::getUrlNameViewPath($p);
                $this->p = 'app/'.$p;
            }
            if(!\Solves\Solves::isNotBlank($this->p)){
                $this->p= 'index';
            }
            if($this->checkIfFileExists($this->p)) {
                $this->pagInclude = $this->p;
                $defined = true;
            }else{
                $this->p= str_replace('app/', '', $this->p);
            }
        }
        if(!$defined){
            if(!\Solves\Solves::isNotBlank($this->p)){
                $this->p= 'index';
            }
            if($this->checkIfFileExists($this->p)) {
              $this->pagInclude = $this->p;
            }else{
                $this->include404();
            }
        }
    }
    private function include404(){
        $p= 'views/404.php';
        if($this->checkIfFileExists($p)) {
            $this->pagInclude = $p;
        }else{
            $this->pagInclude = \Solves\Solves::getVendorPath().$p;
        }
    }
    private function processaRest(){
        $this->isRest = true;
        $requisicaoRest = substr($this->requestedPage, 5, strlen($this->requestedPage)-5);
        $this->restService =  ((strpos($requisicaoRest, '/')>0)?substr($requisicaoRest, 0, strpos($requisicaoRest, '/')):$requisicaoRest);

        if(strpos($this->restService, 'app/')===0){
            $this->restService = str_replace('app/', '', $this->restService);
        }

        $pos_startDetails=strlen($this->restService)+1;
        $this->restDetails = substr($requisicaoRest, $pos_startDetails, strlen($requisicaoRest)-$pos_startDetails-(substr($requisicaoRest, -1)=='/'?1:0));
        $this->restDetails = \Solves\Solves::removeEspacos($this->restDetails);

        $this->preencheVariaveisDeDados();
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST");
        header("Access-Control-Allow-Headers: GET, POST");
        header("Access-Control-Request-Method: Cache-Control, Pragma, Authorization, Key, Access-Control-Allow-Headers, Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, HTTP_X_USER_LOGIN, HTTP_X_AUTH_TOKEN, X_USER_LOGIN, X_AUTH_TOKEN");
        $this->pagInclude = 'rest/'.$this->restService.'.rest.php';
    }
    private function preencheVariaveisDeDados(){
        $this->dados = $this->_HTTPREQUEST_POST["dados"];

        $this->HTTP_METHOD = $this->_HTTPREQUEST_SERVER['REQUEST_METHOD'];
        if("POST"==$this->HTTP_METHOD) {
          $this->dados = $this->_HTTPREQUEST_POST["dados"];
        }else if("PUT"==$this->HTTP_METHOD || "DELETE"==$this->HTTP_METHOD) {
          $this->_HTTPREQUEST_PUT = \Solves\Solves::parsePutRequest();
          $this->dados = $this->_HTTPREQUEST_PUT["dados"];
        }
        if(substr($this->_HTTPREQUEST_SERVER['CONTENT_TYPE'],0,19)=='multipart/form-data'){ 
            $this->preProcessDataFromMultipartFormData();
        }
        $this->extractData();
    }
    private function preProcessDataFromMultipartFormData(){
        if(substr($this->dados, 0, 1)=='?'){
            $this->dados = substr($this->dados, 1);
        }
        $paramsOfDados = explode('&',$this->dados);
        $query_params = array();
        foreach($paramsOfDados as $paramDados){
            $key = strstr($paramDados, '=', true);
            $val = strstr($paramDados, '=');
            if(substr($val, 0, 1)=='='){
                $val = substr($val, 1);
            }
            $query_params[$key] = urldecode($val);
        }
        $this->dados = $query_params["dados"];
        $this->token = $query_params["token"];
        $this->userData = $query_params["userData"];
        $this->perfil = $query_params["perfil"];
        $this->usuario = $query_params["usuario"];
    }
    private function extractData(){
        $this->dados = json_decode(utf8_encode($this->dados), false);
        $this->userData = json_decode(utf8_encode($this->userData), false);
        $this->perfil = json_decode(utf8_encode($this->perfil), false);
        $this->usuario = json_decode(utf8_encode($this->usuario), false);
    }
    private function processaAvatar(){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET");
        header("Access-Control-Allow-Headers: GET");
        header("Cache-control: private, max-age=0, no-cache");
        header("Access-Control-Request-Method: Cache-Control, Pragma, Authorization, Key, Access-Control-Allow-Headers, Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, HTTP_X_USER_LOGIN, HTTP_X_AUTH_TOKEN, X_USER_LOGIN, X_AUTH_TOKEN");
        header('content-type: image/png');

        $this->restService = 'avatar';
        $this->pagInclude = 'rest/'.$this->restService.'.rest.php';
    }
    private function processaFoto(){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET");
        header("Access-Control-Allow-Headers: GET");
        header("Cache-control: private, max-age=0, no-cache");
        header("Access-Control-Request-Method: Cache-Control, Pragma, Authorization, Key, Access-Control-Allow-Headers, Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, HTTP_X_USER_LOGIN, HTTP_X_AUTH_TOKEN, X_USER_LOGIN, X_AUTH_TOKEN");
        header('content-type: image/jpg');

        $this->restService = 'foto';
        $this->pagInclude = 'rest/'.$this->restService.'.rest.php';
    }


    public function getToken(){
        return $this->token;
    }
    public function getUserData(){
        return $this->userData;
    }
    public function getUsuario(){
        return $this->usuario;
    }
    public function getDados(){
        return $this->dados;
    }
    public function getPerfil(){
        return $this->perfil;
    }
    public function getRestDetails(){
        return $this->restDetails;
    }
    public function getRestService(){
        return $this->restService;
    }

    public function getPagInclude(){
        return $this->pagInclude;
    }
    public function doIncludePage(){
        $CONNECTION = null;
        try{
            if($this->isJs){
              $this->printJS();
            }else if($this->isWebManifest){
              $this->printWebManifest();
            }else{
                //WILL BE PROCESSED BY PHP FILES

                //TURNING INTO ACESSIBLE VARIABLES - EXPLICT WAY //TODO remover este trecho pois já está em SolvesRest
                    $ATUAL_URL = $this->ATUAL_URL;
                    $CANNONICAL = $this->CANNONICAL;
                    $MODO_SOON_ATIVADO = $this->MODO_SOON_ATIVADO;
                    $IS_SOON_PAGE = $this->IS_SOON_PAGE;
                    $IS_APP = $this->IS_APP;
                    $dados = $this->dados;
                    $token = $this->token;
                    $userData = $this->userData;
                    $perfil = $this->perfil;
                    $usuario = $this->usuario;
                    $restService = $this->restService;
                    $restDetails = $this->restDetails;
                    $ROUTER = $this;
                //END TURNING INTO ACESSIBLE VARIABLES

                if($this->isPageController){
                  if($this->checkIfFileExists($this->getPagInclude())) {
                    include $this->getPagInclude();
                    if($this->isRest){
                        $classe = ucwords($this->restService).'Rest';
                        if(class_exists($classe)){
                            $obj = new $classe($this); 
                            $obj->execute();
                        }
                    }
                  }else{
                    header ("HTTP/1.0 404 Not Found");
                    echo '{"router":"Página ['.$this->getPagInclude().'] não encontrada. Requisição ['.$this->requestedPage.']!"}';
                    return;
                  }
                }else{ 
                  $CONNECTION = \SolvesDAO\SolvesDAO::openConnection();  

                  if($this->incluiTopo){
                    include "includes/topo.php";
                  }else if($this->incluiTopoPublic){
                    include "includes/topoPublic.php";
                  }else if($this->incluiTopoApp){
                    include "includes/topoApp.php";
                  }else if($this->incluiTopoAppPublic){
                    include "includes/topoAppPublic.php";
                  }else if($this->IS_SOON_PAGE){
                    include "includes/topo_soon.php";
                  }else{
                    include "includes/cabecalho.php";
                  }

                  //MAIN INCLUSION
                  include $this->getPagInclude();

                  if($this->incluiTopo){
                    include "includes/rodape.php";
                  }else if($this->incluiTopoPublic){
                    include "includes/rodapePublic.php";
                  }else if($this->incluiTopoApp){
                    include "includes/rodapeApp.php";
                  }else if($this->incluiTopoAppPublic){
                    include "includes/rodapeAppPublic.php";
                  }else if($this->IS_SOON_PAGE){
                    include "includes/rodape_soon.php";
                  }else{
                    include "includes/includes_js.php";
                  }

                  //FECHA A CONEXãO COM O BANCO
                  \SolvesDAO\SolvesDAO::closeConnection($CONNECTION);
                }
            }
        } catch (Exception $e) {
            //FECHA A CONEXãO COM O BANCO
            \SolvesDAO\SolvesDAO::closeConnection($CONNECTION);            
            header("HTTP/1.1 500 Internal Server Error");
        }
    }
    private function printJS(){
        header("Content-Type: text/javascript");
        if($this->checkIfFileExists($this->pagInclude)) {
            readfile(\Solves\Solves::getVendorInsideNavs().$this->pagInclude);
        }else if($this->isServiceWorkerFile){ 
            echo \SolvesUi\SolvesServiceWorker::getScript();
        }else if($this->isServiceWorkerRegisterFile){ 
            echo \SolvesUi\SolvesServiceWorkerRegister::getScript();
        }else if($this->isConfigJsFile){ 
            echo \SolvesUi\SolvesConfigJS::getScript();
        }else{
            header ("HTTP/1.0 404 Not Found");
            return;
        }
    }
    private function printWebManifest(){
          header('Content-Type: application/json');
          if($this->checkIfFileExists($this->pagInclude)) {
            readfile(\Solves\Solves::getVendorInsideNavs().$this->pagInclude);
          }else{
            echo \SolvesUi\SolvesWebmanifest::getManifest();
          }
    }
    private function checkIfFileExists($f){
        return file_exists($f);
    }
    private function doReadFile($f){
        return $this->readfile($f);
    }


    public function getHttpMethod(){
        return $this->HTTP_METHOD;
    }
    public function getHttpRequestServer(){
        return $this->_HTTPREQUEST_SERVER;
    }
    public function getHttpRequestPost(){
        return $this->_HTTPREQUEST_POST;
    }
    public function getHttpRequestGet(){
        return $this->_HTTPREQUEST_GET;
    }
    public function getHttpRequestPut(){
        return $this->_HTTPREQUEST_PUT;
    }
    public function getHttpRequestDelete(){
        return $this->_HTTPREQUEST_DELETE;
    }
}