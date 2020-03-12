<?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 12/03/2020
 
*/

use SolvesDAO\DAO;
use SolvesDAO\DAOJoin;
use SolvesDAO\SolvesObject;

class SolvesObjectUserMock extends SolvesObject{ 

	public static $TABELA = 'user_mock';
 	public static $PK = 'user_mock_id';
	public static $SEQUENCIA = '';

    
    protected $user_id;
    protected $user_legado_id;
    protected $nome;
    protected $email;
    protected $senha;
    protected $avatar;
    protected $img;
    protected $pontos;
    protected $xp;
    protected $qtd_canais;
    protected $qtd_videos;
    protected $data_nascimento;
    protected $data_nascimentoLabel;
    protected $sexo;
    protected $ultimo_login= null;
    protected $ultimo_loginLabel;
    protected $ultimo_acesso= null;
    protected $ultimo_acessoLabel;
    protected $email_enviado;
    protected $email_enviadoLabel;
    protected $email_confirmado;
    protected $email_confirmadoLabel;
    protected $email_confirm_token;
    protected $email_recuperacao_token;
    protected $user_agree;
    protected $user_agreeLabel;
    protected $social_link_face;
    protected $social_link_instagram;
    protected $social_link_youtube;
    protected $social_link_linkedin;
    protected $social_link_twitter;
    protected $completeUrl;
    protected $firebaseUid;
    protected $credentialProviderId;

    protected $cidade_id;
    protected $cidade_id_label;
    protected $uf_id;
    protected $uf_id_label;
    protected $uf_id_sigla;
    protected $empresa_id;
    protected $nomeCurto;
    protected $descricao;


	public function __construct($con, $parentDao=null) {
		parent::__construct($con, self::$TABELA, self::$PK, self::$SEQUENCIA, $parentDao);

        
        $this->setArrIdsColunasSensiveis(array(3, 9, 10, 12, 13, 18, 20, 21, 28, 29));

        $this->dao->addColunaObrigatoria(1, "nome", "string", true, null);
        $this->dao->addColunaObrigatoria(2, "email", "string", false, null);
        $this->dao->addColunaObrigatoria(3, "senha", "string", false, null);
        $this->dao->addColuna(4, "avatar", "string", false, null);
        $this->dao->addColuna(5, "pontos", "integer", false, null);
        $this->dao->addColuna(6, "data_nascimento", "date", true, null);
        $this->dao->addColunaObrigatoria(7, "created_at", "timestamp", true, null);
        $this->dao->addColuna(8, "updated_at", "timestamp", true, null);
        $this->dao->addColuna(9, "ultimo_login", "timestamp", true, null);
        $this->dao->addColuna(10, "email_enviado", "boolean", true, null);
        $this->dao->addColuna(11, "email_confirmado", "boolean", true, null);
        $this->dao->addColuna(12, "email_confirm_token", "string", false, null);
        $this->dao->addColuna(13, "removed", "boolean", true, null);
        $this->dao->addColuna(14, "qtd_canais", "integer", false, null);
        $this->dao->addColuna(15, "qtd_videos", "integer", false, null);
        $this->dao->addColuna(16, "user_agree", "boolean", false, null);
        $this->dao->addColuna(17, "ultimo_acesso", "timestamp", true, null);
        $this->dao->addColuna(18, "email_recuperacao_token", "string", false, null);
        $this->dao->addColuna(19, "xp", "integer", false, null);
        $this->dao->addColuna(20, "empresa_id", "integer", false, null);
        $this->dao->addColuna(21, "img", "string", false, null);
        $this->dao->addColunaObrigatoria(22, "nome_curto", "string", true, null);
        $this->dao->addColuna(23, "social_link_face", "string", true, null);
        $this->dao->addColuna(24, "social_link_instagram", "string", true, null);
        $this->dao->addColuna(25, "social_link_youtube", "string", true, null);
        $this->dao->addColuna(26, "social_link_linkedin", "string", true, null);
        $this->dao->addColunaObrigatoria(27, "complete_url", "string", false, null);
        $this->dao->addColuna(28, "firebase_uid", "string", false, null);
        $this->dao->addColuna(29, "credential_provider_id", "string", false, null);
        $this->dao->addColuna(30, "descricao", "string", false, null);
        $this->dao->addColuna(31, "cidade_id", "integer", false, null);
        $this->dao->addColuna(32, "uf_id", "integer", false, null);
        $this->dao->addColuna(33, "social_link_twitter", "string", true, null);
        $this->dao->addColuna(34, "sexo", "string", false, null);
        $this->dao->addColuna(35, "user_legado_id", "integer", false, null);

        $this->dao->setColunaLabelOrder(1);
        $this->dao->addColunaLabelOrder(4);
        $this->dao->addColunaLabelOrder(19);
        $this->dao->addColunaLabelOrder(31);
        $this->dao->addColunaLabelOrder(32);
        $this->dao->addColunaLabelOrder(35);

        $this->pontos = 0;
        $this->xp = 0;
        $this->qtd_canais = 0;
        $this->qtd_videos = 0;
	}

//DAO
    public function beforeSaveAndUpdate(){

    }
    public function afterSave(){

    }
    public function afterUpdate($old){

    }
    public function afterDelete(){

    }

    public static function create($connection, $empresaId, $createdAt, $email, $senha, $nome, $data_nascimento, $img, $agree, $cidade_id,$uf_id){
        $euser = new SolvesObjectUserMock($connection);
        $senha = \Solves\Solves::criptografaSenha($email, $senha);
        $euser->setEmpresaId($empresaId);
        $euser->setNome($nome);
        $euser->setEmail($email);
        $euser->setDataNascimento($data_nascimento);
        $euser->setSenha($senha);
        $euser->setCreatedAt($createdAt);
        $euser->setCreatedAtNew($createdAt);
        $euser->setImg($img);
        $euser->setAtivo(1);
        $euser->setRemoved(0);
        $euser->setPontos(0);
        $euser->setUserAgree($agree);
        $euser->setEmailEnviado(0);
        $euser->setEmailConfirmado(0);
        $euser->setUltimoAcesso($createdAt);
        $euser->setUltimoLogin($createdAt);
        $euser->setCidadeId($cidade_id);
        $euser->setUfId($uf_id);

        $id = $euser->saveReturningId();
        return $euser;
    }
}
?>