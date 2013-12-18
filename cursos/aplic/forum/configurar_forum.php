<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/forum/configurar_forum2.php

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
  ARQUIVO : cursos/aplic/forum/configurar_forum2.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("forum.inc");

  $sock = Conectar("");
  $lista_frases_grupos = RetornaListaDeFrases($sock, 11);
  Desconectar($sock);

  $cod_ferramenta=9;
  include("../topo_tela.php");

  //=================================
  //Inicio das fun�es em JavaScript 
  //=================================
  echo("<script type=\"text/javascript\">\n");

  echo("    function Iniciar()\n");
  echo("    {\n");
  echo("      startList();\n");
  echo("    }\n\n");

  echo("function RetornaElemento(nome)\n");
  echo("{\n");
  echo("  var temp;\n");
  echo("  for (var j=0; j < document.frmConf.elements.length; j++)\n");
  echo("    if (document.frmConf.elements[j].name==nome)\n");
  echo("      temp=document.frmConf.elements[j];\n");
  echo("  return temp;\n");
  echo("}\n\n");

  // função seleciona_elementos_grupo : marca como selecionado todos os elementos
  // do select
  echo("function seleciona_elementos_grupo()\n");
  echo("{\n");
  echo("  var destino=RetornaElemento('select_participantes_permissao[]');\n");
  echo("  tamanho_destino=destino.length;\n");
  echo("  for (var i=tamanho_destino-1; i>=0; i--)\n");
  echo("    destino.options[i].selected=true;\n");
  echo("  return (true);\n");
  echo("}\n");

  //------------------------------------------
  // Adicionar, Remover, Esvaziar Participantes
  //------------------------------------------
  echo("function adicionar()\n");
  echo("{\n");
  echo("  var usuarios=RetornaElemento('select_participantes[]');\n");
  echo("  var destino=RetornaElemento('select_participantes_permissao[]');\n");
  echo("  tamanho_usuarios=usuarios.length;\n");
  echo("  for (var i=0; i < tamanho_usuarios; i++)\n");
  echo("  {\n");
  echo("    if (usuarios.options[i].selected)\n");
  echo("    {\n");
  echo("      valor=usuarios[i].value;\n");
  echo("      nome=usuarios[i].text;\n");
  echo("      if (!ja_existe(valor) && nome.charAt(0)!='-')\n");
  echo("      {\n");
  echo("        var option0 = new Option(nome, valor);\n");
  echo("        destino.options[destino.length]=option0;\n");
  echo("      }\n");
  echo("    }\n");
  echo("  }\n");
  echo("}\n");

  echo("function remover()\n");
  echo("{\n");
  echo("  var destino=RetornaElemento('select_participantes_permissao[]');\n");
  echo("  tamanho_destino=destino.length;\n");
  echo("  for (var i=tamanho_destino-1; i>=0; i--)\n");
  echo("    if (destino.options[i].selected)\n");
  echo("      destino.options[i]=null;\n");
  echo("}\n");

  echo("function esvaziar()\n");
  echo("{\n");
  /* 110  - Você quer realmente apagar a lista de alunos com permissão? */
  echo("  if (confirm('".RetornaFraseDaLista($lista_frases,110)."'))\n");
  echo("  {\n");
  echo("    var destino=RetornaElemento('select_participantes_permissao[]');\n");
  echo("    tamanho_destino=destino.length;\n");
  echo("    for (var i=tamanho_destino-1; i>=0; i--)\n");
  echo("      destino.options[i]=null;\n");
  echo("  }\n");
  echo("}\n");

  //--------------------------------------

  echo("function ja_existe(valor)\n");
  echo("{\n");
  echo("  var destino=RetornaElemento('select_participantes_permissao[]');\n");
  echo("  tamanho_destino=destino.length;\n");
  echo("  var i=0;\n");
  echo("  var achei=false;\n");
  echo("  while (i < tamanho_destino && !achei)\n");
  echo("    achei=(destino[i++].value==valor);\n");
  echo("  return(achei);\n");
  echo("}\n");


  echo("function ChecaVazio()\n");
  echo("{\n");
  echo("    var n_participantes_permissao=RetornaElemento('select_participantes_permissao[]');\n");
  echo("    tamanho_n_participantes_permissao=n_participantes_permissao.length;\n");
  echo("    if (tamanho_n_participantes_permissao == 0){\n");
  /* 112 - Lista de alunos com permissão neste fórum está vazia. Por favor, preencha com algum participante. */
  echo("      alert('".RetornaFraseDaLista($lista_frases,112)."');\n");
  echo("      return false;\n");
  echo("    }\n");
  echo("    return true;\n");
  echo("}\n");

  echo("  function mudafonte(tipo) {\n");
  echo("    if ( tipo == 0 ) {");
  echo("          document.getElementById(\"tabelaInterna\").style.fontSize=\"1.0em\";\n");
  echo("          tipo=''; \n");
  echo("    } \n");
  echo("    if ( tipo == 1 ) {\n");
  echo("      document.getElementById(\"tabelaInterna\").style.fontSize=\"1.2em\";\n");
  echo("      tipo=''; \n");
  echo("    }\n");
  echo("    if ( tipo == 2 ) { \n");
  echo("      document.getElementById(\"tabelaInterna\").style.fontSize=\"1.4em\";\n");
  echo("      tipo=''; \n");
  echo("    }\n");
  echo("  }\n");

  echo("</script>\n\n");
  //================================
  //Fim das funções em JavaScript
  //================================

  echo("  </head>\n");
  echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" onload=\"this.focus();Iniciar();\">\n");

  // 1 - Fórum
  echo("  <br /><br /><h4>".RetornaFraseDaLista ($lista_frases, 1)."</h4><br />\n");
  echo("  <div id=\"feedback\" class=\"feedback_hidden\"><span id=\"span_feedback\">ocorreu um erro na sua solicitacao</span></div>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\" style=\"top: 42px;\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("      <tr>\n");
  echo("        <td valign=\"top\" colspan=\"3\">\n");
  echo("          <ul class=\"btAuxTabs\">\n");
   /* 13 - Fechar (ger) */
  echo("            <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  echo("          </ul>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td colspan=\"3\">\n");    
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("            <tr>\n");
  echo("              <td colspan=\"3\" align=\"center\"><br /><b>");
  if($status == 'G'){
    echo(RetornaFraseDaLista($lista_frases,107));
  }else if($status == 'R'){  
    echo(RetornaFraseDaLista($lista_frases,108));
  }
  echo("              </b><br /></td>\n");
  echo("            </tr>\n");

  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <form name=\"frmConf\" action=\"acoes.php\" method=\"post\" onsubmit='seleciona_elementos_grupo(); return(ChecaVazio());'>\n");
  echo("                  <input type=\"hidden\" name=\"cod_forum\" value='".$cod_forum."' />\n");
  echo("                  <input type=\"hidden\" name=\"acao\" value='configurar_forum' />\n");
  echo("                  <input type=\"hidden\" name=\"status\" value='".$status."' />\n");
  echo("                  <input type=\"hidden\" name=\"cod_curso\" value='".$cod_curso."' />\n");
  echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                    <tr>\n");
  echo("                      <td style=\"border:0pt;\" align=\"center\">\n");
  /* 113 - Lista de participantes do curso */
  echo("                        ".RetornaFraseDaLista($lista_frases,113)."<br />\n");
  echo("                        <select style=\"width:100pt;\" name='select_participantes[]' size=\"5\" multiple class=\"g1field\">\n");                                                                  
  /* 29 - Todos */
  echo("                          <option value=\"T*\">".RetornaFraseDaLista($lista_frases_grupos,29)."</option>\n");
  /* Separador entre Todos-Todos os formadores */
  echo("                          <option value=\"\">--------------------</option>\n");
  /* 30 - Todos os formadores */
  echo("                          <option value=\"F*\">".RetornaFraseDaLista($lista_frases_grupos,30)."</option>\n");
  /* Formadores um por um */
  $linha= RetornaCodigoFormadoresDoCurso($sock, $cod_curso);
  $num= count($linha);
  for($c=0; $c<$num; $c++)
    if ($linha[$c]['cod_usuario']>=0) /* exclui o administrador do TelEduc da Lista */
      echo("                          <option value=\"".$linha[$c]['cod_usuario']."\">".TruncaString(RetornaNomeUsuarioDeCodigo($sock, $linha[$c]['cod_usuario']), 35)."</option>\n");
  /* Separador entre ultimo formador-Todos os alunos */
  echo("                          <option value=\"\">--------------------</option>\n");
  /* 31 - Todos os alunos */
  echo("                          <option value=\"A*\">".RetornaFraseDaLista($lista_frases_grupos,31)."</option>\n");
  /* Alunos um por um */
  $linha= RetornaCodigoAlunosDoCurso($sock, $cod_curso);
  $num= count($linha);
  for($c=0; $c<$num && $linha != ""; $c++)
    echo("                          <option value=\"".$linha[$c]['cod_usuario']."\">".TruncaString(RetornaNomeUsuarioDeCodigo($sock, $linha[$c]['cod_usuario']), 35)."</option>\n");
  /* Separador entre ultimo aluno e colaboradores */
  echo("                          <option value=\"\">--------------------</option>\n");
  $lista_colaboradores = RetornaTodosColaboradores($sock, $cod_curso);
  $num=count($lista_colaboradores);
  if($num >0){
    // 117 - Todos os colaboradores
    echo("                          <option value=\"Z*\">".RetornaFraseDaLista($lista_frases_grupos, 117)."</option>\n");
    // Colaboradores um por um
    if (is_array ($lista_colaboradores))
      foreach ($lista_colaboradores as $c => $linha_colaborador)
        echo("                        <option value=\"".$linha_colaborador['cod_usuario']."\">".TruncaString(RetornaNomeUsuarioDeCodigo($sock, $linha_colaborador['cod_usuario']), 35)."</option>\n");
    /* Separador entre ultimo colaborador e grupos */
    echo("                          <option value=\"\">--------------------</option>\n");
  }

  $lista=RetornaCodigoGruposDoCurso($sock, $cod_curso);
  $num= count($lista);
  if(!empty($lista)){

    // 32 - Todos os grupos
    if ($ferramenta_grupos = StatusFerramentaGrupos ($sock, $cod_curso, $cod_usuario)){
      echo("                          <option value=\"G*\">".RetornaFraseDaLista($lista_frases_grupos,32)."</option>\n");
      /* Grupos um por um */
      for($c=0;$c<$num;$c++)
        echo("                          <option value=\"g".$lista[$c]['cod_grupo']."\">".RetornaGrupoComCodigo($sock,$lista[$c]['cod_grupo'])."</option>\n");
    }
  }
  echo("                        </select>\n");
  echo("                      </td>\n");

  echo("                      <td style=\"border:0pt;\">\n");
  echo("                        <input class=\"input\" type=\"button\" value='------>' onclick='adicionar();' /><br />\n");  
  echo("                      </td>\n");
  /* 109 - Lista dos alunos com permiss�  */
  $usuarios = RetornaUsuariosPermissao($sock,$cod_forum);
  echo("                      <td style=\"border:0pt;\" valign=\"top\" align=\"center\">".RetornaFraseDaLista($lista_frases,109)."<br />\n");
  echo("                        <select style=\"width:100pt;\" name='select_participantes_permissao[]' size=\"5\" multiple=\"multiple\" class=\"g1field\">\n");
  /* Exibe os usuários que já estão setados com permissão */
  foreach($usuarios as $cod => $linha){
    echo("                          <option value=\"".$linha['cod_permitido']."\">".TruncaString(RetornaNomeUsuarioDeCodigo($sock, $linha['cod_permitido']), 35)."\n");	
  }

  $grupos = RetornaGruposPermissao($sock,$cod_forum);

  /* Exibe os grupos que já estão setados com permissão */
  foreach($grupos as $cod => $linha){
    echo("                          <option value=\"g".$linha['cod_permitido']."\">".RetornaGrupoComCodigo($sock,$linha['cod_permitido'])."\n");
  }
  echo("                        </select>\n");
  echo("                      </td>\n");
  echo("                      <td style=\"border:0pt;\">\n");
  /* 7 - Confirmar */
  echo("                          <input class=\"input\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral, 7)."\" />\n");  
  echo("                      </td>\n");

  echo("                    </tr>\n");
/*
  status G: Define os usuários com permissão de escrita e leitura no forum
  status R: Define os usuários com permissão de escrita no forum. Todos têm permissão de leitura.
  107 - Definir os alunos com permissão de Escrita e Leitura
  108 - Definir os alunos com permissão de Escrita (a Leitura é aberta para todos)
*/

  echo("                    <tr>\n");
  echo("                      <td style=\"border:0pt;\" colspan=\"2\"></td>\n");
  echo("                      <td style=\"border:0pt;\">\n");
  /* 133 - Retirar */
  echo("                          <input class=\"input\" type=\"button\" value=\"".RetornaFraseDalista($lista_frases,133)."\" onclick='remover();' />\n");

  /* 134 - Esvaziar */
  echo("                          <input class=\"input\" type=\"button\" value=\"".RetornaFraseDalista($lista_frases,134)."\" onclick='esvaziar();' />\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  echo("                    <tr>\n");
  echo("                      <td style=\"border:0pt;\" colspan=\"3\">\n");

  echo("                      </td>\n");
  echo("                    </tr>\n");
  echo("                  </table>\n");
  echo("                </form>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
