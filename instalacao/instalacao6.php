<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : instalacao/instalacao6.php

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
  ARQUIVO : instalacao/instalacao6.php
  ========================================================== */

  include "instalacao.inc"; 

  ExibirCabecalho(6,"Instalação Concluída");

  session_register("teleduc_login_s");
  session_register("teleduc_senha_s");
  session_register("dbbasegeral_s");
  session_register("dbbasecurso_s");

  $sock=ConectarDB($dbbasegeral_s,$teleduc_login_s,$teleduc_senha_s);

  if ($sock==-1)
    Voltar("Erro inesperado! Reinicie a instalação");

  $query="insert into Config values ('admtele','".crypt($senha_admin,"AA")."')";
  Enviar($sock,$query);
  $query="insert into Config values ('host','".$host."')";
  Enviar($sock,$query);
  $query="insert into Config values ('adm_nome','".$nome_admin."')";
  Enviar($sock,$query);
  $query="insert into Config values ('adm_email','".$mail_admin."')";
  Enviar($sock,$query);
  $query="insert into Config values ('curso_form', 'nao')";
  Enviar($sock, $query);
  $query="insert into Config values ('normas', '')";
  Enviar($sock, $query);
  $query="insert into Config values ('extrator', 'nao')";
  Enviar($sock, $query);
  
  AbreFormFinal();

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("<tr>\n");
  /* <!----------------- Tabela Interna -----------------> */
  echo("<td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("<tr><td style=\"padding-left: 150px; padding-top: 15px; padding-right: 150px; padding-bottom: 15px; font-size: small;\" align=\"left\">\n");
  Paragrafo("1 - Dados da administração configurados com sucesso!");

  echo("<br/>\n");

  Paragrafo("O TelEduc está agora instalado e pronto para uso!");

  Paragrafo("Para fechar a segurança dos diretórios principais, não esqueça de executar o passo 5 da instalação em LeiaMe.txt");

  echo("</td></tr></table>\n");
  echo("</td></tr></table>\n");

  EncerraPaginaFinal();
?>

