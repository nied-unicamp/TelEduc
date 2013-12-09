<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/menu.inc

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

// include("enquete/enquete.inc");
// include("grupos/grupos.inc");

/*==========================================================
  ARQUIVO : cursos/aplic/menu.inc
  ========================================================== */

/* *********************************************************************
   AdmiteVisitante - Retorna o Diret�rio da tabela de diretorios
   Entrada: $sock - BASE EXTERNA ou Interna
            $cod_curso - curso atual
   Saida: true se admite
*/
function AdmiteVisitante($sock,$cod_curso)
{
  $query="select acesso_visitante from Cursos where cod_curso=".$cod_curso;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha[0]=="A")
    return true;
  else
    return false;
}

/* *********************************************************************
   RetornaCSSInline - Dado um array de arquivos css, retorna seu conteudo
                      inline 
 */
function RetornaCSSInline($estilos_css){
  $inline = "";

  foreach($estilos_css as $css){
    $inline .= file_get_contents($css);
  }

  return $inline;
}

/* *********************************************************************
   RetornaListaFerramentas - Retorna a lista de ferramentas
   Entrada: $sock - BASE EXTERNA
   Saida: array [$cod_ferramenta]['cod_texto_nome']
                [$cod_ferramenta]['cod_texto_descricao']
                [$cod_ferramenta]['diretorio']
*/
if(!function_exists("RetornaListaFerramentas"))
{
  function RetornaListaFerramentas($sock)
  {
    $query ="select * from Ferramentas order by cod_ferramenta";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);
    $lista1 = "";
    unset($lista1);
    foreach($lista as $cod=>$linha)
    {
      $lista1[$linha['cod_ferramenta']]['cod_texto_nome']=$linha['cod_texto_nome'];
      $lista1[$linha['cod_ferramenta']]['cod_texto_descricao']=$linha['cod_texto_descricao'];
      $lista1[$linha['cod_ferramenta']]['diretorio']=$linha['diretorio'];
    }
    return ($lista1);
  }
}

/* *********************************************************************
   RetornaListaTitulos - Retorna a lista de titulos
   Entrada: $sock - BASE EXTERNA
            $cod_lingua - idioma em que deve ser retornado o texto
   Saida: [$cod_ferramenta]
 
*/
function RetornaListaTitulos($sock, $cod_lingua)
{
  $query ="select * from Lingua_textos where (cod_lingua = ".$cod_lingua.") and (cod_texto = 1) order by cod_ferramenta";
  $res=Enviar($sock, $query);
  $lista=RetornaArrayLinhas($res);
  $lista1 = "";
  unset($lista1);
  foreach($lista as $cod => $linha)
  {

    $lista1[$linha['cod_ferramenta']]=$linha['texto'];
  }
  return ($lista1);
}

/* *********************************************************************
   RetornaOrdemFerramentas - Retorna a ordem das ferramentas no menu
   Entrada: $sock - BASE EXTERNA
   Saida: array []['cod_ferramenta']= codigo da ferramenta
                []['posicao'] = posicao da ferramenta. -1 se barra
*/
if(!function_exists("RetornaOrdemFerramentas"))
{
  function RetornaOrdemFerramentas($sock)
  {
    $query ="select * from Menu order by posicao";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);
    return ($lista);
  }
}

/* *********************************************************************
   RetornaFerramentasCurso - Retorna a lista de ferramentas com status e 
                             acessibilidade
   Entrada: $sock - BASE DO CURSO
   Saida: array [$cod_ferramenta]['status'] - D, F, T
                [$cod_ferramenta]['acesso_visitante'] - A ou N
*/
function RetornaFerramentasCurso($sock)
{
  $query ="select * from Curso_ferramentas order by cod_ferramenta";
  $res=Enviar($sock,$query);
  $lista1 = "";
  unset($lista1);
  if (RetornaNumLinhas($res)>0)
  {
    $lista=RetornaArrayLinhas($res);
    foreach($lista as $cod=>$linha)
    {
      $lista1[$linha['cod_ferramenta']]['status']=$linha['status'];
      $lista1[$linha['cod_ferramenta']]['acesso_visitante']=$linha['acesso_visitante'];
    }
  }
  return ($lista1);
}

/* *********************************************************************
   RetornaNovidadeFerramentas - retorna um array com as datas das �ltimas 
                                atualiza��es para o usu�rio dado.
   Entrada: $sock - BASE DO CURSO
            $cod_curso
            $cod_usuario
   Saida: array [$cod_ferramenta] - data ultimo acesso
*/
function RetornaNovidadeFerramentas($sock,$cod_curso,$cod_usuario)
{
  $saida[0]="0"; /* para ser usado como retorno padr�o */

  /*****************************************************
   Pega sigla que representa o ator 
   *****************************************************/
  $ator = RetornaStatusUsuario($sock,$cod_curso,$cod_usuario) ;
  if (ECoordenadorMesmo($sock,$cod_curso,$cod_usuario)){
    /* R - coordenador */
    $ator = 'R';
  }
  
  $eformador = ($ator == 'F' || $ator == 'R');
  
  /***************************************************** 
   Pega todas as ferramentas ativas do curso 
   *****************************************************/  
  $query="select cod_ferramenta from Curso_ferramentas where status='A' ";
  $res=Enviar($sock,$query);
  $lista_ferramentas=RetornaArrayLinhas($res);
  /* inicia como 0 a data de atualiza��o de cada ferramenta ativa do curso */
  foreach($lista_ferramentas as $cod => $ferramenta)
  
    $saida[$ferramenta['cod_ferramenta']]= 0;
  
    
  /************************************************************************************************
   Pega as ferramentas de tratamento comum.
   Ferramentas que s� tem compartilhamento para atores. 
   N�o tem compartilhamento com usuarios especificos
   e/ou grupos 
  *************************************************************************************************/
  $query="select cod_ferramenta, data from Ferramentas_nova where tipo_compartilhamento='".$ator."' or tipo_compartilhamento='T' order by cod_ferramenta";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  /* Atualiza o vetor de saida para cada codigo de ferramenta */
  if (count($lista) > 0){
    foreach($lista as $cod => $linha){
    	
      $saida[$linha['cod_ferramenta']] = $linha['data'];
      
    }
  } 
  
  /*************************************************************************************************
   Inicio do c�digo para Ferramentas que possuem 
   compartilhamento de baixa granularidade
   (usuarios e/ou grupos) 
  *************************************************************************************************/
  /*
      9 - F�rum
  */
  
  /******************************************************
   Pega os grupos dos quais o usu�rio faz parte 
   ******************************************************/
  $grupos_usuario = RetornaGruposDoUsuario($sock,$cod_usuario);
  /******************************************************
   Pega o maior data de atualiza��o para aquele usu�rio ou 
   para qualquer um dos grupos aos quais ele pertence 
   ******************************************************/
  $query="select cod_ferramenta, max(data) as 'data' from Ferramentas_nova_usuarios where (tipo='U' and cod='".$cod_usuario."') " ;
  /* Faz com que a query busque por todos os grupos dos quais ele faz parte */
  if ((count($grupos_usuario) > 0) && $grupos_usuario != ""){
    $tmp = "(";
    foreach($grupos_usuario as $cod => $grupo){
      if($cod > 0) $tmp .= ", ";
      $tmp .= $grupo['cod_grupo'];
    }
    $tmp .= ")";

    $query .= " or (tipo='G' and cod in ".$tmp.")" ;

  }
  $query .= " group by cod_ferramenta";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  /* atualiza o vetor de saida se a data de atualiza��o 
  encontrada para o usuario numa dada ferramenta � maior
  do que o valor atual (vindo da data de atualiza��o pelo tipo do usuario)  */
  if ((is_array($lista)) && (count($lista) > 0)){
    foreach($lista as $cod => $linha){
      
      $saida[$linha['cod_ferramenta']]= max($saida[$linha['cod_ferramenta']], $linha['data']);
    }
  }  
 
  /*****************************************************
   Fim do c�digo para Ferramentas que possuem 
   compartilhamento de baixa granularidade
   (usuarios e/ou grupos) 
  ******************************************************/  
    
  /* Correio (11)*/
  /* Se tiver alguma mensagem com N, aparece a estrela */
  $query="select count(*) from Correio_destinos where estado='N' and cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha[0]>0)
    $saida[11]=time();

  /* Diario (14)*/
  if ((isset($eformador)) && ($eformador))
  {
    /* Se formador, verifica se h� itens ou coment�rios novos em qualquer usu�rio */
    /* Itens novos */
    $query="select max(data) from Diario_itens where status='A'";
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $data_itens=$linha[0];

    $query="select max(data) from Diario_comentarios where status='A' and cod_comentarist<>".$cod_usuario;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $data_comentarios=$linha[0];

    $saida[14]=max($data_comentarios, $data_itens);
  }
  else
  {
    /* Se for aluno, checa se h� coment�rio novo em seu di�rio */
    $query="select max(Dc.data) from Diario_itens Di, Diario_comentarios Dc where Di.cod_item=Dc.cod_item and Di.cod_usuario=".$cod_usuario." and Di.status='A' and Dc.status='A'";
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $saida[14]=$linha[0];   
  }
 
  /* Portfolio e Portfolio de Grupos */

  return $saida;
}

/* **********************************************************************
   RetornaConfiguracao - Retorna o conte�do de um item da configura��o
   Entrada: $sock - sock da base externa
            $item - item a ser buscado
   Saida: conte�do
*/
function RetornaConfiguracao($sock,$item)
{
  $query="select valor from Config where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha['valor']);
}

if(!function_exists("RetornaMaiorDataItemComentario")){
  /* *********************************************************************
    RetornaMaiorDataItemComentario - Retorna a maior data de item ou coment�rio
                                      dentro de um t�pico ou subt�pico
    Entrada: $sock - Conex�o com a base interna
              $cod_topico - codigo do topico a ser checado
              $tipo_comp - tipo do compartilhamento (F ou T)
              $data - Data do t�pico
              $cod_usuario - usuario logado
    Saida: maior data 
  */
  function RetornaMaiorDataItemComentario($sock,$cod_topico,$tipo_comp,$data,$cod_usuario)
  {
    $query="select cod_usuario,cod_grupo from Portfolio_topicos where cod_topico=".$cod_topico;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $cod_usuario_portfolio=$linha['cod_usuario'];
    $cod_grupo_portfolio=$linha['cod_grupo'];
  
    if ($cod_grupo_portfolio=="" && $cod_usuario==$cod_usuario_portfolio)
      $tipo_comp="P";
    else 
      if (PertenceAoGrupo($sock,$cod_usuario,$cod_grupo_portfolio))
        $tipo_comp="P";
  
    $query="select cod_topico,data from Portfolio_topicos where cod_topico_pai=".$cod_topico;
    if ($tipo_comp=="T")
      $query.=" and tipo_compartilhamento='T'";
    if ($tipo_comp=="F")
      $query.=" and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
  
    $res=Enviar($sock,$query);
    $lista_topicos=RetornaArrayLinhas($res);
  
    /* Verifica se existem subt�picos */
    if ((is_array($lista_topicos)) && (count($lista_topicos)>0))
    {
      foreach($lista_topicos as $cod=>$linha)
      {
        $data_tmp=RetornaMaiorDataItemComentario($sock,$linha['cod_topico'],$tipo_comp,$linha['data'],$cod_usuario);
        if ($data_tmp>$data)
          $data=$data_tmp;
      }
    }
  
    $query="select max(Pi.data) 'PiData', max(Pic.data) 'PicData' from Portfolio_itens Pi, Portfolio_itens_comentarios Pic where Pi.cod_topico=".$cod_topico." and Pi.cod_item=Pic.cod_item";
    if ($tipo_comp=="T")
      $query.=" and Pi.tipo_compartilhamento='T'";
    if ($tipo_comp=="F")
      $query.=" and (Pi.tipo_compartilhamento='T' or Pi.tipo_compartilhamento='F')";
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    if ($linha['PiData']>$data)
      $data=$linha['PiData'];
    if ($linha['PicData']>$data)
      $data=$linha['PicData'];
  
    $query="select max(Pi.data) 'PiData' from Portfolio_itens Pi where Pi.cod_topico=".$cod_topico;
    if ($tipo_comp=="T")
      $query.=" and Pi.tipo_compartilhamento='T'";
    if ($tipo_comp=="F")
      $query.=" and (Pi.tipo_compartilhamento='T' or Pi.tipo_compartilhamento='F')";
    $res=Enviar($sock,$query);
  
    $linha=RetornaLinha($res);
    if ($linha['PiData']>$data)
      $data=$linha['PiData'];
  
  
    return $data;
  }
}

if(!function_exists("PertenceAoGrupo")) {
  /* *********************************************************************
    PertenceAoGrupo - Retorna trua se o usuario pertence ao grupo e o grupo est� ativo
    Entrada: $sock        - BASE DO CURSO
             $cod_usuario - codigo do usuario
             $cod_grupo   - codigo do grupo
    Saida: True se pertence, false caso contrario
  */
  function PertenceAoGrupo($sock, $cod_usuario, $cod_grupo)
  {
    $cod_grupo = VerificaNumeroQuery($cod_grupo);
    if ($cod_grupo === '')
      return false;

    $query  = "select GU.cod_usuario ";
    $query .= "from Grupos_usuario GU, Grupos G ";
    $query .= "where GU.cod_grupo   = ".$cod_grupo."   and ";
    $query .=       "G.cod_grupo    = ".$cod_grupo."   and ";
    $query .=       "GU.cod_usuario = ".$cod_usuario." and ";
    $query .=       "G.status<>'X'";

    $res = Enviar($sock,$query);

    if (RetornaNumLinhas($res)>0)
      return true;
    else
      return false;
  }
}

/* *********************************************************************
   RetornaFerramentasMarcadas - Retorna a lista com os c�digos das ferramentas
     marcadas.

   Entrada: $sock - BASE DO CURSO
   Saida: array contendo $cod_ferramenta
*/

function RetornaFerramentasMarcadas($sock)
{
  $ferramentas_marcadas = array();

  $query = "select max(data) 'data' from Ferramentas_destaque";

  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);

  $maxdata = $linha['data'];
  if ($maxdata != NULL)
  {
    $query = "select cod_ferramenta from Ferramentas_destaque where data = ".$maxdata;
    $res = Enviar($sock, $query);

    $lista = RetornaArrayLinhas($res);
    if ($lista>0)
      foreach($lista as $cod => $linha)
        $ferramentas_marcadas[$linha['cod_ferramenta']]=true;
  }

  return($ferramentas_marcadas);
}

if(!function_exists("RetornaGruposDoUsuario")){
    /* *******************************************************************
    RetornaGruposDoUsuario - Retorna os cod_grupo de todos os grupos n� apagados
                              a que o usuario pertence
    entrada: $sock - sock de conexao
              $cod_usuario - codigo do usuario do qual listar os grupos
  */
  function RetornaGruposDoUsuario($sock,$cod_usuario)
  {
  
    $query = "select Grupos_usuario.cod_usuario, Grupos.* from Grupos_usuario right join Grupos ON Grupos_usuario.cod_grupo=Grupos.cod_grupo where Grupos_usuario.cod_usuario=".$cod_usuario." and (Grupos.status='A' or Grupos.status='T')";
    $res=Enviar($sock,$query);
    $lista_retorno=RetornaArrayLinhas($res);
    return ($lista_retorno);
  }
}

/************************ Funcoes do Menu **************************/
function ExibeLink($cod_curso,$cod_ferr,$nome_ferramenta,$diretorio,$data,$ultimo_acesso,$style,$cod_ferramenta,$cod_usuario){

  /* As ferramentas do TelEduc foram agrupadas de acordo com
   * sua funcionalidade. As ferramentas que estao na divisa precisam
   * colocar um traco a mais para representar a divisao. Sao estas:
   * Ferramenta 8 - Mural
   * Ferramenta 11 - Correio
   * Ferramenta 15 - Portfolio
   * Ferramenta 17 - Estrutura do Ambiente
   * Ferramenta 19 - Intermap
   * Ferramenta 22 - Avaliacoes 
   */

  /* Verifica se a ferramenta atual eh a que esta selecionada */
  if ($cod_ferr == $cod_ferramenta)
    $style .= "Selecionada ";

  /* Verifica se a ferramenta atual esta na divisa */
  if ($cod_ferr==8 ||$cod_ferr==11 || $cod_ferr==15 || $cod_ferr==17 || $cod_ferr==19 || $cod_ferr==22)
    $style .= "Divisa ";

  /* Verifica se ha novidades na ferramenta atual */
  $novidades = ($data>$ultimo_acesso);
  if ($novidades)
    $style .= "Novidade ";

  echo("            <li class=\"".$style."\">\n");
  echo("              <div>\n");

  if ($cod_ferr==15)
    echo("                <a class=\"".$style."\" href=\"../".$diretorio."/ver_".$diretorio.".php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferr."&amp;exibir=myp\" >");
  else 
    echo("                <a class=\"".$style."\" href=\"../".$diretorio."/".$diretorio.".php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferr."\">");

  /* Coloca estrela se houver novidade */
  if ($novidades)
    echo("<img src='../imgs/estrelinha.gif' border=\"0\" alt=\"\" />");

  echo($nome_ferramenta."\n");
  echo("                </a>\n");
  echo("              </div>\n");
  echo("            </li>\n");

}

/********************************************************************
  Fun�ao RetornaNomeCodCursoUsuario - Retorna os codigos e os nomes dos cursos dos quais o usuario tem acesso
  Entradas: $sock - sock de conexao a base geral
  Saida:    array contendo os codigos e os nomes dos cursos que o usuario tem acesso
********************************************************************/
function RetornaNomeCodCursoUsuario($sock)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query  = "SELECT CUR.cod_curso,CUR.nome_curso ";
  $query .= "FROM ".$dbnamebase.".Usuario USU ";
  $query .= "INNER JOIN ".$dbnamebase.".Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario) ";
  $query .= "INNER JOIN ".$dbnamebase.".Cursos CUR on (CUR.cod_curso = USUC.cod_curso) ";
  $query .= "WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."'";

  $res = Enviar($sock, $query);

  return RetornaArrayLinhas($res);
}

function RetornaListaDeCursosUsuario($sock){

  echo("              <li><a href=\"../../../pagina_inicial/exibe_cursos.php\">Meus Cursos<!--[if IE 7]><!--></a><!--<![endif]-->\n");
  echo("                <!--[if lte IE 6]><table><tr><td><![endif]-->\n");
  echo("                <ul>\n");

  $cursos = RetornaNomeCodCursoUsuario($sock);
  foreach($cursos as $cod => $linha){
    echo("                  <li><a href=\"../index.php?cod_curso=".$linha['cod_curso']."\" title=\"".$linha['nome_curso']."\">".$linha['nome_curso']."</a></li>\n");

  }
  echo("                </ul>\n");
  echo("                <!--[if lte IE 6]></td></tr></table></a><![endif]-->\n");
  echo("              </li>\n");
}

/* **********************************************************************
   RetornaLinguaCurso - Retorna a l�ngua do curso que est� aberto.

   Entrada: $sock - Socket da conex�o
            $cod_curso - C�digo do curso a ter seus dados retornados
   Saida: vari�vel com o c�digo de l�ngua do curso
*/
function RetornaLinguaCurso($sock,$cod_curso)
{
  $query="select cod_lingua from Cursos where cod_curso='".$cod_curso."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha[0];
}
?>
