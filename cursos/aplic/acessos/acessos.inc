<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/acessos/acessos.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½cia
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

    Nied - Ncleo de Informï¿½ica Aplicada ï¿½Educaï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/* ==========================================================
   ARQUIVO : cursos/aplic/acessos/acessos.inc
   ========================================================== */

/* *********************************************************************
   RetornaListaFerramentas - Retorna a lista de ferramentas
   Entrada: $sock - BASE EXTERNA
   Saida: array [$cod_ferramenta]['cod_texto_nome']
                [$cod_ferramenta]['cod_texto_descricao']
                [$cod_ferramenta]['diretorio']
*/

/*
function RetornaListaFerramentas($sock)
{
  $query ="select * from Ferramentas order by cod_ferramenta";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  unset($lista1);
  if (count($lista) > 0)
  {
    foreach($lista as $cod=>$linha)
    {
      $lista1[$linha['cod_ferramenta']]['cod_texto_nome']=$linha['cod_texto_nome'];
      $lista1[$linha['cod_ferramenta']]['cod_texto_descricao']=$linha['cod_texto_descricao'];
      $lista1[$linha['cod_ferramenta']]['diretorio']=$linha['diretorio'];
    }
  }
  return ($lista1);
}
*/
/* *********************************************************************
   RetornaOrdemFerramentas - Retorna a ordem das ferramentas no menu
   Entrada: $sock - BASE EXTERNA
   Saida: array []['cod_ferramenta']= codigo da ferramenta
                []['posicao'] = posicao da ferramenta. -1 se barra
*/
/*
function RetornaOrdemFerramentas($sock)
{
  $query ="select * from Menu order by posicao";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return ($lista);
}
*/

function FirstName($sock, $cod_usuario, $cod_curso) {
	$usuario = RetornaInfosUsuario($sock,$cod_curso, $cod_usuario);
	return $usuario['nome'];
}

/* *********************************************************************
   RetornaInfosUsuario - Retorna informacoes sobre um unico usuario
   entrada: $sock - sock de conexao
            $cod_usuario - codigo do usuario
   saida:   array ['nome'] - nome do usuario
                  ['cidade'] - cidade do usuario
                  ['estado'] - estado do usuario
                  ['local_trab'] - local de trabalho do usuario
*/
function RetornaInfosUsuario($sock,$cod_curso, $cod_usuario)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso);

  $query="select nome,cidade,estado,local_trab from ".$dbnamebase.".Usuario where cod_usuario=".$cod_usuario_global;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha;
}

/* *********************************************************************
   RetornaUsuarios - Retorna lista de usuarios
   Entrada: $sock - sock de conexao
            $ordenacao - parametro de ordenacao
   Saida:   array [$cod_usuario]['nome'] - nome do usuario
                  [$cod_usuario]['tipo'] - tipo do usuario
                  [$cod_usuario]['cidade'] - cidade do usuario
                  [$cod_usuario]['estado'] - estado do usuario
                  [$cod_usuario]['local_trab'] - local de trabalho do usuario
*/
function RetornaUsuarios($sock,$ordenacao, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "select UC.cod_usuario,U.nome,U.cidade,U.estado,U.local_trab,UC.tipo_usuario ";
  $query.= "from ".$dbnamebase.".Usuario_curso UC ";
  $query.= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global and UC.cod_curso='".$cod_curso."') ";
  $query.= "where "; 
  $query.= "UC.cod_usuario >= 0 and ";
             //A - Alunos
  $query.= "(binary UC.tipo_usuario='A' or ";
             //F - Formadores
  $query.= " binary UC.tipo_usuario='F' or ";
             //Z - Colaboradores
  $query.= " binary UC.tipo_usuario='Z' or ";
             //V - Visitantes
  $query.= " binary UC.tipo_usuario='V') ";
  $query.= "order by ".$ordenacao;

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista) > 0)
    foreach ($lista as $cod => $linha)
    {
      $lista_usuarios_retorno[$linha['cod_usuario'] + 0]['nome'] = $linha['nome'];
      $lista_usuarios_retorno[$linha['cod_usuario'] + 0]['cidade'] = $linha['cidade'];
      $lista_usuarios_retorno[$linha['cod_usuario'] + 0]['estado'] = $linha['estado'];
      $lista_usuarios_retorno[$linha['cod_usuario'] + 0]['local_trab'] = $linha['local_trab'];
      $lista_usuarios_retorno[$linha['cod_usuario'] + 0]['cod_usuario'] = $linha['cod_usuario'];
      $lista_usuarios_retorno[$linha['cod_usuario'] + 0]['tipo_usuario'] = $linha['tipo_usuario'];
    }

  return ($lista_usuarios_retorno);
}

/* *********************************************************************
   RetornaUltimoENumeroAcessos - retorna numero total de acessos e data do ultimo acesso
                                 ï¿½ferramenta pedida e o mÃ¡ximo de acessos.
   Entrada: $sock - sock de conexao
            $cod_ferramenta - codigo da ferramenta a consultar
   Saida: array [$cod_usuario]['num_acessos'] - numero de acessos do usuario ï¿½uela ferramenta
                [$cod_usuario]['ultimo_acesso'] - data em UT na qual o usuario realizou o ultimo acesso ï¿½uela ferramenta
          max_qtde_acessos - quantidade mÃ¡xima de acessos
*/
function RetornaUltimoENumeroAcessos($sock,$cod_ferramenta)
{
  if (isset($cod_ferramenta) && $cod_ferramenta != "")
  {
    $tmp = "=".$cod_ferramenta;
  }
  else
  {
    $tmp = " is null";
  }

  $query="select cod_usuario,count(*) 'num_acessos',max(data) 'ultimo_acesso' from Curso_acessos where cod_ferramenta".$tmp." and cod_usuario >= 0 group by cod_usuario";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  
  $max_qtde_acessos=0;
  if (count($lista)>0)
    foreach ($lista as $linha)
    {
      $lista_acessos_retorno[$linha['cod_usuario']]['num_acessos'] = $linha['num_acessos'];
      $lista_acessos_retorno[$linha['cod_usuario']]['ultimo_acesso'] = $linha['ultimo_acesso'];
      $max_qtde_acessos=max($linha['num_acessos'], $max_qtde_acessos);
    }
    
  return array($lista_acessos_retorno, $max_qtde_acessos);
}

/* *********************************************************************
   RetornaUnicoUsuarioAcessosUT - retorna array com os acessos do usuario em UT
   entrada: $sock - sock de conexao
            $cod_usuario - codigo do usuario
            $cod_ferramenta - codigo da ferramenta
            $data_iniUT - data de inicio da busca em UT
            $data_fimUT - data de fim da busca em UT
saida:  array [] - data em UT do acesso
*/
function RetornaUnicoUsuarioAcessosUT($sock,$cod_usuario,$cod_ferramenta,$data_iniUT,$data_fimUT)
{
  if (isset($cod_ferramenta) && $cod_ferramenta > 0)
  {
    $tmp = "=".$cod_ferramenta;
  }
  else
  {
    $tmp = " is null";
  }


  $query="select data from Curso_acessos where cod_usuario=".$cod_usuario." and cod_ferramenta".$tmp." and data>=".$data_iniUT." and data<=".$data_fimUT;
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}

/* *********************************************************************
   RetornaUsuariosAcessos - retorna lista relacionando usuarios e numero de acessos
                            em UnixTime para determinada ferramenta entre duas datas.
   entrada: $sock - sock de conexao
            $cod_ferramenta - codigo da ferramenta para a qual pesquisar os acessos
            $data_iniUT - data em UT de inicio do periodo de procura
            $data_fimUT - data em UT do final do periodo de procura
   saida:   array [$cod_usuario][$dataST] - numero de acessos do usuario $cod_usuario no dia $data para aquela ferramenta
   OBS: Ex: array[10][31/10/2001] == 158 - foram 158 acessos do usuario 10 no dia 31/10/2001
*/
function RetornaUsuariosAcessos($sock,$cod_ferramenta,$data_iniUT,$data_fimUT)
{
  if (isset($cod_ferramenta) && $cod_ferramenta > 0)
  {
    $tmp = "=".$cod_ferramenta;
  }
  else
  {
    $tmp = " is null";
  }

  $query="select cod_usuario,FROM_UNIXTIME(data,'%Y%m%d') 'data', count(*) 'num_acessos' from Curso_acessos where cod_ferramenta".$tmp." and data >= ".$data_iniUT." and data <= ".$data_fimUT." and cod_usuario >=0 group by cod_usuario,FROM_UNIXTIME(data,'%Y/%m/%d') order by cod_usuario,data";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista) > 0)
  {
    foreach ($lista as $cod => $linha)
    {
      $lista_acessos_retorno[($linha['cod_usuario'] + 0)][($linha['data'] + 0)] = $linha['num_acessos'];
    }
  }
  return $lista_acessos_retorno;
}

/* *********************************************************************
   RetornaUsuariosAcessosUT - retorna array relacionando usuarios e acessos, para
                              determinada ferramenta, ordenado por data
   entrada: $sock - sock de conexao
            $cod_ferramenta - codigo da ferramenta a pesquisar os acessos
            $data_iniUT - data de inicio do periodo de busca em UT
            $data_fimUT - data de fim do periodo de busca em UT
   saida:   array []['cod_usuario'] = cod_usuario
                  []['data'] = data em UT
*/
function RetornaUsuariosAcessosUT($sock,$cod_ferramenta,$data_iniUT,$data_fimUT)
{
  if (isset($cod_ferramenta) && $cod_ferramenta > 0)
  {
    $tmp = "=".$cod_ferramenta;
  }
  else
  {
    $tmp = " is null";
  }

  $query="select cod_usuario,data from Curso_acessos where cod_ferramenta".$tmp." and data>=".$data_iniUT." and data<=".$data_fimUT." and cod_usuario >= 0 order by data";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  return $lista;
}

/* *********************************************************************
   RetornaAcessosOrdenadosPorDataUT - retorna array relacionando data de acesso e ferramenta
                                      acessada para cada usuario em determinado periodo.
                                      A saida ï¿½ordenada por usuario
   entrada: $sock - sock de conexao
            $cod_ferramenta - codigo da ferramenta a averiguar
            $data_iniUT - data de inicio do periodo de busca em UT
            $data_fimUT - data de fim do periodo de busca em UT
   saida: array [$cod_usuario][] - array dos acessos do usuario no periodo
*/
function RetornaAcessosOrdenadosPorDataUT($sock,$cod_ferramenta,$data_iniUT,$data_fimUT)
{
  // se o fim do periodo nao for especificado, considero 23:59:59 do dia de inicio
  $data_query_iniUT = $data_iniUT;
  if ($data_fimUT != "")
    $data_query_fimUT = $data_fimUT;
  else
    $data_query_fimUT = Data2UnixTime(UnixTime2Data($data_iniUT)) + 86399;

  if (isset($cod_ferramenta) && $cod_ferramenta > 0)
  {
    $tmp = "=".$cod_ferramenta;
  }
  else
  {
    $tmp = " is null";
  }

  $query="select cod_usuario,data from Curso_acessos where cod_ferramenta".$tmp." and data >= ".$data_query_iniUT." and data <= ".$data_query_fimUT." order by cod_usuario,data";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  $c = 0;
  if (count($lista) > 0)
  {
    foreach ($lista as $cod => $linha)
    {
      $lista_acessos_retorno[$linha['data']][$c++] = $linha['data'];
    }
  }
  return $lista_acessos_retorno;
}

/* *********************************************************************
   RetornaAcessosFerramentasUsuario - retorna array informando data acesso e ferramenta acessada
                                      para cada usuï¿½io em determinado perï¿½do
   entrada: $sock - sock de conexao
            $cod_usuario - codigo do usuario
            $data_iniUT - data em UT de inicio do periodo
            $data_fimUT - data em UT de fim do periodo (EXCLUINDO-SE ULTIMO SEGUNDO)
   saida:   array [$dataUT] == $cod_ferramenta
   OBS:     Na query, o limite superior de data ï¿½excluida ("and data < blah").
   Ex:      array[10000] = 10 - usuario acessou a ferramenta 10 no UnixTime 10000
*/
function RetornaAcessosFerramentasUsuario($sock,$cod_usuario,$data_iniUT,$data_fimUT)
{
  // se o fim do periodo nao for especificado, considero 23:59:59 do dia de inicio
  $data_query_iniUT = $data_iniUT;
  if ($data_fimUT != "")
    $data_query_fimUT = $data_fimUT;
  else
    $data_query_fimUT = Data2UnixTime(UnixTime2Data($data_iniUT)) + 86399;

  $query="select data,cod_ferramenta from Curso_acessos where cod_usuario=".$cod_usuario." and data >= ".$data_query_iniUT." and data < ".$data_query_fimUT." order by data";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista) > 0)
  {
    foreach ($lista as $c => $linha)
    {
      $lista_acessos_retorno[$linha['data']] = $linha['cod_ferramenta'] + 0;
    }
  }
  return $lista_acessos_retorno;
}

/* *********************************************************************
   RetornaAcessosFerramentas - retorna usuarios, datas e ferramentas acessadas
                               para determinado periodo
   entrada: $sock - sock de conexao
            $data_iniUT - data de inicio do periodo de busca em UT
            $data_fimUT - data de fim do periodo de busca, em UT
   saida: array [$cod_usuario][$data] == $cod_ferramenta
   Ex:  array [10][10000] == 3 - usuario 10 acessou a ferramenta 3 no UT 10000
*/
function RetornaAcessosFerramentas($sock,$data_iniUT,$data_fimUT)
{
  // se o fim do periodo nao for especificado, considero 23:59:59 do dia de inicio
  $data_query_iniUT = $data_iniUT;
  if ($data_fimUT != "")
    $data_query_fimUT = $data_fimUT;
  else
    $data_query_fimUT = Data2UnixTime(UnixTime2Data($data_iniUT)) + 86399;

  $query="select cod_usuario,data,cod_ferramenta from Curso_acessos where data >= ".$data_query_iniUT." and data < ".$data_query_fimUT." order by cod_usuario,data";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista) > 0)
  {
    foreach ($lista as $c => $linha)
    {
      $lista_retorno[$linha['cod_usuario'] + 0][$linha['data'] + 0] = $linha['cod_ferramenta'] + 0;
    }
  }
  return $lista_retorno;
}

/* *********************************************************************
   RetornaAcessosFerramentasGrupo - retorna usuario, data e ferramenta acessada entre dois
                                    periodos de acesso de um mesmo usuario
   entrada: $sock - sock de conexao
            $cod_usuario - codigo do usuario de referencia. os acessos retornados estao entre
               dois acessos deste.
            $data_iniUT - data de inicio de pesquisa para o usuario de referencia.
   saida:   array [$cod_usuario][$data] == $cod_ferramenta
*/
function RetornaAcessosFerramentasGrupo($sock,$cod_usuario,$data_iniUT,$dia_inteiro)
{
  // selecionando os limites de tempo para a busca
  if ($dia_inteiro)
    $data_medUT=Data2UnixTime(UnixTime2Data($data_iniUT + 86400));
  else
    $data_medUT=$data_iniUT;
  $query="select MIN(data) 'data' from Curso_acessos where cod_usuario=".$cod_usuario." and data > ".$data_medUT." and cod_ferramenta IS NULL";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $data_fimUT = $linha['data'];
  if ($data_fimUT=="")
    $data_fimUT=time();
  $query="select cod_usuario,data,cod_ferramenta from Curso_acessos where data >= ".$data_iniUT." and data < ".$data_fimUT." order by cod_usuario,data";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista) > 0)
    foreach($lista as $cod => $linha)
    {
      $lista_retorno[$linha['cod_usuario']][$linha['data']] = $linha['cod_ferramenta'] + 0;
    }
  return $lista_retorno;
}


/* *********************************************************************
   RetornaNomesGrupos - retorna lista com os nomes dos grupos
   entrada: $sock - sock de conexao
   saida:   array [$cod_grupo] - nome do grupo de codigo $cod_grupo
*/
function RetornaNomesGrupos($sock)
{
  $query="select cod_grupo,nome from Grupos where status <> 'X'";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0)
  {

    foreach($lista as $cod => $linha)
    {
      $lista_retorno[$linha['cod_grupo']] = $linha['nome'];
    }
  }
  return $lista_retorno;
}

/* *********************************************************************
   RetornaNomesUsuarios - retorna array com nomes dos usuarios
   entrada: $sock - sock de conexao
   saida  : array[ cod_usuario ] =  nome do usuario
*/
function RetornaNomesUsuarios($sock,$cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query  = "select UC.cod_usuario, U.nome ";
  $query .= "from ".$dbnamebase.".Usuario_curso UC ";
  $query .= "inner join ".$dbnamebase.".Usuario U ON ((U.cod_usuario = UC.cod_usuario_global) AND (UC.cod_curso = '".$cod_curso."')) ";
  $query .= "where UC.cod_usuario >= 0";

  $res   = Enviar($sock, $query);

  while ($linha = RetornaLinha($res))
  {
    $saida[ $linha['cod_usuario'] ] = $linha['nome'];
  }
  return $saida;
}

/* *********************************************************************
   RetornaUnicoGrupo - retorna array com os cod_usuario dos usuarios no grupo
   entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo
   saida:  array [$cod_usuario] = 1 - este usuario pertence ao grupo
*/
function RetornaUnicoGrupo($sock,$cod_grupo)
{
  $query="select cod_usuario from Grupos_usuario where cod_grupo=".$cod_grupo;
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0)
  {
    foreach ($lista as $c => $linha)
    {
      $lista_retorno[$linha['cod_usuario']] = true;
    }
  }

  return $lista_retorno;
}

/* *********************************************************************
   RetornaNomeUnicoGrupo - retorna nome do grupo pedido
   entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo
   saida:  nome do grupo
*/
function RetornaNomeUnicoGrupo($sock,$cod_grupo)
{
  $query="select nome from Grupos where cod_grupo=".$cod_grupo;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['nome'];
}

/* *********************************************************************
   HaColaboradores - retorna se ha colaboradores no curso.
   entrada: $sock      - sock de conexao
            $cod_curso - código do curso
   saida: true  - ha colaboradores
          false - nao ha
*/
function HaColaboradores($sock, $cod_curso)
{
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "select cod_usuario from ".$dbnamebase.".Usuario_curso where tipo_usuario='Z' and cod_curso = '".$cod_curso."' limit 1";
  $res   = Enviar ($sock, $query);

  return ( RetornaNumLinhas($res) > 0);
}

/* *********************************************************************
   HaVisitantes - retorna se ha visitantes
   entrada: $sock      - sock de conexao
            $cod_curso - código do curso
   saida: true  - Ha visitantes no curso
          false - Nao ha visitantes no curso
*/
function HaVisitantes($sock, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "select cod_usuario from ".$dbnamebase.".Usuario_curso where tipo_usuario='V' and cod_curso = '".$cod_curso."' limit 1";
  $res   = Enviar($sock, $query);
  return (RetornaNumLinhas($res) > 0);
}

/* *********************************************************************
   StatusFerramentaGrupos - retorna se a ferramenta Grupos esta ativada

   PROVISORIO !!! - O LUGAR DESTA FUNCAO NAO EH AQUI

   entrada: $sock        - sock de conexao
            $cod_curso   - codigo do curso
            $cod_usuario - codigo do usuario

   saida  : true  - ferramenta grupos estah ativada
            false - ferramenta grupos nao estah ativa
*/
function StatusFerramentaGrupos ($sock, $cod_curso, $cod_usuario)
{
  $query = "select status from Curso_ferramentas where cod_ferramenta=12";
  $res   = Enviar ($sock, $query);
  $linha = RetornaLinha ($res);

  $status = $linha['status'];

  if ($status == 'D')
    $retorno = false;
  else if ($status == 'F')
    $retorno = EFormador ($sock, $cod_curso, $cod_usuario);
  else if ($status == 'A')
    // se entrou aqui, status da ferramenta = 'A'. Permito para alunos e visitantes
    $retorno = true;

  return $retorno;
}

/* *********************************************************************
   CriaTabela - Abre a tabela com os acessos dos usuarios
   entrada: nenhuma
   saida: nenhuma
*/
function CriaTabela()
{
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
}

/* *********************************************************************
   FechaTabela - Fecha a tabela com os acessos dos usuarios
   entrada: nenhuma
   saida: nenhuma
*/
function FechaTabela()
{
  echo("          </table>\n");
  echo("          <br />\n");
}

/* *********************************************************************
   ExibeCabecalho - cria o cabecalho de cada tabela menor.
   entrada: $tam_tabela - numero de dias na tabela
            $num_tabelas - eh o numero de tabelas menos um.
               Se for zero, precisamos colocar uma coluna a mais na tabela, para constar os totais do periodo
            $data_iniUT - primeiro dia da tabela
            $SalvarEmArquivo - booleano, indica se deve salvar em arquivo
            $coluna_total - booleano, indica se a tabela conta com uma coluna de total de acessos
   saida: nenhuma
   OBS: O cabecalho contem duas linhas, uma com o nome dos meses abrangidos, outra com os dias do mes
*/
function ExibeCabecalho($tam_tabela, $num_tabelas, $data_iniUT, $SalvarEmArquivo, $coluna_total)
{
  global $data_invertida_g, $lista_frases;

  // primeira linha com os nomes dos meses
  echo("            <tr class=\"head\">\n");
  // casa vazia acima da coluna com os nomes dos usuarios
  echo("              <td style=\"border:0pt;\">&nbsp;</td>\n");

  // descobrindo qtos dias da data passada ateh o fim do mes
  $data_tmp = explode("/",UnixTime2Data($data_iniUT));

  if ($data_invertida_g)
  {
    $tmp1=$data_tmp[0];
    $data_tmp[0]=$data_tmp[1];
    $data_tmp[1]=$tmp1;
  }

  // detectando dezembro
  if ($data_tmp[1] == 12)
  {
    // proximo mes eh Janeiro do ano que vem
    $mes = "01";
    $ano = $data_tmp[2] + 1;
    $prox_mes="01/".$mes."/".$ano;
  }
  else
  {
    // proximo mes
    $mes = $data_tmp[1] + 1;
    $ano = $data_tmp[2];
    $prox_mes="01/".$mes."/".$ano;
  }
  $tmpUT=Data2UnixTime($prox_mes);
  // dias ateh o proximo mes
  $n_dias=ceil(($tmpUT - $data_iniUT) / 86400);
  
  if ($n_dias >= $tam_tabela)
  { // 1 mes na tabela
    $mes_tmp = NomeMes($data_tmp[1])." ".$data_tmp[2];
    // cortando nome do mes para caber na casa
    if ($tam_tabela < 5)
    {
      $mes_tmp = substr($mes_tmp, 0 ,$tam_tabela * 2);
    }
    echo("              <td colspan=".$tam_tabela.">".$mes_tmp."</td>\n");
  }
  else
  { // 2 meses
    $mes_tmp1 = NomeMes($data_tmp[1])." ".$data_tmp[2];
    // cortando nome do mes para caber na casa
    if ($n_dias < 5)
    {
      $mes_tmp1 = substr($mes_tmp1,0,$n_dias * 2);
    }
    echo("              <td colspan=".$n_dias.">".$mes_tmp1."</td>");
    $mes_tmp2 = NomeMes($mes)." ".$ano;
    // cortando nome do mes para caber na tabela
    if (($col_span = ($tam_tabela - $n_dias)) < 5)
    {
      $mes_tmp2 = substr($mes_tmp2, 0, $col_span * 2);
    }
    echo("              <td colspan=".$col_span.">".$mes_tmp2."</td>\n");
  }
  if ($coluna_total)
  {
    echo("              <td>&nbsp;</td>\n");
  }
  echo("            </tr>\n");

  // segunda linha com os dias
  echo("            <tr>\n");
  echo("              <td>&nbsp;</td>\n");
  for ($c = 0; $c < $tam_tabela; $c++)
  {
    $diaST = explode("/",UnixTime2Data($data_iniUT));
    $tmpUT = Data2UnixTime(($c + $diaST[0])."/".$diaST[1]."/".$diaST[2]);
    $dia_semana = UnixTime2DiaSemana($tmpUT);
    /* detectando sabado (1) ou domingo (7) */
    $cor = (($dia_semana == 1 || $dia_semana == 7) ? "#c73e2c" : "#2a6686");
    $data_str = explode("/",UnixTime2Data($tmpUT));
    if (!$SalvarEmArquivo)
    {
      // chamar a funcao JS para todos os acessos naquele dia. Somente interessa o segundo e o quinto parametros, os outros sao dummies
      $link_abre ="<span class=\"link\" style=\"color:".$cor.";\" onClick=\"AbreRelatorioUsuario(0,'".$tmpUT."','','',3);\">";
      $link_fecha="</span>";
    }
    else
    {
      $link_abre ="<span style=\"color:".$cor.";\">";
      $link_fecha="</font>";
    }
    echo("              <td>&nbsp;".$link_abre.$data_str[0].$link_fecha."&nbsp;</td>\n");
  }
  if ($coluna_total)
  {
    // 42 - Total
    echo("              <td>".RetornaFraseDaLista($lista_frases, 42)."</td>\n");
  }
  echo("            </tr>\n");
}

/* *********************************************************************
   ExibeCabecalhoIndividual - cria o cabecalho de cada tabela menor.
   entrada: $tam_tabela - numero de dias na tabela
            $num_tabelas - eh o numero de tabelas menos um.
               Se for zero, precisamos colocar uma coluna a mais na tabela, para constar os totais do periodo
            $data_iniUT - primeiro dia da tabela
            $SalvarEmArquivo - booleano, indica se deve salvar em arquivo
            $coluna_total - booleano, indica se a tabela conta com uma coluna de total de acessos
            $cod_ferramenta - código da ferramenta que aparecerá no relatório do usuário,
                              que aparece ao clicarmos no total de acessos daquele usuário.
                              Se for nulo, a ferramenta que aparecerá será "Entrada no ambiente".
   saida: nenhuma
   OBS: O cabecalho contem duas linhas, uma com o nome dos meses abrangidos, outra com os dias do mes
*/
function ExibeCabecalhoIndividual($tam_tabela, $num_tabelas, $data_iniUT, $SalvarEmArquivo, $coluna_total, $string, $cod_ferramenta = NULL)
{
  global $data_invertida_g, $lista_frases;

  // primeira linha com os nomes dos meses
  echo("            <tr class=\"head\">\n");
  // casa vazia acima da coluna com os nomes dos usuarios
  echo("              <td style=\"border:0pt;\">&nbsp;</td>\n");

  // descobrindo qtos dias da data passada ateh o fim do mes
  $data_tmp = explode("/",UnixTime2Data($data_iniUT));

  if ($data_invertida_g)
  {
    $tmp1=$data_tmp[0];
    $data_tmp[0]=$data_tmp[1];
    $data_tmp[1]=$tmp1;
  }

  // detectando dezembro
  if ($data_tmp[1] == 12)
  {
    // proximo mes eh Janeiro do ano que vem
    $mes = "01";
    $ano = $data_tmp[2] + 1;
    $prox_mes="01/".$mes."/".$ano;
  }
  else
  {
    // proximo mes
    $mes = $data_tmp[1] + 1;
    $ano = $data_tmp[2];
    $prox_mes="01/".$mes."/".$ano;
  }
  $tmpUT=Data2UnixTime($prox_mes);
  // dias ateh o proximo mes
  $n_dias=ceil(($tmpUT - $data_iniUT) / 86400);
  
  if ($n_dias >= $tam_tabela)
  { // 1 mes na tabela
    $mes_tmp = NomeMes($data_tmp[1])." ".$data_tmp[2];
    // cortando nome do mes para caber na casa
    if ($tam_tabela < 5)
    {
      $mes_tmp = substr($mes_tmp, 0 ,$tam_tabela * 2);
    }
    echo("              <td colspan=".$tam_tabela.">".$mes_tmp." - Ferramenta: ".$string."</td>\n");
  }
  else
  { // 2 meses
    $mes_tmp1 = NomeMes($data_tmp[1])." ".$data_tmp[2];
    // cortando nome do mes para caber na casa
    if ($n_dias < 5)
    {
      $mes_tmp1 = substr($mes_tmp1,0,$n_dias * 2);
    }
    echo("              <td colspan=".$n_dias.">".$mes_tmp1."</td>");
    $mes_tmp2 = NomeMes($mes)." ".$ano;
    // cortando nome do mes para caber na tabela
    if (($col_span = ($tam_tabela - $n_dias)) < 5)
    {
      $mes_tmp2 = substr($mes_tmp2, 0, $col_span * 2);
    }
    echo("              <td colspan=".$col_span.">".$mes_tmp2."</td>\n");
  }
  if ($coluna_total)
  {
    echo("              <td>&nbsp;</td>\n");
  }
  echo("            </tr>\n");

  // segunda linha com os dias
  echo("            <tr>\n");
  echo("              <td>&nbsp;</td>\n");
  for ($c = 0; $c < $tam_tabela; $c++)
  {
    $diaST = explode("/",UnixTime2Data($data_iniUT));
    $tmpUT = Data2UnixTime(($c + $diaST[0])."/".$diaST[1]."/".$diaST[2]);
    $dia_semana = UnixTime2DiaSemana($tmpUT);
    /* detectando sabado (1) ou domingo (7) */
    $cor = (($dia_semana == 1 || $dia_semana == 7) ? "#c73e2c" : "#2a6686");
    $data_str = explode("/",UnixTime2Data($tmpUT));
    if (!$SalvarEmArquivo)
    {
      // chamar a funcao JS para todos os acessos naquele dia. Somente interessa o segundo e o quinto parametros, os outros sao dummies
      $link_abre ="<span class=\"link\" style=\"color:".$cor.";\" onClick=\"AbreRelatorioUsuario(0,'".$tmpUT."','','',3, ".$cod_ferramenta.");\">";
      $link_fecha="</span>";
    }
    else
    {
      $link_abre ="<span style=\"color:".$cor.";\">";
      $link_fecha="</font>";
    }
    echo("              <td>&nbsp;".$link_abre.$data_str[0].$link_fecha."&nbsp;</td>\n");
  }
  if ($coluna_total)
  {
    // 42 - Total
    echo("              <td>".RetornaFraseDaLista($lista_frases, 42)."</td>\n");
  }
  echo("            </tr>\n");
}


/* *********************************************************************
    ExibeLinhaGrupoVerdadeiro - exibe linha contendo o nome do grupo e
                                os acessos dos integrantes do grupo em cada dia
    entrada: $cod_grupo - codigo do grupo
             $acessos_grupos - array contendo os acessos do grupo na forma array[data] = numero de acesso na data
             $tam_tabela - tamanho da tabela
             $diaUT - dia inicial da tabela
             $SalvarEmArquivo - bool, se deve salvar em arquivo (exibir links)
             $coluna_total - bool, se a tabela deve exibir a coluna com os totais de acessos
             $nome_grupo - nome do grupo
    saida:   nenhuma
*/
function ExibeLinhaGrupoVerdadeiro($cod_grupo, $acessos_grupo, $tam_tabela, $diaUT, $SalvarEmArquivo, $coluna_total, $nome_grupo, $listar_grupos, $cor_linha, $data_inicioUT)
{
  // casa com o nome do grupo
  if (! $SalvarEmArquivo )
  {
    $link_abre  = "<span class=\"link\" onClick=\"AbreGrupo(".$cod_grupo.");\">";
    $link_fecha = "</span>";
  }
  else
  {
    $link_abre  = "<span style=\"color:#2a6686;\">";
    $link_fecha = "</span>";
  }
  $nome = $link_abre.$nome_grupo.$link_fecha;

  if(!$listar_grupos)
    echo("            <tr class=\"altColor".($cor_linha%2)."\">\n");
  else{
    echo("            <tr class=\"head01\">\n");
    }
    
  echo("              <td class=\"alLeft\">&nbsp;".$nome."</td>\n");

  $diaST = explode("/", UnixTime2Data($diaUT));
  for ($c = 0; $c < $tam_tabela; $c++)
  {
    //inverter a data para o formato YYYY/mm/dd para consertar erro na exibiÃ§Ã£o, pois as datas iniciadas com 0 (zero) sÃ£o interpretadas erradamente (octais)
    $data = UnixTime2Data($tmpUT=Data2UnixTime( ($diaST[0] + $c)."/".$diaST[1]."/".$diaST[2]));
    $dataTmp = explode("/", $data);  
    $data_tmp = $dataTmp[2].$dataTmp[1].$dataTmp[0];
    
    
    if ( isset($acessos_grupo[$data_tmp]) )
    {
      if (! $SalvarEmArquivo )
      {
        $link_abre  = "<span class=\"link\" onClick=\"AbreRelatorioGrupo(".$cod_grupo.",".$tmpUT.",0,0,1);\">";
        $link_fecha = "</span>";
      }
      else
      {
        $link_abre  = "<span style=\"color:#2a6686;\">";
        $link_fecha = "</span>";
      }
      $link = $link_abre.$acessos_grupo[$data_tmp].$link_fecha;
    }
    else
    {
      $link = "&nbsp;";
    }

    echo("              <td>".$link."</td>\n");
  }

  if ( $coluna_total )
  {
    if (is_array($acessos_grupo))
    {
      $num = 0;
      foreach ($acessos_grupo as $num_acessos)
      {
        $num += $num_acessos;
      }
    }
    if ( isset ($num) )
    {
      if (! $SalvarEmArquivo )
      {
//         $link_abre  = "<span class=\"link\" onClick=\"AbreRelatorioGrupo(".$cod_grupo.",0,".$diaUT.",".($diaUT + $tam_tabela * 86400 - 1).",2);\">";

        $link_abre  = "<span class=\"link\" onClick=\"AbreRelatorioGrupo(".$cod_grupo.",0,".$data_inicioUT.",".$tmpUT.",2);\">";
        $link_fecha = "</span>";
      }
      else
      {
        $link_abre  = "<span style=\"color:#2a6686;\">";
        $link_fecha = "</span>";
      }
      $link = $link_abre.$num.$link_fecha;
    }
    else
    {
      $link = "&nbsp;";
    }

    echo("              <td><b>".$link."</b></td>\n");
    $c++;
  }
  echo("            </tr>\n");
}

/* *********************************************************************
   ExibeLinhaFalsoGrupo - exibe uma linha contendo somente o nome do falso grupo
   entrada: $cod_grupo - nome do falso grupo:
                -1 - grupo com alunos
                -2 - grupo com formadores
                -3 - grupo com colaboradores
                -4 - grupo com visitantes
            $tam_tabela - tamanho da tabela
            $coluna_total - booleano, se a tabela conta com uma coluna de total de acessos
*/
function ExibeLinhaFalsoGrupo($cod_grupo, $tam_tabela, $coluna_total)
{
  global $lista_frases;

  // calculamos o tamanho da linha com o falso grupo: 1 para a casa vazia acima do nome dos usuarios mais o tamanho da tabela mais 1 para a coluna de total (se ela existir)
  $colspan = 1 + $tam_tabela;
  if ($coluna_total)
    $colspan += 1;

  switch ( $cod_grupo )
  {
    // 61 - Alunos
    case -1: $nome = RetornaFraseDaLista($lista_frases, 61); break;
    // 62 - Formadores
    case -2: $nome = RetornaFraseDaLista($lista_frases, 62); break;
    // 55 - Colaboradores
    case -3: $nome = RetornaFraseDaLista($lista_frases, 55); break;
    // 57 - Visitantes
    case -4: $nome = RetornaFraseDaLista($lista_frases, 57); break;
  }

  echo("            <tr class=\"head01\">\n");
  echo("              <td><b>&nbsp;&nbsp;".$nome."</b></td>\n");
  echo("              <td colspan=".$colspan.">&nbsp;</td>\n");
  echo("            </tr>\n");
}

/* *********************************************************************
   ExibeLinhaGrupo - Exibe linha com os acessos do grupo, pode ser linha com os
                     acessos do grupo ou linha com somente o nome do grupo.
                     Falsos grupos, como os grupos 'Alunos', 'Formadores', etc. sï¿½ linha
                     com somente o nome do grupo
   entrada:  $cod_grupo - codigo do grupo. codigos negativos indicam grupos falsos
             $acessos_grupo - array contendo os acessos do grupo, no caso de grupos verdadeiros
                              esse array estah na forma $array[ data ] = numero de acessos do grupo na data
             $tam_tabela    - tamanho da tabela
             $diaUT         - data de inicio da tabela, em UT
             $SalvarEmArquivo - booleano, indica se a tabela vai ser salva em arquivo
             $coluna_total  - booleano, indica se a tabela conta com uma coluna de total de acessos
             $listar_grupos - booleano, se ï¿½preciso listar os participantes do grupo ou nao
             $cor_linha     - cor da linha
*/
function ExibeLinhaGrupo($cod_grupo, $acessos_grupo, $tam_tabela, $diaUT, $SalvarEmArquivo, $coluna_total, $nome_grupo, $listar_grupos, $cor_linha, $data_inicioUT)
{
  global $lista_frases;

  if ($cod_grupo > 0)
  {
    // grupo verdadeiro

    // concatenamos a palavra 'Grupo' na frente do nome do grupo
    // 40 - Grupo
    $nome_grupo = RetornaFraseDaLista($lista_frases, 40)." ".$nome_grupo;
    ExibeLinhaGrupoVerdadeiro($cod_grupo, $acessos_grupo, $tam_tabela, $diaUT, $SalvarEmArquivo, $coluna_total, $nome_grupo, $listar_grupos, $cor_linha, $data_inicioUT);
  }
  else
  {
    // falso grupo
    ExibeLinhaFalsoGrupo($cod_grupo, $tam_tabela, $coluna_total);
  }
}

/* *********************************************************************
   ExibeLinhaUsuario - Exibe linha da tabela com os acessos do usuario, em cada dia
   entrada: $cod_usuario     - codigo do usuario
            $nome            - nome do usuario
            $acessos_user    - array com os acessos do usuario, marcado por data Ex: array['26/03/2003'] = 10, sao 10 acessos do usuario nesse dia
            $tam_tabela      - tamanho da tabela
            $diaUT           - dia inicial da tabela
            $SalvarEmArquivo - booleano, indica se eh preciso salvar em arquivo
            $coluna_total    - indica se a tabela deve conter uma coluna de total
            $cor_linha       - int, faz a variacao de cores entre linhas
            $acessos_totais  - total de acessos do usuario no periodo
            data_iniUT       - data de inicio do periodo mostrado na tabela
            data_fimUT       - data de fim do periodo mostrado na tabela
            $cod_ferramenta  - código da ferramenta que aparecerá no relatório do usuário,
                               que aparece ao clicarmos no total de acessos daquele usuário.
   saida: nenhuma
*/
function ExibeLinhaUsuario($cod_usuario, $nome, $acessos_user, $tam_tabela, $diaUT, $SalvarEmArquivo, $coluna_total, $cor_linha, $acessos_totais, $data_iniUT, $data_fimUT, $cod_ferramenta = NULL)
{
  $cor = $cor_linha%2;
  echo("            <tr class=\"altColor".$cor."\" align=center>\n");
  // nome do usuario. A cor desta primeira casa deve ter codigo 1 ou 3
//   $cor = (($cor_linha % 2) * 2 + 1);

  
  if (! $SalvarEmArquivo)
  {
    $link_perfil_abre  = "<span class=\"link\" onClick=\"AbrePerfil(".$cod_usuario.");\">";
    $link_perfil_fecha = "</span>";
  }
  else
  {
    $link_perfil_abre  = "";
    $link_perfil_fecha = "";
  }
  $link = "&nbsp;&nbsp;&nbsp;&nbsp;".$link_perfil_abre.$nome.$link_perfil_fecha;

  echo("              <td align=left>".$link."</td>\n");

  // recalculamos a cor para que a casa com o nome tenha cor diferente da primeira casa com a data.
  $diaST = explode("/",UnixTime2Data($diaUT));
  for ($c = 0; $c < $tam_tabela; $c++)
  {
    
    //inverter a data para o formato YYYY/mm/dd para consertar erro na exibiÃ§Ã£o, pois as datas iniciadas com 0 (zero) sÃ£o interpretadas erradamente (octais)
    $data = UnixTime2Data($tmpUT=Data2UnixTime( ($diaST[0] + $c)."/".$diaST[1]."/".$diaST[2]));
    $dataTmp = explode("/", $data);  
    $data_tmp = $dataTmp[2].$dataTmp[1].$dataTmp[0];

    if (isset($acessos_user[$data_tmp]))
    {
      if (! $SalvarEmArquivo)
      {
          // A janela aberta vai tratar um unico usuario, no dia $tmpUT, das
          // zero as 24 horas. Na janela, a ferramenta que aparecerá será a 
          // especificada pelo parâmentro $cod_ferramenta, se o relatório 
          // for individual, ou pelo $cod_ferramenta da url da página de 
          // relatórios, se o relatório for por ferramenta.
          // Os outros valores sao dummies.
          if (isset($cod_ferramenta))
            $link_acesso_abre ="<span class=\"link\" onClick=\"AbreRelatorioUsuario(".$cod_usuario.",'".$tmpUT."','0','0',1, ".$cod_ferramenta.");\">";
          else
            $link_acesso_abre ="<span class=\"link\" onClick=\"AbreRelatorioUsuario(".$cod_usuario.",'".$tmpUT."','0','0',1);\">";
          $link_acesso_fecha="</span>";
      }
      else
      {
          $link_acesso_abre ="<span style=\"color:#2a6686;\">";
          $link_acesso_fecha="</span>";
      }
      $link = $link_acesso_abre.$acessos_user[$data_tmp].$link_acesso_fecha;
    }
    else
    {
      $link = "&nbsp;";
    }
    // se a primeira casa tiver cor 1, as demais na linha tem cor 1 ou 2. Se a primeira casa tiver cor 3, as demais tem 3 ou 4
    echo("              <td>".$link."</td>\n");
  }

  if ($coluna_total)
  {
    if (isset ($acessos_totais))
    {
      if (! $SalvarEmArquivo)
      {
        // A janela aberta vai tratar um unico usuario, no dia $tmpUT, das
        // zero as 24 horas. Na janela, a ferramenta que aparecerá será a 
        // especificada pelo parâmentro $cod_ferramenta, se o relatório 
        // for individual, ou pelo $cod_ferramenta da url da página de 
        // relatórios, se o relatório for por ferramenta.
        // Os outros valores sao dummies.
        if (isset($cod_ferramenta))
          $link_acesso_abre ="<span class=\"link\" onClick=\"AbreRelatorioUsuario(".$cod_usuario.",'".$tmpUT."','0','0',2, ".$cod_ferramenta.");\">";
        else
          $link_acesso_abre ="<span class=\"link\" onClick=\"AbreRelatorioUsuario(".$cod_usuario.",'".$tmpUT."','0','0',2, ".$cod_ferramenta.");\">";
        $link_acesso_fecha="</span>";
      }
      else
      {
        $link_acesso_abre ="<span style=\"color:#2a6686;\">";
        $link_acesso_fecha="</span>";
      }
      $link = $link_acesso_abre.$acessos_totais.$link_acesso_fecha;
    }
    else
    {
      $link = "&nbsp;";
    }
    echo("              <td>".$link."</td>\n");
  }

  echo("            </tr>\n");
}

/* *********************************************************************
    SomaTotaisDiarios - soma totais em um array
    entrada: array no formato array[ indice ] = numero
    saida: soma de todos os numeros no array
*/
function SomaTotaisDiarios($array_totais)
{
  $saida = 0;
  if (is_array($array_totais))
  {
    foreach ($array_totais as $num)
    {
      $saida += $num;
    }
  }
  return $saida;
}


/* *********************************************************************
    ExibeLinhaTotaisDiarios - exibe linha com o total de acessos no periodo
    entrada: $tam_tabela - tamanho da tabela
             $diaUT - data de inicio do periodo
             $coluna_total - bool, indica se a tabela mostra a coluna de total do periodo
             $totais_diarios - array com os totais de acessos dos usuarios, no formato
             array [ data ] = numero de acessos no dia
*/
function ExibeLinhaTotaisDiarios($tam_tabela, $diaUT, $coluna_total, $totais_diarios)
{
  global $lista_frases;

  echo("            <tr class=\"head01\">\n");
  // 42 - Total
  echo("              <td>".RetornaFraseDaLista($lista_frases,42)."</td>\n");
  // cada casa na linha
  $data_str = explode("/", UnixTime2Data($diaUT));
  
  for ($c = 0; $c < $tam_tabela; $c++)
  {
    //inverter a data para o formato YYYY/mm/dd para consertar erro na exibiÃ§Ã£o, pois as datas iniciadas com 0 (zero) sÃ£o interpretadas erradamente (octais)
    $data = UnixTime2Data(Data2UnixTime( ($data_str[0] + $c)."/".$data_str[1]."/".$data_str[2]));
    $dataTmp = explode("/", $data);  
    $data = $dataTmp[2].$dataTmp[1].$dataTmp[0];
    
    if ( isset($totais_diarios[$data]) )
    {
      $num = $totais_diarios[$data];
    }
    else
    {
      $num = "&nbsp;";
    }
    echo("              <td>".$num."</td>\n");
  }
  if ($coluna_total)
  {
      echo("              <td><b>".SomaTotaisDiarios($totais_diarios)."</b></td>\n");
  }
  echo("            </tr>\n");
}


/* *********************************************************************
    RetornaListaGrupos - retorna lista com os grupos do curso
    entrada:  $sock - sock de conexao
              $alunos - se retornar alunos dentro do grupo
              $formadores - se retornar formadores dentro do grupo
    saida:    array associativo multi-dimensional com os grupos do curso
      array[ $cod_grupo ][] = array com os grupos do curso
*/
function RetornaListaGrupos($sock, $alunos, $formadores, $cod_curso)
{
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $sock = Conectar("");
  $tipos = array();
  if ($alunos)
    $tipos[] = "binary USUC.tipo_usuario='A'";
  if ($formadores)
    $tipos[] = "binary USUC.tipo_usuario='F'";

  $retorno = array();

  if (count($tipos)) {

    $condicoes = array();
    $condicoes[] = '('.implode(' or ', $tipos).')';
    $condicoes[] = 'USUC.cod_curso=\''.$cod_curso.'\'';

    $condicoes = implode(' and ', $condicoes);

    $query_usu  = 'SELECT USUC.cod_usuario, USU.nome, USUC.tipo_usuario ';
    $query_usu .= 'FROM '.$dbnamebase.'.Usuario_curso USUC ';
    $query_usu .= 'INNER JOIN '.$dbnamebase.'.Usuario USU ON (USU.cod_usuario = USUC.cod_usuario_global) ';
    $query_usu .= 'WHERE '.$condicoes.' ';
    $query_usu .= 'order by tipo_usuario, nome';

    $res_usu   = Enviar($sock, $query_usu);
    $lista_usu = RetornaArrayLinhas($res_usu);

    if (count($lista_usu) > 0)
    {
      $lista_codigos = Array();
      foreach ($lista_usu as $valor)
      {
        $lista_codigos[] = $valor['cod_usuario'];
      }

      $sock = Conectar($cod_curso);

      $condicoes = array();
      $condicoes[] = 'G.status<>\'X\'';
      $condicoes[] = 'G.cod_grupo=GU.cod_grupo';
      $condicoes[] = 'GU.cod_usuario IN ('.implode (',', $lista_codigos).')';

      $condicoes = implode(' and ', $condicoes);

      $query = 'select GU.cod_usuario, G.cod_grupo from Grupos G, Grupos_usuario GU  where '.$condicoes;

      //$query.=  " order by U.tipo_usuario, U.nome";
      // a ordenacao por tipo_usuario serve para colocar Alunos 'A' antes de Formadores 'F'

      $res   = Enviar($sock, $query);
      while ($linha = RetornaLinha($res))
      {
        $retorno[ $linha['cod_grupo'] ][] = $linha['cod_usuario'];
      }

    }
  }

  // Se $retorno é vazio, ou ambos os parâmetros $alunos e $formadores são
  // falsos ou a query de recuperação de alunos não retornou nenhum resultado.
  if (!count($retorno))
  {
    $sock = Conectar($cod_curso);

    $query = 'select cod_grupo from Grupos where status<>\'X\'';
    $res   = Enviar($sock, $query);
    while ($linha = RetornaLinha($res))
    {
      $retorno[$linha['cod_grupo']] = '';
    }
  }

  return $retorno;
}

/* *********************************************************************
   RetornaFalsosGrupos - retorna lista com falsos grupos. Servem para agrupar
                         alunos, formadores e visitantes
   entrada: $alunos        - booleano, se montar grupo com alunos
            $formadores    - booleano, se montar grupo com formadores
            $colaboradores - booleano, se montar grupo com colaboradores
            $visitantes    - booleano, se montar grupo com visitantes
   saida:   array que agrupa os usuario em grupos, de acordo com seus tipo_usuario
              [-1] = array com os cod_usuario dos alunos
              [-2] = array com os cod_usuario dos formadores
              [-3] = array com os cod_usuario dos colaboradores
              [-4] = array com os cod_usuario dos visitantes
*/
function RetornaFalsosGrupos($sock, $alunos, $formadores, $colaboradores, $visitantes, $cod_curso)
{
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  // primeiro, descobrimos os tipos de usuarios a retornar
  $tipos = array();
  if ($alunos)
    $tipos[] = 'binary tipo_usuario=\'A\'';
  if ($formadores)
    $tipos[] = 'binary tipo_usuario=\'F\'';
  if ($colaboradores)
    $tipos[] = 'binary tipo_usuario=\'Z\'';
  if ($visitantes)
    $tipos[] = 'binary tipo_usuario=\'V\'';

  // Se nenhuma das trÃªs booleanas de entrada for verdadeira, Ã© porque queremos
  // exibir apenas os grupos. Entï¿½ nï¿½ retornamos usuï¿½ios e a funï¿½o pÃ¡ra por aqui.

    $retorno = array();

    if (count($tipos))
    {

      $condicoes = array();

      $condicoes[] = '('.implode(' or ', $tipos).')';
      $condicoes[] = 'cod_usuario>0';
      $condicoes[] = 'cod_curso=\''.$cod_curso.'\'';

      $condicoes = implode(' and ', $condicoes);

      // a query
      // a ordenacao por tipo_usuario desc serve para colocar Formadores 'F' antes de Aluno 'A'
      $query = 'select cod_usuario, tipo_usuario from '.$dbnamebase.'.Usuario_curso where '.$condicoes.' order by tipo_usuario';

      $res   = Enviar($sock, $query);

      // este array auxiliar nos ajuda a separar os usuarios em seus grupos
      $separa = array('A' => -1, 'F' => -2, 'Z' => -3, 'V' => -4);

      while ($linha = RetornaLinha($res))
      {
        $retorno[ $separa[$linha['tipo_usuario'] ] ][] = $linha['cod_usuario'];
      }

      // Ordenar saida, para aparecer na ordem Alunos, Formadores, Visitantes
      krsort($retorno);

    }

  return $retorno;
}

/* *********************************************************************
    RetornaGrupos - retorna lista com os grupos do curso
    entrada:
        $sock - sock de conexao
        $exibir_grupos - booleano. true  = retornar grupos do curso
                                   false = retornar falsos grupos, de alunos, formadores e visitantes
        $exibir_alunos        - booleano, indica se retornar grupo com os alunos, NO CASO DE RETORNAR FALSOS GRUPOS ($exibir_grupos = false). 

        $exibir_formadores    - booleano, indica se retornar grupo com os formadores, NO CASO DE RETORNAR FALSOS GRUPOS ($exibir_grupos = false)

        $exibir_colaboradores - booleano, indica se retornar grupo com os colaboradores, NO CASO DE RETORNAR FALSOS GRUPOS ($exibir_grupos = false)

        $exibir_visitantes    - booleano, indica se retornar grupo com os visitantes, NO CASO DE RETORNAR FALSOS GRUPOS ($exibir_grupos = false)

    saida: array associativo multi-dimensional com os grupos do curso
        array[$cod_grupo] - ï¿½um array contendo os cod_usuario dos alunos do curso
*/
function RetornaGrupos($sock, $exibir_grupos, $exibir_alunos, $exibir_formadores, $exibir_colaboradores, $exibir_visitantes, $cod_curso)
{
  if ($exibir_grupos)
  {
    // Retornamos não somente os grupos verdadeiros, mas também os grupos com visitantes, se forem pedidos.
    $grupos = RetornaListaGrupos($sock, $exibir_alunos, $exibir_formadores, $cod_curso);

    $sock = Conectar("");

    $falsos_grupos = RetornaFalsosGrupos($sock, $exibir_alunos, $exibir_formadores, $exibir_colaboradores, $exibir_visitantes, $cod_curso);
    if (is_array($falsos_grupos))
    {
      foreach ($falsos_grupos as $cod_grupo => $array_usuarios)
      {
        $grupos[ $cod_grupo ] = $array_usuarios;
      }
    }

    return $grupos;
  }
  else
    return RetornaFalsosGrupos($sock, $exibir_alunos, $exibir_formadores, $exibir_colaboradores, $exibir_visitantes, $cod_curso);
}

/* *********************************************************************
   RetornaAcessosGrupos - retorna acessos diarios dos grupos (verdadeiros) para determinada ferramenta em determinado periodo
   entrada: $sock - conexao com a base
            $cod_ferramenta - codigo da ferramenta
            $data_iniUT - data de inicio do periodo
            $data_fimUT - data final do periodo
   saida: array [ $cod_grupo ][ data ] = numero de acessos do grupo naquela data
*/
function RetornaAcessosGrupos($sock, $cod_ferramenta, $data_iniUT, $data_fimUT)
{
  // determinando para que ferramenta olhar os acessos
  if (isset ($cod_ferramenta) )
  {
    $ferr = "cod_ferramenta=".$cod_ferramenta;
  }
  else
  {
    $ferr = "cod_ferramenta is null";
  }

  //inverter a data para o formato YYYY/mm/dd para consertar erro na exibiÃ§Ã£o, pois as datas iniciadas com 0 (zero) sÃ£o interpretadas erradamente (octais)
  $query = "select FROM_UNIXTIME(data,'%Y%m%d') as 'data', count(*) as 'num_acessos', GU.cod_grupo as 'cod_grupo' from Curso_acessos CA, Grupos_usuario GU, Grupos G";
  $query.= " where CA.cod_usuario=GU.cod_usuario";
  $query.=   " and G.cod_grupo=GU.cod_grupo";
  $query.=   " and G.status<>'X'";
  $query.=   " and ".$ferr;
  $query.=   " and data>=".$data_iniUT;
  $query.=   " and data<=".$data_fimUT;
  $query.= " group by cod_grupo, FROM_UNIXTIME(data,'%Y%m%d')";
  $res   = Enviar($sock, $query);
  while ($linha = RetornaLinha($res) )
  {
    $saida[ $linha['cod_grupo'] ][ $linha['data'] ] = $linha['num_acessos'];
  }
  return $saida;
}

/* *********************************************************************
   RetornaTotaisUsuarios - retorna os totais de acessos de todos os usuarios no periodo
   entrada: $acessos_users - array [ $cod_usuario ][ $data ] = numero de acessos do usuario $cod_usuario na data
   saida:   array [$cod_usuario] = soma dos acessos do usuario no array de entrada
*/
function RetornaTotaisUsuarios( $acessos_users )
{
  $retorno = array();
  if (is_array ($acessos_users) )
  {
    foreach ( $acessos_users as $cod_usuario => $acessos_usuario )
    {
      if (is_array ($acessos_usuario) )
      {
        foreach ($acessos_usuario as $data => $num_acessos)
        {
          if (!isset($retorno[$cod_usuario]))
            $retorno[$cod_usuario]  = 0;
          else
            $retorno[$cod_usuario] += $num_acessos;
        }
      }
    }
  }
  return $retorno;
}

/* *********************************************************************
   RetornaAcessosDiarios - retorna o total de acessos em cada dia durante um periodo
   entrada
        $sock                  - sock de conexao
        $cod_ferramenta        - ferramenta na qual verificar os acessos (null para entrada no ambiente)
        $data_iniUT            - data de inicio do periodo
        $data_fimUT            - data de final do periodo
        $exibir_alunos         - bool, se exibir alunos
        $exibir_formadores     - bool, se exibir formadores
        $exibir_colaboradores  - bool, se exibir colaboradores
        $exibir_visitantes     - bool, se exibir visitantes
   saida: array associativo na data, contendo o numero de acessos no dia
       array [ data ] = numero de acessos no dia
*/
function RetornaAcessosDiarios($sock, $cod_ferramenta, $data_iniUT, $data_fimUT, $exibir_alunos, $exibir_formadores, $exibir_colaboradores, $exibir_visitantes, $exibir_grupos, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  // determinando para que ferramenta olhar os acessos
  if (isset ($cod_ferramenta) )
  {
    $ferr = "cod_ferramenta=".$cod_ferramenta;
  }
  else
  {
    $ferr = "cod_ferramenta is null";
  }

  // determinando o tipo de usuario para olhar os acessos
  $tmp = array();
  $bool = false;

  if ($exibir_alunos)
  {
    $tmp[] = "binary tipo_usuario='A'";
    $bool  = true;
  }
  if ($exibir_formadores && !$exibir_grupos)
  {
    $tmp[] = "binary tipo_usuario='F'";
    $bool = true;
  }
  if ($exibir_colaboradores)
  {
    $tmp[] = "binary tipo_usuario='Z'";
    $bool = true;
  }
  if ($exibir_visitantes)
  {
    $tmp[] = "binary tipo_usuario='V'";
    $bool = true;
  }
  if ($bool)
    $tipo_usuario = " (".implode(" or ", $tmp).")";

  $saida = array();

  if ($exibir_grupos)
  {
    if ($exibir_formadores)
    {
      if ($exibir_alunos)
      {
        $query_usu  = "SELECT cod_usuario FROM ".$dbnamebase.".Usuario_curso ";
        $query_usu .= "WHERE (binary tipo_usuario='A' or binary tipo_usuario='F') ";
        $query_usu .= "and cod_curso='".$cod_curso."'";

        $res_usu    = Enviar($sock, $query_usu);
        $lista_usu  = RetornaArrayLinhas($res_usu);

        if (count ($lista_usu) > 0)
        {
          $lista1 = Array();
          foreach ($lista_usu as $valor)
          {
            $lista1[] = $valor['cod_usuario'];
          }
          
          $sock = Conectar($cod_curso);
          
          //inverter a data para o formato YYYY/mm/dd para consertar erro na exibiÃ§Ã£o, pois as datas iniciadas com 0 (zero) sÃ£o interpretadas erradamente (octais)
          $query = "select FROM_UNIXTIME(data,'%Y%m%d') 'data',count(*) 'num_acessos' from Curso_acessos CA where CA.cod_usuario IN ('".implode(",", $lista1)."')";
          $query.= " and ".$ferr;
          $query.= " and data>=".$data_iniUT;
          $query.= " and data<=".$data_fimUT;
          $query.= " group by FROM_UNIXTIME(data,'%Y%m%d')";
          $res   = Enviar($sock, $query);
          
          while ($linha = RetornaLinha($res))
          {
            $saida[ $linha['data'] ] = $linha['num_acessos'];
          }
          
        }
      }
      else
      {
        $query_usu  = "SELECT cod_usuario FROM ".$dbnamebase.".Usuario_curso ";
        $query_usu .= "WHERE binary tipo_usuario='F' ";
        $query_usu .= "and cod_curso='".$cod_curso."'";

        $res_usu    = Enviar($sock, $query_usu);
        $lista_usu  = RetornaArrayLinhas($res_usu);

        if (count ($lista_usu) > 0)
        {
          $lista1 = Array();
          foreach ($lista_usu as $valor)
          {
            $lista1[] = $valor['cod_usuario'];
          }
          
          $sock = Conectar($cod_curso);
          
          //inverter a data para o formato YYYY/mm/dd para consertar erro na exibiÃ§Ã£o, pois as datas iniciadas com 0 (zero) sÃ£o interpretadas erradamente (octais)
          $query = "select FROM_UNIXTIME(data,'%Y%m%d') 'data',count(*) 'num_acessos' from Curso_acessos CA where CA.cod_usuario IN ('".implode(",", $lista1)."')";
          $query.= " and ".$ferr;
          $query.= " and data>=".$data_iniUT;
          $query.= " and data<=".$data_fimUT;
          $query.= " group by FROM_UNIXTIME(data,'%Y%m%d')";
          $res   = Enviar($sock, $query);
          
          while ($linha = RetornaLinha($res))
          {
            $saida[ $linha['data'] ] = $linha['num_acessos'];
          }
          
        }
      }
     }
     else
     {
       if ($exibir_alunos)
       {
         $query_usu  = "SELECT cod_usuario FROM ".$dbnamebase.".Usuario_curso ";
         $query_usu .= "WHERE binary tipo_usuario='A' ";
         $query_usu .= "and cod_curso='".$cod_curso."'";

         $res_usu   = Enviar($sock, $query_usu);
         $lista_usu = RetornaArrayLinhas($res_usu);

         if (count ($lista_usu) > 0)
         {
           $lista1 = Array();
           foreach ($lista_usu as $valor)
           {
             $lista1[] = $valor['cod_usuario'];
           }
           
           $sock = Conectar($cod_curso);
           
           //inverter a data para o formato YYYY/mm/dd para consertar erro na exibiÃ§Ã£o, pois as datas iniciadas com 0 (zero) sÃ£o interpretadas erradamente (octais)
           $query = "select FROM_UNIXTIME(data,'%Y%m%d') 'data',count(*) 'num_acessos' from Curso_acessos CA where CA.cod_usuario IN ('".implode(",", $lista1)."')";
           $query.= " and ".$ferr;
           $query.= " and data>=".$data_iniUT;
           $query.= " and data<=".$data_fimUT;
           $query.= " group by FROM_UNIXTIME(data,'%Y%m%d')";
           $res   = Enviar($sock, $query);
           if (isset($saida)) unset($saida);
           while ($linha = RetornaLinha($res))
           {
             $saida[ $linha['data'] ] = $linha['num_acessos'];
           }
           
         }
       }
       else
       {
         $query_usu  = "SELECT cod_usuario FROM ".$dbnamebase.".Usuario_curso ";
         $query_usu .= "WHERE (binary tipo_usuario='A' or binary tipo_usuario='F') ";
         $query_usu .= "and cod_curso='".$cod_curso."'";

         $res_usu   = Enviar($sock, $query_usu);
         $lista_usu = RetornaArrayLinhas($res_usu);

         if (count ($lista_usu) > 0)
         {
           $lista1 = Array();
           foreach ($lista_usu as $valor)
           {
             $lista1[] = $valor['cod_usuario'];
           }
           
           $sock = Conectar($cod_curso);
           
           //inverter a data para o formato YYYY/mm/dd para consertar erro na exibiÃ§Ã£o, pois as datas iniciadas com 0 (zero) sÃ£o interpretadas erradamente (octais)
           $query = "select FROM_UNIXTIME(data,'%Y%m%d') 'data',count(*) 'num_acessos' from Curso_acessos CA where CA.cod_usuario IN ('".implode(",", $lista1)."')";
           $query.= " and ".$ferr;
           $query.= " and data>=".$data_iniUT;
           $query.= " and data<=".$data_fimUT;
           $query.= " group by FROM_UNIXTIME(data,'%Y%m%d')";
           $res   = Enviar($sock, $query);
           if (isset($saida)) unset($saida);
           while ($linha = RetornaLinha($res))
           {
             $saida[ $linha['data'] ] = $linha['num_acessos'];
           }
           
         }
       }
     }

     //Desconectar($sock);
     $sock = Conectar("");

     $query_usu  = "SELECT cod_usuario FROM ".$dbnamebase.".Usuario_curso ";
     $query_usu .= "WHERE ".(($bool) ? $tipo_usuario: "1")." ";
     $query_usu .= "and cod_curso='".$cod_curso."'";

     $res_usu   = Enviar($sock, $query_usu);
     $lista_usu = RetornaArrayLinhas($res_usu);

     if (count ($lista_usu) > 0)
     {
       $lista1 = Array();
       foreach ($lista_usu as $valor)
       {
         $lista1[] = $valor['cod_usuario'];
       }
       
       Desconectar($sock);
       $sock = Conectar($cod_curso);
       
       //inverter a data para o formato YYYY/mm/dd para consertar erro na exibiÃ§Ã£o, pois as datas iniciadas com 0 (zero) sÃ£o interpretadas erradamente (octais)
       $query= "select FROM_UNIXTIME(data,'%Y%m%d') 'data',count(*) 'num_acessos' from Curso_acessos CA, Grupos_usuario GU, Grupos G where CA.cod_usuario IN (".implode(",", $lista1).") and GU.cod_grupo = G.cod_grupo and G.status<>'X'";
       
       /*if ($bool)
        $query.= $tipo_usuario;*/
       $query.= " and ".$ferr;
       $query.= " and data>=".$data_iniUT;
       $query.= " and data<=".$data_fimUT;
       $query.= " group by FROM_UNIXTIME(data,'%Y%m%d')";
       $res   = Enviar($sock, $query);
       if (isset($saida)) unset($saida);
       while ($linha1 = RetornaLinha($res))
       {
         $saida1 [ $linha1['data'] ] = $linha1['num_acessos'];
       }
       
       if($saida['data'] == $saida1['data'])
       {
         $saida['data'] = $saida['data'] + $saida1['data'];
       }
       
     }
  }
  else
  {
     $query_usu  = "SELECT cod_usuario FROM ".$dbnamebase.".Usuario_curso ";
     $query_usu .= "WHERE ".(($bool) ? $tipo_usuario." and " : "");
     $query_usu .= "cod_curso='".$cod_curso."'";

     $res_usu   = Enviar($sock, $query_usu);
     $lista_usu = RetornaArrayLinhas($res_usu);

     $lista1 = Array();
     if (count ($lista_usu) > 0)
     {
       $lista1 = Array();
       foreach ($lista_usu as $valor)
       {
         $lista1[] = $valor['cod_usuario'];
       }
       
       $sock = Conectar($cod_curso);
       
       //inverter a data para o formato YYYY/mm/dd para consertar erro na exibiÃ§Ã£o, pois as datas iniciadas com 0 (zero) sÃ£o interpretadas erradamente (octais)
       $query = "select FROM_UNIXTIME(data,'%Y%m%d') 'data',count(*) 'num_acessos' from Curso_acessos CA where ";
       $query.= (count ($lista1) > 0)?"CA.cod_usuario IN (".implode(",", $lista1).") and ": "";
       /*if ($bool)
        $query.= $tipo_usuario;*/
       $query.= $ferr." and";
           $query.= " data>=".$data_iniUT." and ";
               $query.= " data<=".$data_fimUT;
               $query.= " group by FROM_UNIXTIME(data,'%Y%m%d')";
               $res   = Enviar($sock, $query);
               if (isset($saida)) unset($saida);
               while ($linha = RetornaLinha($res))
               {
                 $saida [ $linha['data'] ] = $linha['num_acessos'];
               }
       
     }
  }

  return $saida;
}

/* Funcao ExistemGrupos
 *Entrada Conexao com base do curso.
 *Saida Boolean, true caso existam grupos no curso, falso caso contrario. */
function ExistemGrupos($sock)
{
    $query = "select COUNT(cod_grupo) as total_grupos from Grupos";
    $res = Enviar($sock, $query);
    $linha = RetornaLinha($res);
    if ($linha['total_grupos'] > 0)
        return true;
    else
        return false;
}

/* Funcao ExistemAlunox
 *Entrada Conexao com base do curso.
 *Saida Boolean, true caso existam alunos no curso, falso caso contrario. */
function ExistemAlunos($sock, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

    $query = "select COUNT(cod_usuario) as total_alunos from ".$dbnamebase.".Usuario_curso where binary tipo_usuario='A' and cod_curso='".$cod_curso."'";
    $res = Enviar($sock, $query);
    $linha = RetornaLinha($res);

    return ($linha['total_alunos'] > 0);
}

/* *********************************************************************
   ExpulsaVisitante - verifica se o usuario eh visitante e impede seu
                      acesso se for
   entrada: $sock        - sock de conexao
            $cod_curso   - código do curso atual.
            $cod_usuario - código do usuário o qual pretende-se testar o
                           acesso à ferramenta correio.
            $eh_popup    - booleano que indica se a página que o chamou
                           é ou não um popup. Se não for, devemos fechar
                           as tags html do template comum de uma página
                           de ferramenta (com rodapé, fechando o tabelão,
                           etc).
            saida:   nenhuma
   OBS:     Para impedir o acesso, exibe tela com restricao de acesso e
            executa o comando exit
*/
function ExpulsaVisitante($sock, $cod_curso, $cod_usuario, $eh_popup = false) {

  global $lista_frases;
  global $lista_frases_geral;

  if (EVisitante($sock, $cod_curso, $cod_usuario)) {

    //if (!$ehPopup) {
    //  include("../menu_principal.php");
    //  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
    //}
    if ($eh_popup) {
      echo("  </head>\n");
      echo("  <body>\n");
      echo("    <h3 style=\"margin-top:20px;\">".NomeCurso($sock,$cod_curso)."</h3>\n");
    }

    /* 1 - Acessos 504 - Área restrita a alunos e formadores */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases_geral,504)."</h4>\n");

    if (!$eh_popup) {
      echo("          <ul class=\"btsNav\">\n");
      echo("            <li>\n");
      /* 509 - Voltar */
      echo("              <span onclick=\"javascript:history.back(-1);\">\n");
      echo("                &nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;\n");
      echo("              </span>\n");
      echo("            </li>\n");
      echo("          </ul>\n");
    }

    if (!$eh_popup) {
      echo("          <div id=\"mudarFonte\">\n");
      echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
      echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
      echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
      echo("          </div>\n");
    }

    if ($eh_popup) {
      /* 13 - Fechar */
      echo("          <form>\n");
      echo("            <input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral, 13)."\" onclick=\"javascript:self.close();\" />\n");
      echo("          </form>\n");
    }
    else {
      /* 23 - Voltar */
      echo("          <form>\n");
      echo("            <input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral, 23)."\" onclick=\"javascript:history.go(-1);\" />\n");
      echo("          </form>\n");
    }

    if (!$eh_popup) {
      echo("        </td>\n");
      echo("      </tr>\n");
      include("../tela2.php");
    }

    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }
}

?>
