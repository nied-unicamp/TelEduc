<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/enquete/identidade_enquete.php

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
  ARQUIVO : cursos/aplic/enquete/identidade_enquete.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("enquete.inc");

  $cod_ferramenta=24;

  include("../topo_tela.php");

  echo("  <script type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\" language=\"JavaScript\">\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function OpenWindowPerfil(funcao)\n");
  echo("      {\n");
  echo("        window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n\n");
  echo("    </script>\n");
  echo("  </head>\n");

  $enquete = getEnquete($sock, $idEnquete); 
  $status = getStatusEnquete($sock, $enquete);
  $alternativas = getAlternativas($sock, $idEnquete);
  $total_votos = getTotalVotos($sock, $idEnquete);
  $ator = getTipoAtor($sock, $cod_curso, $cod_usuario); 
  $cont = 0;

  if (podeVerIdentidadeEnquete($sock, $ator, $enquete, CriadorEnquete($sock, $cod_curso, $cod_usuario, $idEnquete))){
    echo("<body onLoad=Iniciar();>\n");

    echo("    <table border=\"0\" width=\"250px\" cellspacing=\"1\" style=\"margin-top:5px;\">\n");
    echo("      <tr>\n");
    echo("        <td valign=\"top\">\n");
    /* 1 - Enquete */
    echo("            <br/><h4>".RetornaFraseDaLista($lista_frases,1)." - ".$enquete['titulo']."</h4>\n");

    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr>\n");
    echo("        <td style=\"font-size:13px;\">\n");
    /* 81 - Pergunta */
    echo("          <strong>".RetornaFraseDaLista($lista_frases,81)."</strong> ".$enquete['pergunta']."\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr><td>&nbsp;</td></tr>\n");

    foreach ($alternativas as $cod => $alternativa){
      $votos = getVotos($sock, $alternativa['idAlternativa'],$cod_curso);

      echo("        <tr class=\"head\">\n");
      echo("          <td class=\"center\">\n");
      echo("                  ".(++$cont).") ".$alternativa['texto']."\n");
      echo("          </td>\n");
      echo("        </tr>\n");

      if (RetornaNumLinhas($votos) == 0){
        echo("        <tr>\n");
        echo("          <td style=\"font-size:12px;\">\n");
        /* 82 - nenhum voto */
        echo("            ".RetornaFraseDaLista($lista_frases,82)."\n");
        echo("          </td>\n");
        echo("        </tr>\n");
      }

      $linha = 0;
      while ($voto = RetornaLinha($votos)){
        $linha = (($linha + 1) % 2);
        echo("        <tr class=\"altColor".($linha)."\">\n");
        echo("          <td class=\"itens\" style=\"font-size:12px;\"><a href=\"#\" onclick=\"javascript: OpenWindowPerfil(".$voto['cod_usuario'].");\">".NomeUsuario($sock, $voto['cod_usuario'], $cod_curso)."</a></td>\n");
// echo("          <td class=\"itens\" style=\"font-size:12px;\"><a href=\"#\" onclick=\"javascript: OpenWindowPerfil(".$voto['cod_usuario'].");\">".$voto['nome']."</a></td>\n");
        echo("        </tr>\n");
      }
    }
    echo("      <tr>\n");
    echo("        <td>&nbsp;\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr>\n");
    echo("        <td style=\"font-size:14px;\">\n");
    /* 63 - Total de votos: */
    echo("          <strong>".RetornaFraseDaLista($lista_frases,63)."</strong>".$total_votos."\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("              <tr>\n");
    echo("                <td align=\"center\">\n");
    echo("                  <ul class=\"btAuxTabs\">\n");
    echo("                    <li><span onclick=\"self.close();\">Fechar</span></li>\n");
    echo("                  </ul>\n");
    echo("                </td>\n");
    echo("              </tr>\n");

    echo("    </table>\n");

    /* Fim da Pagina ***************************/
    echo("</body>\n");
    echo("</html>\n");

    Desconectar($sock);
    exit;

  }else{

    echo("<body onLoad=Iniciar();>\n");
    echo("\n");

    /* Pagina Principal */
    /* 1 - Enquete */
    $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b><font style='color: #CE3500'> - ".$enquete['titulo']."</font>\n";

    /* Cabecalho */
    echo(PreparaCabecalho($sock,$cod_curso,$cabecalho,$cod_ferramenta,3));

    /* 71 - Acesso negado. */
    echo("<p>".RetornaFraseDaLista($lista_frases,71)."</p>");

    /* 23 - Voltar (gen) */
    echo("<form><input type=\"button\" class=\"input\" value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1);></form>\n");

    echo("</body>\n");
    echo("</html>\n");

    Desconectar($sock);
    exit;
  }

?>
