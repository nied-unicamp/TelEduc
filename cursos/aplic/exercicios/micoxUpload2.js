function micoxUpload2(form,timeout,loading,callback,arqNum,pasta,nomeArq,cod_curso,cod_questao,cod_usuario){
/**
* micoxUpload2 - Submete um form para um iframe oculto e pega o resultado. Consequentemente pode
*               ser usado pra fazer upload de arquivos de forma assincrona.
* Versao: 2.0 - 02/01/2008
* Autor: Micox - www.elmicox.com - elmicox.blogspot.com
* Licenca: Creative Commons - http://creativecommons.org/licenses/by/2.5/br/
* Some Rights Reserved - http://creativecommons.org/licenses/by/2.5/
**/

	var $gE, addEvent, removeEvent, periodic, loadAnim, loaded, abortFrame; //small functions
	var error_prog = []; //errors by programer	
	var new_form, loading_msg, loadpos=0; //the new form that will replace old form AND loading msg
	var z, old_action, concat, timeload, timecounter=0, iframe, name;
	var loads = ['&nbsp;&nbsp;&nbsp;','.&nbsp;&nbsp;','..&nbsp;','...']; //loading animation
	//Gustavo
	var td;
	
	/*** small functions */
	$gE = function(quem){ return document.getElementById(quem) }
	addEvent = function(obj, evType, fn){
		if (obj.addEventListener){ obj.addEventListener(evType, fn, true) ; }
		if (obj.attachEvent) { obj.attachEvent("on"+evType, fn);}
	}
	removeEvent = function( obj, type, fn ) {
		if ( obj.detachEvent ) { obj.detachEvent( 'on'+type, fn ); }
		if ( obj.removeEventListener ) { obj.removeEventListener( type, fn, false ); }
	} 
	loadAnim = function(){ //get animation of array loads
		if(loading.indexOf('<img')<0){ // 3 dots just if no image
			if(loadpos>loads.length - 1){ loadpos = 0; }
			return loads[loadpos++] + ' ';
		}else{ return '';}	
	}
	periodic = function(){
		timecounter++ ;
		if(timecounter/2 > timeout && timeout > 0){ //timeout expired (timeout = 0 is infinite)
			clearInterval(timeload); //fim do contador
			abortFrame(name);
			loaded('timeout');
		}
		loading_msg.innerHTML = loading + nomeArq + ' ' + loadAnim() + '<br/>';
	}
	abortFrame = function(o_frame){ //stop iframe
		var o_frame = typeof(o_frame)=="string" ? $gE(o_frame):o_frame;
		if(!o_frame){ return false; }
		try{ o_frame.contentWindow.stop(); //FF e OP
		}catch(e){ 
			try{ o_frame.contentWindow.document.execCommand('stop');//IE
			} catch(e){ 	o_frame.src = ''; /* tenta parar mermo */ }
		}
	}
	cloneEvents = function(source2,target,recursive){
		for(var p in source2){ //all params
			try{if(source2[p].constructor==Function){
					target[p] = source2[p]
			}}catch(e){}
		}
		if(recursive){
			for(var el=0; el<source2.childNodes.length; el++){
				var elem = source2.childNodes[el]
				var elem_target = target.childNodes[el]
				if(elem.nodeType==1){
					cloneEvents(elem,elem_target);
				}
			}
		}
	}


	//testing callback
	if(typeof(callback)!='function'){ error_prog.push("The 'callback' parameter must be a function") }
	
	//testing if 'form' is a html object or a id string
	form = typeof(form)=="string" ? $gE(form):form;
	if(form.nodeName.toUpperCase()!='FORM'){
		error_prog.push("The first parameter must be a form element ID or a form element reference") }
		
	//testing if form have some input file
	var input_file = false;
	var infile = form.getElementsByTagName('input')
	for(z in infile){
		if(infile[z].type=='file'){
			if(infile[z].value==''){ 
				alert("The input is empty. I cant upload this.")
				return true;
			}else{
				input_file = infile[z];
			}
		}
	}
	if(input_file==false){ error_prog.push("The form must be a input type file") }
	
	//exit if programmer errors
	if(error_prog.length>0) {
		alert("Error in parameters of micoxUpload:\n\n" + error_prog.join('\n'));
		/* uncoment this if you want use try-catch-throw
		throw(error_prog.join('\n'))		*/		
		return true;		
	}
		
	//random id for multiple calls
	rand = (m=Math).round( 20 * m.random() );
	
	//adding callback function to global scope
	//window['micoxCallbackTemp' + rand] = callback
	
	//creating the iframe
	name = "micox-temp" + rand;
	iframe = document.createElement("iframe");
	iframe.setAttribute("id",name);
	iframe.setAttribute("name",name);
	iframe.setAttribute("width","0");
	iframe.setAttribute("height","0");
	iframe.setAttribute("border","0");
	iframe.setAttribute("style","width: 0; height: 0; border: none;");
	//add to document
	form.parentNode.appendChild(iframe);
	window.frames[name].name = name; //ie sucks
		
	//event after load
	loaded = function(){
		//var iframe2 = $gE(name);
		clearInterval(timeload); //fim do contador
		//first, removing the event of iframe
		removeEvent(iframe,'load',loaded)
		//removind loading msg
		//loading_msg.parentNode.removeChild(loading_msg);
		document.getElementById('divAnexando').className = 'divHidden';
		document.getElementById('divArquivo').className='';
		
		xajax_ExibeArquivoAnexadoDinamic(cod_curso,cod_questao,cod_usuario,arqNum,pasta,nomeArq,'');
		
		//removing old form
		form.parentNode.removeChild(form);	
		
		//calling callback with the return
		if(arguments[0]!='timeout'){
			callback(iframe.contentWindow.document.body.innerHTML);
		}else{
			callback('Timeout expired. ' + timeout + ' secs.');
		}
		
		//removing old iframe
		abortFrame(iframe);
		iframe.src=''; //to stop 'loadind' in FF. bug.
		iframe.parentNode.removeChild(iframe);
		delete iframe;
	}
	//adding the event
	addEvent(iframe,'load',loaded)
	
	//properties of form to a normal upload
	form.setAttribute("target",name);
	form.setAttribute("method","post");
	form.setAttribute("enctype","multipart/form-data");
	form.setAttribute("encoding","multipart/form-data");
	//aditional information if micoxUpload
	old_action = form.action;
	if(form.action.indexOf('?')>1){ concat = '&' } else { concat = '?' }
	form.setAttribute("action",form.action + concat + 'micoxUpload=1');
	
	//submit
	form.submit();
	
	//make loading
	document.getElementById('divArquivoEdit').className='divHidden';
    document.getElementById('divArquivo').className='divHidden';
	loading_msg = document.getElementById('divAnexando');
	loading_msg.className = '';
	loading_msg.innerHTML = loading+nomeArq;
    //form.parentNode.insertBefore(loading_msg,form);
	
	//making new form and hidden old form
	input_file.value='';
	form.reset();
	new_form = form.cloneNode(true);
	//cloneEvents(form,new_form,true);
	new_form.reset();
	new_form.action = old_action;
	form.style.display = 'none';
	form.parentNode.insertBefore(new_form,form);
	//if you want new input file before the 'loading div', use this above (invert the comment)
	//form.parentNode.insertBefore(new_form,loading_msg);

	timeload = setInterval(periodic,500);
	
	//no submit default
	return false;
	
}