<?php

/** *
 * @author Thiago Goulart
  @since 2013-09-04
 */
namespace SolvesUi;

class ComponenteDetalhe {

    private $id;
    private $label;
    private $value;
    private $classe;

    public function __construct($id, $label, $value) {
        $this->id = $id;
        $this->label = $label;
        $this->value = $value;
        $this->classe = ComponenteInput::$CLASSE_INPUT_GRANDE;
    }
    public function setClasse($p) {
        $this->classe = $p;
    }
    
    public function criarHtml() {
        $html = '<div class="input-group '.$this->classe.'">
			<span class="input-group-addon" 
			title="' . $this->label . '" alt="' . $this->label . '">' . $this->label . ': </span>';

        $html .='<div class="form-control '.$this->classe.'" id="'.$this->id.'">' . $this->value . '</div>';

        $html .='</div>';
        return $html;
    }

    public function __destruct() {
        
    }

}

?>
