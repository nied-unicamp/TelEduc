<?php

/**
 * Agenda_Item Value Object.
 * This class is value object representing database table Agenda_Item
 * This class is intented to be used together with associated Dao object.
 */

class Agenda_Item {

    /** 
     * Persistent Instance variables. This data is directly 
     * mapped to the columns of database table.
     */
    var $cod_item;
    var $Curso_cod_curso;
    var $Usuario_cod_usuario;
    var $titulo;
    var $texto;
    var $situacao;
    var $data_criacao;
    var $data_publicacao;
    var $status;
    var $inicio_edicao;



    /** 
     * Constructors. DaoGen generates two constructors by default.
     * The first one takes no arguments and provides the most simple
     * way to create object instance. The another one takes one
     * argument, which is the primary key of the corresponding table.
     */

    function Agenda_Item () {

    }

    /* function Agenda_Item ($cod_itemIn) {

          $this->cod_item = $cod_itemIn;

    } */


    /** 
     * Get- and Set-methods for persistent variables. The default
     * behaviour does not make any checks against malformed data,
     * so these might require some manual additions.
     */

    function getCod_item() {
          return $this->cod_item;
    }
    function setCod_item($cod_itemIn) {
          $this->cod_item = $cod_itemIn;
    }

    function getCurso_cod_curso() {
          return $this->Curso_cod_curso;
    }
    function setCurso_cod_curso($Curso_cod_cursoIn) {
          $this->Curso_cod_curso = $Curso_cod_cursoIn;
    }

    function getUsuario_cod_usuario() {
          return $this->Usuario_cod_usuario;
    }
    function setUsuario_cod_usuario($Usuario_cod_usuarioIn) {
          $this->Usuario_cod_usuario = $Usuario_cod_usuarioIn;
    }

    function getTitulo() {
          return $this->titulo;
    }
    function setTitulo($tituloIn) {
          $this->titulo = $tituloIn;
    }

    function getTexto() {
          return $this->texto;
    }
    function setTexto($textoIn) {
          $this->texto = $textoIn;
    }

    function getSituacao() {
          return $this->situacao;
    }
    function setSituacao($situacaoIn) {
          $this->situacao = $situacaoIn;
    }

    function getData_criacao() {
          return $this->data_criacao;
    }
    function setData_criacao($data_criacaoIn) {
          $this->data_criacao = $data_criacaoIn;
    }

    function getData_publicacao() {
          return $this->data_publicacao;
    }
    function setData_publicacao($data_publicacaoIn) {
          $this->data_publicacao = $data_publicacaoIn;
    }

    function getStatus() {
          return $this->status;
    }
    function setStatus($statusIn) {
          $this->status = $statusIn;
    }

    function getInicio_edicao() {
          return $this->inicio_edicao;
    }
    function setInicio_edicao($inicio_edicaoIn) {
          $this->inicio_edicao = $inicio_edicaoIn;
    }



    /** 
     * setAll allows to set all persistent variables in one method call.
     * This is useful, when all data is available and it is needed to 
     * set the initial state of this object. Note that this method will
     * directly modify instance variales, without going trough the 
     * individual set-methods.
     */

    function setAll($cod_itemIn,
          $Curso_cod_cursoIn,
          $Usuario_cod_usuarioIn,
          $tituloIn,
          $textoIn,
          $situacaoIn,
          $data_criacaoIn,
          $data_publicacaoIn,
          $statusIn,
          $inicio_edicaoIn) {
          $this->cod_item = $cod_itemIn;
          $this->Curso_cod_curso = $Curso_cod_cursoIn;
          $this->Usuario_cod_usuario = $Usuario_cod_usuarioIn;
          $this->titulo = $tituloIn;
          $this->texto = $textoIn;
          $this->situacao = $situacaoIn;
          $this->data_criacao = $data_criacaoIn;
          $this->data_publicacao = $data_publicacaoIn;
          $this->status = $statusIn;
          $this->inicio_edicao = $inicio_edicaoIn;
    }


    /** 
     * hasEqualMapping-method will compare two Agenda_Item instances
     * and return true if they contain same values in all persistent instance 
     * variables. If hasEqualMapping returns true, it does not mean the objects
     * are the same instance. However it does mean that in that moment, they 
     * are mapped to the same row in database.
     */
    function hasEqualMapping($valueObject) {

          if ($valueObject->getCod_item() != $this->cod_item) {
                    return(false);
          }
          if ($valueObject->getCurso_cod_curso() != $this->Curso_cod_curso) {
                    return(false);
          }
          if ($valueObject->getUsuario_cod_usuario() != $this->Usuario_cod_usuario) {
                    return(false);
          }
          if ($valueObject->getTitulo() != $this->titulo) {
                    return(false);
          }
          if ($valueObject->getTexto() != $this->texto) {
                    return(false);
          }
          if ($valueObject->getSituacao() != $this->situacao) {
                    return(false);
          }
          if ($valueObject->getData_criacao() != $this->data_criacao) {
                    return(false);
          }
          if ($valueObject->getData_publicacao() != $this->data_publicacao) {
                    return(false);
          }
          if ($valueObject->getStatus() != $this->status) {
                    return(false);
          }
          if ($valueObject->getInicio_edicao() != $this->inicio_edicao) {
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
        $out = $out."\nclass Agenda_Item, mapping to table Agenda_Item\n";
        $out = $out."Persistent attributes: \n"; 
        $out = $out."cod_item = ".$this->cod_item."\n"; 
        $out = $out."Curso_cod_curso = ".$this->Curso_cod_curso."\n"; 
        $out = $out."Usuario_cod_usuario = ".$this->Usuario_cod_usuario."\n"; 
        $out = $out."titulo = ".$this->titulo."\n"; 
        $out = $out."texto = ".$this->texto."\n"; 
        $out = $out."situacao = ".$this->situacao."\n"; 
        $out = $out."data_criacao = ".$this->data_criacao."\n"; 
        $out = $out."data_publicacao = ".$this->data_publicacao."\n"; 
        $out = $out."status = ".$this->status."\n"; 
        $out = $out."inicio_edicao = ".$this->inicio_edicao."\n"; 
        return $out;
    }


    /**
     * Clone will return identical deep copy of this valueObject.
     * Note, that this method is different than the clone() which
     * is defined in java.lang.Object. Here, the retuned cloned object
     * will also have all its attributes cloned.
     */
    function clonar() {
        $cloned = new Agenda_Item();

        $cloned->setCod_item($this->cod_item); 
        $cloned->setCurso_cod_curso($this->Curso_cod_curso); 
        $cloned->setUsuario_cod_usuario($this->Usuario_cod_usuario); 
        $cloned->setTitulo($this->titulo); 
        $cloned->setTexto($this->texto); 
        $cloned->setSituacao($this->situacao); 
        $cloned->setData_criacao($this->data_criacao); 
        $cloned->setData_publicacao($this->data_publicacao); 
        $cloned->setStatus($this->status); 
        $cloned->setInicio_edicao($this->inicio_edicao); 

        return $cloned;
    }
}

?>
