<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/agenda/importar_ver.php

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
  ARQUIVO : cursos/aplic/agenda/importar_ver.php
  ========================================================== */
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include($bibliotecas."importar.inc");
  include("agenda.inc");

  // **************** VARI�VEIS DE ENTRADA ****************
  // Recebe de 'importar_curso2.php'
  //    c�digo do curso
  $cod_curso = $_POST['cod_curso'];
  //    c�digo da categoria que estava sendo listada.
  $cod_categoria = $_POST['cod_categoria'];
  //    c�digo do curso do qual itens ser�o importados
  $cod_curso_import = $_POST['cod_curso_import'];
  //    tipo do curso: A(ndamento), I(nscri��es abertas), L(atentes),
  //  E(ncerrados)
  $tipo_curso = $_POST['tipo_curso'];
  if ('E' == $tipo_curso)
  {
    //    per�odo especificado para listar os cursos
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
  //    c�digo do t�pico do curso do qual itens ser�o importados.
  $cod_topico_raiz_import = $_POST['cod_topico_raiz_import'];

  // ******************************************************
  $sock=Conectar("");
  $lista_frases_biblioteca=RetornaListaDeFrases($sock,-2);
  if ($curso_extraido)
    $diretorio_arquivos=RetornaDiretorio($sock, 'Montagem');
  else
    $diretorio_arquivos=RetornaDiretorio($sock, 'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock, 'ArquivosWeb');
  Desconectar($sock);

  $cod_ferramenta=1;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=7;
  include("../topo_tela.php");


  $tabela="Agenda";
  $dir="agenda";


//   echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmllib.js\"></script>\n");
  echo("          <script type=\"text/javascript\" src=\"../bibliotecas/javacrypt.js\" defer></script>\n");
  echo("          <script type=\"text/javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");

  echo("          <script type=\"text/javascript\" defer>\n\n");

  echo("            function Iniciar()\n");
  echo("            {\n");
  echo("              startList();\n");
  echo("            }\n\n");
  
  echo("            function WindowOpenVer(id)\n");
  echo("            {\n");
  echo("              popup = window.open('".$dir_item_temp['link']."'+id,'Agenda".$cod_ferramenta."','top=50,left=100,width=600,height=400,resizable=yes,menubar=yes,status=yes,toolbar=yes,scrollbars=yes');\n");
  echo("              popup.focus();\n");
  echo("            }\n\n");

  echo("            function WindowOpenVerURL(end)\n");
  echo("            {\n");
  echo("              popup2 = window.open(end,'MaterialURL".$cod_ferramenta."','top=50,left=100,width=600,height=400,resizable=yes,menubar=yes,status=yes,toolbar=yes,scrollbars=yes');\n");
  echo("              popup2.focus();\n");
  echo("            }\n\n");
  
  echo("            function BtnVoltarClick()\n");
  echo("            {\n");  
  echo("              document.frmImportar.action = 'importar_agenda.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=1';");
  echo("              document.frmImportar.submit();\n");
  echo("            }\n\n");

  echo("          </script>\n\n");
  
  include("../menu_principal.php");
  Desconectar($sock);
  
  /* Forms */
  echo("    <form method=\"post\" name=\"frmImportar\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
  echo("      <input type=\"hidden\" name=\"cod_categoria\" value=\"".$cod_categoria."\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso_import\" value=\"".$cod_curso_import."\">\n");
  echo("      <input type=\"hidden\" name=cod_usuario value=\"".$cod_usuario."\">\n");
  echo("      <input type=\"hidden\" name=\"cod_item\" value=''>\n");
  echo("      <input type=\"hidden\" name=\"curso_compartilhado\" value=\"".$curso_compartilhado."\">\n");
  echo("      <input type=\"hidden\" name=\"curso_extraido\" value=\"".$curso_extraido."\">\n");

  echo("      <input type=\"hidden\" name=\"tipo_curso\" value=\"".$tipo_curso."\">\n");
  if ('E' == $tipo_curso)
  {
    echo("      <input type=\"hidden\" name=\"data_inicio\" value='".$data_inicio."'>\n");
    echo("      <input type=\"hidden\" name=\"data_fim\" value='".$data_fim."'>\n");
  }
  echo("    </form>\n");
  
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
    
  // P�gina Principal
  // 1 - Agenda
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1));
  /*66 - Importando Agenda */
  $cabecalho.= (" - ".RetornaFraseDaLista($lista_frases,66)."</h4>\n");
  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/			
  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");
  
  $sock = Conectar($cod_curso_import);

  $nome_curso_import = NomeCurso($sock, $cod_curso_import);

  if (!$curso_compartilhado)
  {
    VerificaAcessoAoCurso($sock, $cod_curso_import, $cod_usuario);
    VerificaAcessoAFerramenta($sock,$cod_curso_import, $cod_usuario, $cod_ferramenta);
  }

  // Verifica��o se o item est� em Edi��o
  // Se estiver, voltar a tela anterior, e disparar a tela de Em Edi��o...
  $linha = RetornaUltimaPosicaoHistorico ($sock, $cod_item);

  if ($linha['acao']=="E")
  {
    if (($linha['data']<(time()-1800)) || ($cod_usuario == $linha['cod_usuario'])){
      AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 0);
    }else{
      /* Está em edição... */
      echo("          <script type=\"text/javascript\">\n");
      echo("            window.open('em_edicao.php?&cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=importar_ver','EmEdicao','width=400,height=250,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
      echo("            BtnVoltarClick();\n");
      echo("          </script>\n");
      echo("        </td>\n");
      echo("      </tr>\n");
      echo("    </table>\n");
      echo("  </body>\n");
      echo("</html>\n");
      exit();
    }
  }

  $dir_item_temp = CriaLinkVisualizar($sock, $dir, $cod_curso_import, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

  if (isset($caminho_original))
  {
    // 88 - Importando para:
    echo("          ".RetornaFraseDaLista($lista_frases,88));
    echo($caminho_original);
    echo("          <br />\n");
  }

  /* Tabela Externa */
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");		
   /*23 - (Geral) Voltar*/
  echo("                  <li><span onclick=\"BtnVoltarClick();\">".RetornaFraseDaLista($lista_frases_geral, 23)."</span></li>\n");
  echo("                </ul>\n");	
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");		
  /* Tabela Interna */	
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /*18 - Titulo */
  echo("                    <td class=\"alLeft\" align=\"left\" width=\"82%\">".RetornaFraseDaLista($lista_frases, 18)."</td>\n");
  /*7 - Data */
  echo("                    <td  align=\"center\">".RetornaFraseDaLista($lista_frases, 7)."</td>\n");
  echo("                  </tr>\n");
  
  /* Conteudo */
  
  $linha_item = RetornaDadosDoItem($sock, $cod_item);

  $icone="<img src=\"../figuras/arqp.gif\" border=\"0\" /> ";
  $titulo=$linha_item['titulo'];

  $data = UnixTime2DataHora($linha_item['data']);

  echo("                  <tr>\n");
  echo("                    <td class=\"itens\">".$icone.$titulo."</td>\n");
  echo("                    <td class=\"itens\" align=\"center\">".$data."</td>\n");
  echo("                  </tr>\n");	

  /*Verifica se ha arquivo de entrada*/
  $arquivo_entrada="";
  $lista_arq=RetornaArquivosAgendaVer($cod_curso_import, $dir_item_temp['diretorio']);
  if (count($lista_arq)>0)
    foreach($lista_arq as $cod => $linha1)
      if ($linha1['Status'] && $linha1['Arquivo']!="")
        $arquivo_entrada = $dir_item_temp['link'].ConverteUrl2Html($linha1['Diretorio']."/".$linha1['Arquivo']);

  /*Se houver, cria um iframe para exibi-lo*/
  if(($linha_item['texto']=="")&&($arquivo_entrada!=""))
    $conteudo="<span id=\"text_".$linha_item['cod_item']."\"><iframe id=\"iframe_ArqEntrada\" texto=\"ArqEntrada\" src=\"".$arquivo_entrada."\" width=\"100%\" height=\"400\" frameBorder=\"0\" scrolling=\"Auto\"></iframe></span>"; 
  /*Senaum, exibe o texto da agenda*/
  else
    $conteudo="<span id=\"text_".$linha_item['cod_item']."\">".AjustaParagrafo($linha_item['texto'])."</span>";

  if(($linha_item['texto']!="")||($arquivo_entrada!=""))
  {
    echo("                  <tr class=\"head\">\n");
    /* 91 - Conteudo  */
    echo("                    <td colspan=\"4\" align=\"left\">".RetornaFraseDaLista($lista_frases,91)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"4\">\n");
    echo("                      <div class=\"divRichText\">\n");
    echo("                        ".$conteudo."\n");
    echo("                      </div>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }
   
  $lista_arq = RetornaArquivosAgendaVer($cod_curso_import, $dir_item_temp['diretorio']);

  if(count($lista_arq)>0){
        echo("                  <tr class=\"head\">\n");
        /* 57(biblioteca) - Arquivos */
        echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases_biblioteca,57)."</td>\n");
        echo("                  </tr>\n");    
    
        $conta_arq=0;

        echo("                  <tr>\n");
        echo("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
        // Procuramos na lista de arquivos se existe algum visivel
        $ha_visiveis = true;

        while (( list($cod, $linha) = each($lista_arq) ) && !$ha_visiveis)
        {
          if ($linha[Arquivo] != "")
            $ha_visiveis = !($linha['Status']);
        }
 
        if (($ha_visiveis))
        {
          $nivel_anterior=0;
          $nivel=-1;

          foreach($lista_arq as $cod => $linha)
          {
            if (!($linha['Arquivo']=="" && $linha['Diretorio']==""))
              if ((!$linha['Status'])||($linha['Status']))
              {
                $nivel_anterior=$nivel;
                $espacos="";
                $espacos2="";
                $temp=explode("/",$linha['Diretorio']);
                $nivel=count($temp)-1;
                for ($c=0;$c<=$nivel;$c++){
                  $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                  $espacos2.="  ";
                }

                $caminho_arquivo = $dir_item_temp['link'].ConverteUrl2Html($linha['Diretorio']."/".$linha['Arquivo']);

                if ($linha[Arquivo] != "")
                {

                  if ($linha['Diretorio']!=""){
                    $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                    $espacos2.="  ";
                  }


                  if ($linha['Status']) $arqOculto="arqOculto='sim'";
                  else $arqOculto="arqOculto='nao'";

                  if (eregi(".zip$",$linha['Arquivo']))
                  {
                    // arquivo zip
                    $imagem    = "<img alt=\"\" src=\"../imgs/arqzip.gif\" border=\"0\" />";
                    $tag_abre  = "<span class=\"link\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".$caminho_arquivo."');\" tipoArq=\"zip\" nomeArq=\"".htmlentities($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqOculto.">";
                  }
                  else
                  {
                    // arquivo comum
                    $imagem    = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
                    $tag_abre  = "<span class=\"link\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".$caminho_arquivo."');\" tipoArq=\"comum\" nomeArq=\"".htmlentities($caminho_arquivo)."\" ".$arqOculto.">";
                  }

                  $tag_fecha = "</span>";

                  echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
                  echo("                          ".$espacos2.$espacos.$imagem.$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha[Tamanho]/1024),2)."Kb)");

                  echo("<span id=\"local_oculto_".$conta_arq."\">");
                  if ($linha['Status']) 
                    // 59 - Entrada
                      echo("<span id=\"arq_oculto_".$conta_arq."\">- <span style='color:red;'>".RetornaFraseDaLista($lista_frases,59)."</span></span>");
                  echo("</span>\n");
                  echo("                          ".$espacos2."<br />\n");
                  echo("                        ".$espacos2."</span>\n");
                }
                else{
                  if ($nivel_anterior>=$nivel){
                    $i=$nivel_anterior-$nivel;
                    $j=$i;
                    $espacos3="";
                    do{
                      $espacos3.="  ";
                      $j--;
                    }while($j>=0);
                    do{
                      echo("                      ".$espacos3."</span>\n");
                      $i--;
                    }while($i>=0);
                  }
                  // pasta
                  $imagem    = "<img alt=\"\" src=\"../imgs/pasta.gif\" border=\"0\" />";
                  echo("                      ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
                  echo("                        ".$espacos2."<span class=\"link\" id=\"nomeArq_".$conta_arq."\" tipoArq=\"pasta\" nomeArq=\"".htmlentities($caminho_arquivo)."\"></span>\n"); 
                  echo("                        ".$espacos2.$espacos.$imagem.$temp[$nivel]."\n");
                  echo("                        ".$espacos2."<br>\n");
               }

              }
            $conta_arq++;
          }
          do{
            $j=$nivel;
            $espacos3="";
            do{
              $espacos3.="  ";
              $j--;
            }while($j>=0);
            echo("                      ".$espacos3."</span>\n");
            $nivel--;
          }while($nivel>=0);
        }
        echo("                      <script type=\"text/javascript\">js_conta_arq=".$conta_arq.";</script>\n");
        echo("                    </td>\n");
        echo("                  </tr>\n");
  }

  /*Fim tabela interna*/		
  echo("                </table>\n");
  /*Fim tabela externa*/
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  include("../tela2.php"); 
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
