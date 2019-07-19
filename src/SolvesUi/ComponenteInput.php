<?php

/** *
 * @author ricardo.cuevas
  @version 1.0

 * @author Thiago Goulart
  @since 2013-09-04
  @version 2.0
 */
class ComponenteInput {

    public static $CLASSE_INPUT_GRANDE = 'input-group-lg';
    public static $CLASSE_INPUT_MEDIO = '';
    public static $CLASSE_INPUT_PEQUENO = 'input-group-sm';
    public static $CLASSE_MASK_DATA = 'field-data';
    public static $CLASSE_MASK_HORA = 'field-hora';
    public static $CLASSE_MASK_FONE = 'field-fone';
    public static $CLASSE_MASK_RG = 'field-rg';
    public static $CLASSE_MASK_CPF = 'field-cpf';
    public static $CLASSE_MASK_CNPJ = 'field-cnpj';
    public static $CLASSE_MASK_CEP = 'field-cep';
    public static $CLASSE_MASK_DOLAR = 'field-dolar';
    public static $CLASSE_MASK_MOEDA = 'field-moeda';
    public static $CLASSE_MASK_EURO = 'field-euro';
    public static $CLASSE_MASK_EMAIL = 'field-email';
    public static $CLASSE_MASK_DOUBLE = 'field-double';
    public static $CLASSE_MASK_INT = 'field-int';
    public static $CLASSE_CKE_MIN = 'cke_textarea_min';
    public static $CLASSE_CKE_BIG = 'cke_textarea_big';
    private $id;
    private $classe;
    private $ckeClasse;
    private $name;
    private $type;
    private $label;
    private $value;
    private $exibitionValue;
    private $help;
    private $colName;
    private $hasColName=false;
    private $showLabel;
    private $usingClasse;
    private $obrigatorio;
    private $validationType;
    private $minlength;
    private $maxlength;
    private $placeHolder;
    private $optionsSelect;
    private $optionPkName;
    private $optionValueName;
    private $optionMaskLabel;
    private $attributes;
    private $onKeyUp = '';
    private $onKeyDown = '';
    private $onSelect = '';
    private $onBlur = '';
    private $onChange = '';//'validarCampo(this);';
    private $containerStyle = '';
    private $classTamanho = '';

    public function __construct($id, $type, $label, $obrigatorio, $validationType, $minlength, $maxlength, $placeHolder, $help, $value, $tamanho=null) {
        $this->id = $id;
        $this->name = $this->id;
        $this->type = $type;
        $this->label = $label;
        $this->obrigatorio = $obrigatorio;
        $this->validationType = $validationType;
        $this->minlength = $minlength;
        $this->maxlength = $maxlength;
        $this->placeHolder = $placeHolder;
        $this->help = $help;
        $this->value = $value;
        if ($this->type == "checkbox") {
            $this->onChange = 'clickCheck(this);';
        }
        if(!\Solves\Solves::isNotBlank($this->help)){
            $this->help = $this->label;
        }
        if($tamanho==null){
            $tamanho = ComponenteInput::$CLASSE_INPUT_GRANDE;
        }
        $this->classTamanho = $tamanho;
        $this->classe = $tamanho;
        $this->showLabel = true;
        $this->usingClasse = true;
        $this->attributes = '';
    }

    public function setId($p) {
        $this->id = $p;
    }

    public function getId() {
        return $this->id;
    }

    public function setExibitionValue($p) {
        $this->exibitionValue = $p;
    }

    public function setContainerStyle($p) {
        $this->containerStyle = $p;
    }

    public function setShowLabel($p) {
        $this->showLabel = $p;
    }

    public function setValue($p) {
        $this->value = $p;
    }

    public function setObrigatorio($p) {
        $this->obrigatorio = $p;
    }

    public function setHelp($p) {
        $this->help = $p;
    }

    public function setLabel($p) {
        $this->label = $p;
    }

    public function getLabel() {
        return $this->label;
    }

    public function setOptionMaskLabel($p) {
        $this->optionMaskLabel = $p;
    }

    public function getOptionMaskLabel() {
        return $this->optionMaskLabel;
    }

    public function getType() {
        return $this->type;
    }

    public function getValidationType() {
        return $this->validationType;
    }

    public function setOptionsSelect($opts, $optionPkName, $optionValueName) {
        $this->optionsSelect = $opts;
        $this->optionPkName = $optionPkName;
        $this->optionValueName = $optionValueName;
    }

    public function getColName() {
        return $this->colName;
    }

    public function setColName($colName) {
        $this->colName = $colName;
        $this->hasColName = true;
    }
    
    public function hasColName() {
        return $this->hasColName;
    }
    public function getCkeClasse() {
        return $this->ckeClasse;
    }

    public function setCkeClasse($ckeClasse) {
        $this->ckeClasse = $ckeClasse;
    }
    
    public function criarHtml() {
        $isHidden = ($this->type == 'hidden');
        if($isHidden){
            $this->containerStyle.='display:none';
        }
        $containerId = 'continputgr_'.$this->id;
        $html = '<div id="'.$containerId.'" ' . 
                ($this->usingClasse ? 'class="input-group ' . ($this->getClasse()) . '"' : '') . ' ' .
                (\Solves\Solves::isNotBlank($this->containerStyle) ? 'style="'.$this->containerStyle.'"' : '') . '>';

        if ($this->showLabel) {
            $html .= '<span class="input-group-addon ' . ($this->obrigatorio ? 'label_obrigatorio' : '') . '" 
                title="' . $this->help . '" alt="' . $this->label . '">
                    ' . ($this->obrigatorio ? ' * ' : '') . '
                    ' . $this->label . ': </span>';
        }
        if ($this->type == "select") {
            //select    
            $html .='<div ' . ($this->usingClasse ? 'class="form-control ' . ($this->getClasse()) . '"' : '') . ' style="padding: 0;">
            <select ' . $this->attributes . '  alt="' . $this->label . '" title="' .$this->help. '" 
                     id="' . $this->id . '" name="' . $this->name . '" 
                     class="form-control select_chosen ' . ($this->getClasse()) . '" 
                    ' . ($this->obrigatorio ? ' obrigatorio="true" ' : '') . ' 
                     minlength="' . $this->minlength . '" 
                     maxlength="' . $this->maxlength . '"
                    ' . (strlen($this->onKeyUp) > 0 ? ' onKeyUp="' . $this->onKeyUp . '" ' : '') . '
                    ' . (strlen($this->onKeyDown) > 0 ? ' onKeyDown="' . $this->onKeyDown . '" ' : '') . '
                    ' . (strlen($this->onSelect) > 0 ? ' onchange="' . $this->onSelect . '" ' : '') . '
                    ' . (strlen($this->onChange) > 0 ? ' onchange="' . $this->onChange . '" ' : '') . '
                    ' . (strlen($this->onBlur) > 0 ? ' onBlur="' . $this->onBlur . '" ' : '') . '>';

            $html .='<option value=""></option>';
            if (isset($this->optionsSelect)) {
                $hasOptionMaskLabel = (isset($this->optionMaskLabel));
                $qtdOptions = count($this->optionsSelect);
                foreach ($this->optionsSelect as $opt) {
                    $html .='<option value="' . $opt[$this->optionPkName] . '" 
                            ' . ((isset($this->value) && $this->value == $opt[$this->optionPkName]) ? ' selected' : '') . '>' .
                            ($hasOptionMaskLabel ? $this->getMaskValue($opt) : $opt[$this->optionValueName . '_label']) . '</option>';
                }
            }
            $html .= '</select></div>';
        } else if ($this->type == "textarea") {
            //textarea
            $html .='<div ' . ($this->usingClasse ? 'class="form-control ' . ($this->getClasse()) . '"' : '') . ' style="padding: 0;">
                <textarea ' . $this->attributes . ' 
                     alt="' . $this->label . '" title="'.$this->help.'" 
                     id="' . $this->id . '" name="' . $this->name . '" 
                     style="margin: 0px; " 
                     class="form-control ' . ($this->getClasse()) . '" 
                                             cke_class="'.$this->getCkeClasse().'" 
                    ' . ($this->obrigatorio ? ' obrigatorio="true" ' : '') . '
                    validationType="' . $this->validationType . '"
                     minlength="' . $this->minlength . '" 
                     maxlength="' . $this->maxlength . '"
                    ' . (strlen($this->onKeyUp) > 0 ? ' onKeyUp="' . $this->onKeyUp . '" ' : '') . '
                    ' . (strlen($this->onKeyDown) > 0 ? ' onKeyDown="' . $this->onKeyDown . '" ' : '') . '
                    ' . (strlen($this->onSelect) > 0 ? ' onSelect="' . $this->onSelect . '" ' : '') . '
                    ' . (strlen($this->onChange) > 0 ? ' onchange="' . $this->onChange . '" ' : '') . '
                    ' . (strlen($this->onBlur) > 0 ? ' onBlur="' . $this->onBlur . '" ' : '') . '
                    placeholder="' . $this->placeHolder . '">' . $this->value . '</textarea></div>'.
                    '<script type="text/javascript">'.
                    ("var editor = CKEDITOR.inline( '".$this->name."' );").
                    '</script>';
        } else if ($this->type == "checkbox") {
            //checkbox  
            $marcado = \Solves\Solves::checkBoolean($this->value);
            $html .='<div ' . ($this->usingClasse ? 'class="form-control ' . ($this->getClasse()) . '"' : '') . ' style="padding: 0;">
                    <input ' . $this->attributes . '  type="' . $this->type . '"
                     alt="' . $this->label . '" title="'.$this->help.'" 
                     id="' . $this->id . '" name="' . $this->name . '" 
                     class="' . ($this->getClasse()) . '" 
                     ' . ($marcado ? ' checked="checked" ' : '') . '
                    ' . (strlen($this->onKeyUp) > 0 ? ' onKeyUp="' . $this->onKeyUp . '" ' : '') . '
                    ' . (strlen($this->onKeyDown) > 0 ? ' onKeyDown="' . $this->onKeyDown . '" ' : '') . '
                    ' . (strlen($this->onSelect) > 0 ? ' onSelect="' . $this->onSelect . '" ' : '') . '
                    ' . (strlen($this->onChange) > 0 ? ' onchange="' . $this->onChange . '" ' : '') . '
                    ' . (strlen($this->onBlur) > 0 ? ' onBlur="' . $this->onBlur . '" ' : '') . '
                    value="' . ($marcado ? 'true' : 'false') . '" ></div>';
        } else if ($this->type == "radio") {
            //radio  
            $html .='<div ' . ($this->usingClasse ? 'class="form-control ' . ($this->getClasse()) . '"' : '') . ' style="padding: 0;">';
            if (isset($this->optionsSelect)) {
                $hasOptionMaskLabel = (isset($this->optionMaskLabel));
                $qtdOptions = count($this->optionsSelect);
                foreach ($this->optionsSelect as $opt) {
                    $html .='<div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="' . $this->name . '" id="' . $this->id . '_' . $opt[$this->optionPkName] . '" value="' . $opt[$this->optionPkName] . '" ' . ((isset($this->value) && $this->value == $opt[$this->optionPkName]) ? ' checked' : '') . '>
                              <label class="form-check-label" for="' . $this->id . '_' . $opt[$this->optionPkName] . '">' .
                            ($hasOptionMaskLabel ? $this->getMaskValue($opt) : $opt[$this->optionValueName . '_label']) . '</label>
                            </div>';
                }
            }       
            $html .='</div>';
        } else if ($this->type == "periodo") {
            $vlInicial = $this->value[0];
            $vlFinal = $this->value[1];
            //input 
            $html .='<div ' . ($this->usingClasse ? 'class="form-control ' . ($this->getClasse()) . '"' : '') . ' style="padding: 0;">
                <div style="width:170px; float:left; margin-right: 30px;">
                <input ' . $this->attributes . ' type="text"
                     alt="Data inicial" title="Data inicial" 
                     id="' . $this->id . '_inicio" name="' . $this->name . '_inicio" 
                     class="form-control ' . ($this->getClasse()) . '" 
                    ' . ($this->obrigatorio ? ' obrigatorio="true" ' : '') . '
                    validationType="' . $this->validationType . '"
                     minlength="' . $this->minlength . '" 
                     maxlength="' . $this->maxlength . '"
                    ' . (strlen($this->onKeyUp) > 0 ? ' onKeyUp="' . $this->onKeyUp . '" ' : '') . '
                    ' . (strlen($this->onKeyDown) > 0 ? ' onKeyDown="' . $this->onKeyDown . '" ' : '') . '
                    ' . (strlen($this->onSelect) > 0 ? ' onSelect="' . $this->onSelect . '" ' : '') . '
                    ' . (strlen($this->onChange) > 0 ? ' onchange="' . $this->onChange . '" ' : '') . '
                    ' . (strlen($this->onBlur) > 0 ? ' onBlur="' . $this->onBlur . '" ' : '') . '
                    value="' . $vlInicial . '" 
                    placeholder="Data inicial">
                        </div>
                    
                    <div style="width:170px; float:left; ">
                    <input ' . $this->attributes . ' type="text"
                     alt="Data final" title="Data final" 
                     id="' . $this->id . '_final" name="' . $this->name . '_final" 
                     class="form-control ' . ($this->getClasse()) . '" 
                    ' . ($this->obrigatorio ? ' obrigatorio="true" ' : '') . '
                    validationType="' . $this->validationType . '"
                     minlength="' . $this->minlength . '" 
                     maxlength="' . $this->maxlength . '"
                    ' . (strlen($this->onKeyUp) > 0 ? ' onKeyUp="' . $this->onKeyUp . '" ' : '') . '
                    ' . (strlen($this->onKeyDown) > 0 ? ' onKeyDown="' . $this->onKeyDown . '" ' : '') . '
                    ' . (strlen($this->onSelect) > 0 ? ' onSelect="' . $this->onSelect . '" ' : '') . '
                    ' . (strlen($this->onChange) > 0 ? ' onchange="' . $this->onChange . '" ' : '') . '
                    ' . (strlen($this->onBlur) > 0 ? ' onBlur="' . $this->onBlur . '" ' : '') . '
                    value="' .$vlFinal. '" 
                    placeholder="Data final">
                        </div>
                    <div style="clear:both"></div>
                    </div>';
        } else {
            //input 
            $html .='<div ' . ($this->usingClasse ? 'class="form-control ' . ($this->getClasse()) . '"' : '') . ' style="padding: 0;">
                <input ' . $this->attributes . ' type="' . $this->type . '"
                     alt="' . $this->label . '" title="'.$this->help.'" 
                     id="' . $this->id . '" name="' . $this->name . '" 
                     class="form-control ' . ($this->getClasse()) . '" 
                    ' . ($this->obrigatorio ? ' obrigatorio="true" ' : '') . '
                    validationType="' . $this->validationType . '"
                     minlength="' . $this->minlength . '" 
                     maxlength="' . $this->maxlength . '"
                    ' . (strlen($this->onKeyUp) > 0 ? ' onKeyUp="' . $this->onKeyUp . '" ' : '') . '
                    ' . (strlen($this->onKeyDown) > 0 ? ' onKeyDown="' . $this->onKeyDown . '" ' : '') . '
                    ' . (strlen($this->onSelect) > 0 ? ' onSelect="' . $this->onSelect . '" ' : '') . '
                    ' . (strlen($this->onChange) > 0 ? ' onchange="' . $this->onChange . '" ' : '') . '
                    ' . (strlen($this->onBlur) > 0 ? ' onBlur="' . $this->onBlur . '" ' : '') . '
                    value="' . $this->value . '" 
                    placeholder="' . $this->placeHolder . '"></div>';
        }
        $html .='</div>';
        return $html;
    }

    public function __destruct() {
        
    }

    public function getMaskValue($opt) {
        $mask = $this->optionMaskLabel;
        reset($opt);
        while (list($chave, $valor) = each($opt)) {
            $tag = '${' . $chave . '}';
            $mask = str_replace($tag, $valor, $mask);
        }
        return $mask;
    }

    public function setNoClasse() {
        $this->usingClasse = false;
        $this->classe = ' ';
    }

    public function setClasse($p) {
        $this->classe.=' ' . $p;
    }

    public function getClasse() {
        return (isset($this->classe) ? ' ' . $this->classe : $this->classTamanho);
    }

    public function setOnKeyUp($p) {
        $this->onKeyUp.=' ' . $p;
    }

    public function setOnKeyDown($p) {
        $this->onKeyDown.=' ' . $p;
    }

    public function setOnSelect($p) {
        $this->onSelect.=' ' . $p;
    }

    public function setOnChange($p) {
        $this->onChange.=' ' . $p;
    }

    public function setOnBlur($p) {
        $this->onBlur.=' ' . $p;
    }

    public function addAttribute($name, $value) {
        $this->attributes.=' ' . $name . '="' . $value . '"';
    }

}

?>
