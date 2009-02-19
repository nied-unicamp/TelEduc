<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/avaliar_atividade.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
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

    Nied - Núcleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/avaliacoes/avaliar_atividade.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,22);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  $tabela="Avaliacao_notas";

  if(!isset($cod_grupo))
     $cod_grupo=$cod_grupo_portfolio;

   $cod_item = $_GET['cod_item'];

  /* Verifica se a pessoa a editar é formador */
  if (!EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("<html>\n");
    /* 1 - Avaliações*/
    echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");

    echo("  <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");
    $tabela="Avaliacao";
    $dir="avaliacao";


    echo("<body link=#0000ff vlink=#0000ff bgcolor=white");
    /* 1 - Avaliações*/
    echo("<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n");
    /* 8 - Área restrita ao formador. */
    echo("<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,8)."</b><br>\n");
    /* 23 - Fechar (gen) */
echo("<form><input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,13)."' onclick=self.close();></form>\n");
echo("</body></html>\n");
Desconectar($sock);
exit;
}

$portfolio_grupo = ( ( ($dados['Ferramenta'] == 'P') || ($dados['Ferramenta']=='N') ) && ($dados['Tipo'] == 'G') );
$exercicio_grupo = ( ($dados['Ferramenta'] == 'E') && ($dados['Tipo'] == 'G') );

echo("<script language=JavaScript>\n");

/*Variavel para controlar quando deve ou não fazer alguma coisa no evento onUnload*/
echo("var unload=1;\n");

/*Função chamada quando a página é descarregada*/
echo("function Descarregar(codigo) \n");
echo("{ \n");
echo("   if (unload==1)\n");
echo("   {\n");
echo("      window.location('cancelar_avaliacao_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&ferramenta=".$dados['Ferramenta']."&cod_aluno=".$cod_aluno."&cod_grupo=".$cod_grupo."&portfolio_grupo=".$portfolio_grupo."&exercicio_grupo=".$exercicio_grupo."&cod_nota='+codigo ,'','width=1,height=1,top=60,left=60');\n");
echo("   }\n");
echo("}\n");

echo("function AbrePerfil(cod_usuario)\n");
echo("{\n");
echo("  window.open('../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
echo("  return(false);\n");
echo("}\n");

echo("function VerObj()\n");
echo("{\n");
$param = "'width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes'";
$nome_janela = "'AvaliacoesHistorico'";
echo("  window.open('ver_popup.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."', ".$nome_janela.", ".$param.");\n");
echo("  return false; \n");
echo("}\n");

echo("function VerItemPortfolio(cod_item) \n");
echo("{ \n");
echo("  window_handle = window.open('../portfolio/ver_item_avaliacao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_usuario_portfolio=".$cod_aluno."&cod_item='+ cod_item, 'JanelaPortfolio', 'width=600,height=400,top=150,left=150,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes'); \n");
echo("  window_handle.focus();  \n");
echo("  return false; \n");
echo("} \n");

echo("function VerExercicio(cod_modelo, cod_dono) \n");
echo("{ \n");
$param = "'width=600,height=400,top=150,left=150,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes'";
echo(" window.open('../exercicios/ver_aplicado_popup.php?&origem=avaliacao&cod_dono='+cod_dono+'&cod_modelo='+cod_modelo+'&cod_curso=".$cod_curso."' ,'ExercicioResolvido',".$param."); \n");
echo(" return(false);\n");
echo("} \n");

$dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
echo("function RetornaFalasAluno(funcao) \n");
echo("{ \n");
$param = "'width=600,height=400,top=150,left=150,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes'";
echo("  window.open('../batepapo/ver_falas_aluno.php?&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_assunto=".$dados['Cod_atividade']."&cod_aluno='+funcao,'FalasParticipante',".$param."); \n");
echo("  return(false); \n");
echo("} \n");

echo("  function RetornaMensagensAluno(funcao)\n");
echo("  {\n");
echo("    window.open('../forum/ver_mensagens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_forum=".$dados['Cod_atividade']."&cod_aluno='+funcao,'MensagensParticipante',".$param.");\n");
echo("    return(false);\n");
echo("  }\n");

// retorna true se a nota contiver digitos estranhos
// retorna false se a nota estiver no formato adequado
echo("function nota_com_digito_estranho(nota) {\n");
echo("  re_com_virgula = /^[0-9]+(\.|,)?[0-9]+\$/; \n"); // nota com decimal
echo("  re_somente_numeros = /^[0-9]+\$/; \n"); // somente numeros
echo("  if (nota == '' || re_com_virgula.test(nota) || re_somente_numeros.test(nota) ) { \n");
echo("    return false;\n");
echo("  } else {\n");
echo("    return true;\n");
echo("  }\n");
echo("}\n");

echo("function VerificaCampos() \n");
echo("{ \n");
echo("  unload=0;\n");
echo("  var nota = document.avaliado.nota.value; \n");
// echo("  var comp = document.avaliado.compartilhamento.value; \n");
echo("  if (nota == '') { \n");
// 40 - O campo nota não pode ser vazio
echo("    alert('".RetornaFraseDaLista($lista_frases,40)."'); \n");
echo("    return false; \n");
echo("  } \n");
echo("  if (nota_com_digito_estranho(nota)) { \n");
// 5 - Você digitou caracteres estranhos nesta nota.
// 6 - Use apenas dígitos de 0 a 9 e o ponto ( . ) ou a vírgula ( , ) para o campo valor (exemplo: 7.5). \n");
// 7 - Por favor retorne e corrija.
echo("      alert('".RetornaFraseDaLista($lista_frases,5)."\\n".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,7)."'); \n");
echo("      return(false); \n");
echo("  } \n");
// verificamos se a nota tem virgula, se tiver, convertemos para ponto
echo("  nota = nota.replace(/\,/, '.'); \n");
echo("  if (nota > ".$dados['Valor'].") { \n");
// 169 - O valor mínimo para a nota é       
echo("      alert('".RetornaFraseDaLista($lista_frases, 169)." ".$dados['Valor']."'); \n");
echo("      return(false); \n");
echo("  } \n");
// 24 - A nota não pode ser negativa
echo("  if (nota < 0) { \n");
echo("    alert('".RetornaFraseDaLista($lista_frases,24)."'); \n");
echo("    return false; \n");
echo("  }  \n");
echo("  if ( ! (document.avaliado.compartilhamento.value == 'T'    \n");
echo("          || document.avaliado.compartilhamento.value == 'F' \n");
echo("          || document.avaliado.compartilhamento.value == 'G' \n");
echo("          || document.avaliado.compartilhamento.value == 'A')\n");
echo("          ) { \n");
// 42 - Voce não selecionou o modo de compartilhamento !
echo("    alert('".RetornaFraseDaLista($lista_frases, 42)."'); \n");
echo("    return false; \n");
echo("  } \n");
echo("\n");
echo("  return true; \n");
echo("} \n");

echo("function MudaComp(novo_comp) {  \n");
echo("    document.avaliado.compartilhamento.value = novo_comp;  \n");
echo("} \n");
if(strcmp($dados['Ferramenta'],'E'))
{
echo("function AbreGrupo(cod_grupo)\n");
echo("{\n");
echo("  window.open('componentes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_grupo='+cod_grupo,'Componentes','width=400,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
echo("  return false;\n");
echo("}\n");
}
elseif(!strcmp($dados['Ferramenta'],'E'))
{
echo("function AbreGrupo(cod_grupo)\n");
echo("{\n");
echo("  window.open('componentes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_grupo='+cod_grupo,'Componentes','width=400,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
echo("  return false;\n");
echo("}\n");

echo("function Corrigir(cod_modelo)\n");
echo("{\n");
echo("  window.open('../exercicios/correcao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_dono=".$cod_usuario."&cod_modelo='+cod_modelo,'Corrigir','width=600,height=400,top=170,left=170,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
echo("  return false;\n");
echo("}\n");
}

echo("</script>\n");

echo("<html>\n");
/* 1 - Avaliações */
echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
echo("  <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");

$portfolio_grupo = ( ($dados['Ferramenta'] == 'P' || $dados['Ferramenta']=='N') && ($dados['Tipo'] == 'G') );
$exercicio_grupo = ( ($dados['Ferramenta'] == 'E') && ($dados['Tipo'] == 'G') );

$alteracao = "";

if ((!$portfolio_grupo)&&(!$exercicio_grupo))
{

   $verificacao=AlunoFoiAvaliado($sock,$cod_avaliacao,$cod_aluno);
   if ($verificacao['Existe'])
   {
      if (($verificacao['Cont']==1) && ($verificacao['Edicao'])) // é a primeira avaliação realizada, mas havia uma em edicao antes que foi cancelada
      {
         if (($portfolio_grupo)||($exercicio_grupo))
         {
            $lista_integrantes=RetornaListaIntegrantes($sock,cod_grupo_portfolio);
            foreach ($lista_integrantes as $cod_aluno => $linha)
            {
               $alteracao = ("  <input type=hidden name=EhAlteracaoDeNota value=0>\n");
               $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_grupo_portfolio);
            }
         }
         else
         {
            $alteracao = ("  <input type=hidden name=EhAlteracaoDeNota value=0>\n");
            $cod_null='NULL';
            $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_null);
         }
      }
      else           //         O aluno ja foi avaliado
      {
         $alteracao = ("  <input type=hidden name=EhAlteracaoDeNota value=".$verificacao['Existe'].">\n");
         if (($portfolio_grupo)||($exercicio_grupo))
         {
            $lista_integrantes=RetornaListaIntegrantesMomentoAvaliacao($sock,$cod_grupo_portfolio,$cod_avaliacao);
            foreach ($lista_integrantes as $cod_aluno => $linha)
            {
               $cod_nota=IniciaAvaliacaoAluno($sock,$cod_aluno, $cod_avaliacao, $cod_usuario, $cod_grupo_portfolio);
            }
         // mudança
            $avaliacao_atual=RetornarAvaliacaoGrupo($sock,$cod_avaliacao,$cod_grupo_portfolio);
         }
         else
         {
            $cod_null='NULL';
            $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_null);
            $avaliacao_atual=RetornarAvaliacaoAluno($sock,$cod_avaliacao,$cod_aluno);
         }
      }
   }
   else //$verificacao[existe]=false      A avaliação não existe, o formador pode avaliar o aluno
   {
      $alteracao = ("  <input type=hidden name=EhAlteracaoDeNota value=".$verificacao['Existe'].">\n");
      if (($portfolio_grupo)&&($exercicio_grupo))
      {
         $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo_portfolio);
         foreach ($lista_integrantes as $cod_aluno => $linha)
         {
            $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_grupo_portfolio);
         }
      }
      else
      {
         $cod_null='NULL';
         $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_null);
      }
   }
}
else if (($portfolio_grupo)||($exercicio_grupo))
{
   if (GrupoFoiAvaliado($sock,$cod_avaliacao,$cod_grupo))
   {
      $lista_integrantes=RetornaListaIntegrantesMomentoAvaliacao($sock,$cod_grupo,$cod_avaliacao);
      if(is_array($lista_integrantes))
      foreach ($lista_integrantes as $cod_aluno => $linha)
      {
         $alteracao = ("  <input type=hidden name=EhAlteracaoDeNota value=1>\n");
         $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_grupo);
      }
   }
   else
   {
      $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo);
      if(is_array($lista_integrantes))
      foreach ($lista_integrantes as $cod_aluno => $linha)
      {     
         $alteracao = ("  <input type=hidden name=EhAlteracaoDeNota value=0>\n");
         $cod_null='NULL';
         $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_grupo);
      }
   }
}

// echo("<body link=#0000ff vlink=#0000ff bgcolor=white onLoad='Iniciar();'>\n");

echo("<body link=#0000ff vlink=#0000ff bgcolor=white onUnload=Descarregar($cod_nota);>");/*.$onUnload);/*'window.location=\"cancelar_avaliacao_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&ferramenta=".$dados['Ferramenta']."&cod_aluno=".$cod_aluno."&cod_grupo=".$cod_grupo."&portfolio_grupo=".$portfolio_grupo."&exercicio_grupo=".$exercicio_grupo."&cod_nota=".$cod_nota."\";'");

/* 1 - Avaliações */
$cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
/* 34 - Avaliar participantes */
$cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,34)."</b>";
$cod_pagina=8;
/* Cabecalho */
echo(PreparaCabecalho($cod_curso, $cabecalho, COD_AVALIACAO, $cod_pagina));
echo("<br>");

if ($dados['Ferramenta'] == 'F') // Avaliacao no Forum
{
  // 145 - Fórum de Discussão
   $tipo = RetornaFraseDaLista($lista_frases, 145);
   $titulo = RetornaForum($sock,$dados['Cod_atividade']);
}
elseif ($dados['Ferramenta'] == 'B') //Avaliacao no Bate-Papo
{
   // 146 - Sessão de Bate-Papo
   $tipo = RetornaFraseDaLista($lista_frases,146);
   $titulo = RetornaAssunto($sock,$dados['Cod_atividade']);
}
elseif ($dados['Ferramenta'] == 'E')
{
   if ($dados['Tipo'] == 'I')
      // 176 - Exercicio inidividual
      $tipo = RetornaFraseDaLista($lista_frases, 176);
   else
// 174 - Exercicio em grupo
      $tipo = RetornaFraseDaLista($lista_frases, 174);
$titulo = RetornaExercicio($sock,$dados['Cod_atividade']);
}
elseif ($dados['Ferramenta'] == 'P')
{
   if ($dados['Tipo'] == 'I')
      // 161 - Atividade inidividual no portfolio
      $tipo = RetornaFraseDaLista($lista_frases, 161);
   else
      // 162 - Atividade em grupo no portfolio
      $tipo = RetornaFraseDaLista($lista_frases, 162);
   $titulo = RetornaAtividade($sock,$dados['Cod_atividade']);
}
elseif($dados['Ferramenta']=='N')
{
   $dados_nota_externa=RetornaDadosDoItemExterna($sock,$dados['Cod_atividade']);
   if($dados_nota_externa['status']=='I')
   {
      $tipo=RetornaFraseDaLista($lista_frases,185);
   }
   else
   {
      $tipo=RetornaFraseDaLista($lista_frases,186);
   }
   $titulo=$dados_nota_externa['titulo'];   
}

echo("<p>\n");
echo("<table cellpadding='0' cellspacing='0' border='0' style='width: 100%; text-align: left;'>\n");
echo("  <tbody>\n");
echo("    <tr class='colorfield'>\n");
// 123 - Título
echo("      <td style='vertical-align: top;'>&nbsp; ".RetornaFraseDaLista($lista_frases, 123)."</td>\n");
/*
// ?? - [Ferramenta]
echo("      <td style='vertical-align: top;'>&nbsp; "."[Ferramenta]"."</td>\n"); */
// 113 - Tipo da Avaliação
echo("      <td style='vertical-align: top;'>&nbsp; ".RetornaFraseDaLista($lista_frases, 113)."</td>\n");
// 19 - Valor
echo("      <td style='vertical-align: top;'>&nbsp; ".RetornaFraseDaLista($lista_frases, 19)."</td>\n");
echo("    </tr>\n");
echo("    <tr class='text'>\n");
echo("      <td style='vertical-align: top;'>&nbsp; ".$titulo."</td>\n");
// echo("      <td style='vertical-align: top;'>&nbsp; ".$nome_ferramenta."</td>\n");
echo("      <td style='vertical-align: top;'>&nbsp; ".$tipo."</td>\n");
echo("      <td style='vertical-align: top;'>&nbsp; ".FormataNota($dados['Valor'])."</td>\n");
echo("    </tr>\n");
echo("  </tbody>\n");
echo("</table>\n");
echo("</p>\n");

echo("<p>\n");
echo("<table border=0 width=100%>\n");
echo("  <tbody>\n");
echo("    <tr class=menu3>\n");
// 46 - Ver objetivos/critérios da avaliação
echo("      <td align=center><a href=# class=menu3 onclick='VerObj();return false;'>".RetornaFraseDaLista($lista_frases, 46)."</a></td>\n");
echo("    </tr>\n");
echo("  </tbody>\n");
echo("</table>\n");
echo("</p>\n");

if ((!$portfolio_grupo)&&(!$exercicio_grupo))
{
   $funcao = "AbrePerfil(".$cod_aluno.");";
   $nome = NomeUsuario($sock, $cod_aluno);
   $cod_aluno_grupo = $cod_aluno;

   $frase_compartilhamento = array (
   // 51 - Totalmente Compartilhado
   'T' => RetornaFraseDaLista($lista_frases, 51),
   // 52 - Compartilhado com Formadores
   'F' => RetornaFraseDaLista($lista_frases, 52),
   // 170 - Não compartilhado
   'P' => RetornaFraseDaLista($lista_frases, 170)
   );
}

else
{
   $funcao = "AbreGrupo(".$cod_grupo.");";
   $nome = NomeGrupo($sock, $cod_grupo);
   $cod_aluno_grupo = $cod_grupo;
   $frase_compartilhamento = array (
   // 51 - Totalmente Compartilhado
   'T' => RetornaFraseDaLista($lista_frases, 51),
   // 52 - Compartilhado com Formadores
   'F' => RetornaFraseDaLista($lista_frases,52),
   // 171 - Compartilhado com o grupo
   'P' => RetornaFraseDaLista($lista_frases, 171)
);
}

echo("  <table cellpadding=0 cellspacing=0 border=0 width=100%>\n");
echo("    <tbody>\n");
if ((!$portfolio_grupo)&&(!$exercicio_grupo))
{
   // 47 - Participante
   echo("      <tr>\n");
   echo("        <td class=colorfield rowspan=1 colspan=5>&nbsp; ".RetornaFraseDaLista($lista_frases, 47)."</td>\n");
   echo("      </tr>\n");
}
else
{
   // 48 - Grupo
   echo("      <tr>\n");
   echo("        <td class=colorfield rowspan=1 colspan=5>&nbsp; ".RetornaFraseDaLista($lista_frases, 48)."</td>\n");
   echo("      </tr>\n");
}
// nome do participante
echo("      <tr>\n");
echo("        <td rowspan=1 colspan=5>&nbsp;&nbsp;&nbsp;<a href=# class=text onClick='".$funcao." return false;'>".$nome."</a></td>\n");
echo("      </tr>\n");

// linha em branco parea separar nome do participante dos itens
echo("<tr><td colspan=5>"."&nbsp;"."</td></tr>\n");

if ($dados['Ferramenta'] == 'P' )
{
   $lista_itens = RetornaListaItensAvaliacaoPortfolio($sock, $cod_avaliacao, $cod_aluno_grupo);  
   if (! is_array($lista_itens))
   {
      // 49 - Participações
      echo "      <tr><td class=colorfield colspan=5>"."&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases, 49)."</td></tr>\n";
      // 164 - Não há participações
      echo "      <tr><td colspan=5>"."&nbsp;&nbsp;"."<font class=text>".RetornaFraseDaLista($lista_frases, 164)."</font></td></tr>\n";
   }
   else
   {
      echo("      <tr class=colorfield>\n");
      echo("        <td class=colorfield></td>\n");
      // 172 - Itens
      echo("        <td class=colorfield>&nbsp; ".RetornaFraseDaLista($lista_frases, 172)."</td>\n");
      // 101 - Data
      echo("        <td class=colorfield>&nbsp; ".RetornaFraseDaLista($lista_frases, 101)."</td>\n");
      // 63 - Compartilhamento
      echo("        <td class=colorfield align=center>&nbsp; ".RetornaFraseDaLista($lista_frases, 63)."</td>\n");
      // 106 - Avaliado
      echo("        <td class=colorfield align=center>&nbsp; ".RetornaFraseDaLista($lista_frases, 106)."</td>\n");
      echo("      </tr>\n");

      foreach ($lista_itens as $cod_item_p => $linha_item)
      {
         $icone = "<img src=../portfolio/figuras/arquivo.gif>";

         if ($linha_item['status'] == 'A')
         {
         // G 35 - Sim
            $av = RetornaFraseDaLista($lista_frases_geral, 35);
         }
         else
         {
            $cod_item=$linha_item['cod_item'];
            $av = RetornaFraseDaLista($lista_frases_geral, 36);
         }

         echo("      <tr>\n");
         // icone do item de avaliacao
         echo("        <td>".$icone."</td>\n");
         // titulo do item de avaliacao
         $a1 = "<a href=# onClick='VerItemPortfolio(".$linha_item['cod_item'].");return false;'>";
         $a2 = "</a>";
         echo("        <td><font  class=text>&nbsp; ".$a1.$linha_item['titulo'].$a2."</font></td>\n");
         // data
         echo("        <td><font class=text>&nbsp; ".UnixTime2Data($linha_item['data'])."</font></td>\n");
         // Compartilhamento
         echo("        <td align=center><font class=text>&nbsp; ".$frase_compartilhamento[ $linha_item['tipo_compartilhamento'] ]."</font></td>\n");
         // Avaliado ? Sim ou Nao
         echo("        <td align=center><font class=text>&nbsp; ".$av."</font></td>\n");
         echo("      </tr>\n");
         echo("      <tr>\n");
         echo("        <td rowspan=1 colspan=5><hr></td>\n");
         echo("      </tr>\n");
      }/*foreach*/
   }/*else*/
}/*if*/


if ($dados['Ferramenta'] == 'E')
{
   $lista_itens = RetornaDadosExercicioAvaliado($sock, $cod_avaliacao, $cod_aluno_grupo,$exercicio_grupo);
   if(!is_array($lista_itens))
   {
      // 49 - Participações
      echo "      <tr><td class=colorfield colspan=5>"."&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases, 49)."</td></tr>\n";      // 164 - Não há participações
      echo "      <tr><td colspan=5>"."&nbsp;&nbsp;"."<font class=text>".RetornaFraseDaLista($lista_frases, 164)."</font></td></tr>\n";
   }
   else
   {
      $cod_modelo=$lista_itens['cod_modelo'];
      echo("      <tr class=colorfield>\n");
      echo("        <td class=colorfield></td>\n");
      // 173 - Exercicio
      echo("        <td class=colorfield>&nbsp; ".RetornaFraseDaLista($lista_frases, 173)."</td>\n");
      // 101 - Data
      echo("        <td class=colorfield>&nbsp; ".RetornaFraseDaLista($lista_frases, 101)."</td>\n");
      // 63 - Compartilhamento
      echo("        <td class=colorfield align=center>&nbsp; ".RetornaFraseDaLista($lista_frases, 63)."</td>\n");
      // 106 - Avaliado
      echo("        <td class=colorfield align=center>&nbsp; ".RetornaFraseDaLista($lista_frases, 106)."</td>\n");
      echo("      </tr>\n");
      $icone = "<img src=../portfolio/figuras/arquivo.gif>";

      if (($lista_itens['status'] == 'G') || ($lista_itens['status'] == 'N'))
      {
         // G 35 - Sim
         $av = RetornaFraseDaLista($lista_frases_geral, 35);
      }
      else
      {
         $av = RetornaFraseDaLista($lista_frases_geral, 36);
      }
      echo("      <tr>\n");
      // icone do item de avaliacao
      echo("        <td>".$icone."</td>\n");
      // titulo do item de avaliacao
      $a1 = "<a href=# onClick='VerExercicio(".$lista_itens['cod_modelo'].", ".$cod_aluno_grupo.");return false;'>";
      $a2 = "</a>";
      echo("        <td><font  class=text>&nbsp; ".$a1.$lista_itens['titulo'].$a2."</font></td>\n");
      // data
      echo("        <td><font class=text>&nbsp; ".UnixTime2Data($lista_itens['dt_submissao'])."</font></td>\n");
      // Compartilhamento
      echo("        <td align=center><font class=text>&nbsp; ".$frase_compartilhamento[ $lista_itens['compartilhada'] ]."</font></td>\n");
      // Avaliado ? Sim ou nao
      echo("        <td align=center><font class=text>&nbsp; ".$av."</font></td>\n");
      echo("      </tr>\n");
      echo("      <tr>\n");
      echo("        <td rowspan=1 colspan=5><hr></td>\n");
      echo("      </tr>\n");
   }
}
else if ($dados['Ferramenta'] == 'F')
{
   // 49 - Participações
   echo "      <tr><td class=colorfield colspan=5>"."&nbsp;".RetornaFraseDaLista($lista_frases, 49)."</td></tr>\n";
   if (ParticipouDoForum($sock,$cod_aluno_grupo,$dados['Cod_atividade']))
   {
      $a1 = "<a class=text href=# onClick='RetornaMensagensAluno(".$cod_aluno_grupo."); return false;'>";
      $a2 = "</a>";
      // 160 - Ver falas do participante
      echo "      <tr><td colspan=5> &nbsp;&nbsp;".$a1.RetornaFraseDaLista($lista_frases, 160).$a2."</td></tr>\n";
   }
   else
   {
   // 164 - Não há participações
   echo "      <tr><td colspan=5>"."&nbsp;&nbsp;"."<font class=text>".RetornaFraseDaLista($lista_frases, 164)."</font></td></tr>\n";
   }
}
else if ($dados['Ferramenta'] == 'B')
{
   // 49 - Participações
   echo "      <tr><td class=colorfield colspan=5>"."&nbsp;".RetornaFraseDaLista($lista_frases, 49)."</td></tr>\n";
   if (ParticipouDaSessao($sock,$cod_aluno_grupo,$dados['Cod_atividade']))
   {
      $a1 = "<a class=text href=# onClick='RetornaFalasAluno($cod_aluno_grupo); return false;'>";
      $a2 = "</a>";
      // 160 - Ver falas do participante
      echo "      <tr><td colspan=5><img src=../figuras/batepapo.gif> &nbsp;&nbsp;".$a1.RetornaFraseDaLista($lista_frases, 160).$a2."</td></tr>\n";
   }
   else
   {
      // 164 - Não há participações
      echo "      <tr><td colspan=5>"."&nbsp;&nbsp;"."<font class=text>".RetornaFraseDaLista($lista_frases, 164)."</font></td></tr>\n";
   }
}

// linha em branco parea separar itens dos campos de notas
// echo("<tr><td colspan=5>"."&nbsp;"."</td></tr>\n");
echo("<tr><td colspan=5>"."&nbsp;"."</td></tr>\n");

echo("      <form name=avaliado action=avaliar_atividade2.php onSubmit=return(VerificaCampos());> \n");
echo(RetornaSessionIDInput());
echo("      <tr class=colorfield>\n");
// 60 - Nota
echo("        <td rowspan=1 colspan=3>&nbsp; ".RetornaFraseDaLista($lista_frases, 60)."</td>\n");
// 19 - Valor
echo("        <td rowspan=1 colspan=2>&nbsp; ".RetornaFraseDaLista($lista_frases, 19)."</td>\n");
echo("      </tr>\n");
echo("      <tr>\n");
if ($dados['Ferramenta'] == 'E')
{
  $array_exercicio = RetornaDadosNotaExercicio($sock, $cod_avaliacao, $cod_aluno_grupo,$exercicio_grupo);
  echo("        <td rowspan=1 colspan=3>");
  if($array_exercicio['existe'] == false)
  {
    /* 179 - IndisponÃ­vel*/  /* 180 - Corrigir dissertativas*/
    echo(RetornaFraseDaLista($lista_frases, 179));
    echo("<input type=hidden name=nota value=0>");
    if($array_exercicio['resolvido'] == true)
      echo("  <a href=# onClick=return(Corrigir(".$cod_modelo.")); class=text>".RetornaFraseDaLista($lista_frases, 180)."</a>");
  }
  else
  {
    echo($array_exercicio['nota']);
    echo("<input type=hidden name=nota value='".$array_exercicio['nota']."'>");
  }
  echo("</td>\n");
}
else
{
  echo("        <td rowspan=1 colspan=3><input type=text name=nota class=text size=6></td>\n");
}
echo("        <td rowspan=1 colspan=2 class=text align=left>"."&nbsp; &nbsp; ".FormataNota($dados['Valor'])."</td>\n");
echo("      </tr>\n");

echo("      <tr class=colorfield>\n");
// 163 - Justificativa
echo("        <td rowspan=1 colspan=5>&nbsp; ".RetornaFraseDaLista($lista_frases, 163)."</td>\n");
echo("      </tr>\n");
echo("      <tr>\n");
if (($dados['Ferramenta'] == 'E')&&($array_exercicio['existe'] == false))
{
  echo("        <td rowspan=1 colspan=5>");
    /* 179 - IndisponÃ­vel*/
    echo(RetornaFraseDaLista($lista_frases, 179));
    echo("<input type=hidden name=comentario value=''>");
  echo("</td>\n");
}
else
{
     echo("        <td rowspan=1 colspan=5>"."<textarea name=comentario class=text rows=5 cols=60 wrap=soft></textarea>"."</td>\n");
  }
  echo("      </tr>\n");
  // 50 - Compartilhar
  echo("      <tr class=colorfield>\n");
  echo("        <td colspan=5 rowspan=1>&nbsp; ".RetornaFraseDaLista($lista_frases, 50)."</td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td rowspan=1 colspan=5 class=g1field>");
  // 51 - Totalmente Compartilhado
  echo("        <input type=hidden name=compartilhamento value=0> \n");
  if (($dados['Ferramenta'] != 'E')||($array_exercicio['existe'] == true))/*Se existe avaliacao para este exercicio*/
  {
    echo("        <input type=radio name=comp onClick=MudaComp('T');><font class=text>".RetornaFraseDaLista($lista_frases, 51)."</font><br>\n");
    echo("        <input type=radio name=comp onClick=MudaComp('F');><font class=text>".RetornaFraseDaLista($lista_frases, 52)."</font><br>\n");
    if(($portfolio_grupo)||($exercicio_grupo))
    echo("        <input type=radio name=comp onClick=MudaComp('G');><font class=text>".RetornaFraseDaLista($lista_frases, 53)."</font><br>\n");
  else
    echo("        <input type=radio name=comp onClick=MudaComp('A');><font class=text>".RetornaFraseDaLista($lista_frases, 54)."</font><br>\n");
  }
  else
  {
    echo("       <font class=text>".RetornaFraseDaLista($lista_frases, 51)."</font><br>\n");
    echo("       <font class=text>".RetornaFraseDaLista($lista_frases, 52)."</font><br>\n");
    if($exercicio_grupo)
       echo("        <font class=text>".RetornaFraseDaLista($lista_frases, 53)."</font><br>\n");
    else
       echo("        <font class=text>".RetornaFraseDaLista($lista_frases, 54)."</font><br>\n");
  }
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </tbody>\n");
  echo("  </table>\n");
  echo("  <br>\n");

  echo("    <div align=right width=100%>\n");
  // 11 - Enviar
  if((is_null($array_exercicio['existe'])) || ($array_exercicio['existe'] != false))
    echo("      <input class=text type=submit value=".RetornaFraseDaLista($lista_frases_geral, 11).">\n");

  echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");


  echo("  <input type=hidden name=cod_item value=".$cod_item.">\n");  // Diogo
  if ((!$portfolio_grupo)&&(!$exercicio_grupo))
    echo("  <input type=hidden name=cod_aluno value=".$cod_aluno.">\n");
  else
    echo("  <input type=hidden name=cod_grupo value=".$cod_grupo.">\n");
  echo("  <input type=hidden name=ferramenta value=".$dados['Ferramenta'].">\n");
  echo($alteracao);        
  echo("  <input type=hidden name=cod_nota value=".$cod_nota."> \n");
  echo("</form>\n");
  // 2 - Cancelar (ger)
  //echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,2)."' onclick=self.close();>"); /*'location=\"cancelar_avaliacao_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&ferramenta=".$dados['Ferramenta']."&cod_aluno=".$cod_aluno."&cod_grupo=".$cod_grupo."&portfolio_grupo=".$portfolio_grupo."&exercicio_grupo=".$exercicio_grupo."&cod_nota=".$cod_nota."\";'>");*/
    echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,2)."' onclick='location=\"cancelar_avaliacao_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&ferramenta=".$dados['Ferramenta']."&cod_aluno=".$cod_aluno."&cod_grupo=".$cod_grupo."&portfolio_grupo=".$portfolio_grupo."&exercicio_grupo=".$exercicio_grupo."&cod_nota=".$cod_nota."\";'>");
  echo("    </div>\n");

  Desconectar($sock);
  echo("</body>\n");
  echo("</html>\n");

?>

