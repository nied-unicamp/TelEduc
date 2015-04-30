<?php
/*
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perfil/perfil.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
    Copyright (C) 2001  NIED - Unicamp

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2 as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    You could contact us through the following addresses:

    Nied - Ncleo de Inform�ica Aplicada �Educa�o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------

==========================================================
  ARQUIVO : cursos/aplic/perfil/perfil.inc
========================================================== */


/*
Biblioteca de funcoes Perfil.inc
Para uso com a Ferramenta Perfil no Programa Teleduc
Autor: Leonel Aguilar Gayard
*/

/*
Funcoes nessa biblioteca:

ListaUsuario
ListaCoordenador
DadosUsuario
FichaUsuario
AtualizaFichaUsuario
FuncaoPorExtenso
RetornaPerfil
RetornaDiretorio
CaminhoFoto
AtualizaArquivo
ApagarFoto
RetornaOrientacaoPerfil
AtualizaOrientacao
InsereOrientacao
ExisteOrientacao
RetornaListaEscolaridade
ModificaSenha
EnviaPerfil
CoordComoFormador
RetornaDadosPerfilDinamic
EditarPerfilDinamic
*/


/***********************************************************************************************
  ListaUsuario - Retorna uma lista com o codigo, nome e tipo de usuario (ou formador)
                 do curso, dado o tipo
  Entradas: $sock - sock de conexao,
            $tipo_usuario - Tipo do Usuario("A","F","Z","V"),
  Saida:   Array com: ['cod_usuario'] - codigo usuario
                      ['nome'] - nome
                      ['tipo_usuario'] - tipo de usuario
*************************************************************************************************/
class Perfil{
  static function ListaUsuario($sock,$tipo_usuario,$cod_curso)
  {
     // 2Session
    $dbnamebase = $_SESSION['dbnamebase'];

    $query  = "select UC.cod_usuario, U.nome, UC.tipo_usuario ";
    $query .= "from ".$dbnamebase.".Usuario_curso UC ";
    $query .= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global) ";
    $query .= "where UC.cod_usuario!=-1 ";
    $query .= "  and binary UC.tipo_usuario=\"".$tipo_usuario."\" ";
    $query .= "  and UC.cod_curso=\"".$cod_curso."\" ";
    $query .= "order by nome";
    $res= AcessoSQL::Enviar($sock,$query); 
    $lista= AcessoSQL::RetornaArrayLinhas($res);
    return ($lista);
  }


  /************************************************************************************************
    ListaCoordenador - Retorna uma lista com o codigo e o nome do coordenador do curso
    Entradas: $sock - sock de conexao,
              $cod_curso - codigo do curso,
    Saida:   Array com: ['cod_usuario'] - codigo usuario
                        ['nome'] - nome
  *************************************************************************************************/
  static function ListaCoordenador($sock, $cod_curso)
  {
     // 2Session
    $dbnamebase = $_SESSION['dbnamebase'];

    $query = "select cod_coordenador from Cursos where cod_curso = ".$cod_curso;
    $res=AcessoSQL::Enviar($sock,$query);
    $linha=AcessoSQL::RetornaArrayLinhas($res);
    $query  = "select UC.cod_usuario, U.nome, Cf.valor 'status' ";
    $query .= "from Config as Cf, ".$dbnamebase.".Usuario_curso as UC ";
    $query .= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global) ";
    $query .= "where UC.cod_usuario = ".$linha[0]['cod_coordenador']." ";
    $query .= "and UC.cod_curso = '".$cod_curso."' ";
    $query .= "and Cf.item = 'status_coordenador'";

    $res = AcessoSQL::Enviar($sock,$query);

    $linha = AcessoSQL::RetornaArrayLinhas($res);

    return ($linha);
  }


  /*********************************************************************************************************
    DadosUsuario - Retorna array com dados basicos do usuario
    Entradas: $sock - sock de conexao,
              $cod_curso - codigo do curso,
              $cod_usuario - codigo do usuario
    Saida:    Array com ['nome'] - nome do Usuario
                        ['email'] - endereco email do usuario
                        ['tipo_usuario'] - (char) tipo de usuario - (A)luno,(F)ormador,(C)oordenador
  *********************************************************************************************************/
  static function DadosUsuario($sock,$cod_curso,$cod_usuario)
  {
     // 2Session
    $dbnamebase = $_SESSION['dbnamebase'];

    $query="SELECT U.nome,U.email,UC.tipo_usuario
            from ".$dbnamebase.".Usuario_curso UC
            inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
            where UC.cod_usuario='".$cod_usuario."'
            and UC.cod_curso='".$cod_curso."'";

    $res=AcessoSQL::Enviar($sock,$query);
    $linha=AcessoSQL::RetornaArrayLinhas($res);

    if(ECoordenador($sock,$cod_curso,$cod_usuario)==1)
      $linha['tipo_usuario']="C";

    return($linha);
  }


  /***********************************************************************************************
    FichaUsuario - Retorna array com TODOS os dados do usuario
    Entradas: $sock - sock de conexao,
              $cod_usuario - codigo do usuario
    Saida:    Array com ['nome'] - nome do Usuario
                        ['endereco'] - endereco (rua e numero) do usuario
                        ['cidade'] - cidade do usuario
                        ['estado'] - sigla do estado do usuario
                        ['pais'] - pa� do usuario
                        ['telefone'] - numero de telefone do usuario
                        ['email'] - email do usuario
                        ['data_nasc'] - data de nascimento do usuario(formato UnixTime)
                        ['sexo'] - sexo do usuario, M ou F
                        ['local_trab'] - Local de trabalho do usuario
                        ['profissao'] - Profissao do usuario
                        ['informacoes'] - informacoes adicionais sobre o usuario
                        ['cod_escolaridade'] - codigo da escolaridade do usuario
  *************************************************************************************************/
  static function FichaUsuario($sock,$cod_usuario,$cod_curso)
  {
     // 2Session
    $dbnamebase = $_SESSION['dbnamebase'];

    $query="select U.nome,U.endereco,U.cidade,U.estado,U.pais,U.telefone,U.email,U.data_nasc,U.sexo,
                   U.local_trab,U.profissao,U.informacoes,U.cod_escolaridade,U.rg
            from ".$dbnamebase.".Usuario_curso UC
            inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
            where UC.cod_usuario='".$cod_usuario."'
            and UC.cod_curso='".$cod_curso."'";
    $res=AcessoSQL::Enviar($sock,$query);
    $linha=AcessoSQL::RetornaArrayLinhas($res);
    return($linha);
  }


  /******************************************************************************************
    AtualizaFichaUsuario - Atualiza a ficha com os dados do usuario
    Entrada: $sock - sock de conexao
             $cod_usuario - codigo do usuario
             $nome - novo nome do usuario
             $endereco - novo endereco do usuario
             $cidade - nova cidade do usuario
             $estado - novo estado  do usuario
             $pais - novo pais do usuario
             $telefone - novo telefone do usuario
             $email - novo email do usuario
             $data_nasc - nova data de nascimento do usuario
             $sexo - novo sexo do usuario
             $local_trab - novo local de trabalho do usuario
             $profissao - nova profissao do usuario
             $informacoes - novas informacoes adicionais sobre o usuario
             $cod_escolaridade - nova escolaridade do usuario
             $rg - RG
    Saida:   Nenhuma
  **********************************************************************************************/
  static function AtualizaFichaUsuario($sock,$cod_curso,$cod_usuario,$nome,$endereco,$cidade,$estado,$pais,$telefone,$email,$data_nasc,$sexo,$local_trab,$profissao,$informacoes,$cod_escolaridade,$rg)
  {
     // 2Session
    $dbnamebase = $_SESSION['dbnamebase'];

    $nome_limpo=LimpaTitulo($nome);
    $endereco_limpo=LimpaTitulo($endereco);
    $cidade_limpo=LimpaTitulo($cidade);
    $estado_limpo=LimpaTitulo($estado);
    $pais_limpo=LimpaTitulo($pais);
    $telefone_limpo=LimpaTitulo($telefone);
    $email_limpo=LimpaTitulo($email);
    $data_nasc_limpo=LimpaTitulo($data_nasc);
    $sexo_limpo=LimpaTitulo($sexo);
    $local_trab_limpo=LimpaTitulo($local_trab);
    $profissao_limpo=LimpaTitulo($profissao);
    $informacoes_limpo=LimpaTitulo($informacoes);
    $cod_escolaridade_limpo=LimpaTitulo($cod_escolaridade);
    $rg=LimpaTitulo($rg);

    $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso);

    $query="update ".$dbnamebase.".Usuario set nome='".$nome_limpo."',endereco='".$endereco_limpo."',cidade='".$cidade_limpo."',estado='".$estado_limpo."',pais='".$pais_limpo."',telefone='".$telefone_limpo."',email='".$email_limpo."',data_nasc='".$data_nasc_limpo."',sexo='".$sexo_limpo."',local_trab='".$local_trab_limpo."',profissao='".$profissao_limpo."',informacoes='".$informacoes_limpo."',cod_escolaridade='".$cod_escolaridade_limpo."',rg='".$rg."' where cod_usuario=".$cod_usuario;
    $res=AcessoSQL::Enviar($sock,$query);

    AtualizaFerramentasNova($sock, 13, 'T');
  }


  /***************************************************************************************
    FuncaoPorExtenso - Retorna por extenso a funcao do usuario no curso
    Entrada: $sock - conexao com a base
             $lista_frases - lista de frases
             $funcao - char com a funcao (tipo_usuario) do usuario
    Saida:   Array (string) com a funcao por extenso
  ****************************************************************************************/
  static function FuncaoPorExtenso($sock, $lista_frases,$funcao)
  {
    /* 19 - aluno,formador,candidato a aluno,candidato a formador,visitante,
            aluno rejeitado,formador rejeitado,administrador do sistema,coordenador,colaborador,colaborador rejeitado */
    $funcoes=RetornaFraseDaLista($lista_frases,19);
    $letras="A,F,a,f,V,r,R,S,C,Z,z";

    $array_funcoes=explode(",",$funcoes);
    $array_letras=explode(",",$letras);

    if ($funcao == 'C')
      if (CoordComoFormador($sock))
      {
        $funcao = 'F';
      }
    
    $resp="(desconhecida)";
    for ($c=0;$c<count($array_letras);$c++)
      if ($funcao==$array_letras[$c])
        $resp=$array_funcoes[$c];


    return ($resp);
  }


  /**************************************************************************************
  Funcao RetornaPerfil - Retorna Array com os Perfis
    Entrada: $sock - sock de conexao
             $cod_usuario_ficha - codigo do usuario
    Saida  : Array ['perfil'] - texto com perfil do usuario
                   ['data']   - int. data da ultima altera�o do perfil
  ***************************************************************************************/
  static function RetornaPerfil($sock,$cod_usuario_ficha)
  {
    $query="select perfil,data from Perfil_usuarios where cod_usuario=".$cod_usuario_ficha;
    $res=AcessoSQL::Enviar($sock,$query);

    if (AcessoSQL::RetornaNumLinhas($res)>0)
    {
      $linha=AcessoSQL::RetornaArrayLinhas($res);
      return ($linha);
    }
    else
    {
      return null;
    }
  }


  /*******************************************************************************************
    RetornaDiretorio - Retorna Diretorio (path) do item pedido
    Entrada: $sock - sock de conexao (BASE EXTERNA)
             $item - arquivo do qual se quer descobrir o diretorio
    Saida:   path do item
  ********************************************************************************************/
  static function RetornaDiretorio($sock,$item)
  {
    $query="select diretorio from Diretorio where item='".$item."'";
    $res=Enviar($sock,$query); // Base externa!!!!
    $linha=AcessoSQL::RetornaArrayLinhas($res);
    return ($linha[0]);
  }


  /*******************************************************************************************************
    CaminhoFoto - Retorna path da foto do usuario
    Entrada: $cod_curso - codigo do curso
             $cod_usuario_ficha - codigo do usuario dono da foto
             $diretorio_arquivo - diretorio real onde ficam armazenados os arquivos (invisivel a web)
             $diretorio_temp - diretorio simbolico
    Saida:   path da foto, se esta existir, ou vazio caso contrario
  *******************************************************************************************************/
  static function CaminhoFoto($cod_curso,$cod_usuario_ficha,$diretorio_arquivo,$diretorio_temp)
  {
    $caminho="../../diretorio/perfil_".$cod_curso."/"; // Visivel e acessivel pela Web
    $camreal=$diretorio_temp."/perfil_".$cod_curso;  // Visivel a Web
    $camorig=$diretorio_arquivo."/".$cod_curso."/perfil/"; // Invisivel a Web
    if (!ExisteArquivo($camreal))
    {
      CriaLinkSimbolico($camorig,$camreal);
    }
    $arquivo="";
    $lista=RetornaArrayDiretorio($camreal);
    if (count($lista)>0)
      foreach ($lista as $cod=>$linha)
      {
        if (eregi("cod_usuario_".$cod_usuario_ficha."\.",$linha['Arquivo']))
          $arquivo=$linha['Arquivo'];
      }
    else
      $caminho="";
    if ($arquivo!="")
      $caminho.=$arquivo;
    else
      $caminho="";
    return $caminho;
  }


  /********************************************************************************
    AtualizaArquivo - Faz upload de um arquivo
    Entrada: $sock - conexão com a base do curso
             $arquivo - arquivo a atualizar
             $nomefinal - nome do arquivo a enviar
             $diretorio_temp - caminho onde o arquivo deve ser colocado
             $cod_curso - codigo do curso
    Saida:   true, se conseguiu realizar o upload
             false, caso contrario
  *********************************************************************************/
  static function AtualizaArquivo($sock,$arquivo,$nomefinal,$diretorio_temp,$cod_curso)
  {
    $camreal=$diretorio_temp."/perfil_".$cod_curso;  // Visivel a Web

    $tmp=explode(".",$nomefinal);

    $lista=RetornaArrayDiretorio($camreal);
    if (is_array ($lista)){
      foreach ($lista as $cod=>$linha)
      {
        if (eregi($tmp[0]."\.",$linha['Arquivo']))
          RemoveArquivo($linha['Caminho']);
      }
    }
    
    AtualizaFerramentasNova($sock, 13, 'T');
    
    return (RealizaUpload($arquivo,$camreal."/".$nomefinal));

  }


  /*********************************************************************************
     ApagarFoto - Apaga a foto de um usuario
     entrada: $sock - conexão com a base do curso
              $diretorio_temp     - diretorio visivel a Web
              $cod_usuario        - codigo do usuario do qual apagar a foto
              $cod_curso          - codigo do curso
     saida:   true se conseguiu apagar a foto
              false caso houve problema
  ***********************************************************************************/
  static function ApagarFoto ($sock,$diretorio_temp, $cod_usuario, $cod_curso)
  {
    if ( ! is_numeric($cod_usuario) || $cod_usuario == '') {
        return false;
    }

    $camreal = $diretorio_temp."/perfil_".$cod_curso;     // Visivel a Web

    // echo '<pre>'; echo __FILE__; echo ' '; echo __LINE__; $t = RetornaArrayDiretorio($camreal); var_dump($t); echo '</pre>';
    $t = RetornaArrayDiretorio($camreal);

    $caminho_foto = '';
    if (is_array($t)) {
       foreach ($t as $arquivo) {
           // procuramos no diretorio todos os arquivos no formato cod_usuario_N, onde N eh numerico
           if ( strstr( $arquivo['Arquivo'], 'cod_usuario_'.$cod_usuario ) ) {
               $caminho_foto = $arquivo['Arquivo'];
           }
       }
    }

    if ( $caminho_foto == '') {
        return false;
    } else {
        return RemoveArquivo($camreal.'/'.$caminho_foto);
    }

    AtualizaFerramentasNova($sock, 13, 'T');
  }


  /******************************************************************************************************
   RetornaOrientacaoPerfil - Retorna texto de orientacao do perfil na lingua selecionada ou padrao
    Entrada: $sock - sock de conexao
             $cod_lingua - codigo da lingua desejada
    Saida:   Texto de orientacao a preenchimento do perfil
  ******************************************************************************************************/
  static function RetornaOrientacaoPerfil($sock, $cod_lingua)
  {
    $query="select orientacao from Perfil_orientacao where cod_lingua=".$cod_lingua;
    $res=AcessoSQL::Enviar($sock,$query);
    $linha=AcessoSQL::RetornaArrayLinhas($res);
    return $linha[0];
  }


  /**********************************************************************************************
    AtualizaOrientacao - Atualiza a orientacao para preenchimento perfil na base de dados
    Entrada: $sock - sock de conexao
             $nova_orientacao_perfil - texto de orientacao para preenchimento de perfil
             $cod_lingua - codigo da lingua desejada
    Saida:   Nenhuma
  ***********************************************************************************************/
  static function AtualizaOrientacao($sock,$nova_orientacao_perfil, $cod_lingua)
  {
    $nova_orientacao_perfil_limpa=LimpaConteudo($nova_orientacao_perfil);
    $nova_orientacao_perfil_limpa=EliminaScript($nova_orientacao_perfil_limpa);

    $query="update Perfil_orientacao set orientacao='".$nova_orientacao_perfil_limpa."' WHERE cod_lingua=".$cod_lingua;
    $res=Enviar($sock,$query);
  }


  /*********************************************************************************************
    InsereOrientacao - Insere a orientacao para preenchimento de perfil na base de dados
    Entrada: $sock - sock de conexao
             $orientacao_perfil - texto de orientacao para preenchimento de perfil
             $cod_lingua - codigo da lingua
    Saida:   Nenhuma
  **********************************************************************************************/
  static function InsereOrientacao($sock,$orientacao_perfil, $cod_lingua)
  {
    $orientacao_perfil_limpa=LimpaConteudo($orientacao_perfil);
    $orientacao_perfil_limpa=EliminaScript($orientacao_perfil_limpa);

    $query="insert into Perfil_orientacao values (".$cod_lingua.",'".$orientacao_perfil_limpa."', ".$cod_lingua.")";
    $res=AcessoSQL::Enviar($sock,$query);
  }


  /****************************************************************************************
    ExisteOrientacao - Verifica se existe a orientacao na tabela Perfil_Orientacao
    Entrada: $sock - sock de conexao
             $cod_lingua - codigo da lingua
    Saida:   true, se existir a orientacao
             false, se nao existir, ou seja, a tabela esta virgem
  *****************************************************************************************/
  static function ExisteOrientacao($sock, $cod_lingua)
  {
    $query="select orientacao from Perfil_orientacao where cod_lingua=".$cod_lingua;
    $res=Enviar($sock,$query);
    $num=AcessoSQL::RetornaNumLinhas($res);
    if($num>0)
      return true;
    else
      return false;
  }


  /************************************************************************************************
    RetornaListaEscolaridade - Verifica se existe a orientacao na tabela Perfil_Orientacao
    Entrada: $sock - sock de conexao EXTERNA
    Saida:   array com: []['cod_escolaridade']
                        []['cod_texto_escolaridade']
  *************************************************************************************************/
  static function RetornaListaEscolaridade($sock)
  {
    $query="select * from Escolaridade order by cod_escolaridade";
    $res=AcessoSQL::Enviar($sock,$query);
    $lista=AcessoSQL::RetornaArrayLinhas($res);
    return($lista);
  }


  /**********************************************************************************************
    ModificaSenha - Se a senha fornecida for igual a da base, modifica-a para
                    a nova senha. Caso contrario, nao faz nada.
    Entrada: $sock - sock de conexao
             $cod_usuario - codigo do usuario
             $senha_antiga - senha atual que o usuario quer modificar
             $senha_nova - senha nova que o usuario escolheu
    Saida:   true - se conseguiu atualizar a senha
             false - caso contrario
  ***********************************************************************************************/
  static function ModificaSenha($sock,$cod_curso,$cod_usuario,$senha_antiga,$senha_nova)
  {
     // 2Session
    $dbnamebase = $_SESSION['dbnamebase'];

    $senha_antiga=crypt($senha_antiga,"AA");
    $senha_nova=crypt($senha_nova,"AA");

    $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso);

    $query="select senha from ".$dbnamebase.".Usuario where cod_usuario=".$cod_usuario_global;
    $res= AcessoSQL::Enviar($sock,$query);
    $linha=AcessoSQL::RetornaArrayLinhas($res);
    if ($linha[0]!=$senha_antiga)
      return false;
    else
    {
      $query="update ".$dbnamebase.".Usuario set senha='".$senha_nova."' where cod_usuario=".$cod_usuario_global;
      AcessoSQL::Enviar($sock,$query);
      return true;
    }
  }


  /**************************************************************************************
    EnviaPerfil - Envia um novo texto de perfil para a base de dados
    Entrada: $sock - sock de conexao
             $cod_usuario - codigo do usuario
             $tempo - horario da alteracao, em UnixTime
             $perfil - texto de perfil a enviar
    Saida:   Nenhuma
  ***************************************************************************************/
  static function EnviaPerfil($sock,$cod_usuario,$tempo,$perfil)
  {
    $perfil_limpo=LimpaConteudo($perfil);

    $perfil_limpo=EliminaScript($perfil_limpo);

    $query="select * from Perfil_usuarios where cod_usuario=".$cod_usuario;
    $res=AcessoSQL::Enviar($sock,$query);
    $num=AcessoSQL::RetornaNumLinhas($res);
    if($num==0)
    {
      $query="insert into Perfil_usuarios values (".$cod_usuario.",'".$perfil_limpo."',".$tempo.")";
      $res=AcessoSQL::Enviar($sock,$query);
    }
    else
    {
      $query="update Perfil_usuarios set perfil='".$perfil_limpo."',data=".$tempo." where cod_usuario=".$cod_usuario;
      $res=AcessoSQL::Enviar($sock,$query);
    }

    AtualizaFerramentasNova($sock, 13, 'T');
  }


  /***************************************************************************************************
     CoordComoFormador - Retorna TRUE se o coordenador deve ser exibido como formador ou FALSE se o
                         coordenador é exibido como coordenador mesmo
     entrada - $sock - conexao com a base
     saida - TRUE - Esconder coordenador com m�cara de formador
             FALSE - Exibir coordenador como coordenador mesmo
  ****************************************************************************************************/
  static function CoordComoFormador($sock)
  {
    $query = "select item, valor from Config where item='status_coordenador'";
    $res   = AcessoSQL::Enviar($sock, $query);
    $linha = AcessoSQL::RetornaArrayLinhas($res);
    
    if ($linha[valor] == 'F')
      return true;
    else
      return false;
  }


  /************************************************************************************************
    RetornaDadosPerfilDinamic - retorna os dados do perfil do usuário passado p/ parâmetro
      entrada - $cod_curso - código do curso para conexão com a base;
                $cod_usuario - código do usuário do perfil;
      saida - dados do perfil via ajax.
  *************************************************************************************************/
  static function RetornaDadosPerfilDinamic($cod_curso, $cod_usuario){

    $objResponse = new xajaxResponse();

    $sock=Conectar($cod_curso);

    $linha=FichaUsuario($sock,$cod_usuario,$cod_curso);
    
    $nome=$linha['nome'];
    $endereco=$linha['endereco'];
    $cidade=$linha['cidade'];
    $estado=$linha['estado'];
    $pais=$linha['pais'];
    $telefone=$linha['telefone'];
    $email=$linha['email'];
    $data_nasc=UnixTime2Data($linha['data_nasc']);
    $sexo=$linha['sexo'];
    $local_trab=$linha['local_trab'];
    $profissao=$linha['profissao'];
    $informacoes=$linha['informacoes'];

    if($sexo == "M")
      $sexo="Masculino";
    else
      $sexo="Feminino";
    
    $objResponse->assign("tdNome_".$cod_usuario,"innerHTML", $nome."<br />");
    $objResponse->assign("tdEnd_".$cod_usuario,"innerHTML", $endereco."<br />".$cidade." - ".$estado." - ".$pais);
    $objResponse->assign("tdTel_".$cod_usuario,"innerHTML", $telefone);
    $objResponse->assign("tdNasc_".$cod_usuario,"innerHTML", $data_nasc);
    $objResponse->assign("tdEmail_".$cod_usuario, "innerHTML", "<a href=mailto:".$email.">".$email."</a>");
    $objResponse->assign("tdLocalTrab_".$cod_usuario,"innerHTML", $local_trab);
    $objResponse->assign("tdProfis_".$cod_usuario,"innerHTML", $profissao);
    
    if($informacoes != ""){
      $objResponse->clear("tdInfo_".$cod_usuario, "innerHTML");
      $objResponse->assign("tdInfo_".$cod_usuario,"innerHTML", $informacoes);
    }
    
    return $objResponse;
  }


  /**********************************************************************************
    EditarPerfilDinamic - editar os dados do perfil dinâmicamente
      entrada - $cod_curso - código do curso para conexão com a base;
                $cod_usuario - código do usuário do perfil;
                $novo_nome - novo texto de perfil.
      saida - nenhuma.
  *******************************************************************************/
  static function EditarPerfilDinamic($cod_curso, $cod_usuario, $novo_nome, $texto){
   
    $objResponse = new xajaxResponse();
    
    $novo_nome=ConverteAspas2BarraAspas($novo_nome);
    $sock=Conectar($cod_curso);
    $tempo=time();
    
    EnviaPerfil($sock,$cod_usuario,$tempo,$novo_nome);

    $objResponse->script("top.opener.document.getElementById('data_".$cod_usuario."').innerHTML='<b>".UnixTime2DataHora($tempo)."</b>'");
    $objResponse->call("mostraFeedback", $texto, 'true');
    Desconectar($sock);

    return $objResponse;
   
  }

  /**
   * IniciaPaginacaoDinamic - Funcao que cria a paginacao inicial
   * @param $cod_curso
   * @param $tipo_usuario
   * @param $ordem
   */
  static function IniciaPaginacaoDinamic($cod_curso,$tipo_usuario,$ordem) {
  	$objResponse = new xajaxResponse();
  	$sock=Conectar("");
  	$lista_usuarios=ListaUsuario($sock,$tipo_usuario,$cod_curso);
  	$total_usuarios=count($lista_usuarios);

  	/* Numero de paginas a ser mostrada */
  	$paginas=(ceil($total_usuarios/10));

  	/* Caso exista mais que 5 paginas mostramos a primeira paginacao com 5*/
  	/* A variavel que controla o proximo eh liberada, assim a navegacao ainda existe para frente*/
  	$prox="N";
  	if($paginas>5) {
  		$paginas=5;
  		$prox="L";
  	}

  	$objResponse->call("Inicial",(int)$paginas,$prox);

  	return $objResponse;

  }

  /**
   * MudaDinamic - Controla a mudanca no intervalo de paginacao
   * @param unknown_type $intervalo
   * @param unknown_type $flag
   * @param unknown_type $cod_curso
   * @param unknown_type $tipo_usuario
   * @param unknown_type $ordem
   */
  static function MudaDinamic($intervalo,$flag,$cod_curso,$tipo_usuario,$ordem){
  	$objResponse = new xajaxResponse();
  	$sock=Conectar("");
  	$lista_usuarios=ListaUsuario($sock,$tipo_usuario,$cod_curso);
  	$total_usuarios=count($lista_usuarios);
  	if($flag=='L'){
  		$intervalo=(ceil(($total_usuarios-50)/10))+1;
  		$status='BF';
  	}
  	if($flag=='P'){
  		if($total_usuarios-(($intervalo*10)+50)<=0)
  		$status='BF';
  		else
  		$status='LF';
  		$intervalo++;
  	}
  	if($flag=='A'){
  		$intervalo--;
  		if($intervalo==1)
  		$status='BV';
  		else
  		$status='LV';
  	}
  	if($flag=='PR'){
  		$intervalo=1;
  		$status='BV';
  	}
  	$objResponse->call("MudaIntervalo",$intervalo);
  	$objResponse->call("ApagaPagina",'N');
  	$objResponse->call("Paginacao",$status);
  	return $objResponse;
  }

  /**
   * PaginacaoDinamic - Funcao responsavel por criar a paginacao
   * @param $aux
   * @param $pag
   * @param $pag_atual
   * @param $cod_curso
   * @param $tipo_usuario
   * @param $ordem
   * @param $acao
   * @param $ativado
   * @param $desativado
   */
  static function PaginacaoDinamic($aux,$pag,$pag_atual,$cod_curso,$tipo_usuario,$ordem,$acao,$ativado,$desativado){
  	$objResponse = new xajaxResponse();

  	/* Busca no banco os registros a serem mostrados */
  	$sock=Conectar("");
  	$lista_usuarios=ListaUsuario($sock,$tipo_usuario,$cod_curso);
  	$num=count($lista_usuarios);

  	/* comeca a preparar ate onde pegaremos, ou seja, se for a ultima pagina vai ate o fim, caso contrario
  	 * pega vai ate um multiplo de 10
  	 */

  	$limite=((int)$pag_atual*10);
  	$i=(((int)$pag_atual-1)*10)+1;

  	if($i==0) {
  		$i=1;
  	}

  	if(($num-$limite)< 0) {
  		$limite=$num;
  	}

  	/* apaga todas as mensagens anteriores.Teremos 10 mensagens ou ateh terminar caso seja a ultima pagina */
  	$objResponse->call("ApagaPagina",'T');

  	/* colocamos na pagina o conteudo da respectiva pagina */
  	$control=1;
  	foreach($lista_usuarios as $cod_usuario_l => $linha){
  		if($control>=$i && $control<=$limite){
  			if($acao=='R'){
  				if($linha['portfolio'] == "ativado")
  				$port=$ativado;
  				else
  				$port=$desativado;
  			}
  			$objResponse->call("CriaElementoTab",$linha['nome'],Unixtime2Data($linha['data_inscricao']),"dados",$cod_usuario_l,$acao,$port);
  		}
  		$control++;
  	}
  	
  	/* muda a variavel atual via javascript, para tirar o evento onclick da pagina que
  	 esta sendo mostrada*/
  	$objResponse->call("MudaAtual",$pag_atual);

  	$objResponse->call("Paginacao",$aux);
  	$objResponse->call("MudaControleDetalhes",$aux,$pag_atual,$pag,'L');
  	return $objResponse;
  }
}

?>
