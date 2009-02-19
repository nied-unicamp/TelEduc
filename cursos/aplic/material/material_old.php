<? 
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/material.php

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
  ARQUIVO : cursos/aplic/material/material.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("material.inc");

  /* Registrando c�igo da ferramenta nas vari�eis de sess�.
     �necess�io para saber qual ferramenta est�sendo
     utilizada, j�que este arquivo faz parte de quatro
     ferramentas quase distintas.
   */

  if (session_is_registered("cod_ferramenta_m") && (isset($cod_ferramenta)))
  {
    if ($cod_ferramenta_m != $cod_ferramenta) //mudou de ferramenta
    {
      $cod_ferramenta_m = $cod_ferramenta;

      if (session_is_registered("ultima_pasta_criada"))  //variavel que armazena o nome da ultima pasta criada      
	$ultima_pasta_criada = ""; //limpa a vari�el uma vez que o usu�io mudou de ferramenta      
    }
  }
  else //primeira vez que acessa alguma ferramenta que utiliza esse arquivo (material.php)
  {
    if (isset($cod_ferramenta)){
      session_register("cod_ferramenta_m");
      $cod_ferramenta_m  = $cod_ferramenta;
    }else
      $cod_ferramenta = $cod_ferramenta_m;
  }

  if ($cod_ferramenta==3)
    include("avaliacoes_material.inc");

  /* Necess�io para a lixeira. */
  session_register("cod_topico_s");
  unset($cod_topico_s);

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta);

  $AcessoAvaliacao = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  MarcaAcesso($sock,$cod_usuario,$cod_ferramenta);

  echo("<html>\n");
  /* 1 - 3: Atividades
         4: Material de Apoio
         5: Leituras
         7: Parada Obrigat�ia
   */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  

  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  switch ($cod_ferramenta) {
    case 3 :
      echo("  <link rel=stylesheet TYPE=text/css href=atividades.css>\n");
      $tabela="Atividade";
      $dir="atividades";
      break;
    case 4 :
      echo("  <link rel=stylesheet TYPE=text/css href=apoio.css>\n");
      $tabela="Apoio";
      $dir="apoio";
      break;
    case 5 :
      echo("  <link rel=stylesheet TYPE=text/css href=leituras.css>\n");
      $tabela="Leitura";
      $dir="leituras";
      break;
    case 7 :
      echo("  <link rel=stylesheet TYPE=text/css href=obrigatoria.css>\n");
      $tabela="Obrigatoria";
      $dir="obrigatoria";
      break;
  }

  
  $data_acesso=PenultimoAcesso($sock,$cod_usuario,"");
  if (!isset($cod_topico_raiz))
    $cod_topico_raiz=1;

  if (EFormador($sock,$cod_curso,$cod_usuario))
  {

/* Inicio da Paina do Formador ************************/


    if ($acao=="moveritem")
    {
      MoverItem($sock, $tabela, $cod_item, $cod_usuario, $cod_topico_raiz);
      echo("<script language=javascript>\n");
      ArrumaPosicoesItens($sock, $tabela, $cod_topico_ant);
      echo("  document.location='material.php?".RetornaSessionID()."&cod_curso=$cod_curso&cod_topico_raiz=$cod_topico_raiz';\n");
      echo("</script>\n");
    }

    if ($acao=="movertopico")
    {
      echo("<script language=javascript>\n");
      if (NaoExisteTop($sock, $tabela, $cod_topico_raiz, $cod_topico, $cod_usuario)) 
      {
        if (MoverTopico($sock, $tabela, $cod_topico, $cod_usuario, $cod_topico_raiz))
        {
          ArrumaPosicoesTopicos($sock, $tabela, $cod_topico_ant);
          echo("  document.location='material.php?".RetornaSessionID()."&cod_curso=$cod_curso&cod_topico_raiz=$cod_topico_raiz';\n");
        }
        else
        {
          /* 2 - Voc�n� pode mover uma pasta para ela mesma ou para uma subpasta dela. */
          echo("  alert('".RetornaFraseDaLista($lista_frases,2)."');\n");
          echo("  history.go(-1);\n");
        }        
      }
      else
      {
	echo("alert('".RetornaFraseDaLista($lista_frases_geral,71)."');\n"); 
      }
      echo("</script>\n");
    }

    if ($acao=="moverselecionados")
    {
      echo("<script language=javascript>\n");
      if (strlen($cod_topicos))
      {
        $cod_topicos = explode(" ,",$cod_topicos);

        for($i = 0; $i < count($cod_topicos); $i++)
          $cod_topicos[$i] = trim($cod_topicos[$i]);

	if (EPaiTopicos($sock, $tabela, $cod_topico_raiz, $cod_topicos))
	{
          /* 2 - Voc�n� pode mover uma pasta para ela mesma ou para uma subpasta dela. */
          echo("  alert('".RetornaFraseDaLista($lista_frases,2)."');\n");
          echo("  history.go(-1);\n");
	}
	else
	{
	  $existe = 0;
	  for($i = 0; $i < count($cod_topicos); $i++)
	  {
	    if (!NaoExisteTop($sock, $tabela, $cod_topico_raiz, $cod_topicos[$i], $cod_usuario))
	      $existe = 1;
	  }

	  if (!$existe)
	  {
	    // move todos os topicos seleiconados
	    for($i = 0; $i < count($cod_topicos); $i++)
	    {
	      MoverTopico($sock, $tabela, $cod_topicos[$i], $cod_usuario, $cod_topico_raiz);
	      ArrumaPosicoesTopicos($sock, $tabela, $cod_topico_ant);
	    }
	    // se tiver itens selecionados, move-os tamb�
	    if (strlen($cod_itens))
	    {
              $cod_itens = explode(" ,",$cod_itens);
              for($i = 0; $i < count($cod_itens); $i++)
              {
                MoverItem($sock, $tabela, $cod_itens[$i], $cod_usuario, $cod_topico_raiz);
                ArrumaPosicoesItens($sock, $tabela, $cod_topico_ant);
              }
	    }
	  }
	  else //existe topico com mesmo nome no diretorio destino
	  {
	    /* 71- N� foi poss�el mover a pasta, pois j�existe uma pasta com mesmo nome no diret�io destino. */
            /* 72- Verifique as pastas selecionadas.  */
            echo("alert('".RetornaFraseDaLista($lista_frases_geral,71)." ".RetornaFraseDaLista($lista_frases_geral,72)."');");
	  }
	}
      }
      else
      {
	if (strlen($cod_itens))
	{
	  $cod_itens = explode(" ,",$cod_itens);
          for($i = 0; $i < count($cod_itens); $i++)
          {
            MoverItem($sock, $tabela, $cod_itens[$i], $cod_usuario, $cod_topico_raiz);
            ArrumaPosicoesItens($sock, $tabela, $cod_topico_ant);
          }
	}
      }
      echo("  document.location='material.php?".RetornaSessionID()."&cod_curso=$cod_curso&cod_topico_raiz=$cod_topico_raiz';
\n");
      echo("</script>\n");
    }
    if ($acao=="novotopico")
    {
      if (NaoExisteTop($sock, $tabela, $cod_topico_raiz, $novo_nome, $cod_usuario))
      {
        if (!session_is_registered("ultima_pasta_criada")) //primeira pasta que estou criando nessa sessao
        {
          session_register("ultima_pasta_criada");
          $cod_topico=CriarTopico($sock, $tabela, $cod_topico_raiz, $novo_nome, $cod_usuario);
	}
        else
        {
          if ($ultima_pasta_criada != $novo_nome)
	    $cod_topico=CriarTopico($sock, $tabela, $cod_topico_raiz, $novo_nome, $cod_usuario);
        }
        $ultima_pasta_criada = $novo_nome; // guardo o nome da pasta criada para n� duplica-la caso o usuario voltar pelo browser
      }
      else
      {
        if ($ultima_pasta_criada != $novo_nome)
        {
          echo("<script language=javascript>\n");
          echo("  alert('".RetornaFraseDaLista($lista_frases_geral, 70)."');\n");
          echo("</script>\n");
        }
      }
    }

    if ($acao=="renomeartop")
    {
        RenomearTopico($sock, $tabela, $cod_topico, $novo_nome);
    }

    if ($acao=="mudarcomp")
    {
      MudarCompartilhamento($sock, $tabela, $cod_item, $tipo_comp);
    }

    if ($acao=="mudarposicao")
    {
      if ($cod_item=="")
      {
        if ($cod_topico!="")
        {
          MudarPosicaoTopico($sock, $tabela, $cod_topico_raiz, $cod_topico, $posicao);
        }
      }
      else
      {
        if ($cod_topico=="")
        {
          MudarPosicaoItem($sock, $tabela, $cod_topico_raiz, $cod_item, $posicao);
        }
      }
    }


    if ($acao=="apagaritem")
    {
      ApagarItem($sock, $tabela, $cod_item, $cod_usuario);
      if (($cod_ferramenta==3) && ($AcessoAvaliacao))
      {
        if (AtividadeEhAvaliacao($sock,$cod_item))
        {
          $cod_avaliacao=RetornaCodAvaliacao($sock,$cod_item);
          ApagaAvaliacaoPortfolio($sock,$cod_avaliacao,$cod_usuario);
        }
      }
    }

    if ($acao=="apagaravaliacao")
    {
      ApagaAvaliacaoPortfolio($sock,$cod_avaliacao,$cod_usuario);
    }


    if ($acao=="apagartopico")
    {
      ApagarTopico($sock, $tabela, $cod_topico, $cod_usuario);
    }

    if ($acao=="apagarselecionados")
    {
      if (strlen($cod_itens))
      {
	$cod_itens = explode(' ,',$cod_itens);

	for($i = 0; $i < count($cod_itens); $i++)
	{
          ApagarItem($sock, $tabela, $cod_itens[$i], $cod_usuario);
          if (($cod_ferramenta==3) && ($AcessoAvaliacao))
          {
            if (AtividadeEhAvaliacao($sock,$cod_itens[$i]))
            {
              $cod_avaliacao=RetornaCodAvaliacao($sock,$cod_itens[$i]);
              ApagaAvaliacaoPortfolio($sock,$cod_avaliacao,$cod_usuario);
            }
          }
	}
      }
      if (strlen($cod_topicos))
      {
	$cod_topicos = explode(' ,',$cod_topicos);

	for ($i = 0; $i < count($cod_topicos); $i++)
	{
	  ApagarTopico($sock, $tabela, $cod_topicos[$i], $cod_usuario);
	}
      }
    }

    /* Fun�es JavaScript */
    echo("<script language=JavaScript src=../bibliotecas/dhtmllib.js></script>\n");
    echo("<script language=JavaScript>\n");
    echo("var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
    echo("var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
    echo("var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
    echo("var Xpos, Ypos;\n");
    echo("var js_cod_item, js_cod_topico;\n");
    echo("var js_nome_topico;\n");
    echo("var js_tipo_item;\n");
    echo("var js_comp = new Array();\n");

    echo("if (isNav)\n");
    echo("{\n");
    echo("  document.captureEvents(Event.MOUSEMOVE);\n");
    echo("}\n");
    echo("document.onmousemove = TrataMouse;\n");

    echo("function TrataMouse(e)\n");
    echo("{\n");
    echo("  Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
    echo("  Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
    echo("}\n");

    echo("function getPageScrollY()\n");
    echo("{\n");
    echo("  if (isNav)\n");
    echo("    return(window.pageYOffset);\n");
    echo("  if (isIE)\n");
    echo("    return(document.body.scrollTop);\n");
    echo("}\n");

    echo("function AjustePosMenuIE()\n");
    echo("{\n");
    echo("  if (isIE)\n");
    echo("    return(getPageScrollY());\n");
    echo("  else\n");
    echo("    return(0);\n");
    echo("}\n");

    echo("function Iniciar()\n");
    echo("{\n");
    echo("  cod_menu = getLayer(\"menu\");\n");
    echo("  cod_comp = getLayer(\"comp\");\n");
    echo("  cod_menu_top = getLayer(\"menutop\");\n");
    echo("  cod_ren_top = getLayer(\"renomeartop\");\n");
    echo("  cod_novo_top = getLayer(\"novotop\");\n");
    echo("  cod_mover = getLayer(\"mover\");\n");
    echo("  cod_topicos = getLayer(\"topicos\");\n");
    echo("  cod_mudar_pos = getLayer(\"mudarpos\");\n");
    echo("}\n");
    echo("\n");

    echo("function EscondeLayers()\n");
    echo("{\n");
    echo("  hideLayer(cod_menu);\n");
    echo("  hideLayer(cod_comp);\n");
    echo("  hideLayer(cod_menu_top);\n");
    echo("  hideLayer(cod_ren_top);\n");
    echo("  hideLayer(cod_novo_top);\n");
    echo("  hideLayer(cod_mover);\n");
    echo("  hideLayer(cod_topicos);\n");
    echo("  hideLayer(cod_mudar_pos);\n");
    echo("}\n");

    echo("function MostraLayerTopico(cod_layer, ajuste)\n");
    echo("{\n");
    echo("  EscondeLayers();\n");
    echo("  moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
    echo("  showLayer(cod_layer);\n");

    echo("  if (isNav) {\n");
    echo("    document.renomeartop.target = \"form_renomear_top\";\n");
    echo("    document.renomeartop.document.form_renomear_top.novo_nome.value=js_nome_topico;\n");
    echo("    document.renomeartop.document.form_renomear_top.cod_topico.value=js_cod_topico;\n");
//    echo("    document.renomeartop.document.form_renomear_top.novo_nome.focus();\n");
    echo("  }\n");
    echo("  else if (isIE) {\n");
    echo("    document.form_renomear_top.novo_nome.value=js_nome_topico;\n");
    echo("    document.form_renomear_top.cod_topico.value=js_cod_topico;\n");
//    echo("      document.form_renomear_top.novo_nome.select();\n");
    echo("  }\n");
/*    echo("  if (isIE) \n");
    echo("    document.form_renomear_top.novo_nome.focus();\n");
*/
    echo("}\n");

    echo("function testa_titulo_criar(form)\n");
    echo("{\n");
    echo("  return (PossoRenomear(form));\n");
    echo("}\n");

    echo("function PossoRenomear(form) {\n");
    echo("  nome=form.novo_nome.value;\n");
    echo("  if (nome==js_nome_topico) {\n");
    /* 3 - Nenhuma alteracao foi efetuada. */
    echo("    alert(\"".RetornaFraseDaLista($lista_frases,3)."\");\n");
    echo("    hideLayer(cod_ren);\n");
    echo("    return(false);\n");
    echo("  } else {\n");
    echo("// se nome for vazio, nao pode\n");

    echo("      while (nome.search(\" \") != -1)\n");
    echo("      {\n");
    echo("        nome = nome.replace(/ /, \"\");\n");
    echo("      }\n");

    echo("    if (nome==\"\") {\n");
    /* 4 - O titulo do item a ser renomeado nao pode ser vazio. */
    echo("      alert(\"".RetornaFraseDaLista($lista_frases,4)."\");\n");
    echo("      return(false);\n");
    echo("    } else {\n");
    echo("// se nome tiver aspas, <, >, nao pode\n");
    echo("      if (nome.indexOf(\"\\\\\")>=0 || nome.indexOf(\"\\\"\")>=0 || nome.indexOf(\"'\")>=0 || nome.indexOf(\">\")>=0 || nome.indexOf(\"<\")>=0) {\n");
    /* 5 - O titulo do item a ser renomeado nao pode conter \\\", \\\', < ou >. */
    echo("        alert(\"".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases,5)))."\");\n");
    echo("        return(false);\n");
    echo("      } else {\n");
    echo("        return(true);\n");
    echo("      }\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");

    echo("function AtualizaComp(js_tipo_comp)\n");
    echo("{\n");
    echo("  if ((isNav) && (!isMinNS6)) {\n");
    echo("    document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("    document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
    echo("    if (js_tipo_comp=='F') {\n");
    echo("      document.comp.document.form_comp.tipo_comp[0].checked=true;\n");
    echo("      document.comp.document.form_comp.tipo_comp[1].checked=false;\n");
    echo("    } else {\n");
    echo("      document.comp.document.form_comp.tipo_comp[1].checked=true;\n");
    echo("      document.comp.document.form_comp.tipo_comp[0].checked=false;\n");
    echo("    }\n");
    echo("  } else {\n");
    echo("    if (isIE || ((isNav)&&(isMinNS6)) ){\n");
    echo("      document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("      document.form_comp.cod_item.value=js_cod_item;\n");
    echo("      if (js_tipo_comp=='F') {\n");
    echo("        document.form_comp.tipo_comp[0].checked=true;\n");
    echo("        document.form_comp.tipo_comp[1].checked=false;\n");
    echo("      } else {\n");
    echo("        document.form_comp.tipo_comp[1].checked=true;\n");
    echo("        document.form_comp.tipo_comp[0].checked=false;\n");
    echo("      }\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");

    echo("function MostraLayer(cod_layer, ajuste)\n");
    echo("{\n");
    echo("  EscondeLayers();\n");
    echo("  moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
    echo("  showLayer(cod_layer);\n");
    echo("}\n");

    echo("function EscondeLayer(cod_layer)\n");
    echo("{\n");
    echo("  hideLayer(cod_layer);\n");
    echo("}\n");

    echo("function AtualizaCamposPosicao(tipo)\n");
    echo("{\n");
    echo("  if (tipo=='item')\n");
    echo("  {\n");
    echo("    if (((isNav) && (isMinNS6)) || (isIE)) {\n"); // se a versao do Netscape for 6 ou superior,
                                                          // entao referencia a posicao �feita da mesma
                                                          // forma que �feita no IE
    echo("      document.form_posicao.cod_item.value=js_cod_item;\n");
    echo("      document.form_posicao.cod_topico.value='';\n");
    echo("    }\n");
    echo("    else {\n");  // senao �Netscape com versao < 6
    echo("      document.mudarpos.document.form_posicao.cod_item.value=js_cod_item;\n");
    echo("      document.mudarpos.document.form_posicao.cod_topico.value='';\n");
    echo("    }\n");
    echo("  }\n");
    echo("  else\n");
    echo("  {\n");
    echo("    if (tipo=='topico')\n");
    echo("    {\n");
    echo("      if(((isNav) && (isMinNS6)) || (isIE)) {\n");
    echo("          document.form_posicao.cod_topico.value=js_cod_topico;\n");
    echo("          document.form_posicao.cod_item.value='';\n");
    echo("      } else {\n");
    echo("        document.mudarpos.document.form_posicao.cod_topico.value=js_cod_topico;\n");
    echo("        document.mudarpos.document.form_posicao.cod_item.value='';\n");
    echo("      }\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");

    echo("function TemCertezaApagarTop(tmp_cod_topico)\n");
    echo("{\n");
    /* 71 - Voc�tem certeza de que deseja apagar esta pasta? */
    /* 72 - (Todos os materiais contidos nessa pasta ser� movidos para a lixeira) */
    echo("   if (confirm(\"".RetornaFraseDaLista($lista_frases,71)."\\n".RetornaFraseDaLista($lista_frases,72)."\"))\n");
    echo("     document.location='material.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&acao=apagartopico&cod_topico='+tmp_cod_topico+'&cod_topico_raiz=$cod_topico_raiz';\n");
    echo("   return false;\n");
    echo("}\n");

    if ($cod_ferramenta==3)
    {
      echo("function TemCertezaApagarAtividade(varios)\n");
      echo("{\n");
      echo("  if (varios)\n");
      /* 6 - Voc�tem certeza de que deseja apagar esta atividade? */
      /* 117 - (as atividades ser� movidas para a lixeira e se houver alguma avalia�o relacionada, as avalia�es tamb� ser� movidas para a lixeira DAS AVALIA�ES) */
      echo("    return(confirm(\"".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,117)."\"))\n");
      echo("  else\n");
      /* 6 - Voc�tem certeza de que deseja apagar esta atividade? */
      /* 101 - (a atividade ser�movida para a lixeira e se houver alguma avalia�o relacionada, a avalia�o tamb� ser�movida para a lixeira DAS AVALIA�ES) */
      echo("    return(confirm(\"".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,101)."\"))\n");
      echo("}\n");
    }
    else
    {
      echo("function TemCertezaApagar(varios)\n");
      echo("{\n");
      echo("  if (varios)\n");
      echo("    return(confirm(\"".RetornaFraseDaLista($lista_frases,115)."\\n".RetornaFraseDaLista($lista_frases,116)."\"));\n");
      echo("  else\n");
      /* 6 - Voc�tem certeza de que deseja apagar esta atividade? */
      /* 7 - (a atividade ser�movida para a lixeira) */
      echo("  return(confirm(\"".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,7)."\"))\n");
      echo("}\n");
    }

    echo("function MoverItem(link,cod_destino)\n");
    echo("{\n");
    echo("  if (js_tipo_item=='item')\n");
    echo("  {\n");
    echo("    link.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item='+js_cod_item+'&cod_topico_raiz='+cod_destino+'&cod_topico_ant=".$cod_topico_raiz."&acao=moveritem';\n");
    echo("    return true;\n");
    echo("  }\n");
    echo("  else\n");
    echo("  {\n");
    echo("    if (js_tipo_item=='topico')\n");
    echo("    {\n");
    echo("      link.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico='+js_cod_topico+'&cod_topico_raiz='+cod_destino+'&cod_topico_ant=".$cod_topico_raiz."&acao=movertopico';\n");
    echo("      return true;\n");
    echo("    }\n");
    echo("    else\n");
    echo("    {\n");
    echo("	if (js_tipo_item=='selec')\n");
    echo("	{\n");
    echo("        link.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_itens='+js_cod_itens+'&cod_topicos='+js_cod_topicos+'&cod_topico_raiz='+cod_destino+'&cod_topico_ant=".$cod_topico_raiz."&acao=moverselecionados';\n");
    echo("        return true;\n");
    echo("	}\n");
    echo("      return false;\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");

    echo("function nao_numerico(num)\n");
    echo("{\n");
    echo("  var erro=false;\n");
    echo("  var i=0;\n");
    echo("  var c='';\n");
    echo("  while (i<num.length && !erro) \n");
    echo("{\n");
    echo("    c=num.charAt(i);\n");
    echo("    if (c<'0' || c>'9') \n");
    echo("      { erro=true; } \n");
    echo("    i++;\n");
    echo("  }\n");
    echo("  return(erro);\n");
    echo("}\n");

    echo("function MudarPosicao(link) \n");
    echo("{\n");
    echo("  if (((isNav) && (isMinNS6)) || (isIE))\n"); // se a versao do Netscape for 6 ou superior,
                                                        // entao referencia a posicao �feita da mesma
                                                        // forma que �feita no IE
    echo("  {\n");
    echo("    posicao=document.form_posicao.posicao.value;\n");
    echo("  }\n");
    echo("  else \n");    // senao �Netscape com versao < 6
    echo("  {\n");
    echo("    posicao=document.mudarpos.document.form_posicao.posicao.value;\n");
    echo("  }\n");
    echo("  if (posicao=='') \n");
    echo("  {\n");
    /* 73 - Voc�deve digitar um nmero! */
    echo("    alert('".RetornaFraseDaLista($lista_frases,73)."');\n");
    echo("    return(false);\n");
    echo("  }\n");
    echo("  else \n");
    echo("  {\n");
    echo("    if (nao_numerico(posicao)) \n");
    echo("    {\n");
    /* 74 - O valor deve ser um nmero! */
    echo("      alert('".RetornaFraseDaLista($lista_frases,74)."');\n");
    echo("      return(false);\n");
    echo("    }\n");
    echo("    else \n");
    echo("    {\n");
    echo("      return true;\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");

    if(($cod_ferramenta==3) && ($AcessoAvaliacao))
    {
      echo("function VerAvaliacao(id)\n");
      echo("{\n");
      echo("  window.open('../avaliacoes/ver_popup.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&origem=../material/material&cod_topico=".$cod_topico_raiz."&cod_avaliacao='+id,'VerAvaliacao','width=450,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
      echo("  return(false);\n");
      echo("}\n");
    }

    echo("\n");

    echo("function ControlaSelecao(chkbox)\n");
    echo("{\n");
    echo("  var tipo = 'top';\n");
    echo("  var conteudo;\n");
    echo("  if (chkbox.name.indexOf('itm') != -1) //selecionou um item\n");
    echo("  {\n");
    echo("    tipo = 'itm';\n");    
    echo("    conteudo = document.frmMaterial.ItensSelecionados.value;\n");
    echo("  }\n");
    echo("  else //selecionou um topico\n");
    echo("    conteudo = document.frmMaterial.TopSelecionados.value;\n");
    echo("  var achou = conteudo.indexOf(chkbox.value+' ');");
    echo("  //Adiciona o valor\n");
    echo("  if(chkbox.checked)\n");
    echo("  {\n");
    echo("    if (achou == -1)\n");
    echo("    {\n");
    echo("      if (conteudo.length == 0)\n");
    echo("        conteudo = chkbox.value + ' ';\n");
    echo("      else\n");
    echo("        conteudo += ',' + chkbox.value + ' ';\n");
    echo("    }\n");
    echo("  }\n");
    echo("  //Remove o valor\n");
    echo("  else\n");
    echo("  {\n");
    echo("    if (achou == 0) //eh o primeiro item\n");
    echo("    {\n");
    echo("      if (conteudo.indexOf(',') != -1) //tem mais itens selecionados\n");
    echo("        conteudo = conteudo.replace(chkbox.value+' ,','');\n");
    echo("      else\n");
    echo("        conteudo = conteudo.replace(chkbox.value+' ','');\n");
    echo("    }\n");
    echo("    else\n");
    echo("      conteudo = conteudo.replace(','+chkbox.value+' ','');\n");
    echo("  }\n");
    echo("  if (tipo == 'itm')\n");
    echo("    document.frmMaterial.ItensSelecionados.value = conteudo;\n");
    echo("  else\n");
    echo("    document.frmMaterial.TopSelecionados.value = conteudo;\n");
    echo("}\n");

    echo("function MarcaOuDesmarcaTodos()\n");
    echo("{\n");
    echo("  var e;\n");
    echo("  var i;\n");
    echo("  var CabMarcado = document.forms[0].cabecalho.checked;\n");
    echo("  for(i = 0; i < document.forms[0].elements.length; i++)\n");
    echo("  {\n");
    echo("    e = document.forms[0].elements[i];\n");
    echo("    if ((e.name.indexOf(\"itm\") == 0) || (e.name.indexOf(\"top\") == 0))\n");
    echo("    {\n");
    echo("      e.checked = CabMarcado;\n");
    echo("      ControlaSelecao(e);\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");
    echo("\n");
    
    echo("function moverSelecionados() {\n");
    echo("  if ((document.forms[0].ItensSelecionados.value.length > 0) || ");
    echo("     (document.forms[0].TopSelecionados.value.length > 0))\n");
    echo("  {\n");
    echo("    js_cod_itens=document.forms[0].ItensSelecionados.value;\n");
    echo("    js_cod_topicos=document.forms[0].TopSelecionados.value;\n");
    echo("    js_tipo_item='selec';\n");
    echo("    MostraLayer(cod_mover,0);\n");
    echo("    return(false);\n");
    echo("  }\n");
    echo("}\n");
    
    echo("function excluirSelecionados() {\n");
    echo("  if((document.forms[0].ItensSelecionados.value.length > 0) || ");
    echo("    (document.forms[0].TopSelecionados.value.length > 0))\n");
    echo("  {\n");
    if ($cod_ferramenta == 3)
      echo("  if (TemCertezaApagarAtividade(1))\n");
    else
      echo("  if (TemCertezaApagar(1))\n");
    echo("    {\n");
    echo("      js_cod_itens=document.forms[0].ItensSelecionados.value;\n");
    echo("      js_cod_topicos=document.forms[0].TopSelecionados.value;\n");
    echo("        document.location='material.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&acao=apagarselecionados&cod_itens='+js_cod_itens+'&cod_topicos='+js_cod_topicos;\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");
    echo("\n");
    echo("</script>\n");
    echo("\n");

    echo("\n");
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
    echo("\n");

    /* Pagina Principal */
    /* 1 - 3: Atividades
           4: Material de Apoio
           5: Leituras
           7: Parada Obrigat�ia
    */
    $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n";

    $cod_pagina=1;
    if(($cod_ferramenta==3)&&($AcessoAvaliacao)&&(EFormador($sock,$cod_curso,$cod_usuario)))/*verifica�o se aparecer�ajuda de avalia�es*/
    $cod_pagina=6;

    /* Cabecalho */
    echo(PreparaCabecalho($cod_curso,$cabecalho,$cod_ferramenta,$cod_pagina));

    $lista_topicos_ancestrais=RetornaTopicosAncestrais($sock, $tabela, $cod_topico_raiz);
    unset($path);
    foreach ($lista_topicos_ancestrais as $cod => $linha)
    {
      if ($cod_topico_raiz!=$linha['cod_topico'])
      {
        $path="<a class=text href=\"material.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz=".$linha['cod_topico']."\">".$linha['topico']."</a> &gt;&gt; ".$path;
      }
      else
      {
        $path="<b class=text>".$linha['topico']."</b><br>\n";
      }
    }
    echo("<a href=# onMouseDown=\"MostraLayer(cod_topicos,0);return(false);\"><img src=\"../figuras/estrutura.gif\" border=0></a>");
    echo($path);
    echo("<p>\n");
    echo("<table border=0 width=100%>\n");
    echo("  <tr class=menu>\n");

    /* 1 - 3: Atividades
           4: Material de Apoio
           5: Leituras
           7: Parada Obrigat�ia
     */
    echo("    <td align=center><a href=\"material.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\" class=menu><b>".RetornaFraseDaLista($lista_frases,1)."</b></a></td>\n");

    /* 8 - 3: Nova Atividade
           4: Novo Material de Apoio
           5: Nova Leitura
           7: Nova Parada Obrigat�ia
     */
    echo("    <td align=center><a href=\"criar_material.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico=".$cod_topico_raiz."&origem=material\" class=menu><b>".RetornaFraseDaLista($lista_frases,8)."</b></a></td>\n");

     /* 105 - 3: Importar Atividade
             4: Importar Material de Apoio
             5: Importar Leitura
             7: Importar Parada Obrigat�ia
     */
    echo("    <td align=center><a href=\"importar_curso.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."\" class=menu><b>".RetornaFraseDaLista($lista_frases,105)."</b></a></td>\n");

    /* 9 - Nova Pasta */
    echo("    <td align=center><a href=# onMouseDown=\"MostraLayer(cod_novo_top,150);return(false);\" class=menu><b>".RetornaFraseDaLista($lista_frases,9)."</b></a></td>\n");
    /* 10 - Estrutura de Pastas */
    /* echo("    <td align=center><a href=# onClick=\"MostraLayer(cod_topicos,0);return(false);\" class=menu><b>".RetornaFraseDaLista($lista_frases,10)."</b></a></td>\n"); */
    /* 11 - Lixeira */
    echo("    <td align=center><a href=\"lixeira.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico=".$cod_topico_raiz."\" class=menu><b>".RetornaFraseDaLista($lista_frases,11)."</b></a></td>\n");
    
    echo("  </tr>\n");
    echo("</table>\n");
    echo("<br>\n");
    echo("<form name=frmMaterial method=post>\n");
    echo("<input type=hidden name=ItensSelecionados value=''>\n");
    echo("<input type=hidden name=TopSelecionados value=''>\n");
    echo("<table border=0 width=100% cellspacing=0>\n");
    echo("  <tr>\n");
    echo("    <td width=1% class=colorfield><input name=cabecalho type=checkbox onClick=\"MarcaOuDesmarcaTodos();\"></td>\n");
    echo("    <td width=1% class=colorfield>&nbsp;</td>\n");
    /* 12 - 3: Atividade
            4: Material de Apoio
            5: Leitura
            7: Parada Obrigat�ia
     */
    echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases,12)."</td>\n");
    /* 13 - Data */
    echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,13)."</td>\n");
    /* 14 - Compartilhar */
    echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,14)."</td>\n");
    if(($cod_ferramenta==3)&&($AcessoAvaliacao))
    /* 90 - Avaliaçao */
      echo("    <td class=colorfield align=center width=20%>".RetornaFraseDaLista($lista_frases,90)."</td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td colspan=5 height=5></td>\n");
    echo("  </tr>\n");
    $lista_topicos=RetornaTopicosDoTopico($sock, $tabela, $cod_topico_raiz);
    $lista_itens=RetornaItensDoTopico($sock, $tabela, $cod_topico_raiz);
    if (count($lista_topicos)<1 && count($lista_itens)<1)
    {
      echo("  <tr class=text>\n");
      /* 15 - 3: N� h�nenhuma atividade
              4: N� h�nenhum material de apoio
              5: N� h�nenhuma leitura
              7: N� h�nenhuma parada obrigat�ia
       */
      echo("    <td class=text colspan=5>".RetornaFraseDaLista($lista_frases,15)."</td>\n");
      echo("  </tr>\n");
    }
    else
    {
      if (count($lista_topicos)>0)
        foreach ($lista_topicos as $cod => $linha_topico)
        {
          $data="<font class=text>".UnixTime2Data($linha_topico['data'])."</font>";
          if ($linha_topico['tipo_compartilhamento']=="T")
          {
            /* 16 - Totalmente Compartilhado */
            $compartilhamento="<font class=text>".RetornaFraseDaLista($lista_frases,16)."</font>";
          }
          else
          {
            /* 17 - Compartilhado com Formadores */
            $compartilhamento="<font class=text>".RetornaFraseDaLista($lista_frases,17)."</font>";
          }

          $max_data=RetornaMaiorData($sock,$tabela,$linha_topico['cod_topico'],'F',$linha_topico['data']);

          if ($data_acesso<$max_data)
          {
            $marcaib="<b>";
            $marcafb="</b>";
            $marcatr=" bgcolor=#f0f0f0";
          }
          else
          {
            $marcaib="";
            $marcafb="";
            $marcatr="";
          }
          echo("  <tr".$marcatr.">\n");
	  echo("    <td><input type=checkbox name=top".$linha_topico['cod_topico']." value=".$linha_topico['cod_topico']." onClick='ControlaSelecao(this)'></td>\n");
          echo("    <td width=10 align=center><a class=text href=\"material.php?".RetornaSessionID()."&cod_curso=$cod_curso&cod_topico_raiz=".$linha_topico['cod_topico']."\"><img src=../figuras/pasta.gif border=0></a></td>\n");
          echo("    <td><font class=text>".$marcaib.$linha_topico['posicao_topico']." - </font><a class=text href=# onMouseDown=\"js_cod_topico='".$linha_topico['cod_topico']."';js_nome_topico='".ConverteAspas2BarraAspas($linha_topico['topico'])."';MostraLayerTopico(cod_menu_top,0);return(false);\">".$linha_topico['topico']."</a>".$marcafb."</td>\n");
          echo("    <td align=center>".$marcaib.$data.$marcafb."</td>\n");
//          echo("    <td align=center>".$marcaib.$compartilhamento.$marcafb."</td>\n");
          echo("    <td align=center>&nbsp;</td>\n");
          if ($cod_ferramenta==3)
            echo("    <td align=center>&nbsp;</td>\n");
          echo("  </tr>\n");
          echo("  <tr>\n");
          echo("    <td colspan=6 height=1><hr size=1></td>\n");
          echo("  </tr>\n");
        }
      if (count($lista_itens)>0)
      {
        foreach ($lista_itens as $cod => $linha_item)
        {
          $data="<font class=text>".UnixTime2Data($linha_item['data'])."</font>";
          if ($linha_item['tipo_compartilhamento']=="T")
          {
            /* 16 - Totalmente Compartilhado */
            $compartilhamento=RetornaFraseDaLista($lista_frases,16);
          }
          else
          {
            /* 17 - Compartilhado com Formadores */
            $compartilhamento=RetornaFraseDaLista($lista_frases,17);
          }
          if ($data_acesso<$linha_item['data'])
          {
            $marcaib="<b>";
            $marcafb="</b>";
            $marcatr=" bgcolor=#f0f0f0";
          }
          else
          {
            $marcaib="";
            $marcafb="";
            $marcatr="";
          }
          if ($linha_item['status']=="E")
          {
            $linha_historico=RetornaUltimaPosicaoHistorico($sock, $tabela, $linha_item['cod_item']);
            if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario==$linha_historico['cod_usuario'])
            {
//              echo ("<font size=8>Primeiro</font>\n");
              CancelaEdicao($sock, $tabela, $dir, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp,$criacao_avaliacao);
//              echo ("<font size=8>Primeiro</font>\n");
              $compartilhamento=$marcaib."<a class=text href=# onMouseDown=\"js_cod_item=".$linha_item['cod_item'].";AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</a>".$marcafb;
              $titulo=$marcaib."<font class=text>".$linha_item['posicao_item']." - </font><a class=text href=# onMouseDown=\"js_cod_item=".$linha_item['cod_item'].";MostraLayer(cod_menu,0);return(false);\">".$linha_item['titulo']."</a>".$marcafb;
              $icone="<a href=\"ver.php?".RetornaSessionID()."&cod_curso=$cod_curso&cod_item=".$linha_item['cod_item']."&cod_topico_raiz=".$cod_topico_raiz."\"><img src=../figuras/arqp.gif border=0></a>";
            }
            else
            {
              /* 18 - Em Edi�o */
              $data="<a href=# class=text onClick=\"window.open('em_edicao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item=".$linha_item['cod_item']."&origem=material&cod_topico_raiz=".$cod_topico_raiz."','EmEdicao','width=300,height=220,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">".RetornaFraseDaLista($lista_frases,18)."</a>";
              $compartilhamento="<font class=textgray>".$compartilhamento."</font>";
              $titulo="<font class=text>".$linha_item['posicao_item']." - </font><font class=textgray>".$linha_item['titulo']."</font>";
              $icone="<img src=../figuras/arqp.gif border=0>";
            }
          }
          else
          {
            if ($linha_item['status']!="C")
            {
              $compartilhamento=$marcaib."<a class=text href=# onMouseDown=\"js_cod_item=".$linha_item['cod_item'].";AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</a>".$marcafb;
              $titulo=$marcaib."<font class=text>".$linha_item['posicao_item']." - </font><a class=text href=# onMouseDown=\"js_cod_item=".$linha_item['cod_item'].";MostraLayer(cod_menu,0);return(false);\">".$linha_item['titulo']."</a>".$marcafb;
              $icone="<a href=\"ver.php?".RetornaSessionID()."&cod_curso=$cod_curso&cod_item=".$linha_item['cod_item']."&cod_topico_raiz=".$cod_topico_raiz."\"><img src=../figuras/arqp.gif border=0></a>";
            }
          }

          if ($linha_item['status']=="C")
          {
            if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario==$linha_item['cod_usuario'])
            {
//              echo ("<font size=8>Segundo</font>\n");
              CancelaEdicao($sock, $tabela, $dir, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp,$criacao_avaliacao);
//              echo ("<font size=8>Segundo</font>\n");
            }
          }
          else
          {
            echo("  <tr".$marcatr.">\n");
	    echo("    <td><input type=checkbox name=itm".$linha_item['cod_item']." value=".$linha_item['cod_item']." onClick='ControlaSelecao(this)'></td>\n");
            echo("    <td width=10 align=center>".$icone."</td>\n");
            echo("    <td>".$titulo."</td>\n");
            echo("    <td align=center>".$marcaib.$data.$marcafb."</td>\n");
            echo("    <td align=center>".$compartilhamento."</td>\n");
            if (($cod_ferramenta==3)&&($AcessoAvaliacao))
            {
              if (AtividadeEhAvaliacao($sock,$linha_item['cod_item']))
              {
                $cod_avaliacao=RetornaCodAvaliacao($sock,$linha_item['cod_item']);
                /* 35 - Sim (ger)*/
                echo("<td class=text align=center><a class=text href=# onClick='VerAvaliacao(".$cod_avaliacao.");return(false);'>".RetornaFraseDaLista($lista_frases_geral,35)."</a>");
              }
              else
              /* 36 -  Nao (ger)*/
                echo("    <td align=center>".RetornaFraseDaLista($lista_frases_geral,36)."</td>\n");
            }
            echo("  </tr>\n");
            echo("  <tr>\n");
            echo("    <td colspan=6 height=1><hr size=1></td>\n");
            echo("  </tr>\n");
          }
	}
      }
      echo("<tr height=1><td colspan=5>\n");

      if ($cod_ferramenta==3)
      {
        // 68 - Excluir selecionados (gen)
        echo("<input name=Excluir type=button value=\"".RetornaFraseDaLista($lista_frases_geral,68)."\" onClick=\"excluirSelecionados();\">");
      }
      else
      {
        // 68 - Excluir selecionados (gen)
        echo("<input name=Excluir type=button value=\"".RetornaFraseDaLista($lista_frases_geral,68)."\" onClick=\"excluirSelecionados();\">");
      }

      echo("</td></tr>\n");
      echo("<tr height=1><td colspan=5>\n");

      // 69 - Mover selecionados (gen)

      echo("<input type=button value=\"".RetornaFraseDaLista($lista_frases_geral,69)."\"  onClick=\"moverSelecionados();\">\n");

      echo("</td></tr>\n");
    }
    echo("</table>\n");
    echo("</form>\n");

    /* Layers */

    /* Menu */
    echo("<div id=menu class=block visibility=hidden onContextMenu='return(false);'>\n");
    echo("<table class=wtfield cellspacing=1 cellpadding=1 border=2>\n");
    echo("  <tr>\n");
    echo("    <td class=bgcolor align=right><a href=# onClick=\"EscondeLayer(cod_menu);return(false);\"><img src=../figuras/x.gif border=0 ></a></td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("      <td>\n");
    /* 21 - Ver (gen) */
//    echo("        <a class=text href=ver.php onClick=\"this.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_item='+js_cod_item\">".RetornaFraseDaLista($lista_frases_geral,21)."</a><br>\n");
    echo("        <a class=text href=ver.php onClick=\"this.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_item='+js_cod_item\">".RetornaFraseDaLista($lista_frases_geral,21)."</a><br>\n");
    /* 9 - Editar (gen) */
//    echo("        <a class=text href=editar_material.php onClick=\"this.search='?".RetornaSessionID()."&origem=material&cod_curso=".$cod_curso."&cod_topico=".$cod_topico_raiz."&origem=material&cod_item='+js_cod_item\">".RetornaFraseDaLista($lista_frases_geral,9)."</a><br>\n");
    echo("        <a class=text href=editar_material.php onClick=\"this.search='?".RetornaSessionID()."&origem=material&cod_curso=".$cod_curso."&cod_topico=".$cod_topico_raiz."&origem=material&cod_item='+js_cod_item\">".RetornaFraseDaLista($lista_frases_geral,9)."</a><br>\n");
    /* 25 - Mover (gen) */
    echo("        <a class=text href=# onClick=\"js_tipo_item='item';MostraLayer(cod_mover,0);return(false);\">".RetornaFraseDaLista($lista_frases_geral,25)."</a><br>\n");
    /* 19 - Mudar Posi�o */
    echo("        <a class=text href=# onClick=\"AtualizaCamposPosicao('item');MostraLayer(cod_mudar_pos,0);return(false);\">".RetornaFraseDaLista($lista_frases,19)."</a><br>\n");
    /* 1 - Apagar (gen) */
    if ($cod_ferramenta==3)
      echo("        <a class=text href=material.php onClick=\"this.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&acao=apagaritem&cod_item='+js_cod_item;EscondeLayers();return(TemCertezaApagarAtividade());\">".RetornaFraseDaLista($lista_frases_geral,1)."</a><br>\n");
    else
      echo("        <a class=text href=material.php onClick=\"this.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&acao=apagaritem&cod_item='+js_cod_item;EscondeLayers();return(TemCertezaApagar());\">".RetornaFraseDaLista($lista_frases_geral,1)."</a><br>\n");

    echo("      </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("</div>\n");
    echo("\n");

    /* Menu T�icos */
    echo("<div id=menutop class=block visibility=hidden onContextMenu='return(false);'>\n");
    echo("<table class=wtfield cellspacing=1 cellpadding=1 border=2>\n");
    echo("  <tr>\n");
    echo("    <td class=bgcolor align=right><a href=# onClick=EscondeLayer(cod_menu_top);return(false);><img src=../figuras/x.gif border=0 ></a></td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    /* 34 - Abrir (gen) */
    echo("      <a href=material.php onClick=\"this.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz='+js_cod_topico\" class=text>".RetornaFraseDaLista($lista_frases_geral,34)."</a><br>\n");
    /* 19 - Renomear (gen) */
    echo("      <a href=# onClick=\"MostraLayerTopico(cod_ren_top,0,js_cod_topico,js_nome_topico);EscondeLayer(cod_menu_top);return(false);\" class=text>".RetornaFraseDaLista($lista_frases_geral,19)."</a><br>\n");
    /* 25 - Mover (gen) */
    echo("      <a href=# onClick=\"js_tipo_item='topico';EscondeLayers();MostraLayer(cod_mover,0);return(false);\" class=text>".RetornaFraseDaLista($lista_frases_geral,25)."</a><br>\n");
    /* 19 - Mudar Posi�o */
    echo("        <a class=text href=# onClick=\"AtualizaCamposPosicao('topico');MostraLayer(cod_mudar_pos,0);return(false);\">".RetornaFraseDaLista($lista_frases,19)."</a><br>\n");
    /* 1 - Apagar (gen) */
    echo("      <a href=# onClick=\"EscondeLayers();return(TemCertezaApagarTop(js_cod_topico));\" class=text>".RetornaFraseDaLista($lista_frases_geral,1)."</a><br>\n");

    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("</div>\n");

    /* Mover */
    echo("<div id=mover class=block visibility=hidden onContextMenu='return(false);'>\n");
    echo("<table class=wtfield cellspacing=1 cellpadding=1 border=2>\n");
    echo("  <tr>\n");
    echo("    <td class=bgcolor align=right><a href=# onClick=\"EscondeLayer(cod_mover);return(false);\"><img src=../figuras/x.gif border=0></a></td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <font class=text>\n");
    /* 20 - Escolha a pasta destino: */
    echo("".RetornaFraseDaLista($lista_frases,20)."<br>\n");
    echo("      </font>\n");
    $lista_topicos=RetornaListaDeTopicos($sock, $tabela);
    if (count($lista_topicos)>0)
      foreach ($lista_topicos as $cod => $linha_topico)
      {
        if ($cod_topico_raiz==$linha_topico['cod_topico'])
          echo("      ".$linha_topico['espacos']."<b class=text><img src=\"../figuras/pasta.gif\" border=0>".$linha_topico['topico']."</b><br>\n");
        else
          echo("      ".$linha_topico['espacos']."<a class=text href=material.php onClick=\"EscondeLayer(cod_mover);MoverItem(this,".$linha_topico['cod_topico'].");\"><img src=\"../figuras/pasta.gif\" border=0>".$linha_topico['topico']."</a><br>\n");
      }
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("</div>\n");

    /* Novo T�ico */
    echo("<div id=novotop class=block visibility=hidden onContextMenu='return(false);'>\n");
    echo("<form name=form_novo_top method=post action=material.php onSubmit='return(testa_titulo_criar(this));'>\n");
    echo(RetornaSessionIDInput());
    echo("<table class=wtfield cellspacing=1 cellpadding=1 border=2>\n");
    echo("  <tr>\n");
    echo("    <td class=bgcolor align=right><a href=# onClick=EscondeLayer(cod_novo_top);return(false);><img src=../figuras/x.gif border=0></a></td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    /* 21 - Digite o nome da pasta a ser criada aqui: */
    echo("      <font class=text>".RetornaFraseDaLista($lista_frases,21)."</font><BR>\n");
    echo("      <input type=text name=novo_nome value=\"\" maxlength=150><BR>\n");
    echo("      <input type=hidden name=cod_curso value=$cod_curso>\n");
    echo("      <input type=hidden name=acao value=novotopico>\n");
    echo("      <input type=hidden name=cod_topico_raiz value=$cod_topico_raiz>\n");
    /* 18 - Ok (gen) */
    echo("      <input type=submit value=".RetornaFraseDaLista($lista_frases_geral,18).">\n");
    /* 2 - Cancelar (gen) */
    echo("      &nbsp;&nbsp;<input type=button value=".RetornaFraseDaLista($lista_frases_geral,2)." onClick=\"EscondeLayer(cod_novo_top);\">\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("</form>\n");
    echo("</div>\n");

    /* Renomear */
    echo("<div id=renomeartop class=block visibility=hidden onContextMenu='return(false);'>\n");
    echo("<form method=post name=form_renomear_top action=material.php onSubmit=\"this.cod_topico.value=js_cod_topico;return(PossoRenomear(this));\">\n");
    echo(RetornaSessionIDInput());

    echo("<table class=wtfield border=2 cellspacing=1 cellpadding=1>\n");
    echo("  <tr>\n");
    echo("    <td class=bgcolor align=right><a href=# onClick=EscondeLayer(cod_ren_top);return(false);><img src=../figuras/x.gif border=0 ></a></td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    /* 22 - Digite o novo t�ulo do item: */
    echo("      <font class=text>".RetornaFraseDaLista($lista_frases,22)."</font><BR>\n");
    echo("      <input type=text name=novo_nome value=\"\" maxlength=150><BR>\n");
    echo("      <input type=hidden name=cod_curso value=$cod_curso>\n");
    echo("      <input type=hidden name=cod_topico value=\"\">\n");
    echo("      <input type=hidden name=acao value=renomeartop>\n");
    echo("      <input type=hidden name=cod_topico_raiz value=$cod_topico_raiz>\n");
    /* 18 - Ok (gen) */
    echo("      <input type=submit value=".RetornaFraseDaLista($lista_frases_geral,18).">\n");
    /* 2 - Cancelar (gen) */
    echo("      &nbsp;&nbsp;<input type=button value=".RetornaFraseDaLista($lista_frases_geral,2)." onClick=\"EscondeLayer(cod_ren_top);\">\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("</form>\n");
    echo("</div>\n");

    /* Mudar Compartilhamento */
    echo("<div id=comp class=block visibility=hidden onContextMenu='return(false);'>\n");
    echo("<script language=JavaScript>\n");
    if (count($lista_itens)>0)
      foreach ($lista_itens as $cod => $linha_item)
      {
        echo("  js_comp[".$linha_item['cod_item']."]=\"".$linha_item['tipo_compartilhamento']."\";\n");
      }
    echo("</script>\n");
    echo("<form method=post name=form_comp action=material.php>\n");
    echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");

    echo("  <input type=hidden name=cod_topico_raiz value=".$cod_topico_raiz.">\n");
    echo("  <input type=hidden name=cod_item value=\"\">\n");
    echo("  <input type=hidden name=acao value=mudarcomp>\n");
/*    echo("  <input type=hidden name=tipo_comp value=\"\">\n");*/
    echo("<table class=wtfield cellspacing=1 cellpadding=1 border=2>\n");
    echo("  <tr>\n");
    echo("    <td class=bgcolor align=right colspan=2><a href=# onClick=EscondeLayer(cod_comp);return(false);><img src=../figuras/x.gif border=0></a></td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <table class=wtfield border=0>\n");
    echo("        <tr>\n");
    echo("          <td>\n");
    echo("            <input type=radio name=tipo_comp value=\"F\" class=wtfield onClick=\"submit();\">\n");
    echo("          </td>\n");
    echo("          <td>\n");
    /* 17 - Compartilhado com formadores */
    echo("            <font class=text><nobr>".RetornaFraseDaLista($lista_frases,17)."</nobr></font>\n");
    echo("          </td>\n");
    echo("        </tr>\n");
    echo("        <tr>\n");
    echo("          <td>\n");
    echo("            <input type=radio name=tipo_comp value=\"T\" class=wtfield onClick=\"submit();\">\n");
    echo("          </td>\n");
    echo("          <td>\n");
    /* 16 - Totalmente compartilhado */
    echo("            <font class=text><nobr>".RetornaFraseDaLista($lista_frases,16)."</nobr></font>\n");
    echo("          </td>\n");
    echo("        </tr>\n");
    echo("      </table>\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("</form>\n");
    echo("</div>\n");

    /* Mudar Posi�o */
    echo("<div id=mudarpos class=block visibility=hidden onContextMenu='return(false);'>\n");
    echo("<form method=post name=form_posicao action=\"material.php\" onSubmit=\"return(MudarPosicao(this));\">\n");
    echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");

    echo("<table class=wtfield cellspacing=1 cellpadding=1 border=2>\n");
    echo("  <tr>\n");
    echo("    <td class=bgcolor align=right><a href=# onClick=\"EscondeLayer(cod_mudar_pos);return(false);\"><img src=../figuras/x.gif border=0></a></td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <font class=text>\n");
    /* 23 - Digite o nmero da posi�o em */
    echo("      ".RetornaFraseDaLista($lista_frases,23)."<br>\n");
    /* 24 - que a atividade deve aparecer: */
    echo("      ".RetornaFraseDaLista($lista_frases,24)."<br>\n");
    echo("      </font>\n");
    echo("      <input type=text name=posicao value=\"\" maxlength=4 size=4><br>\n");
    echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("      <input type=hidden name=cod_topico_raiz value=".$cod_topico_raiz.">\n");
    echo("      <input type=hidden name=cod_item value=\"\">\n");
    echo("      <input type=hidden name=acao value=\"mudarposicao\">\n");
    echo("      <input type=hidden name=cod_topico value=\"\">\n");
    /* 18 - Ok (gen) */
    echo("      <input type=submit value=".RetornaFraseDaLista($lista_frases_geral,18).">\n");
    /* 2 - Cancelar (gen) */
    echo("      <input type=button value=".RetornaFraseDaLista($lista_frases_geral,2)." onClick=\"EscondeLayer(cod_mudar_pos);return(false);\">\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("</form>\n");
    echo("</div>\n");

    /* Estrutura de T�icos */
    echo("<div id=topicos class=block visibility=hidden onContextMenu='return(false);'>\n");
    echo("<table class=wtfield cellspacing=1 cellpadding=1 border=2>\n");
    echo("  <tr>\n");
    echo("    <td class=bgcolor align=right><a href=# onClick=EscondeLayer(cod_topicos);return(false);><img src=../figuras/x.gif border=0></a></td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    if (count($lista_topicos)>0)
      foreach ($lista_topicos as $cod => $linha_topico)
      {
        if ($cod_topico_raiz==$linha_topico['cod_topico'])
          echo("      ".$linha_topico['espacos']."<b class=text><img src=\"../figuras/pasta.gif\" border=0>".$linha_topico['topico']."</b><br>\n");
        else
          echo("      ".$linha_topico['espacos']."<a class=text href=material.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz=".$linha_topico['cod_topico']." onClick=\"EscondeLayer(cod_topicos);\"><img src=\"../figuras/pasta.gif\" border=0>".$linha_topico['topico']."</a><br>\n");
      }
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("</div>\n");

    echo("</body>\n");
    echo("</html>\n");

    Desconectar($sock);
    exit;

/* Fim da P�ina do Formador ***************************/

  }

  if (EAluno($sock,$cod_curso,$cod_usuario) || EConvidado($sock,$cod_usuario))
  {

/* In�io da P�ina do Aluno e do Visitante ************/

    echo("<script language=JavaScript src=../bibliotecas/dhtmllib.js></script>\n");
    echo("<script language=JavaScript>\n");
    echo("var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
    echo("var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
    echo("var Xpos, Ypos;\n");

    echo("if (isNav)\n");
    echo("{\n");
    echo("  document.captureEvents(Event.MOUSEMOVE);\n");
    echo("}\n");
    echo("document.onmousemove = TrataMouse;\n");

    echo("function TrataMouse(e)\n");
    echo("{\n");
    echo("  Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
    echo("  Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
    echo("}\n");

    echo("function getPageScrollY()\n");
    echo("{\n");
    echo("  if (isNav)\n");
    echo("    return(window.pageYOffset);\n");
    echo("  if (isIE)\n");
    echo("    return(document.body.scrollTop);\n");
    echo("}\n");

    echo("function AjustePosMenuIE()\n");
    echo("{\n");
    echo("  if (isIE)\n");
    echo("    return(getPageScrollY());\n");
    echo("  else\n");
    echo("    return(0);\n");
    echo("}\n");

    echo("function Iniciar()\n");
    echo("{\n");
    echo("  cod_topicos = getLayer(\"topicos\");\n");
    echo("}\n");
    echo("\n");

    echo("function MostraLayer(cod_layer, ajuste)\n");
    echo("{\n");
    echo("  moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
    echo("  showLayer(cod_layer);\n");
    echo("}\n");

    echo("function EscondeLayer(cod_layer)\n");
    echo("{\n");
    echo("  hideLayer(cod_layer);\n");
    echo("}\n");

    echo("</script>\n");

    echo("<body link=#0000ff vlink=#0000ff onLoad=Iniciar();>\n");
    echo("\n");

    /* P�ina Principal */
    /* 1 - 3: Atividades
           4: Material de Apoio
           5: Leituras
           7: Parada Obrigat�ia
     */
    $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n";

    /* Cabecalho */
    echo(PreparaCabecalho($cod_curso,$cabecalho,$cod_ferramenta,1));


    $lista_topicos_ancestrais=RetornaTopicosAncestrais($sock, $tabela, $cod_topico_raiz);
    unset($path);
    foreach ($lista_topicos_ancestrais as $cod => $linha)
    {
      if ($cod_topico_raiz!=$linha['cod_topico'])
      {
        $path="<a class=text href=\"material.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz=".$linha['cod_topico']."\">".$linha['topico']."</a> &gt;&gt; ".$path;
      }
      else
      {
        $path="<b class=text>".$linha['topico']."</b><br>\n";
      }
    }
    echo("<a href=# onMouseDown=\"MostraLayer(cod_topicos,0);return(false);\"><img src=\"../figuras/estrutura.gif\" border=0></a>");
    echo($path);
    echo("<p>\n");
    echo("<table border=0 width=100% cellspacing=0>\n");
    echo("  <tr>\n");
    echo("    <td width=10 class=colorfield width=1%>&nbsp;</td>\n");
    /* 12 - 3: Atividade
            4: Material de Apoio
            5: Leitura
            7: Parada Obrigat�ia
     */
    echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases,12)."</td>\n");
    /* 13 - Data */
    echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,13)."</td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td colspan=2 height=5></td>\n");
    echo("  </tr>\n");
    $lista_topicos=RetornaTopicosDoTopico($sock, $tabela, $cod_topico_raiz);
    $cont=0;
    if (count($lista_topicos)>0)
      foreach ($lista_topicos as $cod => $linha_topico)
      {
        $data=UnixTime2Data($linha_topico['data']);
        if ($linha_topico['tipo_compartilhamento']=="T")
        {
          $cont++;
          $max_data=RetornaMaiorData($sock,$tabela,$linha_topico['cod_topico'],'T',$linha_topico['data']);
          if ($data_acesso<$max_data)
          {
            $marcaib="<b>";
            $marcafb="</b>";
          }
          else
          {
            $marcaib="";
            $marcafb="";
          }
          echo("  <tr>\n");
          echo("    <td width=10 align=center><a class=text href=\"material.php?".RetornaSessionID()."&cod_curso=$cod_curso&cod_topico_raiz=".$linha_topico['cod_topico']."\"><img src=../figuras/pasta.gif border=0></a></td>\n");
          echo("    <td>".$marcaib."<a class=text href=\"material.php?".RetornaSessionID()."&cod_curso=$cod_curso&cod_topico_raiz=".$linha_topico['cod_topico']."\">".$linha_topico['topico']."</a>".$marcafb."</td>\n");
          echo("    <td align=center>".$marcaib.UnixTime2Data($linha_topico['data']).$marcafb."</td>\n");

          echo("  </tr>\n");
          echo("  <tr>\n");
          echo("    <td colspan=4 height=1><hr size=1></td>\n");
          echo("  </tr>\n");
        }
      }
    $lista_itens=RetornaItensDoTopico($sock, $tabela, $cod_topico_raiz);
    if (count($lista_itens)>0)
      foreach ($lista_itens as $cod => $linha_item)
      {
        $data=UnixTime2Data($linha_item['data']);
        if ($linha_item['tipo_compartilhamento']=="T")
        {
          $cont++;
          if ($data_acesso<$linha_item['data'])
          {
            $marcaib="<b>";
            $marcafb="</b>";
          }
          else
          {
            $marcaib="";
            $marcafb="";
          }
          if ($linha_item['status']=="E")
          {
            $linha_historico=RetornaUltimaPosicaoHistorico($sock, $tabela, $linha_item['cod_item']);
            if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario==$linha_historico['cod_usuario'])
            {
//              echo ("<font size=8>TERCEIRO</font>\n");
              CancelaEdicao($sock, $tabela, $dir, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp,$criacao_avaliacao);
//              echo ("<font size=8>TERCEIRO</font>\n");
              $titulo=$marcaib."<a class=text href=\"ver.php?".RetornaSessionID()."&cod_curso=$cod_curso&cod_item=".$linha_item['cod_item']."&cod_topico_raiz=".$cod_topico_raiz."\">".$linha_item['titulo']."</a>".$marcafb;
              $icone="<a href=\"ver.php?".RetornaSessionID()."&cod_curso=$cod_curso&cod_item=".$linha_item['cod_item']."&cod_topico_raiz=".$cod_topico_raiz."\"><img src=../figuras/arqp.gif border=0></a>";
            }
            else
            {
              /* 18 - Em Edi�o */
              $titulo="<font class=textgray>".$linha_item['titulo']."</font> <a href=# class=text onClick=\"window.open('em_edicao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_item=".$linha_item['cod_item']."&origem=material','EmEdicao','width=300,height=220,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">".RetornaFraseDaLista($lista_frases,18)."</a>";
              $icone="<img src=../figuras/arqp.gif border=0>".$marca;
              $nao_exibir=true; /* Alunos n� ve� itens sendo editados */
            }
          }
          else
          {
            if ($linha_item['status']!="C")
            {
              $titulo=$marcaib."<a class=text href=\"ver.php?".RetornaSessionID()."&cod_curso=$cod_curso&cod_item=".$linha_item['cod_item']."&cod_topico_raiz=".$cod_topico_raiz."\">".$linha_item['titulo']."</a>".$marcafb;
              $icone="<a href=\"ver.php?".RetornaSessionID()."&cod_curso=$cod_curso&cod_item=".$linha_item['cod_item']."&cod_topico_raiz=".$cod_topico_raiz."\"><img src=../figuras/arqp.gif border=0></a>";
            }
          }

          if ($linha_item['status']=="C")
          {
            if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario==$linha_item['cod_usuario'])
            {
//              echo ("<font size=8>QUARTO</font>\n");
              CancelaEdicao($sock, $tabela, $dir, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp,$criacao_avaliacao);
//              echo ("<font size=8>QUARTO</font>\n");
            }
          }
          else
          {
            if ($nao_exibir) /* Itens em edi�o n� s� visto por alunos */
            {
              $nao_exibir=false;
            }
            else
            {
              echo("  <tr>\n");
              echo("    <td width=10 align=center>".$icone."</td>\n");
              echo("    <td>".$titulo."</td>\n");
              echo("    <td align=center>".$marcaib."<font class=text>".UnixTime2Data($linha_item['data'])."</font>".$marcafb."</td>\n");

              echo("  </tr>\n");
              echo("  <tr>\n");
              echo("    <td colspan=4 height=1><hr size=1></td>\n");
              echo("  </tr>\n");
            }
          }
        }

      }
    if ($cont<1)
    {
      echo("  <tr class=text>\n");
      /* 15 - 3: N� h�nenhuma atividade
              4: N� h�nenhum material de apoio
              5: N� h�nenhuma leitura
              7: N� h�nenhuma parada obrigat�ia
       */
      echo("    <td class=text colspan=2>".RetornaFraseDaLista($lista_frases,15)."</td>\n");
      echo("  </tr>\n");
    }
    echo("</table>\n");
    echo("<br>&nbsp;\n");

    /* Estrutura de T�icos */
    echo("<div id=topicos class=block visibility=hidden onContextMenu=\"return(false);\">\n");
    echo("<table class=wtfield cellspacing=1 cellpadding=1 border=2>\n");
    echo("  <tr>\n");
    echo("    <td class=bgcolor align=right><a href=# onClick=EscondeLayer(cod_topicos);return(false);><img src=../figuras/x.gif border=0></a></td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    $lista_topicos=RetornaListaDeTopicos($sock, $tabela);
    if (count($lista_topicos)>0)
      foreach ($lista_topicos as $cod => $linha_topico)
      {
        if ($linha_topico['tipo_compartilhamento']!="F")
          if ($cod_topico_raiz==$linha_topico['cod_topico'])
            echo("      ".$linha_topico['espacos']."<b class=text><img src=\"../figuras/pasta.gif\" border=0>".$linha_topico['topico']."</b><br>\n");
          else
            echo("      ".$linha_topico['espacos']."<a class=text href=material.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz=".$linha_topico['cod_topico']." onClick=\"EscondeLayer(cod_topicos);\"><img src=\"../figuras/pasta.gif\" border=0>".$linha_topico['topico']."</a><br>\n");
      }
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("</div>\n");

    echo("</body>\n");
    echo("</html>\n");

    Desconectar($sock);
    exit;

/* Fim da P�ina do Aluno ******************************/

  }

?>
