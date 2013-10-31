<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/sala_assunto.php

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
  ARQUIVO : cursos/aplic/batepapo/sala_assunto.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  $cod_sessao=RetornaSessaoCorrente($sock);

  /* mantem o usu�rio na sess�o */
  ManterOnline($sock,$cod_usuario);

  /* Verifica quem n�o est� mais online */
  LimpaOnline($sock,$cod_curso, 90);

  $cod_sessao=RetornaSessaoCorrente($sock);
  $assunto=RetornaAssuntoSessao($sock,$cod_sessao);

  echo("    <script type=\"text/javascript\" language=\"javascript\">\n");

  echo("      function Iniciar() \n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("    </script>\n");

  echo("    <style type=\"text/css\">\n");
  echo("      .assunto\n");
  echo("      {\n");
  echo("        position: absolute;\n");
  echo("        top: 0;\n");
  echo("        left: 0;\n");
  echo("        visibility: true;\n");
  echo("      }\n");
  echo("    </style>\n");

  echo("  </head>\n");
  echo("  <body onLoad=\"Iniciar();\">\n");

  echo("    <br/>\n");
  /* 17 - Sala de Bate-Papo */
  echo("    <h4>".RetornaFraseDaLista($lista_frases,17));

  echo(" - ".$assunto."</h4>\n");

  echo("  </body>\n");
  echo("</html>\n");


  Desconectar($sock);

?>
