<?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 27/11/2018
 
*/

use SolvesDAO\DAO;
use SolvesDAO\DAOJoin;
use SolvesDAO\SolvesObject;

class SolvesObjectClienteMock extends SolvesObject{ 

	public static $TABELA = 'cliente_mock';
 	public static $PK = 'cliente_mock_id';
	public static $SEQUENCIA = '';

    /**
     * @var string
     * nome do cliente
     */
	protected $nome='';
    /**
     * @var string
     * email do cliente
     */
    protected $email='';
    /**
     * @var string
     * Cpf do cliente
     */
    protected $cpf='';

    /**
     * @var bool
     */
    protected $excluido=false;


	public function __construct($con, $parentDao=null) {
		parent::__construct($con, self::$TABELA, self::$PK, self::$SEQUENCIA, $parentDao);

        $this->dao->addColunaObrigatoria(1, "nome", "string", true, null);
        $this->dao->addColunaObrigatoria(2, "email", "string", false, null);  
        $this->dao->addColunaObrigatoria(3, "cpf", "string", false, null);      

        $this->dao->setColunaLabelOrder(1);
        $this->dao->addColunaLabelOrder(2);  
	}

//DAO
    public function beforeSaveAndUpdate(){

    }
    public function afterSave(){

    }
    public function afterUpdate($old){

    }
    public function afterDelete(){
       // echo '--afterDelete--';
        $this->excluido = true;
    }

}
?>