<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/agenda/importar_agenda2.php

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
  ARQUIVO : cursos/aplic/agenda/importar_agenda2.php
  ========================================================== */
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include($bibliotecas."importar.inc");  
  include("material.inc");

  include("../topo_tela.php");

  // **************** VARI�VEIS DE ENTRADA ****************
  // Recebe de 'importar_agenda.php'
  //    c�digo do curso
  $cod_curso = $_GET['cod_curso'];
  //    c�digo do curso do qual itens ser�o importados
  $cod_curso_import = $_POST['cod_curso_import'];
  //    tipo do curso: A(ndamento), I(nscri��es abertas), L(atentes),

  $cod_categoria = $_POST['cod_categoria'];
  //  E(ncerrados)
  $tipo_curso = $_POST['tipo_curso'];
  if ('E' == $tipo_curso)
  {
    //  per�odo especificado para listar os cursos
    //  encerrados.
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
  }
  //  booleano, se o curso, cujos itens ser�o importados, foi
  //  escolhido na lista de cursos compartilhados.
  $curso_compartilhado = $_POST['curso_compartilhado'];
  //    booleando, se o curso, cujos itens ser�o importados, � um
  //  curso extra�do.
  $curso_extraido = $_POST['curso_extraido'];
  $cod_itens_import = $_POST['cod_itens_import'];
  
  // ******************************************************

  session_register("login_import_s");
  if (isset($login_import))
    $login_import_s = $login_import;
  else
    $login_import = $_SESSION['login_import_s'];

  Desconectar($sock);
  $sock = Conectar("");

  $lista_frases = RetornaListaDeFrases($sock, $cod_ferramenta);
  $lista_frases_geral = RetornaListaDeFrases($sock,-1);
  $lista_frases_biblioteca = RetornaListaDeFrases($sock,-2);

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  echo("    <script type=\"text/javascript\" language=\"JavaScript\">\n");
  echo("      function Iniciar(){\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("    </script>\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  // P�gina Principal
  // 1 - "Material"
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1));
  /*107 - Importando "Material" */
  $cabecalho.= (" - ".RetornaFraseDaLista($lista_frases,107)."</h4>\n");
  echo($cabecalho);

  /*Voltar*/			
  echo("          <span class=\"btsNav\" onClick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  if (!isset($cod_curso_import))
  {
    echo("        <br />\n");
    echo("        <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("          <tr>\n");
    echo("            <td>\n");
    // 23 - Voltar (geral)
    echo("              <ul class=\"btAuxTabs\">\n");
    echo("                <li><<a href=\"importar_curso.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta.";\">".RetornaFraseDaLista($lista_frases_geral, 23)."</a></li>\n");
    echo("              </ul>\n");
    echo("            </td>\n");
    echo("          </tr>\n");
    echo("          <tr>\n");
    echo("            <td>\n");
    echo("              <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("                <tr>\n");
    echo("                  <td>\n");
    /* 51(biblioteca): Erro ! Nenhum c�digo de curso para importa��o foi recebido ! */
    echo("                    ".RetornaFraseDaLista($lista_frases_biblioteca,51)."\n");
    echo("                  </td>\n");
    echo("                </tr>\n");
    echo("        </td>\n");
    echo("      <tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");
    exit();
  }

  // Se o curso DO QUAL ser�o importados itens foi montado
  // na base tempor�ria, ent�o define o par�metro $opt para
  // conex�o a ela.
  if ($curso_extraido)
    $opt = TMPDB;
  else
    $opt = "";

  // Autentica no curso PARA O QUAL ser�o importados os itens.
  $cod_usuario = VerificaAutenticacao($cod_curso);

  // Se o curso foi selecionado na lista de todos cursos e
  // a autentica��o do usu�rio nesse curso n�o � v�lida ent�o
  // encerra a execu��o do script.
  if ((!$curso_compartilhado) &&
      (false === ($cod_usuario_import = UsuarioEstaAutenticadoImportacao($cod_curso, $cod_usuario, $cod_curso_import, $opt))))
  {
    // Testar se � identicamente falso,
    // pois 0 pode ser um valor v�lido para cod_usuario
    echo("          <script language=\"javascript\" type=\"text/javascript\">\n\n");
    echo("            function ReLogar()\n");
    echo("            {\n");
    // 52(biblioteca) - Login ou senha inv�lidos
    echo("              alert(\"".RetornaFraseDaLista($lista_frases_biblioteca, 52)."\");\n");
    echo("              document.frmRedir.submit();\n");
    echo("            }\n\n");

    echo("          </script>\n\n");

    echo("          <form method=\"post\" name=\"frmRedir\" action=\"importar_curso.php\">\n");
    echo("            <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
    echo("            <input type=\"hidden\" name=\"cod_categoria\" value=\"".$cod_categoria."\">\n");
    echo("            <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz_import."\">\n");
    echo("            <input type=\"hidden\" name=\"cod_ferramenta\" value=\"".$cod_ferramenta."\">\n");
    echo("          </form>\n");
    echo("          <script type=\"text/javascript\" language=\"JavaScript\">\n\n");
    echo("            ReLogar();\n");
    echo("          </script>\n\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");
    exit();
  }

  $sock = Conectar("");

  // Marca data de �ltimo acesso ao curso tempor�rio. Esse recurso � importante
  // para elimina��o das bases tempor�rias, mediante compara��o dessa data adicionado
  // um per�odo de folga com a data em que o script para elimina��o estiver rodando.
  MarcarAcessoCursoExtraidoTemporario($sock, $cod_curso_import);

  // Se o curso foi montado (extra�do) lista os arquivos do caminho
  // tempor�rio.
  if ($curso_extraido)
    $diretorio_arquivos_origem = RetornaDiretorio($sock, 'Montagem');
  else
    $diretorio_arquivos_origem = RetornaDiretorio($sock, 'Arquivos');

  // Raiz do diret�rio de arquivos do curso PARA O QUAL ser�o importados
  // os itens.
  $diretorio_arquivos_destino = RetornaDiretorio($sock, 'Arquivos');
  $diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');
  
  Desconectar($sock);

  // Conecta-se � base do curso.
  $sock = Conectar($cod_curso_import, $opt);

  // Obt�m o nome do curso.
  $nome_curso_import = NomeCurso($sock, $cod_curso_import);

  // Se o curso n�o foi selecionado na lista de todos cursos,
  // verifica as permiss�es de acesso ao curso e �s ferramentas.
  if (!$curso_compartilhado)
  {
    VerificaAcessoAoCurso($sock, $cod_curso_import, $cod_usuario_import);
    VerificaAcessoAFerramenta($sock, $cod_curso_import, $cod_usuario_import, $cod_ferramenta);
  }

  Desconectar($sock);
    
  switch ($cod_ferramenta) {
    case 3 :
      $tabela="Atividade";
      $dir="atividades";
      break;
    case 4 :
      $tabela="Apoio";
      $dir="apoio";
      break;
    case 5 :
      $tabela="Leitura";
      $dir="leituras";
      break;
    case 7 :
      $tabela="Obrigatoria";
      $dir="obrigatoria";
      break;
  }

  /*Importa os materiais selecionados*/
      
  if (ImportarMateriais($cod_curso, $cod_topico_raiz_import, $cod_usuario,
                        $cod_curso_import, $curso_extraido, $curso_compartilhado,
                        $cod_topicos_import, $cod_itens_import, $tabela,
                        $dir, $diretorio_arquivos_destino, $diretorio_arquivos_origem))
    $sucesso = true;
  else
    $sucesso = false;

  echo("          <form method=\"post\" name=\"frmImportar\" action=\"importar_agenda3.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."\">\n");
  echo("            <input type=\"hidden\" name=\"cod_categoria\" value=\"".$cod_categoria."\">\n");
  echo("            <input type=\"hidden\" name=\"cod_curso_import\" value=\"".$cod_curso_import."\">\n");
  echo("            <input type=\"hidden\" name=\"curso_extraido\" value=\"".$curso_extraido."\">\n");
  echo("            <input type=\"hidden\" name=\"curso_compartilhado\" value=\"".$curso_compartilhado."\">\n");
  echo("            <input type=\"hidden\" name=\"tipo_curso\" value=\"".$tipo_curso."\">\n");

  if ('E' == $tipo_curso)
  {
    echo("            <input type=\"hidden\" name=\"data_inicio\" value=\"".$data_inicio."\">\n");
    echo("            <input type=\"hidden\" name=\"data_fim\" value=\"".$data_fim."\">\n");
  }

  echo("            <input type=\"hidden\" name=\"sucesso\" value=\"".$sucesso."\">\n");
  echo("          </form>\n");
  echo("          <script type=\"text/javascript\" language=\"JavaScript\">\n\n");
  echo("            document.frmImportar.submit();\n");
  echo("          </script>\n\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");

  echo("  </body>\n");
  echo("</html>\n");

?>
