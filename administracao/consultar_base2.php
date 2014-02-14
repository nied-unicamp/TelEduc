<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/consultar_base2.php

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
  ARQUIVO : administracao/consultar_base2.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");
  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("}\n");
  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");


  session_register("consulta_s");
  session_register("base_s");

  $consulta=ConverteBarraAspas2Aspas($consulta);
  $affected_rows = FALSE;

  $consulta_s=$consulta;
  $base_s=$base;

  $lista_frases=RetornaListaDeFrases($sock,-5);

  Desconectar($sock);

  $consulta=implode(" ",explode("\n",$consulta));

  /* 67 - Todas os Cursos */
  if ($base==RetornaFraseDaLista($lista_frases,67))
      $lista=EnviarTodasBasesCursos($consulta); 
  else
  {
    $lista[0]['NomeBase']=$base;
    $sock=ConectarDB($base);
    $res=Enviar($sock,$consulta);
    
    if (eregi("^(update) ", $consulta)) 
    {
      $affected_rows = mysql_affected_rows($sock);
    }

    if (eregi("^(select|desc|show|describe) ",$consulta))
    {
      $lista[0]['Campos']=RetornaCampos($res);
      $lista[0]['Res']=RetornaArrayLinhas($res, MYSQL_ASSOC);
    }
    Desconectar($sock);
  }

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 5 - Consulta a Base de Dados */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,5)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='consultar_base.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  if (eregi("^(select|desc|show|describe) ",$consulta)) {
    echo("                  <li>");
    echo("                  <form method=\"post\" action=\"exportar_resultados.php\">");
    /* 333 - Exportar para csv */
    echo("                  <input type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases,333)."\"/>\n");
    echo("                  </form>");
    echo("                  </li>");
  }
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  /* 69 - Query enviada */
  echo("                  <tr>\n");
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,69)."</b> ".$consulta);
  if ($affected_rows != FALSE)
    /* 503 - Total de linhas alteradas: */
    echo ("<br />".RetornaFraseDaLista($lista_frases, 503)." ".$affected_rows."<br>\n");

  if (eregi("^(select|desc|show|describe) ",$consulta))
  {
    if (count($lista)>0)
    {
      $_SESSION['lista_s'] = $lista;
      $cont=0;
      foreach($lista as $cod => $linha)
        $cont = $cont + count($linha['Res']);

      /* 70 - Ocorr�ncias encontradas: */
      echo("<br /><b>".RetornaFraseDaLista($lista_frases,70)."</b> ".$cont);

      foreach($lista as $cod => $linha)
      {
        if (count($linha['Res'])>0)
        {
          echo("<br /><p><b>".$linha['NomeBase']."</b><br />\n");
          echo("                      <table class=\"tableInterna\">\n");
          echo("                        <tr class=\"head\">\n");
          foreach($linha['Campos'] as $cod1 =>$linha1)
          {
            echo("                        <td style=\"border:0;\"><b>".$linha1."</b></td>\n");
          }
          echo("                        </tr>\n");
          foreach($linha['Res'] as $cod1 => $linha1)
          {
            echo("                        <tr class=\"altColor".$cod1%(2)."\">\n");
            foreach($linha['Campos'] as $cod2 => $linha2)
            {
              echo("                          <td style=\"border:0;\">".$linha1[$linha2]."</td>\n");
            }
            echo("                        </tr>\n");
          }
          echo("                      </table>\n");
        }
      }
    }
  }

  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>