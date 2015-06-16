<?php


 /**
  * Participa Value Object.
  * This class is value object representing database table Participa
  * This class is intented to be used together with associated Dao object.
  */

class Participa {

    /** 
     * Persistent Instance variables. This data is directly 
     * mapped to the columns of database table.
     */
    var $cod_usuario;
    var $cod_curso;
    var $cod_papel;
    var $data_inscricao;



    /** 
     * Constructors. DaoGen generates two constructors by default.
     * The first one takes no arguments and provides the most simple
     * way to create object instance. The another one takes one
     * argument, which is the primary key of the corresponding table.
     */

    function Participa () {

    }

    /* function Participa ($cod_usuarioIn, $cod_cursoIn, $cod_papelIn) {

          $this->cod_usuario = $cod_usuarioIn;
          $this->cod_curso = $cod_cursoIn;
          $this->cod_papel = $cod_papelIn;

    } */


    /** 
     * Get- and Set-methods for persistent variables. The default
     * behaviour does not make any checks against malformed data,
     * so these might require some manual additions.
     */

    function getCod_usuario() {
          return $this->cod_usuario;
    }
    function setCod_usuario($cod_usuarioIn) {
          $this->cod_usuario = $cod_usuarioIn;
    }

    function getCod_curso() {
          return $this->cod_curso;
    }
    function setCod_curso($cod_cursoIn) {
          $this->cod_curso = $cod_cursoIn;
    }

    function getCod_papel() {
          return $this->cod_papel;
    }
    function setCod_papel($cod_papelIn) {
          $this->cod_papel = $cod_papelIn;
    }

    function getData_inscricao() {
          return $this->data_inscricao;
    }
    function setData_inscricao($data_inscricaoIn) {
          $this->data_inscricao = $data_inscricaoIn;
    }



    /** 
     * setAll allows to set all persistent variables in one method call.
     * This is useful, when all data is available and it is needed to 
     * set the initial state of this object. Note that this method will
     * directly modify instance variales, without going trough the 
     * individual set-methods.
     */

    function setAll($cod_usuarioIn,
          $cod_cursoIn,
          $cod_papelIn,
          $data_inscricaoIn) {
          $this->cod_usuario = $cod_usuarioIn;
          $this->cod_curso = $cod_cursoIn;
          $this->cod_papel = $cod_papelIn;
          $this->data_inscricao = $data_inscricaoIn;
    }


    /** 
     * hasEqualMapping-method will compare two Participa instances
     * and return true if they contain same values in all persistent instance 
     * variables. If hasEqualMapping returns true, it does not mean the objects
     * are the same instance. However it does mean that in that moment, they 
     * are mapped to the same row in database.
     */
    function hasEqualMapping($valueObject) {

          if ($valueObject->getCod_usuario() != $this->cod_usuario) {
                    return(false);
          }
          if ($valueObject->getCod_curso() != $this->cod_curso) {
                    return(false);
          }
          if ($valueObject->getCod_papel() != $this->cod_papel) {
                    return(false);
          }
          if ($valueObject->getData_inscricao() != $this->data_inscricao) {
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
        $out = $out."\nclass Participa, mapping to table Participa\n";
        $out = $out."Persistent attributes: \n"; 
        $out = $out."cod_usuario = ".$this->cod_usuario."\n"; 
        $out = $out."cod_curso = ".$this->cod_curso."\n"; 
        $out = $out."cod_papel = ".$this->cod_papel."\n"; 
        $out = $out."data_inscricao = ".$this->data_inscricao."\n"; 
        return $out;
    }


    /**
     * Clone will return identical deep copy of this valueObject.
     * Note, that this method is different than the clone() which
     * is defined in java.lang.Object. Here, the retuned cloned object
     * will also have all its attributes cloned.
     */
    function clonar() {
        $cloned = new Participa();

        $cloned->setCod_usuario($this->cod_usuario); 
        $cloned->setCod_curso($this->cod_curso); 
        $cloned->setCod_papel($this->cod_papel); 
        $cloned->setData_inscricao($this->data_inscricao); 

        return $cloned;
    }

}

?>

