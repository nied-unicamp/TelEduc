<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/extracao/extracao.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�ncia
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

    Nied - N�cleo de Inform�tica Aplicada � Educa��o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : administracao/extracao/extracao.inc
  ========================================================== */

/* **********************************************************************
   PreparaCabecalhoOpcao - Prepara o cabecalho da op��o da administra��o
   Entrada: Nenhuma
   Saida: Monta o cabe�alho no HTML
*/
function PreparaCabecalhoOpcao($texto)
{
  echo("<b><font face=Verdana,Arial,Helvetica color=#990000 size=3>".$texto."</font></b><br><br>\n");
}

/* **********************************************************************
   RetornaCursosExtraiveis - Retorna lista de cursos que podem ser extraidos (terminados)
   Entrada: nenhuma
   Saida: Retorna array com [$cod_curso]=nome_curso
*/
function RetornaCursosExtraiveis()
{
  $sock = Conectar("");
  $query = "select cod_curso, nome_curso from Cursos where curso_fim < ".(time() + 86399)." order by nome_curso";
  $res = Enviar($sock,$query);
  $lista = RetornaArrayLinhas($res);
  Desconectar($sock);
  if (count($lista)>0)
    foreach($lista as $cod => $linha)
      $saida[$linha['cod_curso']]=$linha['nome_curso'];
  return $saida;
}

/* **********************************************************************
   RetornaDiretorio - Retorna o diretorio especificado do ambiente TelEduc
   Entrada: $item - item da tabela de diretorios a ser retornado
   Saida: Retorna o diretorio
*/
function RetornaDiretorio($item)
{
  $sock = Conectar("");
  $query = "select diretorio from Diretorio where item = '".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  Desconectar($sock);
  return $linha['diretorio'];
}

/* **********************************************************************
   RetornaVersaoAmbiente - Retorna a vers�o em que o curso est� atualmente
   Entrada: $sock - Socket da conex�o
   Saida: Vers�o em que o curso est� atualmente
*/
function RetornaVersaoAmbiente($sock)
{
  $query = "select valor from Config where item = 'versao'";
  $res = Enviar($sock, $query);
  $versao = RetornaLinha($res);

  return ($versao['valor']);
}

/* **********************************************************************
   RetornaVersaoAmbiente2 - Retorna a vers�o em que o curso est� atualmente
   Entrada: $sock - Socket da conex�o
   Saida: Vers�o em que o curso est� atualmente
*/
function RetornaVersaoAmbiente2($sock)
{
  $query = "select valor from Config where item = 'versao'";
  $res = EnviarNC($sock, $query);
  if(ErroSql($sock)!=0)
        return (-1);
  $versao = RetornaLinha($res);

  return ($versao['valor']);
}

/* **********************************************************************
   RetornaDadosCurso - Retorna os dados do curso que ser�o inseridos na 
                       tabela 'Cursos_extraidos'
   Entrada: $sock - Socket da conex�o
            $cod_curso - C�digo do curso a ter seus dados retornados
   Saida: Array com dados do curso que est� sendo extra�do
*/
function RetornaDadosCurso($sock,$cod_curso)
{echo "entro pagina_inicial/administracao/Extracao.inc";
  if (function_exists("RetornaDadosCursoFachada"))
  {
    return(RetornaDadosCursoFachada($sock, $cod_curso));
    exit;
  }

  $query = "select curso_inicio, curso_fim from Cursos where cod_curso = ".$cod_curso;
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  $dados['curso_inicio'] = $linha['curso_inicio'];
  $dados['curso_fim'] = $linha['curso_fim'];

  return $dados;
}

/* **********************************************************************
   RetornaDadosCursoExtraido - Retorna dados do curso extra�do
   Entrada: $sock - Socket da conex�o
            $codigo - C�digo do curso extra�do a ter seus dados retornados
   Saida: Dados do curso extra�do do ambiente
*/
function RetornaDadosCursoExtraido($sock, $codigo)
{
  if (function_exists("RetornaDadosCursoExtraidoFachada"))
  {
    return(RetornaDadosCursoExtraidoFachada($sock, $codigo));
    exit;
  }

  $query = "select codigo, nome, categoria, curso_inicio, curso_fim, versao_teleduc, caminho, montado, data_ultimo_acesso
              from Cursos_extraidos
              where codigo = ".$codigo;
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);

  $dados['codigo'] = $linha['codigo'];
  $dados['nome'] = $linha['nome'];
  $dados['categoria'] = $linha['categoria'];
  $dados['curso_inicio'] = $linha['curso_inicio'];
  $dados['curso_fim'] = $linha['curso_fim'];
  $dados['versao_teleduc'] = $linha['versao_teleduc'];
  $dados['caminho'] = $linha['caminho'];
  $dados['montado'] = $linha['montado'];
  $dados['data_ultimo_acesso'] = $linha['data_ultimo_acesso'];

  return $dados;
}


/* **********************************************************************
   RetornaDadosCursoExtraido2 - Retorna dados do curso extra�do
   Entrada: $sock - Socket da conex�o
            $codigo - C�digo do curso extra�do a ter seus dados retornados
   Saida: Dados do curso extra�do do ambiente
*/
function RetornaDadosCursoExtraido2($sock, $codigo)
{
  if (function_exists("RetornaDadosCursoExtraidoFachada2"))
  {
    return(RetornaDadosCursoExtraidoFachada2($sock, $codigo));
    exit;
  }

  $query = "select codigo, nome, categoria, curso_inicio, curso_fim, versao_teleduc, caminho, montado, data_ultimo_acesso
              from Cursos_extraidos
              where codigo = ".$codigo;
  $res = EnviarNC($sock,$query);
  if(ErroSql($sock)!=0)
    return(-1);
  $linha = RetornaLinha($res);

  $dados['codigo'] = $linha['codigo'];
  $dados['nome'] = $linha['nome'];
  $dados['categoria'] = $linha['categoria'];
  $dados['curso_inicio'] = $linha['curso_inicio'];
  $dados['curso_fim'] = $linha['curso_fim'];
  $dados['versao_teleduc'] = $linha['versao_teleduc'];
  $dados['caminho'] = $linha['caminho'];
  $dados['montado'] = $linha['montado'];
  $dados['data_ultimo_acesso'] = $linha['data_ultimo_acesso'];

  return $dados;
}

/* **********************************************************************
   RetornaFerrCompartCurso - Retorna os dados do curso que ser�o inseridos na
                         tabela 'Cursos_extraidos_compart'
   Entrada: $sock - Socket da conex�o
            $cod_curso - C�digo do curso a ter seus dados retornados
   Saida: Array com dados de compartilhamento do curso que est� sendo extra�do
*/

function RetornaFerrCompartCurso($sock, $cod_curso)
{
  $query = "select cod_ferramenta from Cursos_compart where cod_curso = ".$cod_curso;
  $res = Enviar($sock,$query);
  $total = RetornaNumLinhas($res);
  if ($total > 0)
  {
    $ferr = array();
    $linhas = RetornaArrayLinhas($res);
    for ($i = 0; $i < $total; $i++)
    {
      array_push ($ferr, $linhas[$i]['cod_ferramenta']);
    }
  }
  return $ferr;
}

/* **********************************************************************
   RetornaFerrCompartCursoExtraido2 - Retorna ferramentas compartilhadas do curso extra�do
   Entrada: $sock - Socket da conex�o
            $codigo - C�digo do curso extra�do a ter suas ferramentas retornadas
   Saida: Ferramentas compartilhadas do curso extra�do do ambiente
*/
function RetornaFerrCompartCursoExtraido2($sock, $codigo)
{
  $query = "select cod_ferramenta from Cursos_extraidos_compart where codigo=".$codigo;
  $res = EnviarNC($sock,$query);
  if(ErroSql($sock)!=0)
    return(-2);
  $total = RetornaNumLinhas($res);
  if ($total > 0)
  {
    $ferr = array();
    $linhas = RetornaArrayLinhas($res);
    for ($i = 0; $i < $total; $i++)
    {
      array_push ($ferr, $linhas[$i]['cod_ferramenta']);
    }
  }
  return $ferr;
}

/* **********************************************************************
   RetornaFerrCompartCursoExtraido - Retorna ferramentas compartilhadas do curso extra�do
   Entrada: $sock - Socket da conex�o
            $codigo - C�digo do curso extra�do a ter suas ferramentas retornadas
   Saida: Ferramentas compartilhadas do curso extra�do do ambiente
*/
function RetornaFerrCompartCursoExtraido($sock, $codigo)
{
  $query = "select cod_ferramenta from Cursos_extraidos_compart where codigo=".$codigo;
  $res = Enviar($sock,$query);
  $total = RetornaNumLinhas($res);
  if ($total > 0)
  {
    $ferr = array();
    $linhas = RetornaArrayLinhas($res);
    for ($i = 0; $i < $total; $i++)
    {
      array_push ($ferr, $linhas[$i]['cod_ferramenta']);
    }
  }
  return $ferr;
}


/* **********************************************************************
   CopiaDadosCursoParaExtraidos - Insere na tabela cursos_extraidos os dados do
                          curso que est� sendo extra�do
   Entrada: $sock - Socket da conex�o
            $codigo - c�digo da entrada na tabela Cursos_extraidos
            $cod_curso - c�digo do curso do qual ser�o copiadas as entradas.
            $versao - vers�o do ambiente TelEduc
   Saida: nenhuma
*/
function CopiaDadosCursoParaExtraidos($sock, $codigo, $cod_curso, $versao)
{
  if (function_exists("CopiaDadosCursoParaExtraidosFachada"))
  {
    return(CopiaDadosCursoParaExtraidosFachada($sock, $codigo, $cod_curso, $versao));
    exit;
  }
  $query = "insert into Cursos_extraidos
              (codigo, nome, categoria, curso_inicio, curso_fim, versao_teleduc, caminho, montado, data_ultimo_acesso)
              select ".$codigo.", C.nome_curso, Cp.pasta, C.curso_inicio, C.curso_fim, '".$versao."', '', 'nao', null
                from Cursos C left join Cursos_pastas Cp
                on C.cod_pasta = Cp.cod_pasta
                where C.cod_curso = ".$cod_curso;
      $res=EnviarNC($sock,$query);
      if(ErroSql($sock)!=0)
        return (-1);
      return($res);
}


/* **********************************************************************
   CopiaFerrCompartCursoParaExtraidos - Copia para a tabela 'Cursos_extraidos_compart'
                                 as entradas das ferramentas compartilhadas do
                                 do curso que est� sendo extra�do
   Entrada: $sock - Socket da conex�o
            $codigo - c�digo da entrada da tabela Cursos_extraidos
            $cod_curso - c�digo do curso do qual ser�o copiadas as entradas.
   Saida: nenhuma
*/
function CopiaFerrCompartCursoParaExtraidos($sock, $codigo, $cod_curso)
{
  if (function_exists("CopiaFerrCompartCursoParaExtraidosFachada"))
  {
    return(CopiaFerrCompartCursoParaExtraidosFachada($sock, $codigo, $cod_curso));
    exit;
  }
  $query = "insert into Cursos_extraidos_compart (codigo, cod_ferramenta)
              select ".$codigo.", cod_ferramenta
                 from Cursos_compart
                 where cod_curso = ".$cod_curso;
  $res=EnviarNC($sock, $query);
  
  if(ErroSql($sock)!=0)
    return(-1);
    return($res);
}

  /* **********************************************************************
   MontaCursoResumo - Monta texto para gravar no arquivo de resumo
   Entrada: $sock - Sock de conex�o com a base do AMBIENTE
            $codigo - codigo do curso na tabela Cursos_extraidos
   Saida: Texto para ser gravado no arquivo de resumo
*/
function MontaCursoResumo2($sock, $codigo)
{
  if (function_exists("MontaCursoResumoFachada2"))
  {
    return(MontaCursoResumoFachada2($sock, $codigo));
    exit();
  }

  $dados = RetornaDadosCursoExtraido2($sock, $codigo);
  if(ErroSql($sock))
    return($dados);
  $ferr = RetornaFerrCompartCursoExtraido2($sock, $codigo);
  if(ErroSql($sock))
    return($ferr);

  $texto = "id_curso=".$dados['codigo']."\n";
  $texto .= "nome_curso=".$dados['nome']."\n";
  $texto .= "curso_categoria=".$dados['categoria'];
  $texto .= "curso_inicio=".$dados['curso_inicio']."\n";
  $texto .= "curso_fim=".$dados['curso_fim']."\n";
  $texto .= "versao_teleduc=".$dados['versao_teleduc']."\n";
  //$texto.= $dados['montado'].$sep;

  $ferr_compart = "";
  if (count($ferr) > 0)
  {
    $ferr_compart = implode(",", $ferr);
  }
  $texto .= "ferr_compart=".$ferr_compart;

  return $texto;
}

/* **********************************************************************
   MontaCursoResumo - Monta texto para gravar no arquivo de resumo
   Entrada: $sock - Sock de conex�o com a base do AMBIENTE
            $codigo - codigo do curso na tabela Cursos_extraidos
   Saida: Texto para ser gravado no arquivo de resumo
*/
function MontaCursoResumo($sock, $codigo)
{
  if (function_exists("MontaCursoResumoFachada"))
  {
    return(MontaCursoResumoFachada($sock, $codigo));
    exit();
  }

  $dados = RetornaDadosCursoExtraido($sock, $codigo);
  $ferr = RetornaFerrCompartCursoExtraido($sock, $codigo);

  $texto = "id_curso=".$dados['codigo']."\n";
  $texto .= "nome_curso=".$dados['nome']."\n";
  $texto .= "curso_categoria=".$dados['categoria']."\n";
  $texto .= "curso_inicio=".$dados['curso_inicio']."\n";
  $texto .= "curso_fim=".$dados['curso_fim']."\n";
  $texto .= "versao_teleduc=".$dados['versao_teleduc']."\n";
  //$texto.= $dados['montado'].$sep;

  $ferr_compart = "";
  if (count($ferr) > 0)
  {
    $ferr_compart = implode(",", $ferr);
  }
  $texto .= "ferr_compart=".$ferr_compart;

  return $texto;
}

/* **********************************************************************
   LeCursoResumo - Le o arquivo de resumo do curso
   Entrada: $arquivo - caminho e nome do arquivo
   Saida: array['id_curso']
               ['nome_curso']
               ['curso_categoria']
               ['curso_inicio']
               ['curso_fim']
               ['versao_teleduc']
               ['ferr_compart'][3]
                               [7]
                               [10]
*/
function LeCursoResumo($arquivo)
{
  if (ExisteArquivo($arquivo))
  {
    if (!($fh = fopen($arquivo, "r")))
      die(__FUNCTION__.": erro ao abrir o arquivo: ".$arquivo);
    else
    {
      while (!feof($fh))
      {
        $linha = fgets($fh, 256);
        $partes = explode("=", $linha, 2);
        $conteudo = rtrim($partes[1], "\n");
        if ($partes[0] == "id_curso")
          $resultado['id_curso'] = $conteudo;
        if ($partes[0] == "nome_curso")
          $resultado['nome_curso'] = $conteudo;
        if ($partes[0] == "curso_categoria")
          $resultado['curso_categoria'] = $conteudo;
        if ($partes[0] == "curso_inicio")
          $resultado['curso_inicio'] = $conteudo;
        if ($partes[0] == "curso_fim")
          $resultado['curso_fim'] = $conteudo;
        if ($partes[0] == "versao_teleduc")
          $resultado['versao_teleduc'] = $conteudo;
        if ($partes[0] == "ferr_compart")
          $ferr = $conteudo;

      }
      fclose($fh);
      $resultado['ferr_compart'] = explode(",", $ferr);
      return ($resultado);
    }
  }
}


/* **********************************************************************
   CompactarCurso - Verifica se o curso a ser extra�do deve ser compactado
   Entrada: $sock - Socket da conex�o
   Saida: true se precisa compactar , false se n�o.
*/
function CompactarCurso($sock)
{
  $query = "select valor from Extracao where item = 'compactar'";
  $res = Enviar($sock,$query);
  $compactar = RetornaLinha($res);

  return ($compactar['valor'] == 'S');
}

/* **********************************************************************
   CompactarCurso2 - Verifica se o curso a ser extra�do deve ser compactado
   Entrada: $sock - Socket da conex�o
   Saida: true se precisa compactar , false se n�o.
*/
function CompactarCurso2($sock)
{
  $query = "select valor from Extracao where item = 'compactar'";
  $res = EnviarNC($sock,$query);
  if(ErroSql($sock)!=0)
    return(-2);
  $compactar = RetornaLinha($res);

  return ($compactar['valor'] == 'S');
}

/* **********************************************************************
   RetornaCodUltimoExtraido - Retorna c�digo do �ltimo curso extra�do
   Entrada: $sock - Socket da conex�o
   Saida: Dados do �ltimo curso extra�do do ambiente
*/
function RetornaCodUltimoExtraido($sock)
{
  $query = "select max(codigo) from Cursos_extraidos";
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  $codigo = $linha['max(codigo)'];

  return $codigo;
}

/* **********************************************************************
   RetornaCodExtracao - Retorna a chave prim�ria a ser usada para achar o caminho
   Entrada: $sock - Socket da conex�o
   Saida: Chave prim�ria
*/
function RetornaCodExtracao($sock)
{
  $codigo = RetornaCodUltimoExtraido($sock);
  return ($codigo);
}

/* **********************************************************************
   AtualizaCaminho - Atualiza o caminho onde o curso est� sendo extra�do
   Entrada: $sock - Socket da conex�o
            $caminho - O caminho onde est� sendo extra�doi
            $codigo - C�digo do curso a ter o caminho atualizado
   Saida: nenhuma
*/
function AtualizaCaminho($sock,$caminho,$codigo)
{
  $query = "update Cursos_extraidos set caminho='".$caminho."' where codigo=".$codigo;
  EnviarNC($sock,$query);
}

/* **********************************************************************
   RetornaCaminhoCurso - Retorna a caminho do diret�rio onde o curso ficar� armazenado
   Entrada: $sock - Socket da conex�o
            $codigo - C�digo do curso extra�do
   Saida: Diret�rio onde curso ser� armazenado
*/
function RetornaCaminhoCurso($sock, $campos_restricao)
{
  // $campos_restricao = array("codigo" => $codigo);

  $caminho_extracao = RetornaCaminhoExtracao($sock, $campos_restricao);

  return $caminho_extracao;
}

/* **********************************************************************
   ApagaReferencias - Apaga refer�ncias ao curso extra�do
   Entrada: $cod_curso - C�digo do curso a ter referencias retiradas
   Sa�da : nenhuma
*/
function ApagaReferencias($cod_curso)
{
  if (function_exists("ApagaReferenciasFachada"))
  {
    return(ApagaReferenciasFachada($cod_curso));
    exit;
  }

  $sock = Conectar("");

  $query = "delete from Cursos_compart where cod_curso = ".$cod_curso;
  EnviarNC($sock, $query);
   if(ErroSql($sock)!=0)
   return(-1);
 
  $query = "delete from Cursos where cod_curso = ".$cod_curso;
  EnviarNC($sock,$query);
  if(ErroSql($sock)!=0)
  return(-2);

  return (true);
}

/* **********************************************************************
   ApagaReferencias2 - Apaga refer�ncias ao curso extra�do
   Entrada: $cod_curso - C�digo do curso a ter referencias retiradas
   Sa�da : nenhuma
*/
function ApagaReferencias2($sock,$cod_curso)
{
  if (function_exists("ApagaReferenciasFachada2"))
  {
    return(ApagaReferenciasFachada2($sock,$cod_curso));
    exit;
  }


  $query = "delete from Cursos_compart where cod_curso = ".$cod_curso;
  EnviarNC($sock, $query);
   if(ErroSql($sock)!=0)
   return(-1);

  $query = "delete from Cursos where cod_curso = ".$cod_curso;
  EnviarNC($sock,$query);
  if(ErroSql($sock)!=0)
  return(-2);

  return (true);
}

 /* *********************************************************************
   RemovedaBase- Retira curso da base
   Entrada: $sock - sock da base em uso
            $dbnamecurso - nome da base a ser removida
            $cod_curso - codigo do curso a ser removido da base
   Saida:
*/
function RemovedaBase($sock,$dbnamecurso,$cod_curso)
{
 $query = "drop database ".$dbnamecurso.$cod_curso;
 EnviarNC($sock,$query);
}

/* *********************************************************************
   RetornadadosParaLog- Retorna dados para o arquivo de log
   Entrada: $sock - sock da base em uso
             $cod_curso - codigo do curso a ser extraido
   Saida: dados do surso
*/
function RetornadadosParaLog($sock,$cod_curso)
{
 $query = "select  cdisc, cturma from Curso_espelho where cod_curso=".$cod_curso;
 $res = EnviarNC($sock,$query);
 $linha = RetornaLinha($res);
 return $linha;
}

/* *********************************************************************
   RetornaProximoCodigoExtracao- Retorna o pr�ximo n�mero a ser usado nessa base
   Entrada: $sock - sock da base em uso
            $tabela - nome da tabela a ser retirada a sequencia
   Saida: pr�ximo numero da sequencia
*/
function RetornaProximoCodigoExtracao($sock,$tabela)
{
  global $cod_usuario;
  $cod_usuario_tmp=$cod_usuario;
  if ($cod_usuario_tmp=="")
    $cod_usuario_tmp=0;

  $data=time();
  $query="insert into ".$tabela."_sequencia (cod_usuario,data) values (".$cod_usuario_tmp.",".$data.")";
  if (!($res=mysql_query($query, $sock)))
  {
    /* Criar tabela e repetir */
    $query = "CREATE TABLE ".$tabela."_sequencia (cod int PRIMARY KEY AUTO_INCREMENT,cod_usuario INT,data INT)";
    $res=EnviarNC($sock,$query);
    if(ErroSql($sock))
      return(-1);
    $query="insert into ".$tabela."_sequencia (cod_usuario,data) values (".$cod_usuario_tmp.",".$data.")";
    $res=EnviarNC($sock,$query);
    if(ErroSql($sock))
      return(-1);
  }
    $query="select cod from ".$tabela."_sequencia where cod_usuario=".$cod_usuario_tmp." and data=".$data;
    $res=EnviarNC($sock,$query);
    if(ErroSql($sock))
      return(-1);
    $linha=RetornaLinha($res);
    $query="delete from ".$tabela."_sequencia where cod_usuario=".$cod_usuario_tmp." and data=".$data;
    $res=EnviarNC($sock,$query);
    if(ErroSql($sock))
      return(-1);
    return ($linha[0]);
}

?>
