<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/busca/busca2.php

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
  ARQUIVO : cursos/aplic/busca/busca2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("busca.inc");

  $sock=Conectar("");
  // lista de frases com os nomes das ferramentas
  $lista_frases_ferramentas = RetornaListaDeFrases($sock,-4);
  Desconectar($sock);

  $cod_ferramenta=30;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;

  include("../topo_tela.php");

  //para ser usado no acesso �s ferramentas
  $tipo_usuario=RetornaTipoUsuario($sock,$cod_curso,$cod_usuario);

  /*
  ==================
  Funcoes JavaScript
  ==================
  */

  echo("    <script type=\"text/javascript\" language=\"javascript\">\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n");

  /*
  ==================
  Programa Principal
  ==================
  */

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* 1 - Busca */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)."</h4>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <div id=\"mudarFonte\">\n");
  echo("	    <a href=\"#\" onClick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	    <a href=\"#\" onClick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	    <a href=\"#\" onClick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("          </div>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span title=\"Voltar\" onClick=\"document.location='busca.php?cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases_geral, 23)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  /* <!----------------- Tabela Interna -----------------> */
  echo("                <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  /* retira espa�os e outros caracteres do in�cio e fim do texto */
  $texto=trim($texto);

  /* retira caracteres usados em express�es regulares */
  $texto=preg_quote($texto);

  /* retira espa�os adicionais no meio do texto */
  $texto=explode(' ',$texto);
  foreach ($texto as $cod => $id)
    if ($id == '')
      unset($texto[$cod]);

  $texto_original=$texto;

  $cont=0;
  
  $cod_ferr = $_POST['cod_ferr'];

  if (count($cod_ferr)>0)
  {
    foreach($cod_ferr as $cod => $ferramenta)
      switch ($ferramenta)
      {
        case 16:
          $tabela="Dinamica";
          $par='texto';
          $busca=RetornaBuscaOpcao($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par);

          //echo("<input type=hidden name=cod_ferr[] value=".$ferramenta.">\n");

          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../dinamica/dinamica.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$ferramenta;
              $pagina_item=$pagina.$parametros;
              // F 3 - Din�mica do Curso
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">"
                  .RetornaFraseDaLista($lista_frases_ferramentas, 3)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['texto'],$texto)."\n";
            }
        break;
        case 1:
          $tabela='Agenda_itens';
          $par1='texto'; $par2='titulo';
          $busca=RetornaBusca($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par1,$par2);

          //echo("<input type=hidden name=cod_ferr[] value=".$ferramenta.">\n");

          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../agenda/ver_linha.php";
              $parametros="?cod_curso=".$cod_curso."&amp;visualizar=sim&amp;cod_item=".$retorno['cod_item']."&amp;cod_ferramenta=".$ferramenta;
              $pagina_item=$pagina.$parametros;
              // F 5 - Agenda
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">"
                  .RetornaFraseDaLista($lista_frases_ferramentas,5)." - ".FormataSaida($retorno['titulo'],$texto)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['texto'],$texto)."\n";
            }
        break;
        case 3:
          $tabela='Atividade_itens';
          $par1='texto'; $par2='titulo';
          $busca=RetornaBusca($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par1,$par2);

          //echo("<input type=hidden name=cod_ferr[] value=".$ferramenta.">\n");

          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../material/ver.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$retorno['cod_topico']."&amp;cod_item=".$retorno['cod_item']."&amp;cod_ferramenta=".$ferramenta;
              $pagina_item=$pagina.$parametros;
              // F 7 - Atividades
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">"
                  .RetornaFraseDaLista($lista_frases_ferramentas, 7)." - ".FormataSaida($retorno['titulo'],$texto)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['texto'],$texto)."\n";
            }
        break;
        case 4:
          $tabela='Apoio_itens';
          $par1='texto'; $par2='titulo';
          $busca=RetornaBusca($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par1,$par2);

          //echo("<input type=hidden name=cod_ferr[] value=".$ferramenta.">\n");

          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../material/ver.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$retorno['cod_topico']."&amp;cod_item=".$retorno['cod_item']."&amp;cod_ferramenta=".$ferramenta;
              $pagina_item=$pagina.$parametros;
              // F 9 - Material de Apoio
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">"
                  .RetornaFraseDaLista($lista_frases_ferramentas, 9)." - ".FormataSaida($retorno['titulo'],$texto)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['texto'],$texto)."\n";
            }
        break;
        case 5:
          $tabela='Leitura_itens';
          $par1='texto'; $par2='titulo';
          $busca=RetornaBusca($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par1,$par2);

          //echo("<input type=hidden name=cod_ferr[] value=".$ferramenta.">\n");

          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../material/ver.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$retorno['cod_topico']."&amp;cod_item=".$retorno['cod_item']."&amp;cod_ferramenta=".$ferramenta;
              $pagina_item=$pagina.$parametros;
              // F 11 - Leituras
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">"
                  .RetornaFraseDaLista($lista_frases_ferramentas, 11)." - ".FormataSaida($retorno['titulo'],$texto)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['texto'],$texto)."\n";
            }
        break;
        case 6:
          $tabela='Pergunta_itens';
          $par1='pergunta'; $par2='resposta';
          $busca=RetornaBusca($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par1,$par2);


          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
                            
              $pagina="../perguntas/perguntas.php";
              //$parametros="?cod_curso=".$cod_curso."&amp;cod_assunto_pai=".$retorno['cod_assunto']."&amp;check[]=".$retorno['cod_pergunta']."&amp;cod_ferramenta=".$ferramenta;
              $parametros="?cod_curso=".$cod_curso."&cod_ferramenta=".$ferramenta."&cod_assunto_pai=".$retorno['cod_assunto']."&acao=exibirPergunta&cod_pergunta=".$retorno['cod_pergunta'];
              $pagina_item=$pagina.$parametros;
              // F 13 - Perguntas Freq�entes
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">"
                  .RetornaFraseDaLista($lista_frases_ferramentas, 13)." - ".FormataSaida($retorno['pergunta'],$texto)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['resposta'],$texto)."\n";
            }
        break;
        case 7:
          $tabela='Obrigatoria_itens';
          $par1='texto'; $par2='titulo';
          $busca=RetornaBusca($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par1,$par2);


          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../material/ver.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$retorno['cod_topico']."&amp;cod_item=".$retorno['cod_item']."&amp;cod_ferramenta=".$ferramenta;
              $pagina_item=$pagina.$parametros;
              // F 15 - Parada Obrigat�ria
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">"
                  .RetornaFraseDaLista($lista_frases_ferramentas, 15)." - ".FormataSaida($retorno['titulo'],$texto)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['texto'],$texto)."\n";
            }
        break;
        case 8:
          $tabela='Mural';
          $par1='texto'; $par2='titulo';
          $busca=RetornaBusca($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par1,$par2);

          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../mural/mural.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_mural=".$retorno['cod_mural']."&amp;cod_ferramenta=".$ferramenta;
              $pagina_item=$pagina.$parametros;
              // F 17 - Mural
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">"
                  .RetornaFraseDaLista($lista_frases_ferramentas, 17)." - ".FormataSaida($retorno['titulo'],$texto)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['texto'],$texto)."\n";
            }
        break;
        case 9:
          $tabela="Forum_mensagens";
          $par1='mensagem'; $par2='titulo';
          $busca=RetornaBusca($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par1,$par2);

          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../forum/ver_forum.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_msg=".$retorno['cod_msg']."&amp;cod_msg_pai=".$retorno['cod_msg_pai']."&amp;cod_forum=".$retorno['cod_forum']."&amp;status=".$retorno['status']."&amp;cod_ferramenta=".$ferramenta;
              $pagina_item=$pagina.$parametros;
              // F 19 - F�runs de Discuss�o
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">"
                  .RetornaFraseDaLista($lista_frases_ferramentas, 19)." - ".FormataSaida($retorno['titulo'],$texto)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['mensagem'],$texto)."\n";
            }
        break;
        case 10:
          $tabela="Batepapo_conversa";
          $par='mensagem';
          $busca=RetornaBuscaOpcao($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par);

          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../batepapo/ver_sessao.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_sessao=".$retorno['cod_sessao'];
              $pagina_item=$pagina.$parametros;
              // F 21 - Bate-papo
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">".RetornaFraseDaLista($lista_frases_ferramentas, 21)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['mensagem'],$texto)."\n";
            }
        break;
        case 11:
          $tabela="Correio_mensagens";
          $par1='mensagem'; $par2='assunto';
          $busca=RetornaBusca($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par1,$par2);

          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../correio/correio.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_msg=".$retorno['cod_msg']."&amp;cod_ferramenta=".$ferramenta;
              $pagina_item=$pagina.$parametros;
              // F 23 - Correio
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">".RetornaFraseDaLista($lista_frases_ferramentas, 23)." - ".FormataSaida($retorno['assunto'],$texto)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['mensagem'],$texto)."\n";
            }
        break;
        case 14:
          $tabela='Diario_itens';
          $par1='texto'; $par2='titulo';
          $busca=RetornaBusca($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par1,$par2);

          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../diario/ver_item.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_item=".$retorno['cod_item']."&amp;cod_usr=".$cod_usuario."&amp;cod_ferramenta=".$ferramenta."&amp;busca=sim";
              $pagina_item=$pagina.$parametros;
              // F 29 - Di�rio de Bordo
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">".RetornaFraseDaLista($lista_frases_ferramentas, 29)." - ".FormataSaida($retorno['titulo'],$texto)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['texto'],$texto)."\n";
            }

          $tabela="Diario_comentarios";
          $par="comentario";
          $busca=RetornaBuscaOpcao($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par);
          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../diario/comentarios.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_comentario=".$retorno['cod_comentario']."&amp;cod_propriet=".$retorno['cod_comentarist']."&amp;cod_item=".$retorno['cod_item']."&amp;cod_ferramenta=".$ferramenta;
              $pagina_item=$pagina.$parametros;
              // F 29 - Di�rio de Bordo
              // 14 - Coment�rio
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item.";\">"
                  .RetornaFraseDaLista($lista_frases_ferramentas, 29)." - ".RetornaFraseDaLista($lista_frases, 14)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['comentario'],$texto)."\n";
            }
        break;
        case 15:
          $tabela='Portfolio_itens';
          $par1='texto'; $par2='titulo';
          $busca=RetornaBusca($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par1,$par2);

          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../portfolio/ver.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$retorno['cod_topico']."&amp;cod_item=".$retorno['cod_item']."&amp;cod_usuario_portfolio=".$retorno['cod_usuario']."&amp;cod_grupo_portfolio=".$retorno['cod_grupo']."&amp;cod_ferramenta=".$ferramenta;
              $pagina_item=$pagina.$parametros;
              // F 31 - Portf�lio
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">"
                  .RetornaFraseDaLista($lista_frases_ferramentas, 31)." - ".FormataSaida($retorno['titulo'],$texto)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['texto'],$texto)."\n";
            }

          $tabela="Portfolio_itens_comentarios";
          $par="comentario";
          $busca=RetornaBuscaOpcao($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par);
          if (count($busca)>0)
            foreach ($busca as $cod => $retorno)
            {
              $cont++;
              $pagina="../portfolio/comentarios.php";
              $parametros="?cod_curso=".$cod_curso."&amp;cod_item=".$retorno['cod_item']."&amp;cod_topico_raiz=".$retorno['cod_topico']."&amp;cod_usuario_portfolio=".$retorno['cod_usuario']."&amp;cod_ferramenta=".$ferramenta;
              $pagina_item=$pagina.$parametros;
              // F 31 - Portfolio
              // 14 - Coment�rio
              $resultado[$cont-1]="<b>".$cont." - </b><a href=\"".$pagina_item."\">"
                  .RetornaFraseDaLista($lista_frases_ferramentas, 31)." - ".RetornaFraseDaLista($lista_frases, 14)."</a>\n";
              $resultado[$cont-1].="<br />\n";
              $resultado[$cont-1].=FormataSaida($retorno['comentario'],$texto)."\n";
            }
        break;
        case 22:
            $cont = 0;
            if (is_array($busca = RetornaBuscaAvaliacoes($sock, $cod_curso, $cod_usuario, $texto))) {
                $resultado = array();
                foreach ($busca as $cod => $ocorrencia) {
                    $cont++;
                    // Avalia��o
                    $resultado[] =
                        "<b>".$cont." - </b><a href=\"".$ocorrencia['pagina']."\">".$ocorrencia['titulo']."</a>\n"
                        ."<br />\n"
                        .FormataSaida($ocorrencia['texto'],$texto)."\n";
                }
            }
            break;
      }  //fim do switch

    if ($cont==0)
      /* 8 - N�o foi encontrada nenhuma */
      /* 9 - ocorr�ncia do texto solicitado. */
      echo("                  <tr><td align=left>".RetornaFraseDaLista($lista_frases,8)." ".RetornaFraseDaLista($lista_frases,9)."</td></tr>\n");
    else if ($cont==1)
      /* 10 - Foi encontrada */
      /* 9 - ocorr�ncia do texto solicitado. */
      echo("                  <tr class=\"head01\"><td align=left>".RetornaFraseDaLista($lista_frases,10)."<b> ".$cont." </b>".RetornaFraseDaLista($lista_frases,9)."</td></tr>\n");
    else if ($cont>1)
      /* 11 - Foram encontradas */
      /* 12 - ocorr�ncias do texto solicitado. */
      echo("                  <tr class=\"head01\"><td align=left>".RetornaFraseDaLista($lista_frases,11)."<b> ".$cont." </b>".RetornaFraseDaLista($lista_frases,12)."</td></tr>\n");
  }

  if (count($resultado)>0)
    foreach ($resultado as $cod => $linha)
      echo("                  <tr><td align=left>".$linha."</td></tr>\n");

  // Fim Tabela Interna
  echo("                </table>\n");

  echo("                <form name=\"volta\" method=\"get\" action=\"busca.php\">\n");
  echo("                  <div align=\"right\">\n");
                            /* 13 - Nova Busca */
  echo("                    <input class=\"input\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases,13)."\" />\n");
  echo("                  </div>\n");

  echo("                  <input type=\"hidden\" name=\"cod_curso\" value=".$cod_curso." />\n");

  foreach($cod_ferr as $cod => $ferr)
    echo("                  <input type=\"hidden\" name=\"cod_ferr[]\" value=\"".$ferr."\" />\n");

  echo("                </form>\n");

  // Fim Tabel�o
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");

  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>

