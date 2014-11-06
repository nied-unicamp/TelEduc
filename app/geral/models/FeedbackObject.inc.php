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
     * Aчѕes possэveis
     * 
     * @var array 
     */
    var $actions; 
    
    /**
     *
     * @var array 
     */
    var $listOfSentences;
    
    /**
     * Construtor da Classe 
     * 
     * @param array $theListOfSentences 
     */
    function FeedbackObject($theListOfSentences) {
        $this->actions = array();
        $this->listOfSentences = $theListOfSentences;
    }
    
   /**
    * Cria aчуo dos objetos
    * 
    * @param string $newAction Aчуo a ser feita
    * @param string $caseTrue Texto para caso a aчуo seja verdade
    * @param type $caseFalse  Texto para caso a aчуo seja falso
    * @return boolean Retorna True caso seja adicionada a aчуo, e false caso nуo seja possivel.
    */
    function addAction($newAction, $caseTrue, $caseFalse) {
        if ((!isset($newAction)) || (!strcmp($newAction, '')))
            return false;
        $this->action[$newAction] = array(0 => $caseFalse, 1 => $caseTrue);
        return true;
    }
    
    /**
     * Retorna funчуo de Javascript para o Ajax.
     * 
     * @param string $theAction Aчуo escolhida 
     * @param string $theCase Nesta string terс o valor true ou false, relacionada a aчуo
     * @return boolean Caso seja possivel exibir o conteњdo da aчуo  retorna true, caso contrсrio false. 
     */
    function returnFeedback($theAction, $theCase) {
        if (!isset($this->action[$theAction]) || !$this->action[$theAction])
            return false;

        if (!strcmp($theCase, 'true')) {
            if (is_integer($this->action[$theAction][1])) {
                echo("        mostraFeedback('" . htmlentities(Linguas::RetornaFraseDaLista($this->listOfSentences, $this->action[$theAction][1])) . "', 'true');\n");
            } else {
                echo("        mostraFeedback('" . htmlentities($this->action[$theAction][1]) . "', 'true');\n");
            }
        } else if (!strcmp($theCase, 'false')) {
            if (is_integer($this->action[$theAction][0])) {
                echo("        mostraFeedback('" . htmlentities(RetornaFraseDaLista($this->listOfSentences, $this->action[$theAction][0])) . "', 'false');\n");
            } else {
                echo("        mostraFeedback('" . htmlentities($this->action[$theAction][0]) . "', 'false');\n");
            }
        }
        return true;
    }

}

?>