<?php


 /**
  * Curso Value Object.
  * This class is value object representing database table curso
  * This class is intented to be used together with associated Dao object.
  */

class Curso {

    /** 
     * Persistent Instance variables. This data is directly 
     * mapped to the columns of database table.
     */
    var $cod_curso;
    var $nome_curso;
    var $inscricao_inicio;
    var $inscricao_fim;
    var $curso_inicio;
    var $curso_fim;
    var $informacoes;
    var $publico_alvo;
    var $tipo_inscricao;
    var $num_alunos;
    var $cod_coordenador;
    var $acesso_visitante;
    var $cod_pasta;
    var $timestamp;
    var $cod_lingua;



    /** 
     * Constructors. DaoGen generates two constructors by default.
     * The first one takes no arguments and provides the most simple
     * way to create object instance. The another one takes one
     * argument, which is the primary key of the corresponding table.
     */

    function Curso () {

    }

    /* function Curso ($cod_cursoIn) {

          $this->cod_curso = $cod_cursoIn;

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

    function getNome_curso() {
          return $this->nome_curso;
    }
    function setNome_curso($nome_cursoIn) {
          $this->nome_curso = $nome_cursoIn;
    }

    function getInscricao_inicio() {
          return $this->inscricao_inicio;
    }
    function setInscricao_inicio($inscricao_inicioIn) {
          $this->inscricao_inicio = $inscricao_inicioIn;
    }

    function getInscricao_fim() {
          return $this->inscricao_fim;
    }
    function setInscricao_fim($inscricao_fimIn) {
          $this->inscricao_fim = $inscricao_fimIn;
    }

    function getCurso_inicio() {
          return $this->curso_inicio;
    }
    function setCurso_inicio($curso_inicioIn) {
          $this->curso_inicio = $curso_inicioIn;
    }

    function getCurso_fim() {
          return $this->curso_fim;
    }
    function setCurso_fim($curso_fimIn) {
          $this->curso_fim = $curso_fimIn;
    }

    function getInformacoes() {
          return $this->informacoes;
    }
    function setInformacoes($informacoesIn) {
          $this->informacoes = $informacoesIn;
    }

    function getPublico_alvo() {
          return $this->publico_alvo;
    }
    function setPublico_alvo($publico_alvoIn) {
          $this->publico_alvo = $publico_alvoIn;
    }

    function getTipo_inscricao() {
          return $this->tipo_inscricao;
    }
    function setTipo_inscricao($tipo_inscricaoIn) {
          $this->tipo_inscricao = $tipo_inscricaoIn;
    }

    function getNum_alunos() {
          return $this->num_alunos;
    }
    function setNum_alunos($num_alunosIn) {
          $this->num_alunos = $num_alunosIn;
    }

    function getCod_coordenador() {
          return $this->cod_coordenador;
    }
    function setCod_coordenador($cod_coordenadorIn) {
          $this->cod_coordenador = $cod_coordenadorIn;
    }

    function getAcesso_visitante() {
          return $this->acesso_visitante;
    }
    function setAcesso_visitante($acesso_visitanteIn) {
          $this->acesso_visitante = $acesso_visitanteIn;
    }

    function getCod_pasta() {
          return $this->cod_pasta;
    }
    function setCod_pasta($cod_pastaIn) {
          $this->cod_pasta = $cod_pastaIn;
    }

    function getTimestamp() {
          return $this->timestamp;
    }
    function setTimestamp($timestampIn) {
          $this->timestamp = $timestampIn;
    }

    function getCod_lingua() {
          return $this->cod_lingua;
    }
    function setCod_lingua($cod_linguaIn) {
          $this->cod_lingua = $cod_linguaIn;
    }



    /** 
     * setAll allows to set all persistent variables in one method call.
     * This is useful, when all data is available and it is needed to 
     * set the initial state of this object. Note that this method will
     * directly modify instance variales, without going trough the 
     * individual set-methods.
     */

    function setAll($cod_cursoIn,
          $nome_cursoIn,
          $inscricao_inicioIn,
          $inscricao_fimIn,
          $curso_inicioIn,
          $curso_fimIn,
          $informacoesIn,
          $publico_alvoIn,
          $tipo_inscricaoIn,
          $num_alunosIn,
          $cod_coordenadorIn,
          $acesso_visitanteIn,
          $cod_pastaIn,
          $timestampIn,
          $cod_linguaIn) {
          $this->cod_curso = $cod_cursoIn;
          $this->nome_curso = $nome_cursoIn;
          $this->inscricao_inicio = $inscricao_inicioIn;
          $this->inscricao_fim = $inscricao_fimIn;
          $this->curso_inicio = $curso_inicioIn;
          $this->curso_fim = $curso_fimIn;
          $this->informacoes = $informacoesIn;
          $this->publico_alvo = $publico_alvoIn;
          $this->tipo_inscricao = $tipo_inscricaoIn;
          $this->num_alunos = $num_alunosIn;
          $this->cod_coordenador = $cod_coordenadorIn;
          $this->acesso_visitante = $acesso_visitanteIn;
          $this->cod_pasta = $cod_pastaIn;
          $this->timestamp = $timestampIn;
          $this->cod_lingua = $cod_linguaIn;
    }


    /** 
     * hasEqualMapping-method will compare two Curso instances
     * and return true if they contain same values in all persistent instance 
     * variables. If hasEqualMapping returns true, it does not mean the objects
     * are the same instance. However it does mean that in that moment, they 
     * are mapped to the same row in database.
     */
    function hasEqualMapping($valueObject) {

          if ($valueObject->getCod_curso() != $this->cod_curso) {
                    return(false);
          }
          if ($valueObject->getNome_curso() != $this->nome_curso) {
                    return(false);
          }
          if ($valueObject->getInscricao_inicio() != $this->inscricao_inicio) {
                    return(false);
          }
          if ($valueObject->getInscricao_fim() != $this->inscricao_fim) {
                    return(false);
          }
          if ($valueObject->getCurso_inicio() != $this->curso_inicio) {
                    return(false);
          }
          if ($valueObject->getCurso_fim() != $this->curso_fim) {
                    return(false);
          }
          if ($valueObject->getInformacoes() != $this->informacoes) {
                    return(false);
          }
          if ($valueObject->getPublico_alvo() != $this->publico_alvo) {
                    return(false);
          }
          if ($valueObject->getTipo_inscricao() != $this->tipo_inscricao) {
                    return(false);
          }
          if ($valueObject->getNum_alunos() != $this->num_alunos) {
                    return(false);
          }
          if ($valueObject->getCod_coordenador() != $this->cod_coordenador) {
                    return(false);
          }
          if ($valueObject->getAcesso_visitante() != $this->acesso_visitante) {
                    return(false);
          }
          if ($valueObject->getCod_pasta() != $this->cod_pasta) {
                    return(false);
          }
          if ($valueObject->getTimestamp() != $this->timestamp) {
                    return(false);
          }
          if ($valueObject->getCod_lingua() != $this->cod_lingua) {
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
        $out = $out."\nclass Curso, mapping to table curso\n";
        $out = $out."Persistent attributes: \n"; 
        $out = $out."cod_curso = ".$this->cod_curso."\n"; 
        $out = $out."nome_curso = ".$this->nome_curso."\n"; 
        $out = $out."inscricao_inicio = ".$this->inscricao_inicio."\n"; 
        $out = $out."inscricao_fim = ".$this->inscricao_fim."\n"; 
        $out = $out."curso_inicio = ".$this->curso_inicio."\n"; 
        $out = $out."curso_fim = ".$this->curso_fim."\n"; 
        $out = $out."informacoes = ".$this->informacoes."\n"; 
        $out = $out."publico_alvo = ".$this->publico_alvo."\n"; 
        $out = $out."tipo_inscricao = ".$this->tipo_inscricao."\n"; 
        $out = $out."num_alunos = ".$this->num_alunos."\n"; 
        $out = $out."cod_coordenador = ".$this->cod_coordenador."\n"; 
        $out = $out."acesso_visitante = ".$this->acesso_visitante."\n"; 
        $out = $out."cod_pasta = ".$this->cod_pasta."\n"; 
        $out = $out."timestamp = ".$this->timestamp."\n"; 
        $out = $out."cod_lingua = ".$this->cod_lingua."\n"; 
        return $out;
    }


    /**
     * Clone will return identical deep copy of this valueObject.
     * Note, that this method is different than the clone() which
     * is defined in java.lang.Object. Here, the retuned cloned object
     * will also have all its attributes cloned.
     */
    function clonar() {
        $cloned = new Curso();

        $cloned->setCod_curso($this->cod_curso); 
        $cloned->setNome_curso($this->nome_curso); 
        $cloned->setInscricao_inicio($this->inscricao_inicio); 
        $cloned->setInscricao_fim($this->inscricao_fim); 
        $cloned->setCurso_inicio($this->curso_inicio); 
        $cloned->setCurso_fim($this->curso_fim); 
        $cloned->setInformacoes($this->informacoes); 
        $cloned->setPublico_alvo($this->publico_alvo); 
        $cloned->setTipo_inscricao($this->tipo_inscricao); 
        $cloned->setNum_alunos($this->num_alunos); 
        $cloned->setCod_coordenador($this->cod_coordenador); 
        $cloned->setAcesso_visitante($this->acesso_visitante); 
        $cloned->setCod_pasta($this->cod_pasta); 
        $cloned->setTimestamp($this->timestamp); 
        $cloned->setCod_lingua($this->cod_lingua); 

        return $cloned;
    }
}

?>