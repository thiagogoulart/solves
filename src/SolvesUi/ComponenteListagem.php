<?php

/**
 * Description of ComponenteListagem
 *
 * @author ricardo.cuevas
 */
class ComponenteListagem {

    private $ar_colunas;
    private $ar_dados;
    private $rotina;
    private $grupoUsuarioRotina;
    private $usuario;
    private $chave;
    private $adminMode = false;
    private $adminRotinaId = 0;
    private $SOMAS;
    private $campoSoma = null;
    private $removerFunction;
    private $tbodyId = '';
    public $relatorio = "";
    public $editButtonName = "Editar";
    public $exibirCaixaBusca = true;
    public $doSorter = true;
    public $alterar = true;
    public $remover = true;
    public $detalhar = true;
    public $incluir = true;
    public $pesquisa = "";

    public function __construct($r, $grupoUsuarioRotina, $u, $chave) {
        $this->rotina = $r;
        $this->grupoUsuarioRotina = $grupoUsuarioRotina;
        $this->usuario = $u;
        $this->chave = $chave;

        $this->SOMAS = array();
    }

    public function addColuna($header, $campo, $w = '', $class = '', $order = '') {
        $this->ar_colunas[] = Array('header' => $header, 'campo' => $campo, 'w' => $w, 'class' => $class, 'order' => $order);
    }

    public function setDados($ar) {
        $this->ar_dados = $ar;
    }

    public function setRemoverFunction($p) {
        $this->removerFunction = $p;
    }

    public function setTbodyId($p) {
        $this->tbodyId = $p;
    }

    public function getTbodyId() {
        return $this->tbodyId;
    }

    public function setCampoSoma($p) {
        $this->campoSoma = $p;
    }

    public function getCampoSoma() {
        return $this->campoSoma;
    }

    public function getAdminMode() {
        return $this->adminMode;
    }

    public function setAdminMode($adminMode) {
        $this->adminMode = $adminMode;
    }

    public function getAdminRotinaId() {
        return $this->adminRotinaId;
    }

    public function setAdminRotinaId($adminRotinaId) {
        $this->adminRotinaId = $adminRotinaId;
    }

    public function criarHtml() {
        $navInside = '';
        $html = '';
        $gridId = 'table1';
        $colOrder = '1';
        if ($this->exibirCaixaBusca) {
            $html .= '<div style="float:right; text-align:right;">';
            $html .= getSearchBox('1');
            $html .= '</div>';
            $html .= '<div style="float:left; width: 120px; text-align:left;">';

            if ($this->podeInserir()) {
                $html .= '<button onclick="loadView(\'' . $this->getLinkUrlWithAction(ID_VIEW_INCLUSAO) . '\');" class="btn btn-small btn-success"><i class="icon icon-white icon-add"></i>' . $this->getButtonNewTitle() . '</button>';
            }
            $html .= '</div><div style="clear:both;"></div>	';
        }

        $html .= '<table id="' . $gridId . '" class="sortable"><thead><tr>';
        $cIt = 0;
        foreach ($this->ar_colunas as $col) {
            $html .= '<th class="head" ' . (strlen($col['w']) > 0 ? 'width="' . $col['w'] . '"' : '') . '><h3><b>' . $col['header'] . '</b></h3></th>';
            if(isNotEmptyVal($col['order'])){
                $colOrder = $cIt;
            }
            $cIt++;
        }
        $qtdTotalButtons = 3;
        $qtdButtons = ($this->podeDetalhar() ? 1 : 0) + ($this->podeAlterar() ? 1 : 0) + ($this->podeExcluir() ? 1 : 0);
        $widthColAction = (148 / $qtdTotalButtons) * $qtdButtons;
        $html .= '<th class="nosort columnAction" width="' . $widthColAction . '"></th></tr></thead>
		     <tbody id="' . $this->getTbodyId() . '">';

        $qtdDado = count($this->ar_dados);
        $hasDados = ($qtdDado > 0);
        $hasSoma = (isset($this->campoSoma) && strlen($this->campoSoma) > 0);
        if (!$hasDados) {
            $qtdCols = count($this->ar_colunas) + 1;
            $html .= '<tr id="tr_none"><td colspan="' . $qtdCols . '"><span class="nenhum_registro">Nenhum registro</span></td></tr>';
        } else {
            foreach ($this->ar_dados as $dado) {
                $trId = criptografa($dado[$this->chave]);
                $html .= '<tr object_id="' . $trId . '">';
                foreach ($this->ar_colunas as $col) {
                    $colId = $trId . '_' . $col['campo'];
                    if ($hasSoma && $this->campoSoma == $col['campo']) {
                        if (isset($this->SOMAS[$this->campoSoma])) {
                            $this->SOMAS[$this->campoSoma] = $this->SOMAS[$this->campoSoma] + $dado[$col['campo']];
                        } else {
                            $this->SOMAS[$this->campoSoma] = $dado[$col['campo']];
                        }
                    }
                    $html .= '<td class="' . $col['class'] . '" id="' . $colId . '">' . $dado[$col['campo'] . '_label'] . '</td>';
                }
                $html .= '<td class="center">';
                if ($this->detalhar || $this->alterar || $this->remover) {
                    if ($this->podeDetalhar()) {
                        $html .= $this->getHtmlButtonDetail($dado[$this->chave]);
                    }
                    if ($this->podeAlterar()) {
                        $dado[$this->chave];
                        $html .= $this->getHtmlButtonEdit($dado[$this->chave]);
                    }
                    if ($this->podeExcluir()) {
                        $fRemove = (isset($removerFunction) ? $removerFunction : ' if (confirm(\'Deseja realmente executar esta a&ccedil;&atilde;o?\')) { loadView(\'' . $this->getLinkUrlWithActionAndId(ID_VIEW_EXCLUSAO, $dado[$this->chave]) . '\');}');
                        $html .= '<a class="btn btn-sm btn-danger" onclick="' . $fRemove . '" alt="Deletar" title="Deletar"><div class="bt_deletar">&nbsp;</div></a>';
                    }
                }
                $html .= '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody>';
        $html .= '<tfoot><tr>';
        foreach ($this->ar_colunas as $col) {
            $html.='<td id="tfoot_' . $col['campo'] . '">';
            if ($hasDados && $hasSoma) {
                if ($col['campo'] == $this->campoSoma) {
                    $html.= 'SOMA: [' . $this->SOMAS[$this->campoSoma] . ']';
                }
            }
            $html.='</td>';
        }
        $html .= '<td></td></tr></tfoot>';
        $html.='</table>';
        if ($this->doSorter) {
            $html .= getControlsSorter($navInside, '1');
            $html .= getElementScriptSorter($navInside, '1', ''.$colOrder);
        }
        return $html;
    }

    public function podeInserir() {
        return (checkBoolean($this->incluir) && ((isset($this->usuario) && $this->usuario->isAdmin()) || (isset($this->grupoUsuarioRotina) && $this->grupoUsuarioRotina->isInserir())));
    }

    public function podeAlterar() {
        return (checkBoolean($this->alterar) && ((isset($this->usuario) && $this->usuario->isAdmin()) || (isset($this->grupoUsuarioRotina) && $this->grupoUsuarioRotina->isAlterar())));
    }

    public function podeExcluir() {
        return (checkBoolean($this->remover) && ((isset($this->usuario) && $this->usuario->isAdmin()) || (isset($this->grupoUsuarioRotina) && $this->grupoUsuarioRotina->isExcluir())));
    }

    public function podeDetalhar() {
        return (checkBoolean($this->detalhar) && ((isset($this->usuario) && $this->usuario->isAdmin()) || (isset($this->grupoUsuarioRotina) && $this->grupoUsuarioRotina->isVisualizar())));
    }

    public function getButtonNewTitle() {
        return ((!isset($this->relatorio) || $this->relatorio == "") ? '&nbsp;NOVO' : '&nbsp;' . $this->relatorio);
    }

    public function getLinkUrlWithActionAndId($idView, $key) {
        if ($this->adminMode) {
            return 'index.php?r=' . $this->adminRotinaId . '&action=' . criptografa($idView) . '&id=' . criptografa($key);
        } else {
            return $this->rotina->getLinkUrlWithActionAndId($idView, $key);
        }
    }

    public function getLinkUrlWithAction($idView) {
        if ($this->adminMode) {
            return 'index.php?r=' . $this->adminRotinaId . '&action=' . criptografa($idView);
        } else {
            return $this->rotina->getLinkUrlWithAction($idView);
        }
    }

    public function getHtmlButtonEdit($id) {
        return '<a class="btn btn-sm btn-info" 
				  onclick="loadView(\'' . $this->getLinkUrlWithActionAndId(ID_VIEW_ALTERACAO, $id) . '\');" '
                . ' alt="' . $this->editButtonName . '" title="' . $this->editButtonName . '"><div class="bt_editar">&nbsp;</div></a>';
    }

    public function getHtmlButtonDetail($id) {
        return '<a class="btn btn-sm btn-success" 
				  onclick="loadView(\'' . $this->getLinkUrlWithActionAndId(ID_VIEW_DETALHAR, $id) . '\');" alt="Detalhar" title="Detalhar"><div class="bt_detalhar">&nbsp;</div></a>';
    }

}

?>
