<?php

/** *
 * @author Thiago Goulart
  @since 2013-09-11
  @modified 2013-09-19
 */
namespace SolvesUi;

class ComponenteLista {

    private $pkName;
    private $id;
    private $elmId;
    private $label;
    private $itens;
    private $inputTargetId;
    private $inputs;
    private $rotina;
    private $grupoUsuarioRotina;
    private $usuario;
    private $labelColumnName;
    private $urlRotina;
    private $valuesJSON = '';
    private $campoSoma = null;
    private $showSubmit = true;
    private $quebrarLinhaAposLista = true;
    private $classe='';

    public function __construct($id, $label, $inputTargetId, $pkName, $rotina, $grupoUsuarioRotina, $usuario, $urlRotina) {
        $this->id = $id;
        $this->elmId = 'lista' . $id;
        $this->label = $label;
        $this->inputTargetId = $inputTargetId;
        $this->pkName = $pkName;
        $this->itens = array();
        $this->rotina = $rotina;
        $this->grupoUsuarioRotina = $grupoUsuarioRotina;
        $this->usuario = $usuario;
        $this->urlRotina = $urlRotina;
        
        $this->classe= 'well well-sm';
    }

    public function addItem($id, $item) {
        $this->itens[$id] = $item;
    }

    public function setItens($p) {
        $this->itens = $p;
    }

    public function setInputTargetId($p) {
        $this->inputTargetId = $p;
    }

    public function setInputLabel($inputLabel) {
        $this->inputLabel = $inputLabel;
    }

    public function setInputs($p) {
        $this->inputs = $p;
    }

    public function addInput($p) {
        $this->inputs[] = $p;
    }

    public function getId() {
        return $this->id;
    }

    public function getInputs() {
        return $this->inputs;
    }

    public function setValuesJSON($p) {
        if (!isset($p)) {
            $p = '';
        }
        $this->valuesJSON = $p;
    }

    public function setLabelColumnName($p) {
        $this->labelColumnName = $p;
    }

    public function getLabelColumnName() {
        return $this->labelColumnName;
    }

    public function setCampoSoma($p) {
        $this->campoSoma = $p;
    }

    public function getCampoSoma() {
        return $this->campoSoma;
    }

    public function setShowSubmit($p) {
        $this->showSubmit = $p;
    }

    public function setQuebrarLinhaAposLista($p) {
        $this->quebrarLinhaAposLista = $p;
    }
    
    public function addClasse($c){
        $this->classe.= ' '.$c;
    }

    public function criarHtml() {
        $scripts = '';
        $table = new ComponenteListagem($this->rotina, $this->grupoUsuarioRotina, $this->usuario, $this->pkName);
        $table->setCampoSoma($this->campoSoma);
        $html = '<div class="'.$this->classe.'">';
        $qtdInputs = count($this->inputs);
        $hasSoma = (isset($this->campoSoma) && strlen($this->campoSoma) > 0);
        if (isset($this->inputs) && count($this->inputs) > 0 && $qtdInputs > 0) {
            foreach ($this->inputs as $input) {
                $html .= $input->criarHtml();
                $table->addColuna($input->getLabel(), $input->getId(), '');
                $scripts.='$.Lista.instances[' . $this->id . '].addInput(new ComponenteInput(\'' . $input->getId() . '\', ' .
                        '\'' . $input->getLabel() . '\',\'' . $input->getType() . '\', 
							\'' . $input->getValidationType() . '\', 
							' . (($hasSoma && $this->campoSoma == $input->getId()) ? 'true' : 'false') . ',
							\'' . ($input->hasColName() ? $input->getColName() : $input->getId()) . '\'));
							';
            }
            $html .= '<span class="input-group-btn">
              <button class="btn btn-default" type="button" 
			   onclick="$.Lista.instances[' . $this->id . '].add();">Adicionar</button>
            </span>';
        }
        if (isset($this->labelColumnName)) {
            $scripts.='$.Lista.instances[' . $this->id . '].setLabelColumnName(\'' . $this->labelColumnName . '\');
							';
        }
        $scripts.='$.Lista.instances[' . $this->id . '].setPkColumnName(\'' . $this->pkName . '\');
							';
        $scripts = "<script type=\"text/javascript\">
			createLista('" . $this->id . "', '" . $this->elmId . "', '" . $this->inputTargetId . "');			
			" . $scripts . " 
			$.Lista.instances[" . $this->id . "].setValues('" . ($this->valuesJSON) . "');
		</script>";

        //$table->setDados($arr);
        $table->setRemoverFunction('$.Lista.instances[' . $this->id . '].remove(this.object_id)');
        $table->incluir = false;
        $table->alterar = false;
        $table->detalhar = false;
        $table->exibirCaixaBusca = false;
        $table->doSorter = false;
        $table->setTbodyId($this->elmId);
        $html .= $table->criarHtml();

        $html .= '</div>';
        if($this->quebrarLinhaAposLista){
            $html .= '<div style="clear:both;margin-bottom:30px;"></div>';
        }

        if ($this->showSubmit) {
            $html .= '<button type="button" onclick="submitForm(document.formDados,\'' . $this->urlRotina . '\');" class="btn btn-primary">Salvar dados</button>';
        }

        $html .= $scripts;

        return $html;
    }

    public function __destruct() {
        
    }

}

?>
