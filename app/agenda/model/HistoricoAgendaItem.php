<?php


 /**
  * Historico_Agenda_Item Value Object.
  * This class is value object representing database table Historico_Agenda_Item
  * This class is intented to be used together with associated Dao object.
  */
  
class Historico_Agenda_Item {

    /** 
     * Persistent Instance variables. This data is directly 
     * mapped to the columns of database table.
     */
    var $cod_historico;
    var $Usuario_cod_usuario;
    var $Acao_cod_acao;
    var $Agenda;
    var $data;



    /** 
     * Constructors. DaoGen generates two constructors by default.
     * The first one takes no arguments and provides the most simple
     * way to create object instance. The another one takes one
     * argument, which is the primary key of the corresponding table.
     */

    function Historico_Agenda_Item () {

    }

    /* function Historico_Agenda_Item ($cod_historicoIn) {

          $this->cod_historico = $cod_historicoIn;

    } */


    /** 
     * Get- and Set-methods for persistent variables. The default
     * behaviour does not make any checks against malformed data,
     * so these might require some manual additions.
     */

    function getCod_historico() {
          return $this->cod_historico;
    }
    function setCod_historico($cod_historicoIn) {
          $this->cod_historico = $cod_historicoIn;
    }

    function getUsuario_cod_usuario() {
          return $this->Usuario_cod_usuario;
    }
    function setUsuario_cod_usuario($Usuario_cod_usuarioIn) {
          $this->Usuario_cod_usuario = $Usuario_cod_usuarioIn;
    }

    function getAcao_cod_acao() {
          return $this->Acao_cod_acao;
    }
    function setAcao_cod_acao($Acao_cod_acaoIn) {
          $this->Acao_cod_acao = $Acao_cod_acaoIn;
    }

    function getAgenda() {
          return $this->Agenda;
    }
    function setAgenda($AgendaIn) {
          $this->Agenda = $AgendaIn;
    }

    function getData() {
          return $this->data;
    }
    function setData($dataIn) {
          $this->data = $dataIn;
    }



    /** 
     * setAll allows to set all persistent variables in one method call.
     * This is useful, when all data is available and it is needed to 
     * set the initial state of this object. Note that this method will
     * directly modify instance variales, without going trough the 
     * individual set-methods.
     */

    function setAll($cod_historicoIn,
          $Usuario_cod_usuarioIn,
          $Acao_cod_acaoIn,
          $AgendaIn,
          $dataIn) {
          $this->cod_historico = $cod_historicoIn;
          $this->Usuario_cod_usuario = $Usuario_cod_usuarioIn;
          $this->Acao_cod_acao = $Acao_cod_acaoIn;
          $this->Agenda = $AgendaIn;
          $this->data = $dataIn;
    }


    /** 
     * hasEqualMapping-method will compare two Historico_Agenda_Item instances
     * and return true if they contain same values in all persistent instance 
     * variables. If hasEqualMapping returns true, it does not mean the objects
     * are the same instance. However it does mean that in that moment, they 
     * are mapped to the same row in database.
     */
    function hasEqualMapping($valueObject) {

          if ($valueObject->getCod_historico() != $this->cod_historico) {
                    return(false);
          }
          if ($valueObject->getUsuario_cod_usuario() != $this->Usuario_cod_usuario) {
                    return(false);
          }
          if ($valueObject->getAcao_cod_acao() != $this->Acao_cod_acao) {
                    return(false);
          }
          if ($valueObject->getAgenda() != $this->Agenda) {
                    return(false);
          }
          if ($valueObject->getData() != $this->data) {
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
        $out = $out."\nclass Historico_Agenda_Item, mapping to table Historico_Agenda_Item\n";
        $out = $out."Persistent attributes: \n"; 
        $out = $out."cod_historico = ".$this->cod_historico."\n"; 
        $out = $out."Usuario_cod_usuario = ".$this->Usuario_cod_usuario."\n"; 
        $out = $out."Acao_cod_acao = ".$this->Acao_cod_acao."\n"; 
        $out = $out."Agenda = ".$this->Agenda."\n"; 
        $out = $out."data = ".$this->data."\n"; 
        return $out;
    }


    /**
     * Clone will return identical deep copy of this valueObject.
     * Note, that this method is different than the clone() which
     * is defined in java.lang.Object. Here, the retuned cloned object
     * will also have all its attributes cloned.
     */
    function clonar() {
        $cloned = new Historico_Agenda_Item();

        $cloned->setCod_historico($this->cod_historico); 
        $cloned->setUsuario_cod_usuario($this->Usuario_cod_usuario); 
        $cloned->setAcao_cod_acao($this->Acao_cod_acao); 
        $cloned->setAgenda($this->Agenda); 
        $cloned->setData($this->data); 

        return $cloned;
    }

}

?>

          