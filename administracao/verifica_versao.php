<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/verifica_versao.php

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
  ARQUIVO : administracao/verifica_versao.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../administracao/admin.inc");
  include($bibliotecas."extracao.inc");
  include($bibliotecas."conversor.inc");
  include($bibliotecas."sql_dump.inc");
  include("insercao.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  $sock=Conectar("");
  
  include("../menu_principal_tela_inicial.php");
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  //Agora verificamos se a vers�o do curso a ser extraido � a mesma do TelEduc. 
  //Primeiro pegamos a versao atual na tabela Config
  $query = "SELECT valor FROM Config where item='versao'";
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  $versao_atual = "$linha[0]";
  
  /*Aqui pegamos a versao do curso que iremos iserir*/
  $lista_inseriveis=RetornaCursosInseriveis();
  $versao_curso = $lista_inseriveis[$cod_pasta]['versao'];
  $versao_curso = rtrim ($versao_curso);
  
  if (strcmp($versao_curso,$versao_atual)==0)
    header("Location: inserir_curso2.php?cod_pasta=$cod_pasta&categoria=$categoria");
  
  // 340 - Incompatibilidade de vers�es.
  echo ("<center>".RetornaFraseDaLista($lista_frases, 340)."");
  // 341 - A vers�o do curso que voc� est� inserindo � a
  // 342 - por�m seu TelEduc est� na vers�o
  echo ("<br>".RetornaFraseDaLista($lista_frases, 341)." $versao_curso, ".RetornaFraseDaLista($lista_frases, 342)." $versao_atual");
  // 343 - Cursos em vers�es diferentes podem ser incompat�veis. Tem certeza de que deseja inserir o curso?
  echo ("<br>".RetornaFraseDaLista($lista_frases, 343)."");
  
  echo ("<form action='inserir_curso2.php'>");
  
  echo ("<input type=\"hidden\" name=\"cod_pasta\" value='$cod_pasta' >");
  echo ("<input type=\"hidden\" name=\"categoria\" value='$categoria' >");
  // 35 - Sim
  // 36 - N�o
  echo ("<input type=\"submit\" value='".RetornaFraseDaLista($lista_frases_geral, 35)."' >");
  echo ("<input type=\"button\" value='".RetornaFraseDaLista($lista_frases_geral, 36)."' onclick='javascript: window.location.href=\"inserir_curso.php\"'>");
  echo ("</center>");

  include("../cursos/aplic/tela2.php");
  echo("</body>\n");
  echo("</html>\n");
?>
