<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/grupos/novo_grupo.php

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
  ARQUIVO : cursos/aplic/grupos/novo_grupo.php
  ========================================================== */


/* C�igo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("grupos.inc");

  $cod_ferramenta=12;
  include("../topo_tela.php");

  /* para evitar chamar a mesma fun�o varias vezes */
  $bool_grupos_fechados=GruposFechados($sock);
  if ($bool_grupos_fechados)
  {
    /* 48 - Grupos j�formados */
    $imagem="../figuras/grupofechado.gif";
  }
  else
  {
    /* 47 - Grupos em forma�o */
    $imagem="../figuras/grupoaberto.gif";
  }

  if (EConvidado($sock, $cod_usuario, $cod_curso))
  {
  echo("  </head>\n");
  echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" bgcolor=\"white\" onload=\"startList(); self.focus();\">\n");
  echo("    <a name=\"topo\"></a>\n");

  /* 1 - Grupos */
  $cabecalho ="<h4>".RetornaFraseDaLista($lista_frases,1);
    /* 61 - �ea restrita a alunos e formadores */
  $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,61)."</h4>";
  echo("    <br /><br />".$cabecalho."\n");
  echo("    <br />\n");
  echo("    <ul class=\"btAuxTabs\">\n");
  echo("      <li>\n");
  /* G 13 - Fechar */
  echo("        <span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span>\n");
  echo("      </li>\n");
  echo("    </ul>\n");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
  exit();    
  }
  
  // fun�o e_usuario_sem_grupo : Verifica se o usuario, cujo nome foi recebido
  // por parametro, �um usuario sem grupo (se for sem grupo, retorna 1. Caso
  // contr�io, retorna 0).
  function e_usuario_sem_grupo($sock, $cod_usuario)
  {
    $lista_frases=RetornaListaDeFrases($sock,12);
    $lista_frases_geral=RetornaListaDeFrases($sock,-1);

    $query="Select * from Grupos_usuario GU, Grupos G where GU.cod_usuario=".$cod_usuario." and G.cod_grupo=GU.cod_grupo and G.status!='X'";
    $res = Enviar($sock, $query);
    $linhas = RetornaArrayLinhas($res);

    if ($linhas=="") return true;
    
    return false;
  }


  echo("  </head>\n");

  /* Aqui eu assumo que o grupo existe, e que cod_grupo �v�ido */
  $nome_grupo = RetornaNomeGrupo($sock,$cod_grupo);

  if(EVisitante($sock,$cod_curso,$cod_usuario))
  {
    echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" bgcolor=\"white\" onload=\"startList(); self.focus();\">\n");
    echo("    <a name=\"topo\"></a>\n");
    echo("    <br /><br /><h4>".RetornaFraseDaLista($lista_frases,54)."</h4><br />\n");

    echo("    <ul class=\"btAuxTabs\">\n");
    /* 54 - Op�o n� dispon�el para visitantes. */
    echo("      <li>\n");
    /* G 13 - Fechar */
    echo("        <span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span>\n");
    echo("      </li>\n");
    echo("    </ul>\n");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }
  else if (!GruposFechados($sock) || EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" bgcolor=\"white\" onload=\"startList(); self.focus();\">\n");
    echo("    <script type='text/javascript'>\n");
    echo("      function ClickHead(check){\n");
    echo("        var head = document.getElementById('chk_'+check+'_head');\n");
    echo("        var checks = new Array();\n");
    echo("        checks = document.getElementsByName('chk_'+check+'_incluir[]');\n");
    echo("        for (i=0; i < checks.length; i++){\n");
    echo("        checks[i].checked = head.checked;\n");
    echo("        }\n");
    echo("      }\n");

    echo("      function VerificaChk(check){\n");
    echo("        var checks = new Array();\n");
    echo("        checks = document.getElementsByName('chk_'+check+'_incluir[]');\n");
    echo("        var total=0;\n");
    echo("        for (i=0; i < checks.length; i++){\n");
    echo("          if (checks[i].checked){\n");
    echo("            total++;\n");
    echo("          }\n");
    echo("        }\n");
    echo("        if(total==checks.length)\n");
    echo("          document.getElementById('chk_'+check+'_head').checked=true;\n");
    echo("        else if(total >= 0)\n");
    echo("          document.getElementById('chk_'+check+'_head').checked=false;\n");
    echo("      }\n");
    echo("    </script>\n\n");

    echo("    <a name=\"topo\"></a>\n");
  
    /* 1 - Grupos */
    $cabecalho ="<h4>".RetornaFraseDaLista($lista_frases,1);
    /* 65 - Incluir Componentes */
    $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,65)."</h4>";

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\" style=\"top: 42px;\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");
    
    echo("    <br /><br />".$cabecalho."\n");
    echo("    <br />\n");
    echo("      <form method=\"post\" name=\"formIncluir\" action=\"acoes.php\">\n");
    echo("        <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
    
    echo("        <input type=\"hidden\" name=\"cod_grupo\" value=\"".$cod_grupo."\" />\n");
    echo("        <input type=\"hidden\" name=\"acao\" value=\"incluir_no_grupo\" />\n");
    echo("        <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\"  class=\"tabExterna\">\n");
    echo("          <tr>\n");
    echo("            <td valign=\"top\">\n");
    echo("              <ul class=\"btAuxTabs\">\n"); 
    /* 65 - Incluir Componentes */
    echo("                <li><span onclick=\"document.formIncluir.submit();\">".RetornaFraseDaLista($lista_frases,65)."</span></li>");
    /* 2 (ger) - Cancelar */
    echo("                <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,2)."</span></li>\n");
    echo("              </ul>\n");
    echo("            </td>\n");
    echo("          </tr>\n");
    echo("          <tr>\n");
    echo("            <td>\n");
    echo("              <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("                <tr class=\"head\">\n");
  
    /* 35 - Selecione abaixo outros componentes a serem inseridos no grupo: */
    echo("                  <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,35)."</td>\n");
    echo("                </tr>\n");

    $lista_usuarios_fora_grupo = RetornaUsuariosForaDoGrupo($sock, $cod_grupo, $cod_curso);

    $linha=$lista_usuarios_fora_grupo;
    $num=count($linha);
    for($c=0;$c<$num;$c++)
    {
      if ($linha[$c]['tipo_usuario'] == 'A')
        /* 18 - Aluno */
        $tipo_usuario_temp = RetornaFraseDaLista($lista_frases,18);
      else if ($linha[$c]['tipo_usuario'] == 'F')
        /* 19 - Formador */
        $tipo_usuario_temp = RetornaFraseDaLista($lista_frases,19);
      else
        $tipo_usuario_temp = "Erro";

      if (e_usuario_sem_grupo($sock, $linha[$c]['cod_usuario']))
      {
        $vetor_nome_sem_grupo[sizeof($vetor_nome_sem_grupo)] = $tipo_usuario_temp." ".$linha[$c]['nome'];
        $vetor_cod_sem_grupo[sizeof($vetor_cod_sem_grupo)] = $linha[$c]['cod_usuario'];
      }
      else
      {
        $vetor_nome_com_grupo[sizeof($vetor_nome_com_grupo)] = $tipo_usuario_temp." ".$linha[$c]['nome'];
        $vetor_cod_com_grupo[sizeof($vetor_cod_com_grupo)] = $linha[$c]['cod_usuario'];
      }
    }

    echo("                <tr class=\"head01\">\n");
    if ($existe_sem_grupo=is_array($vetor_nome_sem_grupo)){
      echo("                  <td width=\"5%\">\n");
      echo("                    <input type=\"checkbox\" id=\"chk_sem_head\" onclick=\"ClickHead('sem');\" />\n");
      echo("                  </td>\n");
      echo("                  <td width=\"45%\">\n");
      /* 85 - Sem Grupo */
      echo("                    ".RetornaFraseDaLista($lista_frases,85)."\n");
      echo("                  </td>\n");
    }
    if ($existe_com_grupo=is_array($vetor_nome_com_grupo)){
      echo("                  <td width=\"5%\">\n");
      echo("                    <input type=\"checkbox\" id=\"chk_com_head\" onclick=\"ClickHead('com');\" />\n");
      echo("                  </td>\n");
      echo("                  <td width=\"45%\">\n");
      /* 84 - Com Grupo */
      echo("                    ".RetornaFraseDaLista($lista_frases,84)."\n");
      echo("                  </td>\n");
    }
    echo("                </tr>\n");

    $aux = 0;

    while (($aux < sizeof($vetor_nome_sem_grupo))||((is_array($vetor_nome_com_grupo))&&($aux < sizeof($vetor_nome_com_grupo))))
    {
    echo("                <tr>\n");
      if ($aux < sizeof($vetor_nome_sem_grupo))
      {
        echo("                  <td>\n");
        echo("                    <input type=\"checkbox\" name=\"chk_sem_incluir[]\" value=\"".$vetor_cod_sem_grupo[$aux]."\" id=\"chk_".$vetor_cod_sem_grupo[$aux]."\" onclick=\"VerificaChk('sem');\" />\n");
        echo("                  </td>\n");
        echo("                  <td>\n");
        echo("                    ".TruncaString($vetor_nome_sem_grupo[$aux], 35)."\n");
        echo("                  </td>\n");
      }else if($existe_sem_grupo){
        echo("                  <td colspan=\"2\">&nbsp;</td>\n");
      }
  
      if ((is_array($vetor_nome_com_grupo))&&($aux < sizeof($vetor_nome_com_grupo)))
      {
        echo("                  <td>\n");
        echo("                    <input type=\"checkbox\" name=\"chk_com_incluir[]\" value=\"".$vetor_cod_com_grupo[$aux]."\" id=\"chk_".$vetor_cod_com_grupo[$aux]."\" onclick=\"VerificaChk('com');\" />\n");
        echo("                  </td>\n");
        echo("                  <td>\n");
        echo("                    ".TruncaString($vetor_nome_com_grupo[$aux], 35)."\n");
        echo("                  </td>\n");
      }else if($existe_com_grupo){
        echo("                  <td colspan=\"2\">&nbsp;</td>\n");
      }
      $aux++;
    echo("                </tr>\n");  
    }
    echo("              </table>\n");
    echo("            </td>\n");
    echo("          </tr>\n");
    echo("        </table>\n");
    echo("      </form>\n");



  }
  else
  {
  
    echo("<script type='text/javascript'>\n");
    echo("  opener.location.reload();\n");
    echo("</script>\n");
  
    echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" bgcolor=\"white\" onload=\"startList(); self.focus();\">\n");
    echo("    <a name=\"topo\"></a>\n");
  
    /* 1 - Grupos */
    $cabecalho ="<h4>".RetornaFraseDaLista($lista_frases,1);
    /* 65 - Incluir Componentes */
    $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,65)."</h4>";
    echo("    <br /><br />".$cabecalho."\n");
    echo("    <br />\n");
  
    echo("    <table cellpadding=\"0\" cellspacing=\"0\"  class=\"tabExterna\" id=\"tabelaExterna\">\n");
    echo("      <tr>\n");
    echo("        <td valign=\"top\">\n");
    echo("          <ul class=\"btAuxTabs\">\n");  
    /* 13 (ger) - Fechar */
    echo("            <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
    echo("          </ul>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr>\n");
    echo("        <td>\n");
    echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\" \">\n");
    echo("            <tr class=\"head\">\n");
    echo("        <td>\n");
    /* 52 - Os grupos já estão formados. Não foi possível inserir novos componentes. */
    echo("            ".RetornaFraseDaLista($lista_frases,52)."\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </td>\n");
    echo("  </tr>\n");    
    echo("    </table>\n");
  }

  Desconectar($sock);

  echo("</body>\n");
  echo("</html>");

?>
