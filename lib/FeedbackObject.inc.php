<?php

/**
 * Classe FeedbackObject
 * 
 * Auxiliar ao Ajax, javascript.
 * 
 * @author     TelEduc
 * @copyright  2014 TelEduc
 * @license    http://teleduc.org.br/
 */
class FeedbackObject {

    /**
     * Acoes possiveis
     * 
     * @var array 
     */
    var $actions; 
   
    /**
     * Construtor da Classe 
     * 
     * @param array $theListOfSentences 
     */
    function FeedbackObject() {
        $this->actions = array();
    }
    
   /**
    * Cria acao dos objetos
    * 
    * @param string $newAction Acao a ser feita
    * @param string $caseTrue Texto para caso a acao seja verdade
    * @param type $caseFalse  Texto para caso a acao seja falso
    * @return boolean Retorna True caso seja adicionada a acao, e false caso nao seja possivel.
    */
    function addAction($newAction, $caseTrue, $caseFalse) {
        if ((!isset($newAction)) || (!strcmp($newAction, '')))
            return false;
        $this->action[$newAction] = array(0 => $caseFalse, 1 => $caseTrue);
        return true;
    }
    
    /**
     * Retorna funcao de Javascript para o Ajax.
     * 
     * @param string $theAction Acao escolhida 
     * @param string $theCase Nesta string tera o valor true ou false, relacionada a acao
     * @return boolean Caso seja possivel exibir o conteudo da acao  retorna true, caso contrario false. 
     */
    function returnFeedback($theAction, $theCase) {
        if (!isset($this->action[$theAction]) || !$this->action[$theAction])
            return false;

        if (!strcmp($theCase, 'true')) {
                echo("        mostraFeedback('" . $this->action[$theAction][1] . "', 'true');\n");
        } else if (!strcmp($theCase, 'false')) {
               	echo("        mostraFeedback('" . $this->action[$theAction][0] . "', 'false');\n");
        }
        return true;
    }
}

?>