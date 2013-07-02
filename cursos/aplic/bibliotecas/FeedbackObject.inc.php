<?php

class FeedbackObject{

  var $actions; //acoes possiveis
  var $listOfSentences;

  function FeedbackObject($theListOfSentences){
    $this->actions = array();
    $this->listOfSentences = $theListOfSentences;
  }

  function addAction($newAction, $caseTrue, $caseFalse){
    if((!isset($newAction)) || (!strcmp($newAction, ''))) return false;
    $this->action[$newAction] = array( 0 => $caseFalse, 1 => $caseTrue);
    return true;
  }

  function returnFeedback($theAction, $theCase){
    if(!isset($this->action[$theAction]) || !$this->action[$theAction]) return false;

    if(!strcmp($theCase, 'true')){
      if(is_integer($this->action[$theAction][1])){
        echo("        mostraFeedback('".htmlentities(RetornaFraseDaLista($this->listOfSentences, $this->action[$theAction][1]))."', 'true');\n");
      }else{
        echo("        mostraFeedback('".htmlentities($this->action[$theAction][1])."', 'true');\n");
      }
    }else if(!strcmp($theCase, 'false')){
      if(is_integer($this->action[$theAction][0])){
        echo("        mostraFeedback('".htmlentities(RetornaFraseDaLista($this->listOfSentences, $this->action[$theAction][0]))."', 'false');\n");
      }else{
        echo("        mostraFeedback('".htmlentities($this->action[$theAction][0])."', 'false');\n");
      }
    }
    return true;
  }
}

?>