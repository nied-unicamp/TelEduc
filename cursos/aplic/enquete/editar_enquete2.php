<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/enquete/nova_enquete2.php

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
  ARQUIVO : cursos/aplic/enquete/nova_enquete2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("enquete.inc");
  
  $cod_ferramenta=24;

  include("../topo_tela.php");

  /* INICIO - JavaScript */
  echo("<script type=\"text/javascript\" language=\"javascript\">\n\n");
  echo("  function Iniciar()\n");
  echo("  {\n");
  echo("    startList();\n");
  echo("  }\n\n");
  /* Volta a Pagina de edicao desta Enquete */
  echo("  function VoltaPaginaEdicao(atualizacao)\n");
  echo("  {\n");
  echo("     document.location.href='editar_enquete.php?cod_curso=".$cod_curso."&idEnquete=".$idEnquete."&acao=editarEnquete&atualizacao='+atualizacao;\n");
  echo("    return(true);\n");
  echo("  }\n\n");
  echo("</script>\n\n");
  /* FIM - JavaScript */
  
  include("../menu_principal.php");

  $data_inicio=DataHora2Unixtime($data_inicio." ".$hora_inicio.":00");
  $data_fim=DataHora2Unixtime($data_fim." ".$hora_fim.":00");

  # FAVC
  for ($i = 0; $i <= 3; $i++) {
    if (!isset($aplic[$i]))  $aplic[$i] = '-';
    $aplicacao .= $aplic[$i];
  }
  
  # RFAVC
  for ($i = 0; $i <= 4; $i++) {
    if (!isset($result[$i]))  $result[$i] = '-';
    $resultado .= $result[$i];
  }
  
  //retira alternativas repetidas
  $alternativa = array_unique($alternativa); 
  
  /* Se a enquete for salva exibe uma mensagem confirmando o sucesso */
  /* e um botao para fechar a janela.                                 */
  if (EditaEnquete($sock, $idEnquete, $titulo,$pergunta, $data_inicio, $data_fim, $aplicacao, $resultado, $resultado_parcial, $identidade_votos, $num_escolhas , $alternativa))
  {
    AtualizaFerramentasNova($sock,24,'T');
    $atualizacao = "true";
    Desconectar($sock);
    echo("  <script type=\"text/javascript\" language=\"javascript\">VoltaPaginaEdicao('".$atualizacao."');</script>");
    exit;
  } /* Se a enquete NAO pode ser editada apresenta uma mensagem de erro */
  else
  {
    $atualizacao = "false";
    Desconectar($sock);
    echo("  <script type=\"text/javascript\" language=\"javascript\">VoltaPaginaEdicao('".$atualizacao."');</script>");
    exit;
  }

  Desconectar($sock);
  exit;
?>
