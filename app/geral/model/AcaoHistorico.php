
Save this code in a file named "Acao_Historico.php"

<?php


 /**
  * Acao_Historico Value Object.
  * This class is value object representing database table Acao_Historico
  * This class is intented to be used together with associated Dao object.
  */

class Acao_Historico {

    /** 
     * Persistent Instance variables. This data is directly 
     * mapped to the columns of database table.
     */
    var $cod_acao;
    var $nome;



    /** 
     * Constructors. DaoGen generates two constructors by default.
     * The first one takes no arguments and provides the most simple
     * way to create object instance. The another one takes one
     * argument, which is the primary key of the corresponding table.
     */

    function Acao_Historico () {

    }

    /* function Acao_Historico ($cod_acaoIn) {

          $this->cod_acao = $cod_acaoIn;

    } */


    /** 
     * Get- and Set-methods for persistent variables. The default
     * behaviour does not make any checks against malformed data,
     * so these might require some manual additions.
     */

    function getCod_acao() {
          return $this->cod_acao;
    }
    function setCod_acao($cod_acaoIn) {
          $this->cod_acao = $cod_acaoIn;
    }

    function getNome() {
          return $this->nome;
    }
    function setNome($nomeIn) {
          $this->nome = $nomeIn;
    }



    /** 
     * setAll allows to set all persistent variables in one method call.
     * This is useful, when all data is available and it is needed to 
     * set the initial state of this object. Note that this method will
     * directly modify instance variales, without going trough the 
     * individual set-methods.
     */

    function setAll($cod_acaoIn,
          $nomeIn) {
          $this->cod_acao = $cod_acaoIn;
          $this->nome = $nomeIn;
    }


    /** 
     * hasEqualMapping-method will compare two Acao_Historico instances
     * and return true if they contain same values in all persistent instance 
     * variables. If hasEqualMapping returns true, it does not mean the objects
     * are the same instance. However it does mean that in that moment, they 
     * are mapped to the same row in database.
     */
    function hasEqualMapping($valueObject) {

          if ($valueObject->getCod_acao() != $this->cod_acao) {
                    return(false);
          }
          if ($valueObject->getNome() != $this->nome) {
                    return(false);
          }

          return true;
    }



    /**
     * toString will return String object representing the state of this 
     * valueObject. This is useful during application development, and 
     * possibly when application is writing object states in textlog.
     */
    function toString() {
        $out = $this->getDaogenVersion();
        $out = $out."\nclass Acao_Historico, mapping to table Acao_Historico\n";
        $out = $out."Persistent attributes: \n"; 
        $out = $out."cod_acao = ".$this->cod_acao."\n"; 
        $out = $out."nome = ".$this->nome."\n"; 
        return $out;
    }


    /**
     * Clone will return identical deep copy of this valueObject.
     * Note, that this method is different than the clone() which
     * is defined in java.lang.Object. Here, the retuned cloned object
     * will also have all its attributes cloned.
     */
    function clonar() {
        $cloned = new Acao_Historico();

        $cloned->setCod_acao($this->cod_acao); 
        $cloned->setNome($this->nome); 

        return $cloned;
    }
}

?>
