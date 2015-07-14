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
      if(buttons == true){
          writeRichTextOnJSButtons(rte, html, width, height, buttons, readOnly, id, nobuttons);
      }
      else{
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
      }
}

function writeRichTextOnJSButtons(rte, html, width, height, buttons, readOnly, id, nobuttons) {
  var cod=id;
    id='text_'+id;
    
    var local = document.getElementById(id);
    var formTmp;
    style = "width:90%;height:100px;";
    formTmp = ('<div class="rteDiv">');
    formTmp += ('<textarea name="text_'+cod+'_text" id="text_'+cod+'_text" style="'+style+'">'+html+'</textarea>');
    if (typeof(textoOk) == 'undefined')
        textoOk = 'Ok';
    if (typeof(textoCancelar) == 'undefined')
      textoCancelar = 'Cancelar';
    formTmp += ('<br><input type="button" id="OkEdita" class="input" style="margin-bottom: 5px;margin-top:3px;margin-right:3px;" onclick="EdicaoTexto(\''+cod+'\', \''+id+'\', \'ok\');" value="'+textoOk+'"><input type="button" class="input" name="cancelar" id="CancelaEdita" style="margin-bottom: 5px;" onclick="EdicaoTexto(\''+cod+'\', \''+id+'\', \'canc\');" value="'+textoCancelar+'">');
    formTmp += ('</div>');
    local.innerHTML = formTmp;
    loadEditor('text_'+cod+'_text');
    CKEDITOR.on("instanceReady", function(event)
            {
                eval('CKEDITOR.instances.'+id+'_text'+'.focus();');
            });
}

function writeRichTextOnJSButtons_gabarito(rte, html, width, height, buttons, readOnly, id, nobuttons) {
	  var cod=id;
	  id='texto_'+id;
	  
	  var local = document.getElementById(id);
	  var formTmp;
	  style = "width:90%;height:100px;display:none;";
	  formTmp = ('<div class="rteDiv">');
	  formTmp += ('<textarea name="texto_'+cod+'_text" style="'+style+'">'+html+'</textarea>');
	  formTmp += ('<input type="button" id="OkEdita" class="input" style="margin-bottom: 5px;margin-top:3px;margin-right:3px;" onclick="EdicaoTexto_gabarito(\''+cod+'\', \''+id+'\', \'ok\');" value="OK"><input type="button" name="cancelar" id="CancelaEdita" class="input" onclick="EdicaoTexto_gabarito(\''+cod+'\', \''+id+'\', \'canc\');" value="Cancelar">');
	  formTmp += ('</div>');
	  local.innerHTML = formTmp;
	  loadEditor('texto_'+cod+'_text');
	  CKEDITOR.on("instanceReady", function(event)
	    		{
	    			eval('CKEDITOR.instances.'+id+'_text'+'.focus();');
	    		});

}

function writeRichTextOnJS_gabarito(rte, html, width, height, buttons, readOnly, id, nobuttons) {
	  if(buttons == true){
		  writeRichTextOnJSButtons_gabarito(rte, html, width, height, buttons, readOnly, id, nobuttons);
	  }
	  else{
		  var cod=id;
	      id='texto_'+id;
	      
		  var local = document.getElementById(id);
		  var formTmp;
		  style = "width:90%;height:100px;display:none;";
		  formTmp = ('<div class="rteDiv">');
		  formTmp += ('<textarea name="msg_corpo" style="'+style+'">'+html+'</textarea>');
		  formTmp += ('</div>');
	      local.innerHTML = formTmp;
	      loadEditor('msg_corpo');
	  }
}
  
function clearNewRTE(rte, id) {
	rte = 'cke_'+rte;
	
	if (CKEDITOR.instances[rte]) {
	    CKEDITOR.remove(CKEDITOR.instances[rte]);
	}
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


function updateRTE(rte) {}


function writeRichText(rte, html, width, height, buttons, readOnly, alignLeft) {
		document.writeln('<textarea name="' + rte + '" id="' + rte + '" style="width: ' + width + 'px; height: ' + height + 'px;">' + html + '</textarea>');
		loadEditor(rte);
}


function isEditorLoaded(id)
{
    var instance = CKEDITOR.instances[id];
    if(instance)
        return true;
    return false;
}