<?php


 /**
  * CursoFerramenta Value Object.
  * This class is value object representing database table Curso_Ferramenta
  * This class is intented to be used together with associated Dao object.
  */

class CursoFerramenta {

    /** 
     * Persistent Instance variables. This data is directly 
     * mapped to the columns of database table.
     */
    var $cod_curso;
    var $cod_ferramenta;



    /** 
     * Constructors. DaoGen generates two constructors by default.
     * The first one takes no arguments and provides the most simple
     * way to create object instance. The another one takes one
     * argument, which is the primary key of the corresponding table.
     */

    function CursoFerramenta () {

    }

    /* function CursoFerramenta ($cod_cursoIn, $cod_ferramentaIn) {

          $this->cod_curso = $cod_cursoIn;
          $this->cod_ferramenta = $cod_ferramentaIn;

    } */


    /** 
     * Get- and Set-methods for persistent variables. The default
     * behaviour does not make any checks against malformed data,
     * so these might require some manual additions.
     */

    function getCod_curso() {
          return $this->cod_curso;
    }
    function setCod_curso($cod_cursoIn) {
          $this->cod_curso = $cod_cursoIn;
    }

    function getCod_ferramenta() {
          return $this->cod_ferramenta;
    }
    function setCod_ferramenta($cod_ferramentaIn) {
          $this->cod_ferramenta = $cod_ferramentaIn;
    }



    /** 
     * setAll allows to set all persistent variables in one method call.
     * This is useful, when all data is available and it is needed to 
     * set the initial state of this object. Note that this method will
     * directly modify instance variales, without going trough the 
     * individual set-methods.
     */

    function setAll($cod_cursoIn,
          $cod_ferramentaIn) {
          $this->cod_curso = $cod_cursoIn;
          $this->cod_ferramenta = $cod_ferramentaIn;
    }


    /** 
     * hasEqualMapping-method will compare two CursoFerramenta instances
     * and return true if they contain same values in all persistent instance 
     * variables. If hasEqualMapping returns true, it does not mean the objects
     * are the same instance. However it does mean that in that moment, they 
     * are mapped to the same row in database.
     */
    function hasEqualMapping($valueObject) {

          if ($valueObject->getCod_curso() != $this->cod_curso) {
                    return(false);
          }
          if ($valueObject->getCod_ferramenta() != $this->cod_ferramenta) {
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
        $out = $out."\nclass CursoFerramenta, mapping to table Curso_Ferramenta\n";
        $out = $out."Persistent attributes: \n"; 
        $out = $out."cod_curso = ".$this->cod_curso."\n"; 
        $out = $out."cod_ferramenta = ".$this->cod_ferramenta."\n"; 
        return $out;
    }


    /**
     * Clone will return identical deep copy of this valueObject.
     * Note, that this method is different than the clone() which
     * is defined in java.lang.Object. Here, the retuned cloned object
     * will also have all its attributes cloned.
     */
    function clonar() {
        $cloned = new CursoFerramenta();

        $cloned->setCod_curso($this->cod_curso); 
        $cloned->setCod_ferramenta($this->cod_ferramenta); 

        return $cloned;
    }
}

?>

