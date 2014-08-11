<?php
/*
<!--
-------------------------------------------------------------------------------
Arquivo : cursos/aplic/avaliacoes/criar_avaliacao_forum2.php
TelEduc - Ambiente de Ensino-Aprendizagem a Distacia
Copyright (C) 2001 NIED - Unicamp
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
You could contact us through the following addresses:
Nied - Ncleo de Informatica Aplicada a Educacao
Unicamp - Universidade Estadual de Campinas
Cidade Universitaria "Zeferino Vaz"
Bloco V da Reitoria - 2o. Piso
CEP:13083-970 Campinas - SP - Brasil
http://www.nied.unicamp.br
nied@unicamp.br
------------------------------------------------------------------------------
-->
*/
/*==========================================================
ARQUIVO : cursos/aplic/avaliacoes/criar_avaliacao_forum2.php
========================================================== */
$bibliotecas="../bibliotecas/";
include($bibliotecas."geral.inc");
include("avaliacoes.inc");
$sock=Conectar("");
$lista_frases=RetornaListaDeFrases($sock,22);
$lista_frases_geral=RetornaListaDeFrases($sock,-1);
Desconectar($sock);
$sock=Conectar($cod_curso);
VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
echo("<html>\n");
/* 1 - Avaliacoes*/
echo(" <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
echo(" <link rel=\"stylesheet\" type=\"text/css\" href=\"../teleduc.css\">\n");
$tabela="Avaliacao";
$dir="Avaliacao";
echo(" <link rel=\"stylesheet\" type=\"text/css\" href=\"avaliacoes.css\">\n");
/* Verifica se a pessoa a editar eh formador */
if (!EFormador($sock,$cod_curso,$cod_usuario))
{
echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
/* 1 - Avaliacoes*/
echo("<b class=\"titulo\">".RetornaFraseDaLista($lista_frases,1)."</b>\n");
/* 8 - Area restrita ao formador. */
echo("<b class=\"subtitulo\"> - ".RetornaFraseDaLista($lista_frases,8)."</b><br>\n");
/* 23 - Voltar (gen) */
echo("<form><input type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\"></form>\n");
echo("</body></html>\n");
Desconectar($sock);
exit;
}
$virgula = strstr($valor, ",");
if (strcmp($virgula,""))
{
$tmpvalor=explode(",",$valor);
$valor=implode(".", $tmpvalor);
}
$hora_inicio='00';
$hora_fim='23';
$data_inicio=DataHora2Unixtime($data_inicio." ".$hora_inicio.":00");
$data_fim=DataHora2Unixtime($data_fim." ".$hora_fim.":59");
AtualizaCadastroAvaliacao($sock, $tabela, $cod_usuario, trim($objetivos), trim($criterios),'',$valor,$data_inicio,$data_fim,$cod_avaliacao);
AtualizaFerramentasNova($sock,22,'T');
header("Location:../forum/forum.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario);
Desconectar($sock);
?>