
<?php


 /**
  * Tipo_permissao Value Object.
  * This class is value object representing database table Tipo_permissao
  * This class is intented to be used together with associated Dao object.
  */

 
class Tipo_permissao {

    /** 
     * Persistent Instance variables. This data is directly 
     * mapped to the columns of database table.
     */
    var $cod_tipo;
    var $nome;



    /** 
     * Constructors. DaoGen generates two constructors by default.
     * The first one takes no arguments and provides the most simple
     * way to create object instance. The another one takes one
     * argument, which is the primary key of the corresponding table.
     */

    function Tipo_permissao () {

    }

    /* function Tipo_permissao ($cod_tipoIn) {

          $this->cod_tipo = $cod_tipoIn;

    } */


    /** 
     * Get- and Set-methods for persistent variables. The default
     * behaviour does not make any checks against malformed data,
     * so these might require some manual additions.
     */

    function getCod_tipo() {
          return $this->cod_tipo;
    }
    function setCod_tipo($cod_tipoIn) {
          $this->cod_tipo = $cod_tipoIn;
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

    function setAll($cod_tipoIn,
          $nomeIn) {
          $this->cod_tipo = $cod_tipoIn;
          $this->nome = $nomeIn;
    }


    /** 
     * hasEqualMapping-method will compare two Tipo_permissao instances
     * and return true if they contain same values in all persistent instance 
     * variables. If hasEqualMapping returns true, it does not mean the objects
     * are the same instance. However it does mean that in that moment, they 
     * are mapped to the same row in database.
     */
    function hasEqualMapping($valueObject) {

          if ($valueObject->getCod_tipo() != $this->cod_tipo) {
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
        $out = $out."\nclass Tipo_permissao, mapping to table Tipo_permissao\n";
        $out = $out."Persistent attributes: \n"; 
        $out = $out."cod_tipo = ".$this->cod_tipo."\n"; 
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
        $cloned = new Tipo_permissao();

        $cloned->setCod_tipo($this->cod_tipo); 
        $cloned->setNome($this->nome); 

        return $cloned;
    }
}

?>

