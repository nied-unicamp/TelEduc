<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/alterar_avaliacao.php

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

    Nied - Ncleo de Informática Aplicada à Educação
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
  ARQUIVO : cursos/aplic/avaliacoes/alterar_avaliacao.php
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
  $tabela='Avaliacao';

  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

  echo("<html>\n");
  /* 1 - Avaliações*/
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");

  echo("    <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");
  echo("\n");


  /****************** Funções JavaScript **************** */
   GeraJSVerificacaoData();

  echo("  <script language=javascript>\n");

  echo("  function Atualiza() {\n");
  echo("if (verifica_formulario())\n");
  echo("    document.atualizar.submit();\n");
  echo("  }\n");
  
  echo("function verifica_formulario()\n");
  echo("{\n");
  echo("    var data_ini=document.avaliacao.data_inicio;\n");
  echo("    var data_fim=document.avaliacao.data_termino;\n");
  
  echo("      if (data_inicial_maior(data_ini,data_fim)) \n");
  echo("      {\n");
  /* 2 - A data inicial da avaliação deve ser menor ou igual a data final. Volte e corrija. */
  echo("    alert('".RetornaFraseDaLista($lista_frases,2)."');\n");
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
  /* 3 - Você não informou o valor da avaliação. */
  echo("    alert('".RetornaFraseDaLista($lista_frases,3)."');\n");
  echo("    return (false);\n");
  echo("  }\n");
  echo("  if (document.avaliacao.valor.value < 0)\n");
  echo("  {\n");
  /* 4 - A avaliação não pode ter valor negativo. */
  echo("    alert('".RetornaFraseDaLista($lista_frases,4)."');\n");
  echo("    return (false);\n");
  echo("  }\n");

  echo("var check=true;\n");
  echo("    var valor = document.avaliacao.valor.value;\n");
  echo("  if (check)\n");
  echo("      if (valor_com_digito_estranho(valor)) \n");
  echo("      {\n");
  echo("      check=false;\n");
  /* 5 - Você digitou caracteres estranhos neste valor da atividade. */
  /* 6 - Use apenas dígitos de 0 a 9 e o ponto ( . ) ou a vírgula ( , ) para o campo valor (exemplo: 7.5).*/
  /* 7 - Por favor retorne e corrija.*/
  echo("        alert('".RetornaFraseDaLista($lista_frases,5)."\\n".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,7)."');\n");
  echo("        return(false);\n");
  echo("      }\n");

  /* 8 - Verifica se o formato da data esta certo */
  echo "           if (! DataValidaAux(document.avaliacao.data_inicio)) {\n";
  echo "               return false;\n";
  echo "           } \n";
  echo "           if (! DataValidaAux(document.avaliacao.data_termino)) {\n";
  echo "               return false;\n";
  echo "           } \n";
   
  echo("  return (true);\n");
  echo("}\n");

  echo("  function verifica_valor(campo) \n");
  echo("  {\n");
  echo("    var valor = campo.value;\n");
  echo("  if (check)\n");
  echo("      if (valor_com_digito_estranho(valor)) \n");
  echo("      {\n");
  echo("      check=false;\n");
  /* 5 - Você digitou caracteres estranhos neste valor da atividade. */
  /* 6 - Use apenas díitos de 0 a 9 e o ponto ( . ) ou a vírgula ( , ) para o campo valor (exemplo: 7.5).*/
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

  /* Verifica se a pessoa a editar é formador */
  if (!EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    /* 1 - Avaliações*/
    $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
    /* 8 - Área restrita ao formador. */
    $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,8)."</b>";
    echo($cabecalho);
    /* 23 - Voltar (gen) */
    echo("<form><input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1);></form>\n");
    echo("</body></html>\n");
    Desconectar($sock);
    exit;
  }
  else
  {
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white onload=self.focus();>\n");
    /* 1 - Avaliações*/
    $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
    /* 9 - Cadastro de Avaliação */
    $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,9)."</b>";
/*
    if (!strcmp($dados['Ferramenta'],'B')) //Avaliacao no Forum
      $cod_pagina=4;
    elseif (!strcmp($dados['Ferramenta'],'F')) //Avaliacao no Bate-Papo
      $cod_pagina=5;
    elseif (!strcmp($dados['Ferramenta'],'P')) //Avaliacao no portfolio
      $cod_pagina=6;
    else
      // 11 - Erro Interno...
      print (RetornaFraseDaLista($lista_frases,11));
      */

    /* Cabecalho */
    echo(PreparaCabecalho($cod_curso,$cabecalho,22,11));

    echo("<br>\n");
    echo("<p>\n");

    $linha_hist = RetornaUltimaPosicaoHistoricoAvaliacao($sock, 'Avaliacao_historicos', $cod_avaliacao);

    if (count($dados)>0)
    {
      if ($dados['Status']=='E')
      {
        /* alguém já está editando */
        /* Ve se não é você*/
        if ($cod_usuario!=$dados['Cod_usuario'])
        {
          if ($linha_hist['data']>time()-1800)
          {
            echo ("<br><p>");
            /* 10 - A Avaliação já está sendo editada desde */
            echo("<font class=text>".RetornaFraseDaLista($lista_frases,9));

            /* 57 - Por (gen)*/
            echo(Unixtime2DataHora($dados['Data']));

            echo(" ".RetornaFraseDaLista($lista_frases_geral,23)." ".NomeUsuario($sock,$dados['Cod_usuario']).".<br><br>");

            /* 23 - Voltar (gen) */
            echo("<form><input type=button class=text value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1);></form>\n");
            echo("</body></html>\n");
            Desconectar($sock);
            exit;
          }
          /* Passou o tempo limite, captura a edição */
        }
        /* Atualiza data e segue em frente. */
        CancelaEdicaoAvaliacao($sock, $tabela, $cod_avaliacao, $cod_usuario);
        IniciaAlteracaoCadastroAvaliacao($sock,$cod_avaliacao,$cod_usuario);
      }
      else
      {
        /* Pega e segue em frente */
        IniciaAlteracaoCadastroAvaliacao($sock,$cod_avaliacao,$cod_usuario);
      }
    }
    else
    {
      /* Não deveria acontecer... */
      /* 11 - Erro Interno...*/
      exit(RetornaFraseDaLista($lista_frases,11));
    }

    $titulo=RetornaTituloAvaliacao($sock,$dados['Ferramenta'],$dados['Cod_atividade']);

    if (!strcmp($dados['Ferramenta'],'F')) //Avaliacao no Forum
    {
      /* 12 - Frum */
      echo("    <font class=text>".RetornaFraseDaLista($lista_frases,12).":</font>\n");
      echo("    <font class=text> <i>".$titulo."</i>");
    }
    elseif (!strcmp($dados['Ferramenta'],'B')) //Avaliacao no Bate-Papo
    {
      /* 13 - Assunto da Sessão */
      echo("    <font class=text>".RetornaFraseDaLista($lista_frases,13).":</font>\n");
      echo("    <font class=text> <i>".$titulo."</i>");
    }
    elseif (!strcmp($dados['Ferramenta'],'E')) //Avaliacao em Exercicio
    {
      /* 175 - Atividade em Exercício */
      echo("    <font class=text>".RetornaFraseDaLista($lista_frases,175).":</font>\n");
      echo("    <font class=text> <i>".$titulo."</i>");
    }
    elseif (!strcmp($dados['Ferramenta'],'N')) //Avaliacao em Exercicio
    {
      /* 187 - Avaliacao Externa */
      echo("    <font class=text>".RetornaFraseDaLista($lista_frases,187).":</font>\n");
      echo("    <font class=text> <i>".$titulo."</i>");
    }                                 
    else //Avaliacao no portfolio
    {
      /* 14 - Atividade no Portflio */
      echo("    <font class=text>".RetornaFraseDaLista($lista_frases,14).":</font>\n");
      echo("    <font class=text> <i>".$titulo."</i>");
    }

    $valor=$dados['Valor'];
    $objetivos=$dados['Objetivos'];
    $criterios=$dados['Criterios'];

    echo("<form name=avaliacao action=alterar_avaliacao2.php method=post onSubmit=\"return(verifica_formulario())\";>\n");
    echo(RetornaSessionIDInput());
    /* 15 - Forneça abaixo os dados de avaliação desta atividade. */
    echo("  <font class=text>".RetornaFraseDaLista($lista_frases,15)."<br>\n");
    echo("    <br>\n");

    if (strcmp($dados['Ferramenta'],'B'))
    {
      echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
      echo("  <tr class=colorfield>\n");
      /* 16 - Data de início*/
      echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases,16)."</td>\n");

      /* 17 - Data de Término */
      echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases,17)."</td>\n");

      echo("  </tr>\n");
      echo("  <tr class=wtfields>\n");
      echo("    <td class=text>\n");
      echo("      <input type=text maxlength=10 size=10 name=data_inicio value='".Unixtime2Data($dados['Data_inicio'])."' onChange=\"check=true;\" onBlur='DataValida(document.avaliacao.data_inicio,1);'>\n");
      echo("    </td>\n");

      echo("    <td class=text>\n");
      echo("      <input type=text maxlength=10 size=10 name=data_termino value='".Unixtime2Data($dados['Data_termino'])."' onChange=\"check=true;\" onBlur='DataValida(document.avaliacao.data_termino,1);'>\n");
      echo("    </td>\n");

      echo("  </tr>\n");

      echo("  <tr class=wtfields>\n");
      /* 18 - dd/mm/aaaa */
      echo("    <td class=textsmall>(".RetornaFraseDaLista($lista_frases,18).")</td>\n");

      /* 18 - dd/mm/aaaa */
      echo("    <td class=textsmall>(".RetornaFraseDaLista($lista_frases,18).")</td>\n");

      echo("  </tr>\n");
      echo("</table>\n");

      echo("    <br>\n");
    }

    echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
    echo("  <tr class=colorfield>\n");
    /* 19 - Valor*/
    echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases,19)."</td>\n");
    if ((!strcmp($dados['Ferramenta'],'P'))||(!strcmp($dados['Ferramenta'],'E')))
    /* 20 - Tipo da Atividade */
      echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases,20)."</td>\n");

    echo("  </tr>\n");
    echo("      <tr>\n");
    if(strcmp($dados['Ferramenta'],'E'))
    {
      echo("        <td>\n");
      echo("          <input type=text name=valor class=text size=6 maxlength=10 value='".stripslashes($valor)."' onChange=\"check=true;\" onBlur='verifica_valor(document.avaliacao.valor);'>\n");
      echo("        </td>\n");
    }
    else
    {
      echo("        <td>\n");
      echo("<font class='text'>".$valor."</font>");
      echo("<input type=hidden name=valor value='".stripslashes($valor)."'>");
      echo("        </td>\n");

    }
    if (!strcmp($dados['Ferramenta'],'P'))  /*Somente o portflio pode ter o tipo alterado*/
    {
      echo("        <td>\n");
      echo(" <select name=tipo>\n");
      /* 21 - Individual */
      echo("<option ");
      if (!strcmp($dados['Tipo'],'I'))
        echo ("selected");
      echo(" value=I>".RetornaFraseDaLista($lista_frases,21)."</option>\n");
      /* 22 - Em Grupo */
      echo("<option ");
      if (!strcmp($dados['Tipo'],'G'))
        echo ("selected");
      echo(" value=G>".RetornaFraseDaLista($lista_frases,22)."</option>\n");
      echo("</select>\n");
      echo("        </td>\n");
    }
    if(!strcmp($dados['Ferramenta'],'E'))  /*Somente exibe*/
    {
      echo("        <td>\n");
      /* 21 - Individual */
      if (!strcmp($dados['Tipo'],'I'))
      {
         echo("<font class='text'>".RetornaFraseDaLista($lista_frases,21)."</font>");
         echo("<input type=hidden name=tipo value='I'>");
      }
      /* 22 - Em Grupo */
      if (!strcmp($dados['Tipo'],'G'))
      {
         echo("<font class='text'>".RetornaFraseDaLista($lista_frases,22)."</font>");
         echo("<input type=hidden name=tipo value='G'>");
      }
      echo("        </td>\n");
    }
    echo("      </tr>\n");
    echo("      </table>\n");
    echo("    <br>\n");

    echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
    echo("  <tr class=colorfield>\n");
    /* 9 - Objetivos */
    echo("    <td class=colorfield>Objetivos</td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <textarea name=objetivos rows=4 cols=60 wrap=soft>".stripslashes($objetivos)."</textarea>\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("    <br>\n");

    echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
    echo("  <tr class=colorfield>\n");
    /* 23 - Critérios */
    echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases,23)."</td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <textarea name=criterios rows=4 cols=60 wrap=soft>".stripslashes($criterios)."</textarea>\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");

    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("<input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
    echo("<input type=hidden name=ferramenta value=".$dados['Ferramenta'].">\n");
    echo("<input type=hidden name=origem value=".$origem.">\n");
    echo("<input type=hidden name=VeioDaAtividade value=".$VeioDaAtividade.">\n");

    if (!isset($origem))
      // a origem eh a pagina ver.php
      echo("  <input type=hidden name=origem value=ver>\n");

    echo("<div align=right width=100%>\n");
    // 11 - Enviar
    echo("<input class=text type=submit value=".RetornaFraseDaLista($lista_frases_geral, 11).">\n");

    if ($VeioDaAtividade)
    {
      if (!strcmp($dados['Ferramenta'],'P'))
      {
        // 2 - Cancelar (ger)
        echo("<input type=button class=text value='".RetornaFraseDaLista($lista_frases_geral,2)."' onclick=Atualiza();self.close();>\n");
        echo("<input type=hidden name=cod_atividade value=".$cod_atividade.">\n");
        echo("<input type=hidden name=cod_topico value=".$cod_topico.">\n");
        echo("<input type=hidden name=criacao_avaliacao value=".$criacao_avaliacao.">\n");
        echo("</div>\n");
        echo("</form>\n");

        if (!isset($origem))
        {
          $arquivo = "ver";
          $origem  = "ver";
        }
        else if (!strcmp($origem,'../material/material'))
        {
          $arquivo='index_avaliacao';  //para o caso de cancelar a avaliação, ai deve ir para esse outro arquivo
        }
        else
          $arquivo=$origem;
        echo("<form name=atualizar action=".$arquivo.".php method=post>\n");
        echo(RetornaSessionIDInput());
        echo("  <input type=hidden name=cod_ferramenta value=3>\n");
        echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
        echo("  <input type=hidden name=cancelar_edicao_avaliacao value=sim>\n");
        echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
        echo("  <input type=hidden name=cod_topico value=".$cod_topico.">\n");

        if (!strcmp($origem,'../material/material'))
          echo("  <input type=hidden name=origem value=".$origem.">\n");
        echo("</form>\n");
      }
      else
      {
        // 2 - Cancelar (ger)
        echo("<input type=button class=text value='".RetornaFraseDaLista($lista_frases_geral,2)."' onclick=\"location='index_avaliacao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&origem=alterar_avaliacao&cancelar_edicao_avaliacao=sim';\">");
        echo("    </div>\n");
        echo("</form>\n");
      }
      echo("<nobr>\n");
    }
    else if (! isset($origem))
    {
      // veio de ver
      echo("</form>\n");
      echo("&nbsp;&nbsp;");
      echo("<form action=ver.php> \n");
      echo(RetornaSessionIDInput());
      echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
      echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
      echo("  <input type=hidden name=tela_avaliacao value=".$tela_avaliacao.">\n");
      // 2 - Cancelar (ger)
      echo("  <input type=submit value='".RetornaFraseDaLista($lista_frases_geral, 2)."'>");
      echo("</form> \n");
      echo("</div> \n");
    }
    else if ($origem == "avaliacoes")
    {
      // veio de ver
      echo("</form>\n");
      echo("&nbsp;&nbsp;");
     // echo("<form action=avaliacoes.php> \n");
     // echo(RetornaSessionIDInput());
     // echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
    //  echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
   //   echo("  <input type=hidden name=tela_avaliacao value=".$tela_avaliacao.">\n");
    // 2 - Cancelar (ger)
      echo("<input type=button class=text value='".RetornaFraseDaLista($lista_frases_geral,2)."' onclick=\"top.trabalho.location='index_avaliacao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&origem=".$origem."&cancelar_edicao_avaliacao=sim';\">");

      // 2 - Cancelar (ger)
//      echo("  <input type=submit value='".RetornaFraseDaLista($lista_frases_geral, 2)."'>");
 //     echo("</form> \n");
      echo("</div> \n");
    }
    else
    {
      // 2 - Cancelar (ger)
      echo("<input type=button class=text value='".RetornaFraseDaLista($lista_frases_geral,2)."' onclick=\"top.trabalho.location='index_avaliacao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&origem=".$origem."&cancelar_edicao_avaliacao=sim';\">");
      echo("    </div>\n");
      echo("</form>\n");
    }
  }

  echo("</body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
