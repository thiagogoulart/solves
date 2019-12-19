<?php
//PUBLIC GLOBAL FUNCTIONS
function getEmpresaId(){
    return \Solves\SolvesConf::getSolvesConfIdentificacao()->getDefaultEmpresaId();
}
function getUsuarioId(){
    return \Solves\SolvesConf::getSolvesConfIdentificacao()->getDefaultSistemaUsuarioId();
}

function getUrlResponsiveFileManagerSelectImage($fieldElmId, $isMultiple=true){
  return \Solves\SolvesConf::getSolvesConfResponsiveFileManager()->getUrlSelectImage($fieldElmId, $isMultiple);
}
function getUrlResponsiveFileManagerSelectFile($fieldElmId, $isMultiple=true){
  return \Solves\SolvesConf::getSolvesConfResponsiveFileManager()->getUrlSelectFile($fieldElmId, $isMultiple);
}
function getUrlResponsiveFileManagerSelectAll($fieldElmId, $isMultiple=true){
  return \Solves\SolvesConf::getSolvesConfResponsiveFileManager()->getUrlSelectAll($fieldElmId, $isMultiple);
}
function includeJsModel($fileName){
  return '<script src="'.\Solves\SolvesConf::getSolvesConfUrls()->getJsModel().($fileName).'" defer></script>
  ';
}
function includeJsRest($fileName){
  return '<script src="'.\Solves\SolvesConf::getSolvesConfUrls()->getJsRest().($fileName).'" defer></script>
  ';
}
function includeJsView($fileName){
  return '<script src="'.\Solves\SolvesConf::getSolvesConfUrls()->getJsView().($fileName).'" defer></script>
  ';
}

//INCLUDE PAGE
  $ROUTER = new \Solves\SolvesRouter(@$_SERVER, @$_POST, @$_GET, @$_PUT, @$_DELETE, @$_FILES, @$navInside);
  $ROUTER->doIncludePage();
?>