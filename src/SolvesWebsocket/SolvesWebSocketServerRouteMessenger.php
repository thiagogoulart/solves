<?php
namespace SolvesWebsocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * Class SolvesWebSocketServerRouteMessenger
 * @package SolvesWebsocket
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 06/01/2020
 *
 * Classe abstrata para um endpoint de websocket, ou seja, uma classe que vai receber conexõs de websocket pode implementá-la. Esta classe é específica para troca de mensagens, como chat.
 */
abstract class SolvesWebSocketServerRouteMessenger extends SolvesWebSocketServerRoute implements MessageComponentInterface {

    /**
     * SolvesWebSocketServerRouteMessenger constructor.
     * @param string $name
     * @param string $path
     */
    public function __construct(string $name, string $path) {
        parent::__construct($name, $path);
    }

    /**
     * @param ConnectionInterface $from
     * @param $token
     * @param $userData
     * @param $restName
     * @param $methodName
     * @param $receiverId
     * @param $msg
     * @param $msgId
     * @return mixed
     *
     * Quando uma nova mensagem é recebida, este método é chamado.
     */
    public abstract function onEventMessage(\Ratchet\ConnectionInterface $from, string $token, string $userData, ?string $restName, $methodName, $receiverId, $msg, $msgId);

    /**
     * @param ConnectionInterface $conn
     * @return mixed
     *
     * Quando uma nova conexão de cliente é aberta, este método é chamado.
     */
    public abstract function onEventOpen(\Ratchet\ConnectionInterface $conn);

    /**
     * @param ConnectionInterface $conn
     * @return mixed
     * Quando uma conexão de cliente é encerrada, este método é chamado.
     */
    public abstract function onEventClose(\Ratchet\ConnectionInterface $conn);

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     * @return mixed
     * Quando ocorre algum erro na comunicação de websocket, este método é chamado.
     */
    public abstract function onEventError(\Ratchet\ConnectionInterface $conn, \Exception $e);

    /**
     * @param string $rest
     * @param string $method
     * @param string $token
     * @param string $userData
     * @param $dados
     * @param string $prefixDir
     * @return |null
     * Instancia um objeto rest e executa o método cahamdo com base nos parâmetros de nome do rest e de seu método. Só irá executar se o arquivo do rest for encontrado e a classe e método existirem.
     */
    protected function executeRest(string $rest, string $method, string $token, string $userData, $dados, string $prefixDir=''){
        if(null!=$rest){
            $url = $prefixDir.'rest/'.$rest.'/'.$method;
            $_HTTPREQUEST_SERVER = null;
            $_HTTPREQUEST_PUT=null;
            $_HTTPREQUEST_DELETE=null;
            $_HTTPREQUEST_FILES=null;
            $_HTTPREQUEST_POST=array();
            $_HTTPREQUEST_GET=array();

            $_HTTPREQUEST_GET['p'] = $url;
            $_HTTPREQUEST_POST['dados'] = 'token='.$token.'&dados='.json_encode($dados);

            $router = new \Solves\SolvesRouter($_HTTPREQUEST_SERVER, $_HTTPREQUEST_POST, $_HTTPREQUEST_GET, $_HTTPREQUEST_PUT, $_HTTPREQUEST_DELETE, $_HTTPREQUEST_FILES, '', true);
            $router->setToken($token);
            $router->setUserData($userData);

            $fileInclude = $router->getPagInclude();

            if(file_exists($fileInclude)) {
                include $fileInclude;

                $obj = new $rest($router);
                $obj->setNoJson();
                return $obj->execute();
            }else{
                return null;
            }
        }
    }

    /**
     * @param ConnectionInterface $from
     * @param $dataReceived
     * @throws \Exception
     * Trata o recebimento de mensagens dos clientes, obtém dados da mensagem conforme padrão definido e então delega para o método onEventMessage.
     */
    public function onMessage(\Ratchet\ConnectionInterface $from, $dataReceived){
        $json = json_decode(utf8_encode($dataReceived), false);
        $msg = $json->dados;

        $token = \Solves\SolvesJson::getPropertyData($json, 'token', true, 'Token');
        $userData = \Solves\SolvesJson::getPropertyData($json, 'userData', true, 'userData');
        $rest = \Solves\SolvesJson::getPropertyData($json, 'rest', false, 'Rest');
        $rest_method = \Solves\SolvesJson::getPropertyData($json, 'rest_method', false, 'RestMethod');
        $receiver_id = \Solves\SolvesJson::getPropertyData($json, 'receiver_id', false, 'receiver_id');
        $msg_id = \Solves\SolvesJson::getPropertyData($json, 'msg_id', false, 'msg_id');
        $this->onEventMessage($from, $token, $userData, $rest, $rest_method, $receiver_id, $msg, $msg_id);
    }

    /**
     * @param ConnectionInterface $conn
     * Trata a abertura de novas conexões clientes, realiza prcocessamento padrão, chamando registraNovaConexao, e então delega para o método onEventOpen, para a classe que implementa possa fazer seus trataments específicos.
     */
    public function onOpen(\Ratchet\ConnectionInterface $conn) {
        $this->registraNovaConexao($conn);
        $this->onEventOpen($conn);
    }

    /**
     * @param ConnectionInterface $conn
     * Trata o fechamento de conexões clientes, realiza prcocessamento padrão, chamando fechaConexao, e então delega para o método onEventClose, para a classe que implementa possa fazer seus trataments específicos
     */
    public function onClose(\Ratchet\ConnectionInterface $conn) {
        $this->fechaConexao($conn);
        $this->onEventClose($conn);
    }

    /**
     * @param ConnectionInterface $conn Conexão cliente do remetente
     * @param \Exception $e
     * Trata os erros que ocorreram no processamento do websocket, realiza prcocessamento padrão, fechando a conexão, e então delega para o método onEventError, para a classe que implementa possa fazer seus trataments específicos
     */
    public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e) {
        $this->onEventError($conn, $e);
    }

    /**
     * @param ConnectionInterface $from Conexão cliente do remetente
     * @param string $msg Mensagem a ser enviada
     * @param bool $enviaAoRemetenteTambem Deve ser true caso queira que o remetente também receba a mensagem
     * Envia mensagem a todas as conexões de cliente ativas. Transformará a mensagem string recebida em json para poder enviar à conexão cliente
     */
    public function sendMsgToAll(\Ratchet\ConnectionInterface $from, string $msg, $enviaAoRemetenteTambem = false){
        $json = $this->getJsonObjectMessage($from, $msg);
        $this->sendToAll($from, $json, $enviaAoRemetenteTambem);
    }

    /**
     * @param ConnectionInterface $from Conexão cliente do remetente
     * @param object $json  Objeto JSON que será enviado à conexão cliente
     * @param bool $enviaAoRemetenteTambem Deve ser true caso queira que o remetente também reecba a mensagem
     * Envia mensagem a todas as conexões de cliente ativas.
     */
    public function sendToAll(\Ratchet\ConnectionInterface $from, object $json, $enviaAoRemetenteTambem = false){
        try{
            foreach ($this->clients as $client) {
                if ($enviaAoRemetenteTambem || $from != $client) {
                    $jsonString = json_encode($json);
                    $client->send($jsonString);
                }
            }
        }catch(\Exception $e){
            $this->devolveMsgDeErro($from, 'Ocorreu um erro ao enviar mensagem a Todos. '.$e->getMessage());
        }
    }

    /**
     * @param ConnectionInterface $from
     * @param array $arrClients
     * @param string $msg
     * @param bool $enviaAoRemetenteTambem
     * Envia mensagem a todas as conexões de cliente informadas no array $arrClients. Transformará a mensagem string recebida em json para poder enviar à conexão cliente
     */
    public function sendMsgToGroup(\Ratchet\ConnectionInterface $from, array $arrClients, string $msg, $enviaAoRemetenteTambem = false){
        $json = $this->getJsonObjectMessage($from, $msg);
        $this->sendToGroup($from, $arrClients, $json, $enviaAoRemetenteTambem);
    }

    /**
     * @param ConnectionInterface $from
     * @param object $json
     * @param bool $enviaAoRemetenteTambem
     * Envia mensagem a todas as conexões de cliente informadas no array $arrClients.
     */
    public function sendToGroup(\Ratchet\ConnectionInterface $from, array $arrClients, object $json, $enviaAoRemetenteTambem = false){
        try{
            foreach ($arrClients as $client) {
                if ($enviaAoRemetenteTambem || $from != $client) {
                    $jsonString = json_encode($json);
                    $client->send($jsonString);
                }
            }
        }catch(\Exception $e){
            $this->devolveMsgDeErro($from, 'Ocorreu um erro ao enviar mensagem a grupo. '.$e->getMessage());
        }
    }

    /**
     * @param ConnectionInterface $from Conexão cliente do remetente
     * @param int $destinatarioResourceId
     * @param string $msg
     * Envia mensagem aonexão cliente que possuir o resourceId informado no parâmetro $destinatarioResourceId. Transformará a mensagem string recebida em json para poder enviar à conexão cliente
     */
    public function sendMsgToOne(\Ratchet\ConnectionInterface $from, int $destinatarioResourceId, string $msg){
        $json = $this->getJsonObjectMessage($from, $msg);
        $this->sendToOne( $from, $destinatarioResourceId, $json);
    }

    /**
     * @param ConnectionInterface $from Conexão cliente do remetente
     * @param int $destinatarioResourceId
     * @param object $json
     * Envia mensagem aonexão cliente que possuir o resourceId informado no parâmetro $destinatarioResourceId.
     */
    public function sendToOne(\Ratchet\ConnectionInterface $from, int $destinatarioResourceId, object $json){
        try{
            $connDestino = $this->getConnectionClientByResourceId($destinatarioResourceId);
            if(isset($connDestino)){
                $jsonString = json_encode($json);
                $connDestino->send($jsonString);
            }else{
                $this->devolveMsgDeErro($from, 'Não encontrada conexão para este usuário!');
            }
        }catch(\Exception $e){
            $this->devolveMsgDeErro($from, 'Ocorreu um erro ao enviar mensagem a um usuário. '.$e->getMessage());
        }
    }

    /**
     * @param ConnectionInterface $from Conexão cliente do remetente
     * @param string $msg
     * @return object
     */
    protected function getJsonObjectMessage(\Ratchet\ConnectionInterface $from, string $msg): object{
        $obj = $this->getDefaultJsonObjectMessage($from);
        $obj->msg = $msg;
        return $obj;
    }

    /**
     * @param ConnectionInterface $from Conexão cliente do remetente
     * @param null $msg_id
     * @return object
     */
    protected function getDefaultJsonObjectMessage(\Ratchet\ConnectionInterface $from, $msg_id=null): object{
        $timestampAtual = \Solves\SolvesTime::getTimestampAtual();
        $timestampAtualLabel = \Solves\SolvesTime::getDataFormatada($timestampAtual);
        $obj = (object) [
            'from_resource_id' => $this->getResourceId($from),
            'datetime' => $timestampAtual,
            'datetime_label' => $timestampAtualLabel
        ];
        if(isset($msg_id)){
            $obj->msg_id = $msg_id;
        }
        return $obj;
    }

    /**
     * @param ConnectionInterface $conn Conexão cliente do remetente
     * @param string $msg Mensagem de erro a ser enviada
     */
    protected function devolveMsgDeErro(\Ratchet\ConnectionInterface $conn, string $msg='Ocorreu um erro na operação!'){
        $this->sendMsgToOne($conn, $this->getResourceId($conn), $msg);
    }

    /**
     * @param ConnectionInterface $conn Conexão cliente do remetente
     * @return mixed
     */
    protected function getHttpRequest(\Ratchet\ConnectionInterface $conn){
        return $conn->httpRequest;
    }

    /**
     * @param ConnectionInterface $conn
     * @return mixed
     */
    protected function getHttpRequestHeaders(\Ratchet\ConnectionInterface $conn){
        return $conn->httpRequest->getHeaders();
    }

    /**
     * @param ConnectionInterface $conn
     * @return mixed
     */
    public function getResourceId(\Ratchet\ConnectionInterface $conn){
        return $conn->resourceId;
    }

    /**
     * @param ConnectionInterface $conn
     * @return |null
     */
    public function getHost(\Ratchet\ConnectionInterface $conn){
        return $this->getHttpHeaderProperty($conn, 'Host');
    }

    /**
     * @param ConnectionInterface $conn
     * @return |null
     */
    public function getUserAgent(\Ratchet\ConnectionInterface $conn){
        return $this->getHttpHeaderProperty($conn, "User-Agent");
    }

    /**
     * @param ConnectionInterface $conn
     * @return |null
     */
    public function getOrigin(\Ratchet\ConnectionInterface $conn){
        return $this->getHttpHeaderProperty($conn, "Origin");
    }

    /**
     * @param ConnectionInterface $conn
     * @return |null
     */
    public function getCookie(\Ratchet\ConnectionInterface $conn){
        return $this->getHttpHeaderProperty($conn, 'Cookie');
    }

    /**
     * @param ConnectionInterface $conn
     * @return |null
     */
    public function getSecWebSocketVersion(\Ratchet\ConnectionInterface $conn){
        return $this->getHttpHeaderProperty($conn, "Sec-WebSocket-Version");
    }

    /**
     * @param ConnectionInterface $conn
     * @return |null
     */
    public function getSecWebSocketKey(\Ratchet\ConnectionInterface $conn){
        return $this->getHttpHeaderProperty($conn, "Sec-WebSocket-Key");
    }

    /**
     * @param ConnectionInterface $conn
     * @param string $propName
     * @return |null
     */
    private function getHttpHeaderProperty(\Ratchet\ConnectionInterface $conn, string $propName){
        $headers = $this->getHttpRequestHeaders($conn);
        $v = null;
        if(isset($headers) && array_key_exists($propName, $headers)){
            $arr = $headers[$propName];
            $v = ((isset($arr) && count($arr)>0) ? $arr[0] : null);
        }
        return $v;
    }

    /**
     * @param ConnectionInterface $conn
     * @param string $paramName
     * @return |null
     */
    protected function getHttpParameter(\Ratchet\ConnectionInterface $conn, string $paramName){
        $querystring = $conn->httpRequest->getUri()->getQuery();
        parse_str($querystring,$queryarray);
        if(isset($queryarray) && array_key_exists($paramName, $queryarray)){
            $v =  $queryarray[$paramName];
            if(\Solves\Solves::isNotBlank($v)){
                $v2 = json_decode(utf8_encode($v), false);
                if(\Solves\Solves::isNotBlank($v2)){
                    $v = $v2;
                }
            } 
            return $v;
        }
        return null;
    }

    /**
     * @param ConnectionInterface $conn
     * @param ?bool $isObrigatorio
     * @return |null
     */
    protected function getHttpParameterToken(\Ratchet\ConnectionInterface $conn, ?bool $isObrigatorio=true){
        $v = $this->getHttpParameter($conn,'token');
        if($isObrigatorio && !isset($v)){
            $this->devolveMsgDeErro($conn, 'Usuário não autenticado.');
        }
        return $v;
    }

    /**
     * @param ConnectionInterface $conn
     * @param ?bool $isObrigatorio
     * @return |null
     */
    protected function getHttpParameterUserData(\Ratchet\ConnectionInterface $conn, ?bool $isObrigatorio=true){
        $v = $this->getHttpParameter($conn,'userData');
        if($isObrigatorio && !isset($v)){
            $this->devolveMsgDeErro($conn, 'Usuário não autenticado.');
        }
        return $v;
    }

    protected function getUser(?\SolvesDAO\SolvesDAOConnection $CONNECTION,\Ratchet\ConnectionInterface $conn, ?string $token = null, ?string $userData = null){
        if(!isset($token)){
            $token = $this->getHttpParameterToken($conn);
        }
        if(!isset($userData)){
            $userData = $this->getHttpParameterUserData($conn);
        }
        return \SolvesAuth\SolvesAuth::checkToken($CONNECTION,$token,$userData);
    }
}