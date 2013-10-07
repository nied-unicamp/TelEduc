<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/bibliotecas/javascript.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/* ==========================================================
   ARQUIVO : cursos/aplic/bibliotecas/javascript.inc
   ========================================================== */

/*
  biblioteca de funï¿½ï¿½es JavaScript e criaï¿½ï¿½o de campos de data HTML
  Autor: Leonel Aguilar Gayard
*/

$palavras_reservadas_js = array(
  "break",
  "case",
  "catch",
  "continue",
  "debugger",
  "default",
  "delete",
  "do",
  "else",
  "finally",
  "for",
  "function",
  "if",
  "in",
  "instanceof",
  "new",
  "null",
  "return",
  "switch",
  "this",
  "throw",
  "try",
  "typeof",
  "var",
  "void",
  "while",
  "with",
);

/* *********************************************************************
   GeraVariaveis - Echo que gera variáveis JavaScript
   entrada: $variaveis_js - um array associativo com formato chave-valor 
   saida:   nenhuma
   Obs: - a funï¿½ï¿½o jï¿½ gera as tags <script> e </script>
*/
function GeraVariaveis($variaveis_js)
{
  // Aqui tornamos disponível qualquer variável que
  // a página tenha colocado em $variaveis_js para todas
  // as funções JavaScript definidas subsequentemente.
  if (is_array($variaveis_js) && count($variaveis_js)) {
    echo("    <script type=\"text/javascript\">\n");
    foreach ($variaveis_js as $nome_var => $valor) {
      if (!empty($nome_var) && is_string($nome_var) && $valor !== "") {
        $nome_var = trim($nome_var);
        // O nome deve ser válido e
        if (preg_match('/^[a-zA-Z_\$][\w\$]*$/', $nome_var) &&
        // o nome não pode ser uma palavra reservada e
        !in_array($nome_var, $palavras_reservadas_js)          &&
        // deve ter um valor simples.
        !is_array($valor) && !is_object($valor))
        {
          echo("      var ".$nome_var." = ".$valor.";\n");
        }
      }
    }
    echo("    </script>\n");
  }
}
?>
