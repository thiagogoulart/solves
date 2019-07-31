<?php
try{ 
$navInside = '';
$navInsideConfs = '';
$isPageController = false;
$ATUAL_URL='';
$MODO_SOON_ATIVADO = \Solves\Solves::isModoSoon();
$incluiTopo = true;
$incluiTopoPublic = false;
$incluiTopoApp = false;
$isPageController = false;
$IS_SOON_PAGE = false;
$isJs = false;
$isServiceWorkerFile = false;
$isServiceWorkerRegisterFile = false;
$requestedPage = $_GET['p'];
if(isset($requestedPage) && strlen($requestedPage)>1 && strlen($requestedPage)<100){
  if(substr_compare($requestedPage, 'processupload.php', -strlen('processupload.php')) === 0){
    $requestedPage = 'rest/upload';
  }
  if (strpos($requestedPage, 'rest/')===0 || strpos($requestedPage, 'avatar/')===0 ||  strpos($requestedPage, 'thumb/')===0 || strpos($requestedPage, 'perfil/')===0 || strpos($requestedPage, 'file/')===0) {
    $isPageController = true;
    try{ 
        /*************** REGRA DO CONTROLLER  ************************/
        $isRest = false;
        if(isset($_GET['p']) && strlen($_GET['p'])>1){
            if(substr($_GET['p'], 0, 5)=='rest/'){
                $isRest = true;
                $requisicaoRest = substr($_GET['p'], 5, strlen($_GET['p'])-5);
                $restService =  ((strpos($requisicaoRest, '/')>0)?substr($requisicaoRest, 0, strpos($requisicaoRest, '/')):$requisicaoRest);
                $pos_startDetails=strlen($restService)+1;
                $restDetails = substr($requisicaoRest, $pos_startDetails, strlen($requisicaoRest)-$pos_startDetails-(substr($requisicaoRest, -1)=='/'?1:0));
                $file_array= null;
                $dados = $_POST["dados"];
                if(substr($_SERVER['CONTENT_TYPE'],0,19)=='multipart/form-data'){ 
                    if(substr($dados, 0, 1)=='?'){
                        $dados = substr($dados, 1);
                    }
                    $paramsOfDados = explode('&',$dados);
                    $query_params = array();
                    foreach($paramsOfDados as $paramDados){
                        $key = strstr($paramDados, '=', true);
                        $val = strstr($paramDados, '=');
                        if(substr($val, 0, 1)=='='){
                            $val = substr($val, 1);
                        }
                        $query_params[$key] = urldecode($val);
                    }
                    $dados = $query_params["dados"];
                    $token = $query_params["token"];
                    $userData = $query_params["userData"];
                    $perfil = $query_params["perfil"];
                    $usuario = $query_params["usuario"];
                }
                $dados = json_decode(utf8_encode($dados), false);
                $userData = json_decode(utf8_encode($userData), false);
                $perfil = json_decode(utf8_encode($perfil), false);
                $usuario = json_decode(utf8_encode($usuario), false);
                $method = $_SERVER['REQUEST_METHOD'];
                
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Methods: GET, POST");
                header("Access-Control-Allow-Headers: GET, POST");
                header("Access-Control-Request-Method: Cache-Control, Pragma, Authorization, Key, Access-Control-Allow-Headers, Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, HTTP_X_USER_LOGIN, HTTP_X_AUTH_TOKEN, X_USER_LOGIN, X_AUTH_TOKEN");
                include 'rest/'.$restService.'.rest.php';
            }
        }
        //Fecha conexão com a base
        $CONNECTION = \SolvesDAO\SolvesDAO::closeConnection($CONNECTION);
    } catch (Exception $e) {
        //var_dump($e);
        //FECHA A CONEXãO COM O BANCO
        \SolvesDAO\SolvesDAO::closeConnection($CONNECTION);
        header("HTTP/1.1 500 Internal Server Error");
    }
  }else if(substr($requestedPage, 0, 5)=='sw.js'){
    $isServiceWorkerFile = true;
    $isJs = true;
    $pagInclude= '/sw.js';
  }else if(substr($requestedPage, 0, 14)=='sw_register.js'){
    $isServiceWorkerRegisterFile = true;
    $isJs = true;
    $pagInclude= '/sw_register.js';
  }else if(substr($requestedPage, 0, 4)=='soon'){
    //SOON PAGES
    $IS_SOON_PAGE = true;
    $incluiTopo = false;
    $incluiTopoPublic = false;
    $ATUAL_URL = \Solves\Solves::getUrlName('',$requestedPage, false);
    $p = 'views/'.$ATUAL_URL.'.php';
    if(file_exists($p)) {
      $pagInclude= $p;
    }else{
      $pagInclude= 'views/soon.php';
    }
  }else if(substr($requestedPage, 0, 3)=='pre'){
    //SOON PAGES
    $IS_SOON_PAGE = true;
    $incluiTopo = false;
    $incluiTopoPublic = false;
    $ATUAL_URL = \Solves\Solves::getUrlName('',$requestedPage, false);
    $p = 'views/cadastro.php';
    if(file_exists($p)) {
      $pagInclude= $p;
    }else{
      $pagInclude= 'views/soon.php';
    }
  }else{
    $incluiTopo = false;
    $incluiTopoPublic = true;
    $ATUAL_URL = \Solves\Solves::getUrlName('',$requestedPage, false);
    $p = 'views/'.$ATUAL_URL.'.php';
    if(file_exists($p)) {
      $pagInclude= $p;
    }else{
      $pagInclude= 'views/404.php';
    }
  }
}else{
  $incluiTopo = false;
  $incluiTopoPublic = true;
  $pagInclude= 'views/index.php';
}

//POR DEFAULT VAI PARA SOON
if($MODO_SOON_ATIVADO && !$isPageController && !$IS_SOON_PAGE){ 
  $IS_SOON_PAGE = true;
  $incluiTopo = false;
  $incluiTopoPublic = false;
  if($ATUAL_URL=='meu_perfil' || $ATUAL_URL=='termo_uso' || $ATUAL_URL=='termo_privacidade'){
    $pagInclude= 'views/'.$ATUAL_URL.'.php';
    if(!file_exists($pagInclude)) {
      $pagInclude= 'views/soon.php';
    }
  }else{
    $pagInclude= 'views/soon.php';
  }
}
//END SOON
if($isJs){
  header("Content-Type: text/javascript");
  if(file_exists($pagInclude)) {
    readfile($pagInclude);
  }else if($isServiceWorkerFile){ 
    echo \SolvesUi\SolvesServiceWorker::getScript();
  }else if($isServiceWorkerRegisterFile){ 
    echo \SolvesUi\SolvesServiceWorkerRegister::getScript();
  }else{
    header ("HTTP/1.0 404 Not Found");
    return;
  }
}else if($isPageController){
  include $pagInclude;
}else{ 
  $CONNECTION = \SolvesDAO\SolvesDAO::openConnection();
  $CANNONICAL = \Solves\Solves::getSiteUrl().$ATUAL_URL;
  if($incluiTopo){
    include "includes/topo.php";
  }else if($incluiTopoPublic){
    include "includes/topoPublic.php";
  }else if($incluiTopoApp){
    $IS_APP = true;
    include "includes/topoApp.php";
  }else if($IS_SOON_PAGE){
    include "includes/topo_soon.php";
  }else{
    include "includes/cabecalho.php";
  }
  include $pagInclude;

  if($incluiTopo){
    include "includes/rodape.php";
  }else if($incluiTopoPublic){
    include "includes/rodapePublic.php";
  }else if($incluiTopoApp){
    include "includes/rodapeApp.php";
  }else if($IS_SOON_PAGE){
    include "includes/rodape_soon.php";
  }else{
    include "includes/includes_js.php";
  }
  
}
//FECHA A CONEXãO COM O BANCO
    \SolvesDAO\SolvesDAO::closeConnection($CONNECTION);

} catch (Exception $e) {
  //var_dump($e);
    //FECHA A CONEXãO COM O BANCO
    \SolvesDAO\SolvesDAO::closeConnection($CONNECTION);
    
    header("HTTP/1.1 500 Internal Server Error");
}
?>