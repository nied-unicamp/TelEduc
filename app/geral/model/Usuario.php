<?php


 /**
  * Usuario Value Object.
  * This class is value object representing database table Usuario
  * This class is intented to be used together with associated Dao object.
  */

class Usuario {

    /** 
     * Persistent Instance variables. This data is directly 
     * mapped to the columns of database table.
     */
    var $cod_usuario;
    var $login;
    var $senha;
    var $nome_usuario;
    var $rg;
    var $email;
    var $telefone;
    var $endereco;
    var $cidade;
    var $estado;
    var $pais;
    var $data_nasc;
    var $sexo;
    var $local_trabalho;
    var $profissao;
    var $escolaridade;
    var $informacoes;
    var $data_inscricao;
    var $cod_lingua;
    var $confirmacao;



    /** 
     * Constructors. DaoGen generates two constructors by default.
     * The first one takes no arguments and provides the most simple
     * way to create object instance. The another one takes one
     * argument, which is the primary key of the corresponding table.
     */

    function Usuario () {

    }

    /* function Usuario ($cod_usuarioIn) {

          $this->cod_usuario = $cod_usuarioIn;

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

    function getLogin() {
          return $this->login;
    }
    function setLogin($loginIn) {
          $this->login = $loginIn;
    }

    function getSenha() {
          return $this->senha;
    }
    function setSenha($senhaIn) {
          $this->senha = $senhaIn;
    }

    function getNome_usuario() {
          return $this->nome_usuario;
    }
    function setNome_usuario($nome_usuarioIn) {
          $this->nome_usuario = $nome_usuarioIn;
    }

    function getRg() {
          return $this->rg;
    }
    function setRg($rgIn) {
          $this->rg = $rgIn;
    }

    function getEmail() {
          return $this->email;
    }
    function setEmail($emailIn) {
          $this->email = $emailIn;
    }

    function getTelefone() {
          return $this->telefone;
    }
    function setTelefone($telefoneIn) {
          $this->telefone = $telefoneIn;
    }

    function getEndereco() {
          return $this->endereco;
    }
    function setEndereco($enderecoIn) {
          $this->endereco = $enderecoIn;
    }

    function getCidade() {
          return $this->cidade;
    }
    function setCidade($cidadeIn) {
          $this->cidade = $cidadeIn;
    }

    function getEstado() {
          return $this->estado;
    }
    function setEstado($estadoIn) {
          $this->estado = $estadoIn;
    }

    function getPais() {
          return $this->pais;
    }
    function setPais($paisIn) {
          $this->pais = $paisIn;
    }

    function getData_nasc() {
          return $this->data_nasc;
    }
    function setData_nasc($data_nascIn) {
          $this->data_nasc = $data_nascIn;
    }

    function getSexo() {
          return $this->sexo;
    }
    function setSexo($sexoIn) {
          $this->sexo = $sexoIn;
    }

    function getLocal_trabalho() {
          return $this->local_trabalho;
    }
    function setLocal_trabalho($local_trabalhoIn) {
          $this->local_trabalho = $local_trabalhoIn;
    }

    function getProfissao() {
          return $this->profissao;
    }
    function setProfissao($profissaoIn) {
          $this->profissao = $profissaoIn;
    }

    function getEscolaridade() {
          return $this->escolaridade;
    }
    function setEscolaridade($escolaridadeIn) {
          $this->escolaridade = $escolaridadeIn;
    }

    function getInformacoes() {
          return $this->informacoes;
    }
    function setInformacoes($informacoesIn) {
          $this->informacoes = $informacoesIn;
    }

    function getData_inscricao() {
          return $this->data_inscricao;
    }
    function setData_inscricao($data_inscricaoIn) {
          $this->data_inscricao = $data_inscricaoIn;
    }

    function getCod_lingua() {
          return $this->cod_lingua;
    }
    function setCod_lingua($cod_linguaIn) {
          $this->cod_lingua = $cod_linguaIn;
    }

    function getConfirmacao() {
          return $this->confirmacao;
    }
    function setConfirmacao($confirmacaoIn) {
          $this->confirmacao = $confirmacaoIn;
    }



    /** 
     * setAll allows to set all persistent variables in one method call.
     * This is useful, when all data is available and it is needed to 
     * set the initial state of this object. Note that this method will
     * directly modify instance variales, without going trough the 
     * individual set-methods.
     */

    function setAll($cod_usuarioIn,
          $loginIn,
          $senhaIn,
          $nome_usuarioIn,
          $rgIn,
          $emailIn,
          $telefoneIn,
          $enderecoIn,
          $cidadeIn,
          $estadoIn,
          $paisIn,
          $data_nascIn,
          $sexoIn,
          $local_trabalhoIn,
          $profissaoIn,
          $escolaridadeIn,
          $informacoesIn,
          $data_inscricaoIn,
          $cod_linguaIn,
          $confirmacaoIn) {
          $this->cod_usuario = $cod_usuarioIn;
          $this->login = $loginIn;
          $this->senha = $senhaIn;
          $this->nome_usuario = $nome_usuarioIn;
          $this->rg = $rgIn;
          $this->email = $emailIn;
          $this->telefone = $telefoneIn;
          $this->endereco = $enderecoIn;
          $this->cidade = $cidadeIn;
          $this->estado = $estadoIn;
          $this->pais = $paisIn;
          $this->data_nasc = $data_nascIn;
          $this->sexo = $sexoIn;
          $this->local_trabalho = $local_trabalhoIn;
          $this->profissao = $profissaoIn;
          $this->escolaridade = $escolaridadeIn;
          $this->informacoes = $informacoesIn;
          $this->data_inscricao = $data_inscricaoIn;
          $this->cod_lingua = $cod_linguaIn;
          $this->confirmacao = $confirmacaoIn;
    }


    /** 
     * hasEqualMapping-method will compare two Usuario instances
     * and return true if they contain same values in all persistent instance 
     * variables. If hasEqualMapping returns true, it does not mean the objects
     * are the same instance. However it does mean that in that moment, they 
     * are mapped to the same row in database.
     */
    function hasEqualMapping($valueObject) {

          if ($valueObject->getCod_usuario() != $this->cod_usuario) {
                    return(false);
          }
          if ($valueObject->getLogin() != $this->login) {
                    return(false);
          }
          if ($valueObject->getSenha() != $this->senha) {
                    return(false);
          }
          if ($valueObject->getNome_usuario() != $this->nome_usuario) {
                    return(false);
          }
          if ($valueObject->getRg() != $this->rg) {
                    return(false);
          }
          if ($valueObject->getEmail() != $this->email) {
                    return(false);
          }
          if ($valueObject->getTelefone() != $this->telefone) {
                    return(false);
          }
          if ($valueObject->getEndereco() != $this->endereco) {
                    return(false);
          }
          if ($valueObject->getCidade() != $this->cidade) {
                    return(false);
          }
          if ($valueObject->getEstado() != $this->estado) {
                    return(false);
          }
          if ($valueObject->getPais() != $this->pais) {
                    return(false);
          }
          if ($valueObject->getData_nasc() != $this->data_nasc) {
                    return(false);
          }
          if ($valueObject->getSexo() != $this->sexo) {
                    return(false);
          }
          if ($valueObject->getLocal_trabalho() != $this->local_trabalho) {
                    return(false);
          }
          if ($valueObject->getProfissao() != $this->profissao) {
                    return(false);
          }
          if ($valueObject->getEscolaridade() != $this->escolaridade) {
                    return(false);
          }
          if ($valueObject->getInformacoes() != $this->informacoes) {
                    return(false);
          }
          if ($valueObject->getData_inscricao() != $this->data_inscricao) {
                    return(false);
          }
          if ($valueObject->getCod_lingua() != $this->cod_lingua) {
                    return(false);
          }
          if ($valueObject->getConfirmacao() != $this->confirmacao) {
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
        $out = $out."\nclass Usuario, mapping to table Usuario\n";
        $out = $out."Persistent attributes: \n"; 
        $out = $out."cod_usuario = ".$this->cod_usuario."\n"; 
        $out = $out."login = ".$this->login."\n"; 
        $out = $out."senha = ".$this->senha."\n"; 
        $out = $out."nome_usuario = ".$this->nome_usuario."\n"; 
        $out = $out."rg = ".$this->rg."\n"; 
        $out = $out."email = ".$this->email."\n"; 
        $out = $out."telefone = ".$this->telefone."\n"; 
        $out = $out."endereco = ".$this->endereco."\n"; 
        $out = $out."cidade = ".$this->cidade."\n"; 
        $out = $out."estado = ".$this->estado."\n"; 
        $out = $out."pais = ".$this->pais."\n"; 
        $out = $out."data_nasc = ".$this->data_nasc."\n"; 
        $out = $out."sexo = ".$this->sexo."\n"; 
        $out = $out."local_trabalho = ".$this->local_trabalho."\n"; 
        $out = $out."profissao = ".$this->profissao."\n"; 
        $out = $out."escolaridade = ".$this->escolaridade."\n"; 
        $out = $out."informacoes = ".$this->informacoes."\n"; 
        $out = $out."data_inscricao = ".$this->data_inscricao."\n"; 
        $out = $out."cod_lingua = ".$this->cod_lingua."\n"; 
        $out = $out."confirmacao = ".$this->confirmacao."\n"; 
        return $out;
    }


    /**
     * Clone will return identical deep copy of this valueObject.
     * Note, that this method is different than the clone() which
     * is defined in java.lang.Object. Here, the retuned cloned object
     * will also have all its attributes cloned.
     */
    function clonar() {
        $cloned = new Usuario();

        $cloned->setCod_usuario($this->cod_usuario); 
        $cloned->setLogin($this->login); 
        $cloned->setSenha($this->senha); 
        $cloned->setNome_usuario($this->nome_usuario); 
        $cloned->setRg($this->rg); 
        $cloned->setEmail($this->email); 
        $cloned->setTelefone($this->telefone); 
        $cloned->setEndereco($this->endereco); 
        $cloned->setCidade($this->cidade); 
        $cloned->setEstado($this->estado); 
        $cloned->setPais($this->pais); 
        $cloned->setData_nasc($this->data_nasc); 
        $cloned->setSexo($this->sexo); 
        $cloned->setLocal_trabalho($this->local_trabalho); 
        $cloned->setProfissao($this->profissao); 
        $cloned->setEscolaridade($this->escolaridade); 
        $cloned->setInformacoes($this->informacoes); 
        $cloned->setData_inscricao($this->data_inscricao); 
        $cloned->setCod_lingua($this->cod_lingua); 
        $cloned->setConfirmacao($this->confirmacao); 

        return $cloned;
    }

}

?>
