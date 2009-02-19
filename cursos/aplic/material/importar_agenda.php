<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/agenda/importar_agenda.php

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
  ARQUIVO : cursos/aplic/agenda/importar_agenda.php
  ========================================================== */
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include($bibliotecas."importar.inc");
  include("material.inc");

  include("../topo_tela.php");

  // **************** VARI�VEIS DE ENTRADA ****************
  // Recebe de 'importar_curso2.php'
  //    c�digo do curso
  $cod_curso = $_GET['cod_curso'];
  //    c�digo da categoria que estava sendo listada.
  $cod_categoria = $_POST['cod_categoria'];
  //    c�digo do curso do qual itens ser�o importados
  $cod_curso_import = $_POST['cod_curso_import'];
  //    c�digo da ferramenta cujos itens ser�o importados
  $cod_ferramenta = $_GET['cod_ferramenta'];
  //    tipo do curso: A(ndamento), I(nscri��es abertas), L(atentes),
  //  E(ncerrados)
  $tipo_curso = $_POST['tipo_curso'];
  if ('E' == $tipo_curso)
  {
    //  per�odo especificado para listar os cursos
    //  encerrados.
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
  }
  //    booleano, se o curso, cujos itens ser�o importados, foi
  //  escolhido na lista de cursos compartilhados.
  $curso_compartilhado = $_POST['curso_compartilhado'];
  //    booleando, se o curso, cujos itens ser�o importados, � um
  //  curso extra�do.
  $curso_extraido = $_POST['curso_extraido'];

  if (!isset($_POST['cod_topico_raiz_import']))
    $cod_topico_raiz_import = 1;
  else
    $cod_topico_raiz_import = $_POST['cod_topico_raiz_import'];
  // ******************************************************

  session_register("login_import_s");
  if (isset($login_import))
    $login_import_s = $login_import;
  else
    $login_import = $_SESSION['login_import_s'];

  Desconectar($sock);
  $sock = Conectar("");

  $lista_frases_biblioteca = RetornaListaDeFrases($sock,-2);

  Desconectar($sock);

  $sock = Conectar($cod_curso);

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

  echo("    <script type=\"text/javascript\" language=\"JavaScript\" defer>\n\n");

  echo("      function ExibirItem(cod_item)\n");
  echo("       {\n");
  echo("         document.frmImportar.cod_item.value = cod_item;\n");
  echo("         document.frmImportar.action = \"importar_ver.php?\";");
  echo("         document.frmImportar.submit();\n");
  echo("       }\n\n");

  echo("       function Validacheck()\n");
  echo("       {\n");
  echo("         var cont = false;\n");
  echo("         var nome_var1 = 'cod_topicos_import[]';\n");
  echo("         var nome_var2 = 'cod_itens_import[]';\n");
  echo("         var e;\n");

  echo("         for (i = 0, total = document.getElementsByTagName('input').length; ((i < total) && (cont == false)); i++)\n");
  echo("         {\n");
  echo("           e = document.getElementsByTagName('input')[i];\n");
  echo("           if ((e.type == 'checkbox') && ((e.name == nome_var1) || (e.name == nome_var2)) && (e.checked == true))\n");
  echo("           {\n");
  echo("             cont = true;\n");
  echo("           }\n");
  echo("         }\n");

  echo("         if (cont == true)\n");
  echo("           return true;\n");
  echo("         else\n");
  echo("         {\n");
  /*58(biblioteca) - Selecione pelo menos um item*/
  echo("           alert('".RetornaFraseDaLista($lista_frases_biblioteca,58)."');\n");
  echo("           return false;\n");
  echo("         }\n");
  echo("       }\n");
 

  echo("       function Importar()\n");
  echo("       {\n");
  echo("         if(Validacheck())\n");
  echo("         {\n");
  echo("           document.frmImportar.action = \"importar_agenda2.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."\";\n");
  echo("           document.frmImportar.submit();\n");
  echo("         }\n");
  echo("       }\n\n");

  echo("       function CancelarImportacao()\n");
  echo("       {\n");
  echo("         window.location='material.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."';\n");
  echo("       }\n\n");

  echo("       function CheckAll()\n");
  echo("       {\n");
  echo("         var elem = document.frmImportar.elements;\n");
  echo("         var nome_var1 = 'cod_topicos_import[]';\n");
  echo("         var nome_var2 = 'cod_itens_import[]';\n");
  echo("         var nome_var_all = 'select_all';\n");
  echo("         var changed = false;\n\n");

  echo("         var i=0;\n\n");

  echo("         while (i < elem.length)\n");
  echo("         {\n");
  echo("           if (elem[i].name == nome_var_all)\n");
  echo("             changed = elem[i].checked;\n");
  echo("           else if ((elem[i].name == nome_var1) || (elem[i].name == nome_var2))\n");
  echo("             elem[i].checked = changed;\n");
  echo("           i++;\n");
  echo("         }\n");
  echo("       }\n\n");

  echo("       function MudarTopico(cod_topico){\n");
  echo("         document.frmImportar.action = \"importar_agenda.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."\";\n");
  echo("         document.frmImportar.cod_topico_raiz_import.value = cod_topico;\n");
  echo("         document.frmImportar.submit();\n");
  echo("       }\n");

  echo("       function ExibirItem(cod_item){\n");
  echo("         document.frmImportar.cod_item.value = cod_item;\n");
  echo("         document.frmImportar.action = \"importar_ver.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."\";\n");
  echo("         document.frmImportar.submit();\n");
  echo("       }\n");


  echo("      var cod_avaliacao=\"\";\n");

  echo("      function Iniciar(){\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("     </script>\n\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
    
  // P�gina Principal
  // 1 - "Material"
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1));
  /*107 - Importando "Material" */
  $cabecalho.= (" - ".RetornaFraseDaLista($lista_frases,107)."</h4>\n");
  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  if (!isset($cod_curso_import))
  {
    echo("        <br />\n");
    echo("        <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("          <tr>\n");
    echo("            <td>\n");
    // 23 - Voltar (geral)
    echo("              <ul class=\"btAuxTabs\">\n");
    echo("                <li><a href=\"importar_curso.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta.";\">".RetornaFraseDaLista($lista_frases_geral, 23)."</a></li>\n");
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

  if ($curso_extraido)
    $opt = TMPDB;
  else
    $opt = "";

  // Autentica no curso PARA O QUAL ser�o importados os itens.
  $cod_usuario = VerificaAutenticacao($cod_curso);

  if ((!$curso_compartilhado) &&
      (false === ($cod_usuario_import = UsuarioEstaAutenticadoImportacao($cod_curso, $cod_usuario, $cod_curso_import, $opt))))
  {

    // Testar se � identicamente falso,
    // pois 0 pode ser um valor v�lido para cod_usuario
    echo("          <script type=\"text/javascript\" language=\"JavaScript\" defer>\n\n");
    echo("            function ReLogar()\n");
    echo("            {\n");
    // 52(biblioteca) - Login ou senha inv�lidos
    echo("              alert(\"".RetornaFraseDaLista($lista_frases_biblioteca, 52)."\");\n");
    echo("              document.frmRedir.submit();\n");
    echo("            }\n\n");

    echo("          </script>\n\n");

    echo("          <form method=\"post\" name=\"frmRedir\" action=\"importar_curso.php\">\n");
    echo("            <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
    echo("            <input type=\"hidden\" name=\"cod_categoria\" value=\"".$cod_categoria."\" />\n");
    echo("            <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz_import."\" />\n");
    echo("            <input type=\"hidden\" name=\"cod_ferramenta\" value=\"".$cod_ferramenta."\" />\n");
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

  
  if ($curso_extraido)
  {
    $diretorio_arquivos=RetornaDiretorio($sock, 'Montagem');
  }
  else
  {
    $diretorio_arquivos=RetornaDiretorio($sock, 'Arquivos');
  }
  
  $diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');

  Desconectar($sock);

  // Alterna para a base de dados do curso
  $sock = Conectar($cod_curso);

  $data_acesso = PenultimoAcesso($sock, $cod_usuario, "");

  Desconectar($sock);

  $sock = Conectar($cod_curso_import, $opt);

  $nome_curso_import = NomeCurso($sock, $cod_curso_import);

  if (!$curso_compartilhado)
  {
    VerificaAcessoAoCurso($sock, $cod_curso_import, $cod_usuario_import);
    VerificaAcessoAFerramenta($sock,$cod_curso_import, $cod_usuario_import, $cod_ferramenta);
  }

  // Apaga link simbolico que por acaso tenha sobrado daquele usuario
  $link_arquivo = $diretorio_temp."/".$dir."_".$cod_curso_import."_".$cod_usuario_import;
  if (ExisteArquivo($link_arquivo))
  {
    RemoveArquivo($link_arquivo);
  }

  
  echo("\n");

  if (isset($caminho_original))
  {
    // 108 - Importando para:
    echo("          <font class=text>".RetornaFraseDaLista($lista_frases,108)." </font>");
    echo($caminho_original);
    echo("          <br />\n");
  }


  /*Voltar*/			
  echo("          <span class=\"btsNav\" onClick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span>\n");

  $lista_topicos_ancestrais = RetornaTopicosAncestrais($sock, $tabela, $cod_topico_raiz_import);
  unset($path);

  foreach ($lista_topicos_ancestrais as $cod => $linha)
  {
    if ($cod_topico_raiz_import != $linha['cod_topico']){
      $path = "<span class=\"link\" onClick='MudarTopico(".$linha['cod_topico'].")'>".$linha['topico']."</span> &gt;&gt; ".$path;
    }
    else
    {
      $path = "<b>".$linha['topico']."</b><br />\n";
    }
  }
  
  echo($path);


  echo("          <form method=\"post\" name=\"frmImportar\">\n");
  echo("          <input type=\"hidden\" name=\"cod_categoria\" value=\"".$cod_categoria."\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_curso_import\" value=\"".$cod_curso_import."\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_topico_raiz_import\" value=\"".$cod_topico_raiz_import."\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_item\" value=''>\n");
  echo("          <input type=\"hidden\" name=\"curso_compartilhado\" value=\"".$curso_compartilhado."\" />\n");
  echo("          <input type=\"hidden\" name=\"curso_extraido\" value=\"".$curso_extraido."\" />\n");
  echo("          <input type=\"hidden\" name=\"tipo_curso\" value=\"".$tipo_curso."\" />\n");

  if ('E' == $tipo_curso)
  {
    echo("          <input type=\"hidden\" name=\"data_inicio\" value=\"".$data_inicio."\" />\n");
    echo("          <input type=\"hidden\" name=\"data_fim\" value=\"".$data_fim."\" />\n");
  }

  echo("        <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("          <tr>\n");
  echo("            <td valign=\"top\">\n");
  echo("              <ul class=\"btAuxTabs\">\n");
  /* 2 - Cancelar (geral) */
  echo("                <li><span onClick='CancelarImportacao()'>".RetornaFraseDaLista($lista_frases_geral,2)."</span></li>\n");
  echo("              </ul>\n");
  echo("            </td>\n");
  echo("          </tr>\n");
  echo("          <tr>\n");
  echo("            <td>\n");
  echo("              <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                <tr class=\"head\">\n");
  echo("                  <td align=center><b><input type=\"checkbox\" name=\"select_all\" onClick=\"CheckAll()\"></td>\n");
  // 114 - "Materiais" do Curso:
  echo("                  <td align=left><b>".RetornaFraseDaLista($lista_frases,114)." ".$nome_curso_import."</b></td>\n");
  /* 13 - Data */
  echo("                  <td align=center><b>".RetornaFraseDaLista($lista_frases,13)."</b></td>\n");
  /* 14 - Data */
  echo("                  <td align=center><b>".RetornaFraseDaLista($lista_frases,14)."</b></td>\n");
  echo("                </tr>\n");

  /*verificar status... confirmar.. e verificar c eh necessario cancelar a edicao!*/
  $lista_topicos=RetornaTopicosDoTopico($sock, $tabela, $cod_topico_raiz_import);
  $lista_itens=RetornaItensDoTopico($sock, $tabela, $cod_topico_raiz_import);


    if (((count($lista_topicos)<1)||($lista_topicos=="")) && ((count($lista_itens)<1)||($lista_itens=="")))
    {
      echo("<tr>\n");
      /* 15 - 3: Nao ha nenhuma atividade
                4: Nao ha nenhum material de apoio
                5: Nao ha nenhuma leitura
                7: Nao ha nenhuma parada obrigatória
        */
      echo("  <td colspan=\"5\" align=\"center\">".RetornaFraseDaLista($lista_frases,15)."</td>\n");
      echo("</tr>\n");
    }
    else
    {

    $top_index = 0;
    $itens_index = 0;
    for($i=0; $i<((count($lista_topicos))+(count($lista_itens))); $i++){
      if((!isset($lista_topicos[$top_index]['posicao_topico'])) || (isset($lista_itens[$itens_index]['posicao_item']) &&($lista_topicos[$top_index]['posicao_topico'] > $lista_itens[$itens_index]['posicao_item']))) {
        $lista_unificada[$i] = $lista_itens[$itens_index];
        $itens_index++;
      }else{
        //este if é para não alterar a estrutura dos portfólios antigos
        if((isset($lista_itens[$top_index]['posicao_item'])) && ($lista_topicos[$top_index]['posicao_topico'] == $lista_itens[$itens_index]['posicao_item'])) {
          $lista_itens[$itens_index]['posicao_item']++;
        }
        $lista_unificada[$i] = $lista_topicos[$top_index];
        $top_index++;
      }
    }

    foreach($lista_unificada as $cod => $linha){
      //se é tópico...
      if(isset($linha['posicao_topico'])){

        $data=UnixTime2Data($linha['data']);
        $max_data=RetornaMaiorData($sock,$tabela,$linha['cod_topico'],'F',$linha['data']);
        if ($data_acesso<$max_data)
        {
          $marcatr=" class=\"novoitem\"";
        }
        else
        {
          $marcatr="";
        }
        
        echo("<tr".$marcatr."  id=\"tr_top_".$linha['cod_topico']."\">\n");

        echo("<td width=\"2%\">\n");
        echo("<input type=\"checkbox\" id=\"chktop_".$linha['cod_topico']."\" name=\"cod_topicos_import[]\" value=\"".$linha['cod_topico']."\" />\n");
        echo("</td>\n");
        echo("<td width=\"72%\" class=\"alLeft\"><img src=\"../imgs/pasta.gif\" border=0 alt=\"\">&nbsp;&nbsp;<span class=\"link\" onClick=\"MudarTopico('".$linha['cod_topico']."');\">".$linha['topico']."</span></td>\n");
        
        echo("<td width=\"8%\" align=center>".$data."</td>\n"); //dani
        echo("<td width=\"10%\">&nbsp;</td>\n");


        echo("</tr>\n");
      } //é item

      else if( isset($linha['posicao_item'])){ 

        $data=UnixTime2Data($linha['data']);
          if ($linha['tipo_compartilhamento']=="T")
          {
            /* 16 - Totalmente Compartilhado */
            $compartilhamento=RetornaFraseDaLista($lista_frases,16);
          }
          else
          {
            /* 17 - Compartilhado com Formadores */
            $compartilhamento=RetornaFraseDaLista($lista_frases,17);
          }
          if ($data_acesso<$linha['data'])
          {
            $marcatr=" class=\"novoitem\"";
          }
          else
          {
            $marcatr="";
          }

              if ($linha['status']=="E") {
                $linha_historico=RetornaUltimaPosicaoHistorico($sock, $tabela, $linha['cod_item']);
                if ($linha['inicio_edicao']<(time()-1800) || $cod_usuario==$linha_historico['cod_usuario'])
                {
                  CancelaEdicao($sock, $tabela, $dir, $linha['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp,$criacao_avaliacao);
    
                  $titulo="<img src=\"../imgs/arqp.gif\" border=0 alt=\"\">&nbsp;&nbsp;<span class=\"link\" onClick=\"ExibirItem('".$linha['cod_item']."');\">".$linha['titulo']."</span>";
                }
                else
                {
                  /* 18 - Em Edicao */
                  $data="<span class=\"link\" onClick=\"window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_item=".$linha['cod_item']."&origem=material&cod_ferramenta=".$cod_ferramenta."','EmEdicao','width=300,height=220,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">".RetornaFraseDaLista($lista_frases,18)."</a>";

                  $titulo="<img src=\"../imgs/arqp.gif\" border=0 alt=\"\">&nbsp;&nbsp;".$linha['titulo'];
    
                }
              }
              else
              {

                $titulo="<img src=\"../imgs/arqp.gif\" border=0 alt=\"\">&nbsp;&nbsp;<span class=\"link\" onClick=\"ExibirItem('".$linha['cod_item']."');\">".$linha['titulo']."</span>";
              }

            echo("  <tr".$marcatr." id=\"tr_".$linha['cod_item']."\">\n");
            echo("    <td width=\"2%\"><input type=\"checkbox\" id=\"chkitm_".$linha['cod_item']."\" name=\"cod_itens_import[]\" value=\"".$linha['cod_item']."\" /></td>\n");
            echo("    <td width=\"72%\" class=\"alLeft\">".$titulo."</td>\n");
            echo("    <td width=\"8%\" align=\"center\"><span id=\"data_".$linha['cod_item']."\">".$data."</span></td>\n");
            echo("    <td width=\"10%\" align=\"center\">".$compartilhamento."</td>\n");
            echo("</tr>\n");

        } //else
      }
    } // else - count(lista_topicos), count(lista_itens)



  
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 54(biblioteca) - Importar Selecionados */
  echo("                  <li><span onClick='Importar()'>".RetornaFraseDaLista($lista_frases_biblioteca, 54)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n"); 
  echo("          </table>\n");
  echo("          <br>\n");
 
  echo("          </form>\n");
  
  echo("        </td>\n");
  echo("      </tr>\n");
include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
