<?php
$ferramenta_geral = 'geral';
$ferramenta_agenda = 'agenda';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';
$ctrl_agenda = '../../'.$ferramenta_agenda.'/controllers/';
$view_agenda = '../../'.$ferramenta_agenda.'/views/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$diretorio_jscss = '../../../web-content/js-css/';
$diretorio_imgs = '../../../web-content/imgs/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';
require_once $model_geral.'importar.inc';

// **************** VARIaVEIS DE ENTRADA ****************
// Recebe de 'importar_curso2.php'
//    codigo do curso

$cod_item = $_POST['cod_item'];
$cod_curso = $_POST['cod_curso'];
//    cdigo da categoria que estava sendo listada.
$cod_categoria = $_POST['cod_categoria'];
//    codigo do curso do qual itens serao importados
$cod_curso_import = $_POST['cod_curso_import'];
//    tipo do curso: A(ndamento), I(nscricoes abertas), L(atentes),
//  E(ncerrados)
$tipo_curso = $_POST['tipo_curso'];
if ('E' == $tipo_curso)
{
	//    periodo especificado para listar os cursos
	//  encerrados.
	$data_inicio = $_POST['data_inicio'];
	$data_fim = $_POST['data_fim'];
}
//    booleano, se o curso, cujos itens serao importados, foi
//  escolhido na lista de cursos compartilhados.
$curso_compartilhado = $_POST['curso_compartilhado'];
//    booleando, se o curso, cujos itens serao importados, e um
//  curso extraido.
$curso_extraido = $_POST['curso_extraido'];
//    codigo do topico do curso do qual itens serao importados.
$cod_topico_raiz_import = $_POST['cod_topico_raiz_import'];

// ******************************************************
$sock=AcessoSQL::Conectar("");
if ($curso_extraido)
	$diretorio_arquivos=Agenda::RetornaDiretorio($sock, 'Montagem');
else
	$diretorio_arquivos=Agenda::RetornaDiretorio($sock, 'Arquivos');
$diretorio_temp=Agenda::RetornaDiretorio($sock, 'ArquivosWeb');
AcessoSQL::Desconectar($sock);

$cod_ferramenta=1;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda=7;

require_once $view_administracao.'topo_tela.php';

$tabela="Agenda";
$dir="agenda";


//   echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmllib.js\"></script>\n");
echo("          <script type=\"text/javascript\" src=\"".$diretorio_jscss."javacrypt.js\" defer></script>\n");
echo("          <script type=\"text/javascript\" src=\"".$diretorio_jscss."dhtmllib.js\"></script>\n");

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
echo("              document.frmImportar.action = '".$view_agenda."importar_agenda.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=1';");
echo("              document.frmImportar.submit();\n");
echo("            }\n\n");

echo("          </script>\n\n");

require_once $view_administracao.'menu_principal.php';

AcessoSQL::Desconectar($sock);

/* Forms */
echo("    <form method=\"post\" name=\"frmImportar\">\n");
echo("      <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
echo("      <input type=\"hidden\" name=\"cod_categoria\" value=\"".$cod_categoria."\">\n");
echo("      <input type=\"hidden\" name=\"cod_curso_import\" value=\"".$cod_curso_import."\">\n");
echo("      <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\">\n");
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

// Pagina Principal
// 1 - Agenda
$cabecalho = ("          <h4>"._("msg1_1"));
/*66 - Importando Agenda */
$cabecalho.= (" - "._("msg66_1")."</h4>\n");
echo($cabecalho);

// 3 A's - Muda o Tamanho da fonte
echo("<div id=\"mudarFonte\">\n");
echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/*Voltar*/
/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("msg509_-1")."&nbsp;</span></li></ul>\n");

$sock = AcessoSQL::Conectar($cod_curso_import);

$nome_curso_import = Cursos::NomeCurso($sock, $cod_curso_import);

if (!$curso_compartilhado)
{
	Usuarios::VerificaAcessoAoCurso($sock, $cod_curso_import, $cod_usuario);
	Usuarios::VerificaAcessoAFerramenta($sock,$cod_curso_import, $cod_usuario, $cod_ferramenta);
}

// Verificacao se o item esta em Edicao
// Se estiver, voltar a tela anterior, e disparar a tela de Em Edicao...
$linha = Agenda::RetornaUltimaPosicaoHistorico ($sock, $cod_item);

if ($linha['acao']=="E")
{
	if (($linha['data']<(time()-1800)) || ($cod_usuario == $linha['cod_usuario'])){
		Agenda::AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 0);
	}else{
		/* Está em edição... */
		echo("          <script type=\"text/javascript\">\n");
		echo("            window.open('".$view_agenda."em_edicao.php?&cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=importar_ver','EmEdicao','width=400,height=250,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
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

$dir_item_temp = Agenda::CriaLinkVisualizar($sock, $dir, $cod_curso_import, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

if (isset($caminho_original))
{
	// 88 - Importar selecionados
	echo("          "._("msg88_1"));
	echo($caminho_original);
	echo("          <br />\n");
}

/* Tabela Externa */
echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <ul class=\"btAuxTabs\">\n");
/*23 - (Geral) Voltar*/
echo("                  <li><span onClick=history.go(-1);>"._("msg23_-1")."</span></li>\n");
echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
/* Tabela Interna */
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
/*18 - Titulo */
echo("                    <td class=\"alLeft\" align=\"left\" width=\"82%\">"._("msg18_1")."</td>\n");
/*7 - Data */
echo("                    <td  align=\"center\">"._("msg7_1")."</td>\n");
echo("                  </tr>\n");

/* Conteudo */

$linha_item = Agenda::RetornaDadosDoItem($sock, $cod_item);

$icone="<img src=\"".$diretorio_imgs."arqp.gif\" border=\"0\" /> ";
$titulo=$linha_item['titulo'];

$data = Data::UnixTime2DataHora($linha_item['data']);

echo("                  <tr>\n");
echo("                    <td class=\"itens\">".$icone.$titulo."</td>\n");
echo("                    <td class=\"itens\" align=\"center\">".$data."</td>\n");
echo("                  </tr>\n");

/*Verifica se ha arquivo de entrada*/
$arquivo_entrada="";
$lista_arq=Agenda::RetornaArquivosAgendaVer($cod_curso_import, $dir_item_temp['diretorio']);
if (count($lista_arq)>0)
foreach($lista_arq as $cod => $linha1)
if ($linha1['Status'] && $linha1['Arquivo']!="")
	$arquivo_entrada = $dir_item_temp['link'].ConversorTexto::ConverteUrl2Html($linha1['Diretorio']."/".$linha1['Arquivo']);

/*Se houver, cria um iframe para exibi-lo*/
if(($linha_item['texto']=="")&&($arquivo_entrada!=""))
	$conteudo="<span id=\"text_".$linha_item['cod_item']."\"><iframe id=\"iframe_ArqEntrada\" texto=\"ArqEntrada\" src=\"".$arquivo_entrada."\" width=\"100%\" height=\"400\" frameBorder=\"0\" scrolling=\"Auto\"></iframe></span>";
/*Senaum, exibe o texto da agenda*/
else
	$conteudo="<span id=\"text_".$linha_item['cod_item']."\">".ConversorTexto::AjustaParagrafo($linha_item['texto'])."</span>";

if(($linha_item['texto']!="")||($arquivo_entrada!=""))
{
	echo("                  <tr class=\"head\">\n");
	/* 91 - Editar texto  */
	echo("                    <td colspan=\"4\" align=\"left\">"._("msg91_1")."</td>\n");
	echo("                  </tr>\n");
	echo("                  <tr>\n");
	echo("                    <td class=\"itens\" colspan=\"4\">\n");
	echo("                      <div class=\"divRichText\">\n");
	echo("                        ".$conteudo."\n");
	echo("                      </div>\n");
	echo("                    </td>\n");
	echo("                  </tr>\n");
}
 
$lista_arq = Agenda::RetornaArquivosAgendaVer($cod_curso_import, $dir_item_temp['diretorio']);

if(count($lista_arq)>0){
	echo("                  <tr class=\"head\">\n");
	/* 57(biblioteca) - Arquivos: */
	echo("                    <td colspan=\"4\">"._("msg57_-2")."</td>\n");
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

				$caminho_arquivo = $dir_item_temp['link'].ConversorTexto::ConverteUrl2Html($linha['Diretorio']."/".$linha['Arquivo']);

				if ($linha['Arquivo'] != "")
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
						$imagem    = "<img alt=\"\" src=\"".$diretorio_imgs."arqzip.gif\" border=\"0\" />";
						$tag_abre  = "<span class=\"link\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".$caminho_arquivo."');\" tipoArq=\"zip\" nomeArq=\"".htmlentities($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqOculto.">";
					}
					else
					{
						// arquivo comum
						$imagem    = "<img alt=\"\" src=\"".$diretorio_imgs."arqp.gif\" border=\"0\" />";
						$tag_abre  = "<span class=\"link\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".$caminho_arquivo."');\" tipoArq=\"comum\" nomeArq=\"".htmlentities($caminho_arquivo)."\" ".$arqOculto.">";
					}

					$tag_fecha = "</span>";

					echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
					echo("                          ".$espacos2.$espacos.$imagem.$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha['Tamanho']/1024),2)."Kb)");

					echo("<span id=\"local_oculto_".$conta_arq."\">");
					if ($linha['Status'])
						// 59 - entrada
						echo("<span id=\"arq_oculto_".$conta_arq."\">- <span style='color:red;'>"._("msg59_1")."</span></span>");
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
					$imagem    = "<img alt=\"\" src=\"".$diretorio_imgs."pasta.gif\" border=\"0\" />";
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
require_once $view_administracao.'tela2.php';
echo("        </td>\n");
echo("      </tr>\n");
echo("    </table>\n");
echo("  </body>\n");
echo("</html>\n");
AcessoSQL::Desconectar($sock);
?>

