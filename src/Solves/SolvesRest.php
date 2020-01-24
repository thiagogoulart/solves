<?php
/*
Autor:  Thiago Goulart.
Data de criação: 12/09/2019
*/

namespace Solves;

abstract class SolvesRest {

    protected $CONNECTION;
    protected $CONNECTIONS=[];
    protected $router;
    protected $restrito=false;
    protected $restritoTipo=null;
    protected $publicMethods=array();

    protected $msg;
    protected $erro=false;
    protected $logoff=false;

    protected $noJson=false;
    protected $predefinedJson=false;
    protected $json='';
    protected $jsonDados='';
    protected $jsonObjeto='';

    protected $user;
    protected $token;

    protected $classeObject;
    protected $id;
    protected $hasId;
    protected $object;
    protected $pkName = '';

    //HTTP 
    public $HTTP_METHOD;
    public $SERVER;
    public $POST;
    public $GET;
    public $PUT;
    public $DELETE;

    public function __construct($router, $mainClass, $restrito=false, $restritoTipo=null) {
        $this->router = $router;
        $this->restrito = $restrito;
        $this->restritoTipo = $restritoTipo;
        $this->classeObject = $mainClass;

        $this->fillObject();

        $this->HTTP_METHOD = $this->router->getHttpMethod();
        $this->SERVER = $this->router->getHttpRequestServer();
        $this->POST = $this->router->getHttpRequestPost();
        $this->GET = $this->router->getHttpRequestGet();
        $this->PUT = $this->router->getHttpRequestPut();
        $this->DELETE = $this->router->getHttpRequestDelete();
        $this->FILES = $this->router->getHttpRequestFiles();
    }
    protected function fillObject(){
        if(\Solves\Solves::isNotBlank($this->classeObject)){
            $this->id = $this->getDadoInt('id');
            $this->hasId = (@isset($this->id));
            $this->object = new $this->classeObject($this->getConnection());
            if($this->hasId){
                $this->object = $this->object->findById($this->id);
                $this->hasId = (@isset($this->object));
            }
        }
    }

    public abstract function preAction();
    public abstract function posAction();
    public abstract function index();
    public abstract function save();
    public abstract function update();

    public function delete(){
        if($this->hasId){
            if($this->object->remove()){
                $this->setExcluidoComSucesso();
            }else{
                $this->setError('Não foi possível excluir o registro.');
            }
        }else{
            $this->setError('Não foi possível excluir o registro. Falta identificador.');
        }
    }

    public function findById(){
        if($this->hasId){
            $arr = $this->object->toArray();
            if(isset($arr) && count($arr)>0){
                $jsonObj = \Solves\SolvesJson::getJsonByArrayItemFromDao($arr, $this->getPkName(), true);
                $this->setResultObjeto($jsonObj);
            }else{
                $this->setObjetoNaoEncontrado();
            }
        }else{
            $this->setObjetoNaoEncontrado("Nenhum resultado encontrado. Faltam parâmetros para a busca.");
        }
    }

    public function findMain(){
        $classe = $this->classeObject;
        $p = new $classe($this->getConnection());
        $arr = $p->findArrayAll($this->getUser()->getId());
        if(isset($arr) && count($arr)>0){
            $json = \Solves\SolvesJson::arrayFromDaoToJson($arr, $this->getPkName());
            $this->setResultDados($json);
        }else{
            $this->setResultadoNaoEncontrado();
        }
    }
    public function findByFiltros(){
        $classe = $this->classeObject;
        $p = new $classe($this->getConnection());
        $dados = $this->router->getDados();
        $arr = $p->findArrayByFiltros($this->getUser()->getId(), $dados);
        if(isset($arr) && count($arr)>0){
            $json = \Solves\SolvesJson::arrayFromDaoToJson($arr, $this->getPkName());
            $this->setResultDados($json);
        }else{
            $this->setResultadoNaoEncontrado();
        }
    }

    public function getPkName(){return $this->pkName;}
    public function setPkName($p){$this->pkName = $p;}

    public function getDados(){
        return $this->router->getDados();
    }
    public function getDado(string $property, bool $obrigatorio=false, string $label=''){
        $value = null;
        $dados = $this->router->getDados();
        $value = \Solves\SolvesJson::getPropertyData($dados, $property, $obrigatorio, $label);
        return $value;
    }
    public function getDadoInt(string $property, bool $obrigatorio=false, string $label=''){
        $value = null;
        $dado = $this->getDado($property, $obrigatorio, $label);
        if($dado!=null){
            $value = @\Solves\Solves::getIntValue($dado);
        }
        if($obrigatorio && !\Solves\Solves::isNotBlank($value)){
            $label = (\Solves\Solves::isNotBlank($label) ? $label : $property);
            throw new \Exception("Informe o campo obrigatório '".$label."'. Valor está em branco ou fora do padrão.");
        }
        return $value;
    }
    public function getDadoDouble(string $property, bool $obrigatorio=false, string $label=''){
        $value = null;
        $dado = $this->getDado($property, $obrigatorio, $label);
        if($dado!=null){
            $value = @\Solves\Solves::getDoubleValue($dado);
        }
        if($obrigatorio && !\Solves\Solves::isNotBlank($value)){
            $label = (\Solves\Solves::isNotBlank($label) ? $label : $property);
            throw new \Exception("Informe o campo obrigatório '".$label."'. Valor está em branco ou fora do padrão.");
        }
        return $value;
    }
    public function getDadoDate(string $property, bool $obrigatorio=false, string $label=''){
        $value = null;
        $dado = $this->getDado($property, $obrigatorio, $label);
        if($dado!=null){
            $value = @\Solves\SolvesTime::getDateFormated($dado);
        }
        if($obrigatorio && !\Solves\Solves::isNotBlank($value)){
            $label = (\Solves\Solves::isNotBlank($label) ? $label : $property);
            throw new \Exception("Informe o campo obrigatório '".$label."'. Valor está em branco ou fora do padrão.");
        }
        return $value;
    }
    public function getDadoBoolean(string $property, bool $obrigatorio=false, string $label='', $defaultValue=null){
        $value = null;
        $dado = $this->getDado($property, $obrigatorio, $label);
        if($dado!=null){
            $value = @\Solves\Solves::checkBoolean($dado);
        }
        if(!\Solves\Solves::isNotBlank($value)){
            if(null!=$defaultValue){
                $value = $defaultValue;
            }else if($obrigatorio){
                $label = (\Solves\Solves::isNotBlank($label) ? $label : $property);
                throw new \Exception("Informe o campo obrigatório '".$label."'. Valor está em branco ou fora do padrão.");
            }
        }
        return $value;
    }
    public function getDadoEmail(string $property, bool $obrigatorio=false, string $label=''){
        $value = null;
        $dado = $this->getDado($property, $obrigatorio, $label);
        if($dado!=null){
            $value = strtolower(\Solves\Solves::removeEspacos($dado));
        }
        if($obrigatorio && !\Solves\Solves::isNotBlank($value)){
            $label = (\Solves\Solves::isNotBlank($label) ? $label : $property);
            throw new \Exception("Informe o campo obrigatório '".$label."'. Valor está em branco ou fora do padrão.");
        }
        return $value;
    }
    public function getDadoUrl(string $property, bool $obrigatorio=false, string $label=''){
        $value = null;
        $dado = $this->getDado($property, $obrigatorio, $label);
        if($dado!=null){
            $value = (\Solves\Solves::removeEspacos($dado));
        }
        if($obrigatorio && !\Solves\Solves::isNotBlank($value)){
            $label = (\Solves\Solves::isNotBlank($label) ? $label : $property);
            throw new \Exception("Informe o campo obrigatório '".$label."'. Valor está em branco ou fora do padrão.");
        }
        return $value;
    }
    protected function setError($msg){
        $this->setResultDados('',$msg);
        $this->erro = true;
        $this->msg = $msg;
    }
    protected function setSalvoComSucesso($msg="Salvo com sucesso!"){
        $this->setResultDados('',$msg);
        $this->erro = false;
        $this->msg = $msg;
    }
    protected function setAlteradoComSucesso($msg="Alterado com sucesso!"){
        $this->setResultDados('',$msg);
        $this->erro = false;
        $this->msg = $msg;
    }
    protected function setExcluidoComSucesso($msg="Excluído com sucesso!"){
        $this->setResultDados('',$msg);
        $this->erro = false;
        $this->msg = $msg;
    }
    protected function setResultadoNaoEncontrado($msg="Nenhum resultado encontrado."){
        $this->setResultDados('',$msg);
        $this->erro = false;
        $this->msg = $msg;
    }
    protected function setObjetoNaoEncontrado($msg="Nenhum resultado encontrado."){
        $this->setResultDados('');
        $this->setResultObjeto('');
        $this->setResultadoNaoEncontrado($msg);
    }
    protected function setResultDados($json, $msg='Resultado da busca encontrado com sucesso!'){
        $this->jsonDados = $json;
        $this->msg = $msg;
    }
    protected function setResultObjeto($json, $msg='Resultado da busca encontrado com sucesso!'){
        $this->jsonObjeto = $json;
        $this->jsonDados = '{"objeto":'.((isset($this->jsonObjeto) && strlen($this->jsonObjeto)>0) ? $this->jsonObjeto : "{}").'}';
        $this->msg = $msg;
    }
    protected function setResultMultiplos($arrJson, $msg='Resultado da busca encontrado com sucesso!'){
        $this->jsonObjeto = '';
        $i=0;
        $this->jsonDados = '{';
        foreach($arrJson as $key=>$valueJson){
            if($i>0){
                $this->jsonDados.=', ';
            }
            $this->jsonDados.= '"'.$key.'":'.((isset($valueJson) && strlen($valueJson)>0) ? $valueJson : "{}");
            $i++;
        }
        $this->jsonDados .= '}';
        $this->msg = $msg;
    }
    protected function setJson($json){
        $this->json = $json;
        $this->predefinedJson = true;
    }
    protected function setNoJson(){
        $this->noJson = true;
    }
    protected function setToken($t){
        $this->token = $t;
    }
    protected function getUser(){
        return $this->user;
    }
    protected function getConnection(?string $connectionName=null){
        if(null==$connectionName){
            $connectionName = \SolvesDAO\SolvesDAO::DEFAULT_CONNECTION_NAME;
        }
        $this->CONNECTION = $this->CONNECTIONS[$connectionName];
        if($this->CONNECTION==null){
            $this->CONNECTION = \SolvesDAO\SolvesDAO::openConnection($connectionName);
            if($this->router->IS_APP){
                $this->CONNECTION->setIsApp();
            }
            $this->CONNECTIONS[$connectionName] = $this->CONNECTION;
        }
        return $this->CONNECTION;
    }
    protected function closeConnection(?string $connectionName=null){
        if(null==$connectionName){
            $connectionName = \SolvesDAO\SolvesDAO::DEFAULT_CONNECTION_NAME;
        }
        $this->CONNECTION = $this->CONNECTIONS[$connectionName];
        if($this->CONNECTION!=null){
            $this->CONNECTION = \SolvesDAO\SolvesDAO::closeConnection($this->CONNECTION);
            $this->CONNECTION = null;
            unset($this->CONNECTIONS[$connectionName]);
        }
    }
    protected function isLogado(?string $connectionName=null){
        if($this->user==null){
            $this->user = \SolvesAuth\SolvesAuth::checkToken($this->getConnection($connectionName), $this->router->getToken(), $this->router->getUserData());
        }
        return (isset($this->user) && $this->user->getId()>0);
    }
    protected function atualizaUsuarioLogado(?string $connectionName=null){
        $this->user = \SolvesAuth\SolvesAuth::checkToken($this->getConnection($connectionName), $this->router->getToken(), $this->router->getUserData());
    }
    protected function isRestritoTipo(){
        if($this->restritoTipo!=null && $this->user!=null){
            return (strtolower($this->user->getClassName())==strtolower($this->restritoTipo));
        }
        return true;
    }
    private function getJsonUserLogado(){
        $clss = get_class ($this->user);
        return \Solves\SolvesJson::getJsonByArrayItemFromDao($this->user->toArray(), $clss::$PK, true);
    }
    private function getJson(){
        if($this->predefinedJson){
            return $this->json;
        }else{
            $this->json = '{';
            $hasItem=false;
            if(!$this->isAutorizado()){
                $hasItem = true;
                $this->erro = true;
                $this->msg = 'Erro: Requisição não autorizada.';
                $this->json .= '"logoff":true';
            }else if($this->isLogado()){
                $hasItem = true;
                $this->json .= '"usuario_logado":'.$this->getJsonUserLogado().'';
            }
            if(!\Solves\Solves::isNotBlank($this->msg)){
                $this->msg = '""';
            }else{
                $this->msg = \Solves\SolvesJson::escape_query_com_aspas($this->msg, true);
            }
            $this->json .= ($hasItem?',':'').'"msg":'.$this->msg.'';
            $this->json .= ',"error":"'.($this->erro ? 'true':'false').'"';
            $this->json .= ', "dados":'.((isset($this->jsonDados) && strlen($this->jsonDados)>0) ? $this->jsonDados : "[]");
            if(\Solves\Solves::isNotBlank($this->token)){
                $this->json .= ',"token":'.$this->token.'';
            }
            $this->json .='}';
            return $this->json;
        }
    }
    protected function setPublicMethods($arr){
        $this->publicMethods = $arr;
    }
    protected function isPublicMethod(){
        return in_array($this->router->getRestDetails(), $this->publicMethods);
    }
    protected function isAutorizado(){
        return (!$this->restrito || $this->isPublicMethod() || ($this->isLogado() && $this->isRestritoTipo()));
    }
    public function execute(){
        $retorno = null;
        try {
            //OPEN connection
            $this->CONNECTION = $this->getConnection();
            if($this->isAutorizado()){
                //TURNING INTO ACESSIBLE VARIABLES - EXPLICT WAY
                $ATUAL_URL = $this->router->ATUAL_URL;
                $CANNONICAL = $this->router->CANNONICAL;
                $MODO_SOON_ATIVADO = $this->router->MODO_SOON_ATIVADO;
                $IS_SOON_PAGE = $this->router->IS_SOON_PAGE;
                $IS_APP = $this->router->IS_APP;
                $dados = $this->router->getDados();
                $token = $this->router->getToken();
                $userData = $this->router->getUserData();
                $perfil = $this->router->getPerfil();
                $usuario = $this->router->getUsuario();
                $restService = $this->router->getRestService();
                $restDetails = $this->router->getRestDetails();
                $ROUTER = $this->router;
                //END TURNING INTO ACESSIBLE VARIABLES

                $this->preAction();

                //Metodo solicitado
                if(\Solves\Solves::isNotBlank($restDetails) && method_exists($this, $restDetails)){
                    $retorno = eval('$this->'.$restDetails.'();');
                }else{
                    $retorno = $this->index();
                }

                $this->posAction();

            }
            if($this->isPendenteCommit()){
                $this->commitTransaction();
            }
            //Fecha conexão com a base
            $this->closeConnection();
        } catch (\Exception $e) {
            //FECHA A CONEXãO COM O BANCO
            $this->rollbackTransaction();
            $this->closeConnection();
            $this->setError($e->getMessage());
        }
        if(!$this->noJson){
            echo $this->getJson();
        }else{
            return $retorno;
        }
    }
    protected function setCommitManual(){
        if($this->CONNECTION!=null){
            $this->CONNECTION->setCommitManual();
        }
    }
    protected function rollbackTransaction(){
        if($this->CONNECTION!=null){
            $this->CONNECTION->rollbackTransaction();
        }
    }
    protected function commitTransaction(){
        if($this->CONNECTION!=null){
            return $this->CONNECTION->commit();
        }
        return false;
    }
    protected function isPendenteCommit(){
        if($this->CONNECTION!=null){
            return $this->CONNECTION->isTransactionOpened();
        }
        return false;
    }
    protected function getErrorMsg(){
        return $this->CONNECTION->error;
    }
}
?>