<?php
/* Layers */
if ($tela_formador || $tela_colaborador)
{
	switch($categ)
	{
		case 1: 
                        /*******************************************
                         LAYER para ADIANTAR/PRORROGAR enquetes EM ADAMENTO 
                         *******************************************/
                        echo("<div id=\"layer_adiantar_prorrogar\" class=\"popup\">\n");
                        echo("   <form method=\"POST\" name=\"form_adiantar_prorrogar\" action=\"".$_SERVER['REQUEST_URI']."\" onSubmit=\"this.idEnquete.value = selected_item ; return(PodeAdiantarProrrogar(this));\">\n");
                        echo("      <input type=hidden name='data_hoje' value=".UnixTime2Data(time())." />\n");
                        echo("      <input type=hidden name='hora_hoje' value=".UnixTime2Hora(time())." />\n");
			echo("      <div class=\"posX\">\n");
			echo("        <span onclick=\"EscondeLayer(lay_adiantar_prorrogar);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span>\n");
			echo("      </div>\n");
			echo("      <div class=int_popup>\n");
			echo("          <div class=ulPopup>\n");
                        /* 90 - Digite a nova data final da enquete: */
                        echo("          ".RetornaFraseDaLista($lista_frases,90)."<BR>\n");

			echo("          <input type=text class=\"input\" size=10 maxlength=10 id=\"data_fim\" name=\"data_fim\" value=\"".UnixTime2Data(time())."\" />\n");
  			echo("          <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('data_fim'),'dd/mm/yyyy',this);\"/>\n");
                        echo("          <input type=text class=\"input\" maxlength=5 size=5 name=hora_fim value='".UnixTime2Hora(time())."' onBlur='padroniza_hora(document.form_adiantar_prorrogar.hora_fim);'><br>\n");
                        echo("          <input type=hidden name=idEnquete value=\"\">\n");
                        echo("          <input type=hidden name=acao value=adiantar_prorrogar>\n");
                        /* 18 - Ok (gen) */
                        echo("          <input type=submit value=".RetornaFraseDaLista($lista_frases_geral,18).">\n");
                        /* 2 - Cancelar (gen) */
                        echo("          &nbsp;&nbsp;<input type=button value=".RetornaFraseDaLista($lista_frases_geral,2)." onClick=\"EscondeLayer(lay_adiantar_prorrogar);\">\n");
			echo("          </div>\n");
			echo("      </div>\n");
                        echo("   </form>\n");
                        echo("</div>\n");

			break;
		case 2: 
                        /*******************************************
                         LAYER para PRORROGAR enquetes ENCERRADAS
                         *******************************************/
                        echo("<div id=\"layer_adiantar_prorrogar\" class=\"popup\">\n");
                        echo("   <form method=\"POST\" name=\"form_adiantar_prorrogar\" action=\"".$_SERVER['REQUEST_URI']."\" onSubmit=\"this.idEnquete.value = selected_item ; return(PodeAdiantarProrrogar(this));\">\n");
                        echo("      <input type=hidden name='data_hoje' value=".UnixTime2Data(time()).">\n");
                        echo("      <input type=hidden name='hora_hoje' value=".UnixTime2Hora(time()).">\n");
			echo("      <div class=\"posX\">\n");
			echo("        <span onclick=\"EscondeLayer(lay_adiantar_prorrogar);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span>\n");
			echo("      </div>\n");
			echo("      <div class=int_popup>\n");
			echo("          <div class=ulPopup>\n");
                        /* 90 - Digite a nova data final da enquete: */
                        echo("     	".RetornaFraseDaLista($lista_frases,90)."<BR>\n");
			echo("          <input type=text class=\"input\" size=10 maxlength=10 id=\"data_fim\" name=\"data_fim\" value=\"".UnixTime2Data(time())."\" />\n");
  			echo("          <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('data_fim'),'dd/mm/yyyy',this);\"/>\n");
                        echo("     	 <input type=text class=\"input\" maxlength=5 size=5 name=hora_fim value='".UnixTime2Hora(time())."' onBlur='padroniza_hora(document.form_adiantar_prorrogar.hora_fim);'><br>\n");
                        echo("     	 <input type=hidden name=idEnquete value=\"\">\n");
                        echo("     	 <input type=hidden name=acao value=adiantar_prorrogar>\n");
                        /* 18 - Ok (gen) */
                        echo("     	<input type=submit value=".RetornaFraseDaLista($lista_frases_geral,18).">\n");
                        /* 2 - Cancelar (gen) */
                        echo("     	&nbsp;&nbsp;<input type=button value=".RetornaFraseDaLista($lista_frases_geral,2)." onClick=\"EscondeLayer(lay_adiantar_prorrogar);\">\n");
			echo("          </div>\n");
			echo("      </div>\n");
                        echo("   </form>\n");
                        echo("</div>\n");

			break;
		case "0":
		case "N":
                        /* Se estiver na tela de nova enquete ou edicao de enquete
			   inclui LAYER e FUNCOES do CALENDARIO */
			break;
	}
}

?>