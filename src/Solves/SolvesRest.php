<?php
/*
Autor:  Thiago Goulart.
Data de criação: 12/09/2019
*/

namespace Solves;

abstract class SolvesRest {

	protected $CONNECTION;
	protected $router;
	protected $restrito=false;
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

    //HTTP 
    public $HTTP_METHOD;
    public $SERVER;
    public $POST;   
    public $GET;   
    public $PUT;   
    public $DELETE;

	public function __construct($router, $mainClass, $restrito=false) {
		$this->router = $router;
		$this->restrito = $restrito;
		$this->classeObject = $mainClass;

		$this->fillObject();

		$this->HTTP_METHOD = $this->router->getHttpMethod();
	    $this->SERVER = $this->router->getHttpRequestServer();
	    $this->POST = $this->router->getHttpRequestPost();   
	    $this->GET = $this->router->getHttpRequestGet();   
	    $this->PUT = $this->router->getHttpRequestPut();   
	    $this->DELETE = $this->router->getHttpRequestDelete();
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
	public abstract function delete();

	public function getDados(){
		return $this->router->getDados();
	}
	public function getDado($property){
		$dados = $this->router->getDados();
		if($dados!=null && property_exists($dados, $property)){
			return @\Solves\SolvesJson::getJsonFieldValue($dados->{$property});
		}
		return null;
	}
	public function getDadoInt($property){
		$dado = $this->getDado($property);
		if($dado!=null){
			return @\Solves\Solves::getIntValue($dado);
		}
		return null;
	}
	public function getDadoDouble($property){
		$dado = $this->getDado($property);
		if($dado!=null){
			return @\Solves\Solves::getDoubleValue($dado);
		}
		return null;
	}
	public function getDadoDate($property){
		$dado = $this->getDado($property);
		if($dado!=null){
			return @\Solves\SolvesTime::getDateFormated($dado);
		}
		return null;
	}
	public function getDadoBoolean($property){
		$dado = $this->getDado($property);
		if($dado!=null){
			return @\Solves\Solves::checkBoolean($dado);
		}
		return false;
	} 
	public function getDadoEmail($property){
		$dado = $this->getDado($property);
		if($dado!=null){
			return strtolower(\Solves\Solves::removeEspacos($dado));
		}
		return null;
	}
	public function getDadoUrl($property){
		$dado = $this->getDado($property);
		if($dado!=null){
			return (\Solves\Solves::removeEspacos($dado));
		}
		return null;
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
	protected function getConnection(){
		if($this->CONNECTION==null){
			$this->CONNECTION = \SolvesDAO\SolvesDAO::openConnection();
		}
		return $this->CONNECTION;
	}
	protected function closeConnection(){
		if($this->CONNECTION!=null){
			$this->CONNECTION = \SolvesDAO\SolvesDAO::closeConnection($this->CONNECTION);
			$this->CONNECTION = null;
		}
	}
	protected function isLogado(){
		if($this->user==null){
			$this->user = \SolvesAuth\SolvesAuth::checkToken($this->getConnection(), $this->router->getToken(), $this->router->getUserData());
		}
    	return (isset($this->user) && $this->user->getId()>0);
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
				$this->msg = '';
			}
			$this->json .= ($hasItem?',':'').'"msg":"'.$this->msg.'"';
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
		return (!$this->restrito || $this->isLogado() || $this->isPublicMethod());
	}
	public function execute(){
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
	            	eval('$this->'.$restDetails.'();');
	            }else{
	            	$this->index();
	            }

                $this->posAction();

			}
			if($this->isPendenteCommit()){
				$this->commitTransaction();
			}
		 	//Fecha conexão com a base
		    $this->closeConnection();
		} catch (Exception $e) {
			//FECHA A CONEXãO COM O BANCO
			$this->rollbackTransaction();
		    $this->closeConnection();
		    $this->setError($e->getMessage());
		}
		if(!$this->noJson){
			echo $this->getJson();
		}
	}
	protected function setCommitManual(){
		if($this->connection!=null){
			$this->connection->setCommitManual();
		}
	}
	protected function rollbackTransaction(){
		if($this->connection!=null){
			$this->connection->rollbackTransaction();
		}
	}
	protected function commitTransaction(){
		if($this->connection!=null){
			return $this->connection->commit();
		}
		return false;
	}
	protected function isPendenteCommit(){
		if($this->connection!=null){
			return $this->connection->isTransactionOpened();
		}
		return false;
	}
}
?>