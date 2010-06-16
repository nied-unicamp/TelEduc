/*var isRichText = false;
var rng;
var currentRTE;
var allRTEs = "";

var isIE;
var isGecko;
var isSafari;
var isKonqueror;

var imagesPath;
var includesPath;
var cssFile;
var generateXHTML;

var lang = "en";
var encoding = "iso-8859-1";*/  


function writeRichTextOnJS(rte, html, width, height, buttons, readOnly, id, nobuttons) {
	  var cod=id;
      id='text_'+id;
      
	  var local = document.getElementById(id);
	  var formTmp;
	  style = "width:90%;height:100px;display:none;";
	  formTmp = ('<div class="rteDiv">');
	  formTmp += ('<textarea name="msg_corpo" style="'+style+'">'+html+'</textarea>');
	  formTmp += ('</div>');
      local.innerHTML = formTmp;
      loadEditor('msg_corpo');
      //CKEDITOR.replace('msg_corpo',{});
	  //CKEDITOR.replace('msg_corpo',{});
        /*if(!typeof(nobuttons)){
          nobuttons = false;
        }
        var cod=id;
        id='text_'+id;
	//if (isRichText) {
        var local = document.getElementById(id);
		/*if (allRTEs.length > 0) allRTEs += ";";
		allRTEs += rte;*/
		
		/*if (readOnly) buttons = false;
		
		//adjust minimum table widths
		if (isIE) {
			if (buttons && (width < 540)) width = 540;
			var tablewidth = width;
		} else {
			if (buttons && (width < 540)) width = 540;
			var tablewidth = width + 4;
		}
		var formTmp;
		formTmp=('<div class="rteDiv">');
		/*if (buttons == true) {
			formTmp+=('<table class="rteBack" cellpadding=2 cellspacing=0 id="Buttons1_' + rte + '" width="' + tablewidth + '">');
			formTmp+=('	<tr>');
			formTmp+=('		<td>');
                        formTmp+=('			<select id="formatblock_' + rte + '" onchange="selectFont(\'' + rte + '\', this.id);">');
			formTmp+=('				<option value="">[Estilo]</option>');
			formTmp+=('				<option value="<p>">Par&aacute;grafo &lt;p&gt;</option>');
			formTmp+=('				<option value="<h1>">T&iacute;tulo 1 &lt;h1&gt;</option>');
			formTmp+=('				<option value="<h2>">T&iacute;tulo 2 &lt;h2&gt;</option>');
			formTmp+=('				<option value="<h3>">T&iacute;tulo 3 &lt;h3&gt;</option>');
			formTmp+=('				<option value="<h4>">T&iacute;tulo 4 &lt;h4&gt;</option>');
			formTmp+=('				<option value="<h5>">T&iacute;tulo 5 &lt;h5&gt;</option>');
			formTmp+=('				<option value="<h6>">T&iacute;tulo 6 &lt;h6&gt;</option>');
			formTmp+=('				<option value="<address>">Endere&ccedil;o &lt;ADDR&gt;</option>');
			formTmp+=('				<option value="<pre>">Formatado &lt;pre&gt;</option>');
			formTmp+=('			</select>');
			formTmp+=('		</td>');
			formTmp+=('		<td>');
			formTmp+=('			<select id="fontname_' + rte + '" onchange="selectFont(\'' + rte + '\', this.id)">');
			formTmp+=('				<option value="Font" selected>[Fonte]</option>');
			formTmp+=('				<option value="Arial, Helvetica, sans-serif">Arial</option>');
			formTmp+=('				<option value="Courier New, Courier, mono">Courier New</option>');
			formTmp+=('				<option value="Times New Roman, Times, serif">Times New Roman</option>');
			formTmp+=('				<option value="Verdana, Arial, Helvetica, sans-serif">Verdana</option>');
			formTmp+=('			</select>');
			formTmp+=('		</td>');
			formTmp+=('		<td>');
			formTmp+=('			<select unselectable="on" id="fontsize_' + rte + '" onchange="selectFont(\'' + rte + '\', this.id);">');
			formTmp+=('				<option value="Size">[Tamanho]</option>');
			formTmp+=('				<option value="1">1</option>');
			formTmp+=('				<option value="2">2</option>');
			formTmp+=('				<option value="3">3</option>');
			formTmp+=('				<option value="4">4</option>');
			formTmp+=('				<option value="5">5</option>');
			formTmp+=('				<option value="6">6</option>');
			formTmp+=('				<option value="7">7</option>');
			formTmp+=('			</select>');
			formTmp+=('		</td>');

		        formTmp+=('	</tr>');
			formTmp+=('</table>');
			formTmp+=('<table class="rteBack" cellpadding="0" cellspacing="0" id="Buttons2_' + rte + '" width="' + tablewidth + '">');
			formTmp+=('	<tr>');
			formTmp+=('		<td><img id="bold" class="rteImage" src="' + imagesPath + 'bold.gif" width="25" height="24" alt="Negrito" title="Negrito" onClick="rteCommand(\'' + rte + '\', \'bold\', \'\')"></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'italic.gif" width="25" height="24" alt="It�ico" title="It�ico" onClick="rteCommand(\'' + rte + '\', \'italic\', \'\')"></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'underline.gif" width="25" height="24" alt="Sublinhado" title="Sublinhado" onClick="rteCommand(\'' + rte + '\', \'underline\', \'\')"></td>');
			formTmp+=('		<td><img class="rteVertSep" src="' + imagesPath + 'blackdot.gif" width="1" height="20" border="0" alt=""></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'left_just.gif" width="25" height="24" alt="Alinhar �esquerda" title="Alinhar �esquerda" onClick="rteCommand(\'' + rte + '\', \'justifyleft\', \'\')"></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'centre.gif" width="25" height="24" alt="Centralizado" title="Centralizado" onClick="rteCommand(\'' + rte + '\', \'justifycenter\', \'\')"></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'right_just.gif" width="25" height="24" alt="Alinhar �direita" title="Alinhar �direita" onClick="rteCommand(\'' + rte + '\', \'justifyright\', \'\')"></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'justifyfull.gif" width="25" height="24" alt="Justificar" title="Justificar" onclick="rteCommand(\'' + rte + '\', \'justifyfull\', \'\')"></td>');
			formTmp+=('		<td><img class="rteVertSep" src="' + imagesPath + 'blackdot.gif" width="1" height="20" border="0" alt=""></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'hr.gif" width="25" height="24" alt="Linha horizontal" title="Linha Horizontal" onClick="rteCommand(\'' + rte + '\', \'inserthorizontalrule\', \'\')"></td>');
			formTmp+=('		<td><img class="rteVertSep" src="' + imagesPath + 'blackdot.gif" width="1" height="20" border="0" alt=""></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'numbered_list.gif" width="25" height="24" alt="Lista Ordenada" title="Lista Ordenada" onClick="rteCommand(\'' + rte + '\', \'insertorderedlist\', \'\')"></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'list.gif" width="25" height="24" alt="Lista N� Ordenada" title="Lista N� Ordenada" onClick="rteCommand(\'' + rte + '\', \'insertunorderedlist\', \'\')"></td>');
			formTmp+=('		<td><img class="rteVertSep" src="' + imagesPath + 'blackdot.gif" width="1" height="20" border="0" alt=""></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'outdent.gif" width="25" height="24" alt="Remover Indenta�o" title="Remover Indenta�o" onClick="rteCommand(\'' + rte + '\', \'outdent\', \'\')"></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'indent.gif" width="25" height="24" alt="Indentar" title="Indentar" onClick="rteCommand(\'' + rte + '\', \'indent\', \'\')"></td>');
			formTmp+=('		<td><div id="forecolor_' + rte + '"><img class="rteImage" src="' + imagesPath + 'textcolor.gif" width="25" height="24" alt="Cor do Texto" title="Cor do Texto" onClick="dlgColorPalette(\'' + rte + '\', \'forecolor\', \'\')"></div></td>');
			formTmp+=('		<td><div id="hilitecolor_' + rte + '"><img class="rteImage" src="' + imagesPath + 'bgcolor.gif" width="25" height="24" alt="Cor de Fundo" title="Cor de Fundo" onClick="dlgColorPalette(\'' + rte + '\', \'hilitecolor\', \'\')"></div></td>');
			formTmp+=('		<td><img class="rteVertSep" src="' + imagesPath + 'blackdot.gif" width="1" height="20" border="0" alt=""></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'hyperlink.gif" width="25" height="24" alt="Inserir Link" title="Inserir Link" onClick="dlgInsertLink(\'' + rte + '\', \'link\')"></td>');
			formTmp+=('		<td><img class="rteImage" src="' + imagesPath + 'image.gif" width="25" height="24" alt="Inserir Imagem" title="Inserir Imagem" onClick="addImage(\'' + rte + '\')"></td>');
			formTmp+=('		<td><div id="table_' + rte + '"><img class="rteImage" src="' + imagesPath + 'insert_table.gif" width="25" height="24" alt="Inserir Tabela" title="Inserir Tabela" onClick="dlgInsertTable(\'' + rte + '\', \'table\', \'\')"></div></td>');
			formTmp+=('	</tr>');
			formTmp+=('</table>');
		}
		formTmp+=('<iframe id="' + rte + '" name="' + rte + '" width="' + width + 'px" height="' + height + 'px" src="' + includesPath + 'blank.htm"></iframe>');

		formTmp+=('<iframe width="154" height="104" id="cp' + rte + '" src="' + includesPath + 'palette.htm" marginwidth="0" marginheight="0" scrolling="no" style="visibility:hidden; position: absolute;"></iframe>');
		formTmp+=('<input type=\"hidden\" id=\"hdn' + rte + '\" name=\"' + rte + '\" value=\"\">');
        if(!nobuttons){
           formTmp+=('<table class="rteBack" cellpadding="0" cellspacing="0" id="Buttons2_' + rte + '" width="' + tablewidth + '">');
           formTmp+=(' <tr>');
           formTmp+=('  <td>');
           formTmp+=('      <ul class="rteBtConfirm">');
           formTmp+=('        <li><span onClick="EdicaoTexto(\''+cod+'\', \''+id+'\', \'canc\');" id="CancelaEdita" name="cancelar">Cancelar</span></li>');
           formTmp+=('        <li><span onClick="EdicaoTexto(\''+cod+'\', \''+id+'\', \'ok\');" id="OkEdita">OK</span></li>');
           formTmp+=('      </ul>');
           formTmp+=('	</td>');
           formTmp+=(' </tr>');
           formTmp+=('</table');
        }*/
		/*formTmp+=('<script type="text/javascript">');
		formTmp+=('		CKEDITOR.replace(\'msg_corpo\',{});');
		formTmp+=('</script>');
		formTmp+=('</div>');
        //local.innerHTML=formTmp;

        //var local2 = document.getElementById('hdn' + rte);
		//local2.value = html;
		//enableDesignMode(rte, html, readOnly);
	/*} else {
		if (!readOnly) {
			local.innerHTML+=('<textarea name="' + rte + '" id="' + rte + '" style="width: ' + width + 'px; height: ' + height + 'px;">' + html + '</textarea>');
		}else {
			local.innerHTML+=('<textarea name="' + rte + '" id="' + rte + '" style="width: ' + width + 'px; height: ' + height + 'px;" readonly>' + html + '</textarea>');
		}
	}*/
}
  
function clearNewRTE(rte, id) {
  //id2 = 'text_'+id;
	rte = 'cke_'+rte;
	
	if (CKEDITOR.instances[rte]) {
	    CKEDITOR.remove(CKEDITOR.instances[rte]);
	}
  /*id2 = 'msg_corpo';
  var element = document.getElementById(id2);
  id2.innerHTML = '';
  /*while(element.firstChild){
    element.removeChild(element.firstChild);
  }*/

  //writeRichTextOnJS(rte, '', 600, 200, true, false, id, true);
}

function loadEditor(id)
{
    var instance = CKEDITOR.instances[id];
    if(instance)
    {
        CKEDITOR.remove(instance);
    }
    CKEDITOR.replace(id);
}

function clearRTE(rte) {
	rte = 'cke_'+rte;
	if (CKEDITOR.instances[rte]) {
        CKEDITOR.remove(CKEDITOR.instances[rte]);
     }

}


function updateRTE(rte) {
	//if (!isRichText) return;
	
	//check for readOnly mode
	/*var readOnly = false;
	if (document.all) {
		if (frames[rte].document.designMode != "On") readOnly = true;
	} else {
		if (document.getElementById(rte).contentDocument.designMode != "on") readOnly = true;
	}*/
	
	/*if (isRichText && !readOnly) {
		//if viewing source, switch back to design view
		if ((document.getElementById("chkSrc" + rte)) && (document.getElementById("chkSrc" + rte).checked)) document.getElementById("chkSrc" + rte).click();
		setHiddenVal(rte);
	}*/
}
