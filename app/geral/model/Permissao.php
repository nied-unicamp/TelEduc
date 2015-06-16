<?php


 /**
  * Permissao Value Object.
  * This class is value object representing database table Permissao
  * This class is intented to be used together with associated Dao object.
  */

class Permissao {

    /** 
     * Persistent Instance variables. This data is directly 
     * mapped to the columns of database table.
     */
    var $cod_papel;
    var $cod_ferramenta;
    var $cod_tipo_permissao;



    /** 
     * Constructors. DaoGen generates two constructors by default.
     * The first one takes no arguments and provides the most simple
     * way to create object instance. The another one takes one
     * argument, which is the primary key of the corresponding table.
     */

    function Permissao () {

    }

    /* function Permissao ($cod_papelIn, $cod_ferramentaIn, $cod_tipo_permissaoIn) {

          $this->cod_papel = $cod_papelIn;
          $this->cod_ferramenta = $cod_ferramentaIn;
          $this->cod_tipo_permissao = $cod_tipo_permissaoIn;

    } */


    /** 
     * Get- and Set-methods for persistent variables. The default
     * behaviour does not make any checks against malformed data,
     * so these might require some manual additions.
     */

    function getCod_papel() {
          return $this->cod_papel;
    }
    function setCod_papel($cod_papelIn) {
          $this->cod_papel = $cod_papelIn;
    }

    function getCod_ferramenta() {
          return $this->cod_ferramenta;
    }
    function setCod_ferramenta($cod_ferramentaIn) {
          $this->cod_ferramenta = $cod_ferramentaIn;
    }

    function getCod_tipo_permissao() {
          return $this->cod_tipo_permissao;
    }
    function setCod_tipo_permissao($cod_tipo_permissaoIn) {
          $this->cod_tipo_permissao = $cod_tipo_permissaoIn;
    }



    /** 
     * setAll allows to set all persistent variables in one method call.
     * This is useful, when all data is available and it is needed to 
     * set the initial state of this object. Note that this method will
     * directly modify instance variales, without going trough the 
     * individual set-methods.
     */

    function setAll($cod_papelIn,
          $cod_ferramentaIn,
          $cod_tipo_permissaoIn) {
          $this->cod_papel = $cod_papelIn;
          $this->cod_ferramenta = $cod_ferramentaIn;
          $this->cod_tipo_permissao = $cod_tipo_permissaoIn;
    }


    /** 
     * hasEqualMapping-method will compare two Permissao instances
     * and return true if they contain same values in all persistent instance 
     * variables. If hasEqualMapping returns true, it does not mean the objects
     * are the same instance. However it does mean that in that moment, they 
     * are mapped to the same row in database.
     */
    function hasEqualMapping($valueObject) {

          if ($valueObject->getCod_papel() != $this->cod_papel) {
                    return(false);
          }
          if ($valueObject->getCod_ferramenta() != $this->cod_ferramenta) {
                    return(false);
          }
          if ($valueObject->getCod_tipo_permissao() != $this->cod_tipo_permissao) {
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
        $out = $out."\nclass Permissao, mapping to table Permissao\n";
        $out = $out."Persistent attributes: \n"; 
        $out = $out."cod_papel = ".$this->cod_papel."\n"; 
        $out = $out."cod_ferramenta = ".$this->cod_ferramenta."\n"; 
        $out = $out."cod_tipo_permissao = ".$this->cod_tipo_permissao."\n"; 
        return $out;
    }


    /**
     * Clone will return identical deep copy of this valueObject.
     * Note, that this method is different than the clone() which
     * is defined in java.lang.Object. Here, the retuned cloned object
     * will also have all its attributes cloned.
     */
    function clonar() {
        $cloned = new Permissao();

        $cloned->setCod_papel($this->cod_papel); 
        $cloned->setCod_ferramenta($this->cod_ferramenta); 
        $cloned->setCod_tipo_permissao($this->cod_tipo_permissao); 

        return $cloned;
    }
}

?>

