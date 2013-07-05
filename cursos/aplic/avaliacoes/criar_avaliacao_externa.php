<?php
/*
<!--
-------------------------------------------------------------------------------


    Arquivo : cursos/aplic/avaliacoes/criar_avaliacao_externa.php

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
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/avaliacoes/criar_avaliacao_externa.php
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
  $cod_atividade = RetornaProximoCodigoExterna($sock,'N');
    echo("<html>\n");
    /* 1 - Avalia��es*/
    echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
    echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"../teleduc.css\">\n");
    $tabela="Avaliacao";

    echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"avaliacoes.css\">\n");

     GeraJSVerificacaoData();
  /****************** Fun��es JavaScript **************** */

  echo("  <script language=\"javascript\">\n");

  echo("  function Atualiza(cod_avaliacao) {\n");
 // echo("    document.atualizar.titulo.value=top.opener.document.material.titulo.value;\n");
 // echo("    document.atualizar.texto.value=top.opener.document.material.texto.value;\n");
 // echo("    document.atualizar.compartilhamento.value=top.opener.document.material.tipo_comp.value;\n");
  echo("   window.opener.location='avaliacoes.php?&cod_curso=".$cod_curso."&cod_avaliacao='+cod_avaliacao+'&cancelar_edicao_avaliacao=sim'\n");
  echo("  }\n");

  echo("function submit(){\n");
  echo("if (verifica_formulario())");
  echo("document.avaliacao.submit;");
  echo("else return (false);}");

  echo("function verifica_formulario()\n");
  echo("{\n");
  echo("    var data_ini=document.avaliacao.data_inicio;\n");
  echo("    var data_fim=document.avaliacao.data_termino;\n");

  echo("      if (data_inicial_maior(data_ini,data_fim)) \n");
  echo("      {\n");
  /* 2 - A data inicial da avalia��o deve ser menor ou igual a data final. Volte e corrija */
  echo("    alert(' ".RetornaFraseDaLista($lista_frases,2)."');\n");
  echo("    return (false);\n");
  echo("      }\n");
  echo("var out=',';\n");
  echo("var add='.';\n");
  echo("var temp='' + document.avaliacao.valor.value;\n");
  echo("  while (temp.indexOf(out)>-1)\n");
  echo("  {\n");
  echo("    pos= temp.indexOf(out);\n");
  echo("    temp = '' + (temp.substring(0, pos) + add + temp.substring((pos + out.length), temp.length));\n");
  echo("  }\n");
  echo("  document.avaliacao.valor.value=temp;\n");
  echo("  if (document.avaliacao.valor.value=='')\n");
  echo("  {\n");
  /* 3 - Voc� n�o informou o valor da avalia��o. */
  echo("    alert('".RetornaFraseDaLista($lista_frases,3)."');\n");
  echo("    return (false);\n");
  echo("  }\n");

  echo("  if (document.avaliacao.titulo.value=='')\n");
  echo("  {\n");
  /* 3 - Voc� n�o informou o t�tulo da avalia��o. */
  echo("    alert('".RetornaFraseDaLista($lista_frases, 188)."');\n");
  echo("    return (false);\n");
  echo("  }\n");


  echo("  if (document.avaliacao.valor.value < 0)\n");
  echo("  {\n");
  /* 4 - A avalia��o n�o pode ter valor negativo. */
  echo("    alert('".RetornaFraseDaLista($lista_frases,4)."');\n");
  echo("    return (false);\n");
  echo("  }\n");

  echo("var check=true;\n");
  echo("    var valor = document.avaliacao.valor.value;\n");
  echo("  if (check)\n");
  echo("      if (valor_com_digito_estranho(valor)) \n");
  echo("      {\n");
  echo("      check=false;\n");
  /* 5 - Voc� digitou caracteres estranhos neste valor da atividade. */
  /* 6 - Use apenas d�gitos de 0 a 9 e o ponto ( . ) ou a v�rgula ( , ) para o campo valor (exemplo: 7.5).*/
  /* 7 - Por favor retorne e corrija.*/
  echo("        alert('".RetornaFraseDaLista($lista_frases,5)."\\n".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,7)."');\n");
  echo("        return(false);\n");
  echo("      }\n");

  /* 8 - Verifica se as datas estao em um formato valido */
  echo "   if (! DataValidaAux(document.avaliacao.data_inicio)) {\n";
  echo "       return false;\n";
  echo "   } \n";
  echo "   if (! DataValidaAux(document.avaliacao.data_termino)) {\n";
  echo "       return false;\n";
  echo "   } \n";


  echo("  return (true);\n");
  echo("}\n");

  echo("  function verifica_valor(campo) \n");
  echo("  {\n");
  echo("    var valor = campo.value;\n");
  echo("  if (check)\n");
  echo("      if (valor_com_digito_estranho(valor)) \n");
  echo("      {\n");
  echo("      check=false;\n");
 /* 5 - Voc� digitou caracteres estranhos neste valor da atividade. */
  /* 6 - Use apenas d�gitos de 0 a 9 e o ponto ( . ) ou a v�rgula ( , ) para o campo valor (exemplo: 7.5).*/
  /* 7 - Por favor retorne e corrija.*/
  echo("        alert('".RetornaFraseDaLista($lista_frases,5)."\\n".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,7)."');\n");
  echo("        return(false);\n");
  echo("      }\n");
  echo("  }\n");

  echo("  function valor_com_digito_estranho(valor) \n");
  echo("  {\n");
  echo("    var erro=false;\n");
  echo("    var ponto=false;\n");
  echo("    var i=0;\n");
  echo("    var c='';\n");
  echo("   c=valor.charAt(i);\n");
  echo("   if(c<'0' || c>'9')\n");
  echo("   erro=true; \n");
  echo("    while (i<valor.length && !erro) \n");
  echo("    {\n");
  echo("      c=valor.charAt(i);\n");
  echo("      if ((c<'0' || c>'9') && c!='.' && c!=',') \n");
  echo("        erro=true; \n");
  echo("      if((c=='.' || c==',')&&(ponto==false))\n");
  echo("       ponto=true;\n");
  echo("     else if((c=='.' || c==',')&&(ponto==true))\n");
  echo("       erro=true;\n");
  echo("      i++; \n");
  echo("    }\n");
  echo("    return(erro);\n");
  echo("  }\n");

  echo("  function data_inicial_maior(valor1,valor2) \n");
  echo("  {\n");
  echo("    var data_ini=valor1.value;\n");
  echo("    var data_fim=valor2.value;\n");
  echo("    var erro=false;\n");
  echo("    dia_ini = data_ini.substring(0,2);\n");
  echo("    dia_fim = data_fim.substring(0,2);\n");
  echo("    mes_ini = data_ini.substring(3,5);\n");
  echo("    mes_fim = data_fim.substring(3,5);\n");
  echo("    ano_ini = data_ini.substring(6,10);\n");
  echo("    ano_fim = data_fim.substring(6,10);\n");
  echo("    if (ano_fim < ano_ini) \n");
  echo("        erro=true; \n");
  echo("    if ((mes_fim < mes_ini) && (ano_fim==ano_ini)) \n");
  echo("        erro=true; \n");
  echo("    if ((mes_fim==mes_ini) && (ano_fim==ano_ini) && (dia_fim < dia_ini)) \n");
  echo("        erro=true; \n");
  echo("    return(erro);\n");
  echo("  }\n");

  echo("  </script>\n");

  /* Verifica se a pessoa a editar � formador */
  if (!EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    /* 1 - Avalia��es
     */
    echo("<b class=\"titulo\">".RetornaFraseDaLista($lista_frases,1)."</b>\n");
    /* 2 - �rea restrita ao formador. */
    echo("<b class=\"subtitulo\"> - ".RetornaFraseDaLista($lista_frases,8)."</b><br>\n");
    /* 23 - Voltar (gen) */
    echo("<form><input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\"></form>\n");
    echo("</body></html>\n");
    Desconectar($sock);
    exit;
  }
  else
  {
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white onload=self.focus();>\n");
    /* 1 - Avalia��es */
    $cabecalho ="<b class=\"titulo\">".RetornaFraseDaLista($lista_frases,1)."</b>";
    /* 9 - Cadastro de Avalia��o */
    $cabecalho.="<b class=\"subtitulo\"> - ".RetornaFraseDaLista($lista_frases,9)." </b>";

    $cod_pagina=13;
    /* Cabecalho */
    echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));

    echo("<br>\n");
    echo("<p>\n");

     $dados=RetornaAvaliacao($sock,$cod_atividade,'N'); 
    if ($dados['Cont']>0)
    {

      $linha_hist=RetornaUltimaPosicaoHistoricoAvaliacao($sock, 'Avaliacao_historicos', $dados['Cod_avaliacao']);
      if ($dados['Status']=='C')
      {
        /* algu�m j� est� editando */
        /* Ve se n�o � voc� */
        if ($cod_usuario!=$dados['Cod_usuario'])
        {
          if ($linha_hist['data']>time()-1800)
          {
            /* 78 - A Avalia��o j� est� sendo criada desde */
            echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,78)." ");

            /* 57 - por */
            echo(Unixtime2DataHora($dados['Data']));

            echo(" ".RetornaFraseDaLista($lista_frases_geral,57)." ".NomeUsuario($sock,$dados['Cod_usuario']).".<br><br>");

            /* 23 - Fechar (gen) */
        echo("<form><input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" onclick=\"self.close();\"></form>\n");
            echo("</body></html>\n");
            Desconectar($sock);
            exit;
          }
          /* Passou o tempo limite, captura a edi��o */
        }
        /* �. Atualiza data e segue em frente. */
        CancelaEdicaoAvaliacao($sock, $tabela, $dados['Cod_avaliacao'], $cod_usuario);
        $cod_avaliacao=IniciaCriacaoAvaliacao($sock, $tabela,$cod_atividade, $cod_usuario, 'N');
      }
      elseif (($dados['Status']=='F') || ($dados['Status']=='E') || ($dados['Status']=='D'))
      {
        /* 70 - J� existe uma avalia��o criada para esta atividade.*/
        echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,70)."</font><br><br>");

        /* 23 - Fechar (gen) */
        echo("<form><input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" onclick=\"self.close();\"></form>\n");
        echo("</body></html>\n");
        Desconectar($sock);
        exit;
      }
      elseif (($dados['Status']=='A') || ($dados['Status']=='T'))
      {
        /* 71 - J� existe uma avalia��o criada para esta atividade. Por�m, ela foi apagada.*/
        /* 72 - Se desejar criar outra avalia��o, voc� precisa primeiro excluir definitivamente a avalia��o existente.*/
        echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,71)."");
        echo(" ".RetornaFraseDaLista($lista_frases,72)."</font><br><br>");

        /* 23 - Fechar (gen) */
        echo("<form><input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" onclick=\"self.close();\"></form>\n");
        echo("</body></html>\n");
        Desconectar($sock);
        exit;
      }
    }
    else
    {
       /*Trocando P por N */

      $cod_avaliacao=IniciaCriacaoAvaliacao($sock, $tabela,$cod_atividade, $cod_usuario, 'N');
    }

    echo("<form name=\"avaliacao\" action=\"criar_avaliacao_externa2.php?".RetornaSessionID()."\" method=\"post\" onSubmit=\"return(verifica_formulario());\">\n");
    /* 15 - Forne�a abaixo os dados de avalia��o desta atividade. */
    echo("  <font class=\"text\">".RetornaFraseDaLista($lista_frases,15)."<br>\n");
    echo("    <br>\n");

    echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
    /*Gera campo com titulo do exercicio*/   
    echo("<tr class=\"colorfield\"> <td class=\"colorfield\"> Titulo </td> <td class=\"colorfield\"> </td> </tr> \n");

    echo("<tr><td class=\"text\"> <input type=\"text\" name=\"titulo\"> </td></tr> \n");
    echo("</table>\n");
    echo("<br>");

    echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");    
    echo("  <tr class=\"colorfield\">\n");
    /* 16 - Data de in�cio*/
    echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,16)."</td>\n");

    /* 17 - Data de T�rmino */
    echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,17)."</td>\n");

    echo("  </tr>\n");
    echo("  <tr class=\"wtfields\">\n");
    echo("    <td class=\"text\">".GeraCampoData("data_inicio",UnixTime2Data(time()))."</td>\n");

    echo("    <td class=\"text\">".GeraCampoData("data_termino",UnixTime2Data(time()))."</td>\n");

    echo("  </tr>\n");

    echo("  <tr class=\"wtfields\">\n");
    /* 18 - dd/mm/aaaa */
    echo("    <td class=\"textsmall\">(".RetornaFraseDaLista($lista_frases,18).")</td>\n");

    /* 18 - dd/mm/aaaa */
    echo("    <td class=\"textsmall\">(".RetornaFraseDaLista($lista_frases,18).")</td>\n");

    echo("  </tr>\n");
    echo("</table>\n");

    echo("    <br>\n");

    echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
    echo("  <tr class=\"colorfield\">\n");
    /* 19 - Valor*/
    echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,19)."</td>\n");

    /* 20 - Tipo da Atividade */
    echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,20)."</td>\n");

    echo("  </tr>\n");
    echo("      <tr>\n");
    echo("        <td>\n");
    echo("          <input type=\"text\" name=\"valor\" class=\"text\" size=6 maxlength=10 value=\"".stripslashes($valor)."\" onChange=\"check=true;\" onBlur=\"verifica_valor(document.avaliacao.valor);\">\n");
    echo("        </td>\n");
    echo("        <td>\n");
    echo(" <select name=\"tipo\">\n");
    /* 21 - Individual */
    echo("<option value=\"I\">".RetornaFraseDaLista($lista_frases,21)."</option>\n");
    /* 22 - Em Grupo */
    echo("<option value=\"G\">".RetornaFraseDaLista($lista_frases,22)."</option>\n");
    echo("</select>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      </table>\n");
    echo("    <br>\n");

        echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
      echo("  <tr class=\"colorfield\">\n");
    /* 75 - Objetivos */
    echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,75)."</td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <textarea name=\"objetivos\" rows=4 cols=60 wrap=soft>".stripslashes($objetivos)."</textarea>\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("    <br>\n");

        echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
    echo("  <tr class=\"colorfield\">\n");
    /* 23 - Crit�rios */
    echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,23)."</td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <textarea name=\"criterios\" rows=4 cols=60 wrap=soft>".stripslashes($criterios)."</textarea>\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");

    echo("    <div align=right width=100%>\n");
    /* 11 - Enviar */
    echo("      <input class=\"text\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral, 11)."\">\n");

    /* 2 - Cancelar (ger) */
    echo("  <input class=\"text\" type=\"button\"             value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" onclick=\"Atualiza($cod_avaliacao);self.close();\">\n");
    echo("  <input type=\"hidden\" name=\"cod_curso\"         value=\"".$cod_curso."\">\n");
    echo("  <input type=\"hidden\" name=\"cod_atividade\"     value=\"".$cod_atividade."\">\n");
    echo("  <input type=\"hidden\" name=\"cod_avaliacao\"     value=\"".$cod_avaliacao."\">\n");
    echo("  <input type=\"hidden\" name=\"criacao_avaliacao\" value=\"".$criacao_avaliacao."\">\n");
    echo("    </div>\n");
    echo("</form>\n");

    echo("<form name=\"atualizar\" action=\"avaliacoes.php".RetornaSessionID()."\" method=\"post\" target=\"trabalho\">\n");
    echo("  <input type=\"hidden\" name=\"cod_ferramenta\"            value=\"3\">");/*Atividades que perde a variavel de sess�o quando aberta a ajuda*/
    echo("  <input type=\"hidden\" name=\"titulo\"                    value=\"\">\n");
    echo("  <input type=\"hidden\" name=\"texto\"                     value=\"\">\n");
    echo("  <input type=\"hidden\" name=\"compartilhamento\"          value=\"\">\n");
    echo("  <input type=\"hidden\" name=\"cod_curso\"                 value=\"".$cod_curso."\">\n");
    echo("  <input type=\"hidden\" name=\"cancelar_edicao_avaliacao\" value=\"sim\">\n");
    echo("  <input type=\"hidden\" name=\"cod_avaliacao\"             value=\"".$cod_avaliacao."\">\n");
    echo("  <input type=\"hidden\" name=\"criacao_avaliacao\"         value=\"".$criacao_avaliacao."\">\n");
    echo("</form>\n");
  }

  echo("</body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>

