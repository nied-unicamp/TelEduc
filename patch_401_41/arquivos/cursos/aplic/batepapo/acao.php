<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/ver_sessoes_realizadas.php

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
  ARQUIVO : cursos/aplic/batepapo/acao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");
  include("avaliacoes_batepapo.inc");

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  if ($acao=='A')
  {
    $cod_pagina=4;
  }
  else
  {
    $cod_pagina=5;
  }

  if (count($cod_sessao_apagar)>0)
  {
    AtualizaStatusSessao ($sock,$cod_sessao_apagar,$acao);
    AtualizaStatusAvaliacao($sock,$cod_sessao_apagar,$acao,$cod_usuario);

    if ($acao=='A')
    {
      /* 82 - Sessões recuperadas com sucesso */
    header("Location:ver_sessoes_realizadas.php?&cod_curso=".$cod_curso);
    Desconectar($sock);
    }
    else
    {
      /* 76 - Sessões apagadas com sucesso. */
    header("Location:ver_sessoes_realizadas.php?&cod_curso=".$cod_curso."&lixeira=sim");
    Desconectar($sock);
    }
  }

  Desconectar($sock);
  echo("</body></html>\n");
?>