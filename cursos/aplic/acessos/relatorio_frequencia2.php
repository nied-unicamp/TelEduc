<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/acessos/relatorio_frequencia2.php

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
  ARQUIVO : cursos/aplic/acessos/relatorio_frequencia2.php
  ========================================================== */

/* C�igo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("acessos.inc");
  //include("acessos_aux.inc");

  $cod_ferramenta = 18;
  include("../topo_tela.php");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario, true);

  echo("    <script type=\"text/javascript\">\n");

  if (!$SalvarEmArquivo)
  {
    echo("      function AbrePerfil(cod_usuario)\n");
    echo("      {\n");
    echo("         window.open('../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("        return(false);\n");
    echo("      }\n\n");

    echo("      function AbreGrupo(cod_grupo)\n");
    echo("      {\n");
    echo("         window.open('../grupos/exibir_grupo.php?cod_curso=".$cod_curso."&cod_grupo='+cod_grupo+'&esconder_barra=1','MostrarComponentes','width=500,height=300,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("      }\n\n");

    echo("      function AbreDiaUsuario(usuario,diaUT)\n");
    echo("      {\n");
    // ferr atual é entrada no ambiente, prox pagina é relatorio_frequencia_usr.php
    echo("         window.open('relatorio_ferramentas_usr.php?cod_curso=".$cod_curso."&usuario='+usuario+'&diaUT='+diaUT+'&opcao=1','JanelaFreq2','width=400,height=400,top=100,left=500,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("        return false;\n");
    echo("      }\n\n");

    echo("      function AbrePeriodoUsuario(usuario,hora_iniUT,hora_fimUT)\n");
    echo("      {\n");
    echo("         window.open('relatorio_ferramentas_usr.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&usuario='+usuario+'&hora_iniUT='+hora_iniUT+'&hora_fimUT='+hora_fimUT,'JanelaFreq2','width=400,height=400,top=100,left=500,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("        return false;\n");
    echo("      }\n\n");

    echo("      function AbreDiaGrupo(cod_grupo,usuario,diaUT)\n");
    echo("      {\n");
    // ferr atual �entrada no ambiente, prox pagina �relatorio_frequencia_grp.php
    echo("         window.open('relatorio_ferramentas_grp.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_grupo='+cod_grupo+'&diaUT='+diaUT+'&usuario='+usuario,'JanelaFreq2','width=400,height=400,top=100,left=500,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("        return false;\n");
    echo("      }\n\n");

    echo("      function AbrePeriodoGrupo(cod_grupo,usuario,hora_iniUT,hora_fimUT)\n");
    echo("      {\n");
    echo("         window.open('relatorio_ferramentas_grp.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_grupo='+cod_grupo+'&hora_iniUT='+hora_iniUT+'&hora_fimUT='+hora_fimUT+'&usuario='+usuario,'JanelaFreq2','width=400,height=400,top=100,left=500,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("        return false;\n");
    echo("      }\n");

    echo("      function ImprimirRelatorio()\n");
    echo("      {\n");
    echo("        if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape')\n");
    echo("        {\n");
    echo("          self.print();\n");
    echo("        }else{\n");
    /* 51- Infelizmente n� foi poss�el imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
    echo("          alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
    echo("        }\n");
    echo("      }\n\n");
  }

  echo("        function Iniciar()\n");
  echo("        {\n");
  echo("          startList();\n");
  echo("          self.focus();\n");
  echo("        }\n\n");
  echo("    </script>\n");

  echo("  </head>\n");
  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
  echo("    <a name=\"topo\"></a>\n");

  /* 1 - Acessos */
  $cabecalho ="<h4>".RetornaFraseDaLista($lista_frases,1);
  /* 41 - Relatório de Acessos às Ferramentas */
  $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,41)."</h4>";
  echo("    <br /><br />".$cabecalho."\n");
  echo("    <br />\n");
 
  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("      <tr>\n");
  echo("        <td valign=\"top\">\n");

  if (!$SalvarEmArquivo)
  {
    echo("          <form action=\"salvar_arquivo.php\" name=\"formSalvar\">\n");
    echo("            <input type=\"hidden\" name=\"cod_curso\"    value=\"".$cod_curso."\" />\n");
    echo("            <input type=\"hidden\" name=\"origem\"       value=\"freq2\" />\n");
    echo("            <input type=\"hidden\" name=\"nome_arquivo\" value=\"relatorio_frequencia3.html\" />\n");
    if(isset($opcao))
      echo("            <input type=\"hidden\" name=\"opcao\"          value=\"".$opcao."\" />\n");
    if(isset($cod_ferramenta))
      echo("            <input type=\"hidden\" name=\"cod_ferramenta\" value=\"".$cod_ferramenta."\" />\n");
    if(isset($diaUt))
      echo("            <input type=\"hidden\" name=\"diaUT\"          value=\"".$diaUT."\" />\n");
    if(isset($data_iniUT))
      echo("            <input type=\"hidden\" name=\"data_iniUT\"     value=\"".$data_iniUT."\" />\n");
    if(isset($data_fimUT))
      echo("            <input type=\"hidden\" name=\"data_fimUT\"     value=\"".$data_fimUT."\" />\n");
    if (isset($usuario))
      echo("            <input type=\"hidden\" name=\"usuario\"        value=\"".$usuario."\" />\n");
    if (isset($cod_grupo))
      echo("            <input type=\"hidden\" name=\"cod_grupo\"      value=\"".$cod_grupo."\" />\n");
    echo("          </form>\n");
    echo("          <ul class=\"btAuxTabs\">\n");  
    /* 22 - Salvar Em Arquivo */
    echo("            <li><span onClick=\"document.formSalvar.submit();\">".RetornaFraseDaLista($lista_frases,22)."</span></li>\n");
    /* G 14 - Imprimir */
    echo("            <li><span onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");
    /* G 13 - Fechar */
    echo("            <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n"); 
  }
  else
  {
    echo("          <ul class=\"btAuxTabs\">\n");  
    /* G 13 - Fechar */
    echo("            <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  }
  echo("          </ul>\n");
  echo("          <br />\n");
  echo("          <br />\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td>\n");
  
  if (isset($cod_ferramenta_relatorio) && $cod_ferramenta_relatorio != ""){
    $sock = Conectar("");
    $nome_ferramenta = RetornaFraseDaLista($lista_frases_menu,$tela_lista_ferramentas[$cod_ferramenta_relatorio]['cod_texto_nome']);
    $sock = Conectar($cod_curso);
  }else{
    /* 29 - Entrada no ambiente */
    $nome_ferramenta = RetornaFraseDaLista($lista_frases,29);
    $cod_ferramenta = -1;
  }

  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  /* 16 - Ferramenta */
  echo("            <tr>\n");
  echo("              <td><b>".RetornaFraseDaLista($lista_frases,16)."</b></td>\n");
  echo("              <td colspan=2>".$nome_ferramenta."</td>\n");
  echo("            </tr>\n");
  
  if (isset($usuario))
  {
    /* tratar 1 usuario */
    if ($opcao == 1 || $opcao == 2)
    {
      $infos_user=RetornaInfosUsuario($sock,$cod_curso,$usuario);
      if (!$SalvarEmArquivo)
      {
        $link_perfil_abre ="<span class=\"link\" onClick=\"AbrePerfil(".$usuario.");\">";
        $link_perfil_fecha="</span>";
      }
      else
      {
        $link_perfil_abre ="";
        $link_perfil_fecha="";
      }
      /* 18 - Usuário */
      echo("            <tr>\n");
      echo("              <td>\n");
      echo("                <b>".RetornaFraseDaLista($lista_frases,18)."</b>\n");
      echo("              </td>\n");
      echo("              <td>".$link_perfil_abre.$infos_user['nome'].$link_perfil_fecha."</td>\n");
      echo("            </tr>\n");

      if ($opcao == 1)
      {
        /* acessos do usuario no dia, variaveis $usuario e $diaUT */
        $acessos_usuarioUT=RetornaUnicoUsuarioAcessosUT($sock,$usuario,$cod_ferramenta,$diaUT,($diaUT + 86399));
        /* 46 - Acessos no dia */
        echo("            <tr>\n");
        echo("              <td>\n");
        echo("                <b>".RetornaFraseDaLista($lista_frases,46)."</b>\n");
        echo("              </td>\n");
        echo("              <td>\n");
        echo("                ".UnixTime2Data($diaUT)."\n");
        echo("              </td>\n");
        echo("            </tr>\n");
      }
      else if ($opcao == 2)
      {
        /* acessos do usuario no periodo, variaveis $usuario, $data_iniUT e $data_fimUT */
        $acessos_usuarioUT=RetornaUnicoUsuarioAcessosUT($sock,$usuario,$cod_ferramenta,$data_iniUT,$data_fimUT);
        /* 43 - Per�do: */
        /* 44 - a */
        echo("            <tr>\n");
        echo("              <td>\n");
        echo("                <b>".RetornaFraseDaLista($lista_frases,43)."</b>\n");
        echo("              </td>\n");
        echo("              <td>\n");
        echo("                ".UnixTime2Data($data_iniUT)." ".RetornaFraseDaLista($lista_frases,44)." ".UnixTime2Data($data_fimUT)."\n");
        echo("              </td>\n");
        echo("            </tr>\n");
      }
      echo("          </table>\n");
      echo("          <br />\n");
      echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
      /* cabecalho da tabela */
      echo("            <tr class=\"head\">\n");

      if ((is_array($acessos_usuarioUT)) && (($num_total = count($acessos_usuarioUT)) > 0))
      {
        /* 48 - Data */
        echo("              <td width=50%>".RetornaFraseDaLista($lista_frases,48)."</td>\n");
        /* 49 - Horário */
        echo("              <td width=50%>".RetornaFraseDaLista($lista_frases,49)."</td>\n");
        echo("            </tr>\n");
        $dia_tmp = "";
        foreach ($acessos_usuarioUT as $c => $linha_acesso)
        {
          echo("            <tr class=\"altColor".($c%2)."\">\n");
          echo("              <td>\n");
          if ($dia_tmp != UnixTime2Data($linha_acesso['data']))
          {
            // mostrar dia
            $dia_tmp = UnixTime2Data($linha_acesso['data']);
            if (!$SalvarEmArquivo && $cod_ferramenta == -1)
            {
              $link_abre ="<span class=\"link\" onClick=\"AbreDiaUsuario(".$usuario.",'".$linha_acesso['data']."');\">";
              $link_fecha="</span>";
            }
            else
            {
              $link_abre ="";
              $link_fecha="";
            }
            echo("                ".$link_abre.$dia_tmp.$link_fecha."\n");
          }
          else
          {
            echo("                &nbsp;\n");
          }
          echo("              </td>\n");
          echo("              <td>\n");
          // mostrar horario
          if (!$SalvarEmArquivo && $cod_ferramenta == -1)
          {
            $link_abre ="<span class=\"link\" onClick=\"AbrePeriodoUsuario(".$usuario.",'".$acessos_usuarioUT[$c]['data']."','".$acessos_usuarioUT[$c + 1]['data']."');\">";
            $link_fecha="</span>";
          }
          else
          {
            $link_abre ="";
            $link_fecha="";
          }
          echo("                ".$link_abre.UnixTime2Hora($linha_acesso['data']).$link_fecha."\n");

          echo("              </td>\n");
          echo("            </tr>\n");
        }
        /* 51 - Total de acessos: */
        echo("            <tr class=\"head01\">\n");
        echo("              <td colspan=2>".RetornaFraseDaLista($lista_frases,51)." ".$num_total."</td>\n");
      }
      else
      {
        echo("              <td>\n");
        /* 47 - N� houve acessos pelo usu�io no per�do. */
        echo("                ".RetornaFraseDaLista($lista_frases,47)."\n");
        echo("              </td>\n");
      }
      echo("            </tr>\n");
      echo("          </table>\n");
    }
    else if ($opcao == 3)
    {
      /* acessos dos usuarios no dia, variavel $diaUT */
      /* 46 - Acessos no dia */
      echo("            <tr>\n");
      echo("              <td>\n");
      echo("                <b>".RetornaFraseDaLista($lista_frases,46)."</b>\n");
      echo("              </td>\n");
      echo("              <td colspan=2>".UnixTime2Data($diaUT)."</td>\n");
      echo("            </tr>\n");
      echo("          </table>\n");
      echo("          <br />\n");

      $lista_acessos=RetornaUsuariosAcessosUT($sock,$cod_ferramenta,$diaUT,($diaUT + 86399));
      $lista_nomes=RetornaUsuarios($sock,"nome",$cod_curso);

      echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
      /* cabecalho da tabela */
      echo("            <tr class=\"head\">\n");

      if ((is_array($lista_acessos)) && (($num_total = count($lista_acessos)) > 0))
      {

        /* 48 - Data */
        echo("              <td align=center class=\"colorfield\" width=30%>".RetornaFraseDaLista($lista_frases,48)."</td>\n");
        /* 49 - Horário */
        echo("              <td align=center class=\"colorfield\" width=30%>".RetornaFraseDaLista($lista_frases,49)."</td>\n");
        /* 52 - Usuário */
        echo("              <td align=center class=\"colorfield\" width=40%>".RetornaFraseDaLista($lista_frases,52)."</td>\n");
        echo("            </tr>\n");
        $dia_tmp = "";
        $c = 1;
        foreach ($lista_acessos as $linha_acesso)
        {
          $data = $linha_acesso['data'];
          $cod_usuario = $linha_acesso['cod_usuario'];
          echo("            <tr class=\"altColor".(($c+1)%2)."\">\n");
          echo("              <td>");
          // mostrar data
          if ($dia_tmp != UnixTime2Data($data))
          {
            $dia_tmp = UnixTime2Data($data);
            echo("            ".$dia_tmp."\n");
          }
          else
          {
            echo("                &nbsp;");
          }
          echo("              </td>\n");
          // mostrar horario
          echo("              <td>");
          /*
              Devemos consertar aqui. Qdo clicamos na coluna de data (acessos no dia, opcao 3),
              os links na data e nos horarios nao aparecem. O certo seria aparecerem os lnks
              somente nos horarios, para acessos entre aquele horario e a proxima entrada no
              ambiente daquele usuario.

          */
          echo("                ".UnixTime2Hora($linha_acesso['data'])."\n");
          echo("              </td>\n");
          // mostrar usuario
          if (!$SalvarEmArquivo)
          {
            $link_perfil_abre ="<span class=\"link\" onClick=\"AbrePerfil(".$cod_usuario.");\">";
            $link_perfil_fecha="</span>";
          }
          else
          {
            $link_perfil_abre ="";
            $link_perfil_fecha="";
          }
          echo("               <td>".$link_perfil_abre.$lista_nomes[$cod_usuario]['nome'].$link_perfil_fecha."</td>\n");
          echo("            </tr>\n");
          $c++;
        }
        /* 51 - Total de acessos: */
        echo("            <tr>\n");
        echo("              <td colspan=3>".RetornaFraseDaLista($lista_frases,51)." ".$num_total."</td>\n");
        echo("            </tr>\n");
        echo("          </table>\n");
      }
      else
      {
        echo("              <td>\n");
        /* 47 - N� houve acessos pelo usu�io no per�do. */
        echo("                ".RetornaFraseDaLista($lista_frases,47)."\n");
        echo("              </td>\n");
      }
      echo("            </tr>\n");
      echo("          </table>\n");
    }
  }
  else if (isset($cod_grupo))
  {
    /* tratar 1 grupo */
    if ($opcao == 1)
    {
      /* acessos do grupo no dia, variaveis $cod_grupo e $diaUT */
      $lista_acessos=RetornaUsuariosAcessosUT($sock,$cod_ferramenta,$diaUT,($diaUT + 86399));
      /* 46 - Acessos no dia */
      $periodo=("            <tr>\n");
      $periodo.=("              <td>\n");
      $periodo.=("                <b>".RetornaFraseDaLista($lista_frases,46)."</b>\n");
      $periodo.=("              </td>\n");
      $periodo.=("              <td>".UnixTime2Data($diaUT)."</td>\n");
      $periodo.=("            </tr>");
    }
    else if ($opcao == 2)
    {
      /* acessos do grupo no periodo, variaveis $cod_grupo, $data_ini e $data_fim*/
      $lista_acessos=RetornaUsuariosAcessosUT($sock,$cod_ferramenta,$data_iniUT,$data_fimUT);
      /* 43 - Per�do: */
      /* 44 - a */
      $periodo=("            <tr>\n");
      $periodo.=("              <td>\n");
      $periodo.=("                <b>".RetornaFraseDaLista($lista_frases,43)."</b>\n");
      $periodo.=("              </td>\n");
      $periodo.=("              <td>\n");
      $periodo.=("                ".UnixTime2Data($data_iniUT)." ".RetornaFraseDaLista($lista_frases,44)." ".UnixTime2Data($data_fimUT)."\n");
      $periodo.=("              </td>\n");
      $periodo.=("            </tr>\n");
    }

    $lista_usuarios=RetornaUnicoGrupo($sock,$cod_grupo);
    $nome_grupo=RetornaNomeUnicoGrupo($sock,$cod_grupo); 

    $lista_nomes=RetornaUsuarios($sock,"nome",$cod_curso);

    if (!$SalvarEmArquivo)
    {
      $link_grupo_abre ="<span class=\"link\" onClick=\"AbreGrupo(".$cod_grupo.");\">";
      $link_grupo_fecha="</span>";
    }
    else
    {
      $link_grupo_abre ="";
      $link_grupo_fecha="";
    }
    /* 40 - Grupo */
    echo("            <tr>\n");
    echo("              <td>\n");
    echo("                <b>".RetornaFraseDaLista($lista_frases,40)."</b>\n");
    echo("              </td>\n");
    echo("              <td>\n");
    echo("                ".$link_grupo_abre.$nome_grupo.$link_grupo_fecha."\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo($periodo);
    echo("          </table>\n");
    echo("          <br />\n");
    echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    /* cabecalho da tabela */
    echo("            <tr class=\"head\">\n");

    $num_total = 0;
    if ((is_array($lista_acessos)) && (count($lista_acessos) > 0))
    {
      /* 48 - Data */
      echo("              <td width=30%>".RetornaFraseDaLista($lista_frases,48)."</td>\n");
      /* 49 - Hor�io */
      echo("              <td width=30%>".RetornaFraseDaLista($lista_frases,49)."</td>\n");
      /* 18 - Usu�io */
      echo("              <td width=40%>".RetornaFraseDaLista($lista_frases,18)."</td>\n");
      echo("            </tr>\n");
      $dia_tmp = "";
      // variavel de alternancia de cores
      $c = 1;

      foreach ($lista_acessos as $linha_acessos)
      {
        $cod_usuario = $linha_acessos['cod_usuario'];
        $dataUT = $linha_acessos['data'];

        // se o usuario pertence ao grupo
        if ($lista_usuarios[$cod_usuario])
        {
          $c++;
          echo("            <tr class=\"altColor".($c%2)."\">\n");
          // mostrar data
          echo("              <td>");
          if ($dia_tmp != UnixTime2Data($dataUT))
          {
            $dia_tmp = UnixTime2Data($dataUT);
            // mostrar data
            if (!$SalvarEmArquivo && $cod_ferramenta == -1)
            {
              $link_abre ="<span class=\"link\" onClick=\"AbreDiaGrupo(".$cod_grupo.",".$cod_usuario.",'".$dataUT."');\">";
              $link_fecha="</span>";
            }
            else
            {
              $link_abre ="";
              $link_fecha="";
            }
            echo("                ".$link_abre.$dia_tmp.$link_fecha."\n");
          }
          else
          {
            echo("                &nbsp;");
          }
          echo("              </td>\n");
          // mostrar horario
          echo("              <td>\n");
          if (!$SalvarEmArquivo && $cod_ferramenta == -1){
            $link_abre ="<span class=\"link\" onClick=\"AbrePeriodoGrupo(".$cod_grupo.",".$cod_usuario.",".$dataUT.");\">";
            $link_fecha="</span>";

          }else{
            $link_abre ="";
            $link_fecha="";
          }
          echo("                ".$link_abre.UnixTime2Hora($dataUT).$link_fecha."\n");
          echo("              </td>\n");
          // mostrar usuario
          if (!$SalvarEmArquivo)
          {
            $link_perfil_abre ="<span class=\"link\" onClick=\"AbrePerfil(".$cod_usuario.");\">";
            $link_perfil_fecha="</span>";
          }
          else
          {
            $link_perfil_abre ="";
            $link_perfil_fecha="";
          }
          echo("               <td>".$link_perfil_abre.$lista_nomes[$cod_usuario]['nome'].$link_perfil_fecha."</td>\n");
          echo("            </tr>\n");
          $num_total++;
        }
      }
      /* 51 - Total de acessos: */
      echo("            <tr class=\"head01\">\n");
      echo("              <td colspan=3>".RetornaFraseDaLista($lista_frases,51)." ".$num_total."</td>\n");
      echo("            </tr>\n");
      echo("          </table>\n");
    }
    else
    {
        echo("              <td>\n");
        /* 50 - N� houve acessos no per�do. */
        echo("                  ".RetornaFraseDaLista($lista_frases,50)."\n");
        echo("              </td>\n");
    }
    echo("            </tr>\n");
    echo("          </table>\n");
  }
  else
  {
    echo("          </table>\n");
  }

  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");

  Desconectar($sock);

  echo("  </body>\n");
  echo("</html>\n");

?>
