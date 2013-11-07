<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/historico_desempenho_todos.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/avaliacoes/historico_desempenho_todos.php
  ========================================================== */

/* Cï¿½digo principal */

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

  $linha=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

  $usr_formador=EFormador($sock,$cod_curso,$cod_usuario);
  $usr_aluno=EAluno($sock,$cod_curso,$cod_usuario);

  /* Funï¿½ï¿½es javascript */
  echo("<script language=\"javascript\">\n");

  /* *****************************************************************
  Funcao OpenWindow
  Abre nova janela com os historicos de desempenho, se acessados atraves de checkboxes
    Entrada: nenhuma
    Saida:   nenhuma
  ***************************************************************** */
  echo("function OpenWindow() \n");
  echo("{\n");
  echo("  window.open(\"\",\"HistoricoDesempenhoDisplay\",\"width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("}\n\n");

  /* *********************************************************
  Funcao OpenWindowLink
    Abre nova janela com o historico de desempenho, se acessado atraves do link
    Entrada: funcao = $cod_curso - Codigo do curso
    Saida:   false - para nao dar reload na pagina. Conferir a
                     chamada da funï¿½ï¿½o
  */
  echo("function OpenWindowLink(funcao) \n");
  echo("{\n");
  if ((!strcmp($linha['Ferramenta'],'P')) && (!strcmp($linha['Tipo'],'G')))
    echo("  window.open(\"exibir_historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_grupo[]=\"+funcao+\"&cod_usuario=".$cod_usuario."\",\"HistoricoDesempenhoDisplay\",\"width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  else
    echo("  window.open(\"exibir_historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_aluno[]=\"+funcao+\"&cod_usuario=".$cod_usuario."\",\"HistoricoDesempenhoDisplay\",\"width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("  return(false);\n");
  echo("}\n\n");

  echo("function AbrePerfil(cod_usuario)\n");
  echo("{\n");
  echo("  window.open('../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo("  return(false);\n");
  echo("}\n");

  echo("function AbreJanelaComponentes(cod_grupo)\n");
  echo("{\n");
  echo("  window.open('componentes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_grupo='+cod_grupo,'Componentes','width=400,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("  return false;\n");
  echo("}\n");

  /* ******************************************************************
  Funcao Check All
    Marca todas as checkboxes do respectivo grupo de usuarios
    Entrada: funcao - identifica se (1)aluno ou (2)formador
    Saida: nenhuma
  */
  echo("function CheckAll(funcao)\n");
  echo("{\n");
  echo("  var elem=document.HistDsph.elements;\n");
  echo("  var nome_var='cod_aluno[]';\n");
  echo("  var nome_var_all='cod_aluno_all';\n");
  echo("  var changed=false;\n");
  echo("\n");
 // echo("  if (funcao==2)\n");
//  echo("  {\n");
//  echo("    nome_var='cod_formador[]';\n");
 // echo("    nome_var_all='cod_formador_all';\n");
 // echo("  }\n");
  echo("  var i=0;\n");
  echo("  while (i < elem.length)\n");
  echo("  {\n");
  echo("    if (elem[i].name==nome_var_all)\n");
  echo("      changed=elem[i].checked;\n");
  echo("    else if (elem[i].name==nome_var)\n");
  echo("      elem[i].checked=changed;\n");
  echo("    i++;\n");
  echo("  }\n");
  echo("}\n");

   /* ******************************************************************
  Funcao CheckAllGrupos
    Marca todas as checkboxes do respectivo grupo de usuarios
    Entrada: funcao - identifica se (1)aluno ou (2)formador
    Saida: nenhuma
  */
  echo("function CheckAllGrupos(funcao)\n");
  echo("{\n");
  echo("  var elem=document.HistDsph.elements;\n");
  echo("  var nome_var='cod_grupo[]';\n");
  echo("  var nome_var_all='cod_grupo_all';\n");
  echo("  var changed=false;\n");
  echo("\n");
  echo("  var i=0;\n");
  echo("  while (i < elem.length)\n");
  echo("  {\n");
  echo("    if (elem[i].name==nome_var_all)\n");
  echo("      changed=elem[i].checked;\n");
  echo("    else if (elem[i].name==nome_var)\n");
  echo("      elem[i].checked=changed;\n");
  echo("    i++;\n");
  echo("  }\n");
  echo("}\n");

  /* ********************************************************************************************
  Funcao UnCheckHeader
    Desmarca as checkboxes que marcam todas, caso a checkbox de um usuario tenha sido desmarcada
    Entrada: funcao - identifica a checkbox a desmarcar
    Saida:   nenhuma
  */
  echo("function UnCheckHeader(funcao)\n");
  echo("{\n");
  echo("  var elem=document.HistDsph.elements;\n");
  echo("  var nome_var_all='cod_aluno_all';\n");
 // echo("  if (funcao==2)\n");
//  echo("    nome_var_all='cod_formador_all';\n");
  echo("  var i=0;\n");
  echo("  while (i < elem.length)\n");
  echo("  {\n");
  echo("    if (elem[i].name==nome_var_all)\n");
  echo("      elem[i].checked=false;\n");
  echo("    i++;\n");
  echo("  }\n");
  echo("}\n");

   /* ********************************************************************************************
  Funcao UnCheckHeaderGrupo
    Desmarca as checkboxes que marcam todas, caso a checkbox de um usuario tenha sido desmarcada
    Entrada: funcao - identifica a checkbox a desmarcar
    Saida:   nenhuma
  */
  echo("function UnCheckHeaderGrupo(funcao)\n");
  echo("{\n");
  echo("  var elem=document.HistDsph.elements;\n");
  echo("  var nome_var_all='cod_grupo_all';\n");
 // echo("  if (funcao==2)\n");
 // echo("    nome_var_all='cod_formador_all';\n");
  echo("  var i=0;\n");
  echo("  while (i < elem.length)\n");
  echo("  {\n");
  echo("    if (elem[i].name==nome_var_all)\n");
  echo("      elem[i].checked=false;\n");
  echo("    i++;\n");
  echo("  }\n");
  echo("}\n");

  echo("</script>\n");

   echo("  <html>\n");
  /* 1 - Avaliaï¿½ï¿½es */
  echo("  <head><title>TelEduc - Avaliaï¿½ï¿½es</title></head>\n");
  echo("    <link rel=\"stylesheet\" type=\"text/css\" href=\"../teleduc.css\">\n");
  echo("    <link rel=\"stylesheet\" type=\"text/css\" href=\"avaliacoes.css\">\n");
  echo("\n");

  echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
  /* 1 - Avaliaï¿½ï¿½es */
  $cabecalho ="<b class=\"titulo\">".RetornaFraseDaLista($lista_frases,1)."</b>";
    /* 105 - Histï¿½rico de Desempenho dos participantes */
  $cabecalho.="<b class=\"subtitulo\"> - ".RetornaFraseDaLista($lista_frases,105)."</b>";

   $cod_pagina=15;
  /* Cabecalho */
  echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));

  echo("<br>\n");
  echo("<p>\n");

  $titulo=RetornaTituloAvaliacao($sock,$linha['Ferramenta'],$linha['Cod_atividade']);
    if (!strcmp($linha['Ferramenta'],'F')) //Avaliacao no Forum
    {
      //$forum_linha = RetornaForum($sock,$linha['Cod_atividade']);
      /* 12 - Fï¿½rum */
      echo("    <font class=\"text\">".RetornaFraseDaLista($lista_frases,12).":</font>\n");
      echo("    <font class=\"text\"> ".$titulo."");
    }
    elseif (!strcmp($linha['Ferramenta'],'B')) //Avaliacao no Bate-Papo
    {
      //$assunto_sessao = RetornaAssunto($sock,$linha['Cod_atividade']);
      /* 13 - Assunto da Sessï¿½o */
      echo("    <font class=\"text\">".RetornaFraseDaLista($lista_frases,13).":</font>\n");
      echo("    <font class=\"text\"> ".$titulo."");
    }
    else //Avaliacao no portfolio
    {
      //$atividade_linha = RetornaAtividade($sock,$linha['Cod_atividade']);
      /* 14 - Atividade no Portfï¿½lio */
      echo("    <font class=\"text\">".RetornaFraseDaLista($lista_frases,14).":</font>\n");
      echo("    <font class=\"text\"> ".$titulo."<br>");
      /* 20 - Tipo da Atividade*/
      echo("    <font class=\"text\">".RetornaFraseDaLista($lista_frases,20).":</font>\n");
      if (!strcmp($linha['Tipo'],'I'))
      /* 21 - Individual*/
        echo("    <font class=\"text\"> ".RetornaFraseDaLista($lista_frases,21)."");
      else
      /* 22 - Em Grupo*/
        echo("    <font class=\"text\"> ".RetornaFraseDaLista($lista_frases,22)."");
    }

    echo("<br>\n");
    /* 58 - Valor da Atividade */
    echo(RetornaFraseDaLista($lista_frases,58).": ".$linha['Valor']."<br>\n");

    echo("<p>\n");
    if (!$SalvarEmArquivo)
    /* 46 - Ver objetivos/critï¿½rios da avaliaï¿½ï¿½o */
      echo("        <a class=\"text\" href=\"#\" onClick=\"window.open('ver.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&EhAtalho=1&cod_avaliacao=".$cod_avaliacao."','VerAvaliacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');return(false);\">".RetornaFraseDaLista($lista_frases,46)."</a><br><br>\n");


  echo("  <form name=\"HistDsph\" action=\"exibir_historico_desempenho.php\" method=\"post\" target=\"HistoricoDesempenhoDisplay\" onSubmit=\"OpenWindow();\"\n");
  echo(RetornaSessionIDInput());
  echo("<input type=\"hidden\" name=\"cod_curso\"     value=\"".$cod_curso."\">\n");
  echo("<input type=\"hidden\" name=\"cod_avaliacao\" value=\"".$cod_avaliacao."\">\n");

  echo("<p>\n");


  /*
  ================
  Tabela de alunos
  ================
  */

  if ((!strcmp($linha['Ferramenta'],'P')) && (!strcmp($linha['Tipo'],'G')))
  {
    $lista_grupos=RetornaListaGrupos($sock);

    $num_grupos=count($lista_grupos);
    if ($num_grupos > 0)
    {
      /* 3-Alunos */
      echo("<font class=\"text\"><B>Grupos</b></font>\n");

      echo("  <table border=0 width=100% cellspacing=0>\n");
      echo("    <tr class=\"colorfield\">\n");
      echo("      <td class=\"colorfield\" align=center width=5%><input type=\"checkbox\" name=\"cod_grupo_all\" value=\"1\" onclick=\"CheckAllGrupos(1);\" ></td>\n");
      /* 107 -Nome */
      echo("      <td class=\"colorfield\" align=left width=65%><B>".RetornaFraseDaLista($lista_frases,107)."</b></td>\n");
      /* 106 - Avaliado*/
      echo("      <td class=\"colorfield\" align=left width=65%><B>".RetornaFraseDaLista($lista_frases,106)."</b></td>\n");
      echo("    </tr>\n");

      foreach ($lista_grupos as $cod_grupo => $nome)
      {
        echo("  <tr>\n");
        echo("    <td align=center>\n");
        echo("    <input type=\"checkbox\" name=\"cod_grupo[]\" value=\"".$cod_grupo."\" onclick=\"UnCheckHeaderGrupo(1);\">\n");
        echo("    </td>\n");
        echo("    <td>");
        echo("<a href=\"#\" onClick=\"return(AbreJanelaComponentes(".$cod_grupo."));\" class=\"text\">".$nome."</a>");
        echo("</td>\n");
        $foiavaliado=GrupoFoiAvaliado($sock,$cod_avaliacao,$cod_grupo);
        if(!$foiavaliado)
        {
          echo("    <td>");
          if ($usr_formador)
            /* 36 - Não */
            echo("<a href=\"#\" onClick=\"return(OpenWindowLink(".$cod_grupo."));\" class=\"text\">".RetornaFraseDaLista($lista_frases_geral,36)."</a>");
          else
            echo("&nbsp;\n");
          echo("</td>\n");
        }
        else
        {
          if ($usr_formador)
          {
            echo("    <td>");
            /* 35 - Sim */
            echo("<a href=\"#\" onClick=\"return(OpenWindowLink(".$cod_grupo."));\" class=\"text\">".RetornaFraseDaLista($lista_frases_geral,35)."</a>");
            echo("</td>\n");
          }
          elseif ($usr_aluno)   //Aluno so pode ver nota compartilhada com ele
          {
            $cod=RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo);
            $avaliacao=RetornarCompartilhamentosAvaliacaoAluno($sock,$cod_avaliacao,$cod);
            $cont=0;
            foreach ($avaliacao as $cod => $linha)
            {
              if (!strcmp($linha['tipo_compartilhamento'],'T'))
                $podevernota=1;
              elseif (!strcmp($linha['tipo_compartilhamento'],'F'))
                $podevernota=0;
              elseif (!strcmp($linha['tipo_compartilhamento'],'G'))     //ï¿½ portfolio de grupo e nota compartilhada so com o grupo avaliado
              {
                $cod_grupo_usuario=RetornaCodGrupoPortfolio($sock,$cod_usuario);         //retorna o codigo do grupo do usuario que esta acessando
                if ($cod_grupo_usuario==$cod_grupo)    //O usuario pertence ao grupo que foi avaliado
                  $podevernota=1;
                else                   //outro grupo nao pode ver
                  $podevernota=0;
              }
              if ($podevernota==1)
                $cont++;
            }
            if ($cont > 0)
            {
              echo("    <td>");
              echo("<a href=\"#\" onClick=\"return(OpenWindowLink(".$cod_grupo."));\" class=\"text\">Sim</a>");
              echo("</td>\n");
            }
            else
            {
              echo("    <td>");
              echo("&nbsp;\n");
             // echo("<a href=\"#\" onClick=\"return(OpenWindowLink(".$cod_grupo."));\" class=\"text\">Nï¿½o</a>");
              echo("</td>\n");
            }
          }
        }
        echo("  </tr>\n");
      }
      echo("  </table>\n");

      /* 108 - Mostrar Selecionados */
      echo("  <input class=\"text\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases,108)."\"><br><br>\n");
    }
    else
    /* 77 - Nï¿½o hï¿½ grupos criados*/
      echo(RetornaFraseDaLista($lista_frases,77)."<br>");
  }
  else
  {
    $lista = ListaUsuario($sock,"A",$cod_curso);
    $total = count($lista);
    if ($total > 0)
    {
      /* 64 - Alunos */
      echo("<font class=\"text\"><b>".RetornaFraseDaLista($lista_frases,64)."</b></font>\n");

      echo("  <table border=0 width=100% cellspacing=0>\n");
      echo("    <tr class=\"colorfield\">\n");
      echo("      <td class=\"colorfield\" align=center width=5%><input type=\"checkbox\" name=\"cod_aluno_all\" value=\"1\" onclick=\"CheckAll(1);\" ></td>\n");
      /* 107 - Nome */
      echo("      <td class=\"colorfield\" align=left width=65%><b>".RetornaFraseDaLista($lista_frases,107)."</b></td>\n");
      /* 106 - Avaliado*/
      echo("      <td class=\"colorfield\" align=left width=65%><B>".RetornaFraseDaLista($lista_frases,106)."</b></td>\n");
      echo("    </tr>\n");

      foreach ($lista as $i => $dados)
      # for ($c=0;$c<count($lista);$c++)
      {
        echo("  <tr>\n");
        echo("    <td align=center>\n");
        echo("    <input type=\"checkbox\" name=\"cod_aluno[]\" value=\"".$dados['cod_usuario']."\" onclick=\"UnCheckHeader(1);\">\n");
        echo("    </td>\n");
        echo("    <td>");
        echo("<a href=\"#\" onClick=\"return(AbrePerfil(".$dados['cod_usuario']."));\" class=\"text\">".$dados['nome']."</a>");
        echo("</td>\n");
        $foiavaliado=FoiAvaliado($sock,$cod_avaliacao,$dados['cod_usuario']);
        if(!$foiavaliado)
        {
          echo("    <td>");
          if ($usr_formador)
            echo("<a href=\"#\" onClick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">Nï¿½o</a>");
          else
            echo("&nbsp;\n");
          echo("</td>\n");
        }
        else
        {
          if ($usr_formador)
          {
            echo("    <td>");
            echo("<a href=\"#\" onClick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">Sim</a>");
            echo("</td>\n");
          }
          elseif ($usr_aluno)
          {
            $avaliacao=RetornarCompartilhamentosAvaliacaoAluno($sock,$cod_avaliacao,$dados['cod_usuario']);
            $cont=0;
            foreach ($avaliacao as $cod => $linha)
            {
              if (!strcmp($linha['tipo_compartilhamento'],'T'))
                $podevernota=1;
              elseif (!strcmp($linha['tipo_compartilhamento'],'F'))
                $podevernota=0;
              elseif (!strcmp($linha['tipo_compartilhamento'],'A'))     //nota compartilhada so com o aluno avaliado
              {
                if ($cod_usuario==$dados['cod_usuario'])    //O usuario ï¿½ o aluno que foi avaliado
                  $podevernota=1;
                else                   //outro aluno nao pode ver
                  $podevernota=0;
              }
              if ($podevernota==1)
                $cont++;
            }
            if ($cont > 0)
            {
              echo("    <td>");
              echo("<a href=\"#\" onClick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">Sim</a>");
              echo("</td>\n");
            }
            else
            {
              echo("    <td>");
              echo("&nbsp;\n");
              //echo("<a href=\"#\" onClick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">Nï¿½o</a>");
              echo("</td>\n");
            }
          }
        }

      echo("  </tr>\n");
      }
      echo("  </table>\n");

      /* 108 - Mostrar Selecionados */
      echo("  <input class=\"text\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases,108)."\"><br><br>\n");
    }

    $dados_coord = ListaCoordenador($sock, $cod_curso);
    if (isset($dados_coord))
      $cod_coordenador = $dados_coord['cod_usuario'];

    $lista = ListaUsuario($sock,"F",$cod_curso);
    $total = count($lista);
    if (($total > 0) && ($usr_formador))
    {
      /* 156 - Formadores */
      echo("<font class=\"text\"><B>".RetornaFraseDaLista($lista_frases,156)."</b></font>\n");

      echo("  <table border=0 width=100% cellspacing=0>\n");
      echo("    <tr class=\"colorfield\">\n");
      echo("      <td class=\"colorfield\" align=center width=5%><input type=\"checkbox\" name=\"cod_aluno_all\" value=\"1\" onclick=\"CheckAll(1);\" ></td>\n");
      /* 107 - Nome */
      echo("      <td class=\"colorfield\" align=left width=65%><B>".RetornaFraseDaLista($lista_frases,107)."</b></td>\n");
      /* 106 - Avaliado*/
      echo("      <td class=\"colorfield\" align=left width=65%><B>".RetornaFraseDaLista($lista_frases,106)."</b></td>\n");
      echo("    </tr>\n");

      foreach ($lista as $i => $dados)
      # for ($c=0;$c<count($lista);$c++)
      {
        if (($dados['cod_usuario'] >= 0) && (($dados_coord['status'] == 'F') || ($dados['cod_usuario'] != $cod_coordenador) || ($dados['cod_usuario'] == $cod_coordenador)))
        {
          echo("  <tr>\n");
          echo("    <td align=center>\n");
          echo("    <input type=\"checkbox\" name=\"cod_aluno[]\" value=\"".$dados['cod_usuario']."\" onclick=\"UnCheckHeader(1);\">\n");
          echo("    </td>\n");
          echo("    <td>");
          echo("<a href=\"#\" onClick=\"return(AbrePerfil(".$dados['cod_usuario']."));\" class=\"text\">".$dados['nome']."</a>");
          echo("</td>\n");
          $foiavaliado=FoiAvaliado($sock,$cod_avaliacao,$dados['cod_usuario']);
          if(!$foiavaliado)
          {
            echo("    <td>");
            echo("<a href=\"#\" onClick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">Nï¿½o</a>");
            echo("</td>\n");
          }
          else
          {
          //  if ($usr_formador)
          //  {
              echo("    <td>");
              echo("<a href=\"#\" onClick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">Sim</a>");
              echo("</td>\n");
          //  }
          /*  elseif ($usr_aluno)
            {
              $avaliacao=RetornarCompartilhamentosAvaliacaoAluno($sock,$cod_avaliacao,$dados['cod_usuario']);
              $cont=0;
              foreach ($avaliacao as $cod => $linha)
              {
                if (!strcmp($linha['tipo_compartilhamento'],'T'))
                  $podevernota=1;
                elseif (!strcmp($linha['tipo_compartilhamento'],'F'))
                  $podevernota=0;
                elseif (!strcmp($linha['tipo_compartilhamento'],'A'))     //nota compartilhada so com o aluno avaliado
                {
                  if ($cod_usuario==$dados['cod_usuario'])    //O usuario ï¿½ o aluno que foi avaliado
                    $podevernota=1;
                  else                   //outro aluno nao pode ver
                    $podevernota=0;
                }
                if ($podevernota==1)
                  $cont++;
              }
              if ($cont > 0)
              {
                echo("    <td>");
                echo("<a href=\"#\" onClick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">Sim</a>");
                echo("</td>\n");
              }
              else
              {
                echo("    <td>");
                echo("&nbsp;\n");
                //echo("<a href=\"#\" onClick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">Nï¿½o</a>");
                echo("</td>\n");
              }
            }*/
          }
          echo("  </tr>\n");
        }
      }
      echo("  </table>\n");

      /* 108 - Mostrar Selecionados */
      echo("  <input class=\"text\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases,108)."\"><br><br>\n");
    }
  }

    if ($VeioDaAtividade)
    /* 13 - Fechar (ger) */
      echo("  &nbsp;<input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" onClick=\"self.close();\">\n");
    else
    /* 23 - Voltar (gen) */
    echo("<input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"location='".$origem.".php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."';\">");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
