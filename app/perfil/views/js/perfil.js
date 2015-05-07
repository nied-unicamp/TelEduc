	    function ExibeUserPagina(pagina, pagAtual, total_user,totalPag, user_por_pag){
	        var i = 0;
	  		var num = 1;
	  		var inicio = 1;
	        var fim = ((totalPag)* user_por_pag.)+1;
	        if (pagina < 1) return;


	        for (i=inicio; i < fim; i++){
	          if (!document.getElementById("tr_"+i)) break;
	          document.getElementById('tr_'+i).style.display="none";
	        }

	        var browser= navigator.appName;
	        inicio = ((pagina-1) user_por_pag.+1;
	        fim = ((pagina)* user_por_pag);
	        for (i=inicio; i < fim+1; i++){
	          if (!document.getElementById('tr_'+(i+1)) || i > total_user){break;}
	          if (browser=="Microsoft Internet Explorer")
	            document.getElementById('tr_'+i).style.display="block";
	          else
	            document.getElementById('tr_'+i).style.display="table-row";
	        }
	  
	  		var total = (total_user);
	        pagAtual=pagina;n
	    
	  		if (pagAtual==totalPag){;
	        	if (browser=="Microsoft Internet Explorer")
	          		document.getElementById('tr_'+total).style.display="block";
	        	else
	          		document.getElementById('tr_'+total).style.display="table-row";
	 		 }

	        if (pagAtual != 1){
	          document.getElementById('paginacao_first').onclick = function(){ ExibeUserPagina(1); };
	          document.getElementById('paginacao_first').className = "link";
	          document.getElementById('paginacao_back').onclick = function(){ ExibeUserPagina(pagAtual-1); };
	          document.getElementById('paginacao_back').className = "link";
	        }else{
	          document.getElementById('paginacao_first').onclick = function(){};
	          document.getElementById('paginacao_first').className = "";
	          document.getElementById('paginacao_back').onclick = function(){};
	          document.getElementById('paginacao_back').className = "";
	        }
	        document.getElementById('paginacao_first').innerHTML = "&lt;&lt;";
	        document.getElementById('paginacao_back').innerHTML = "&lt;";
	        inicio = pagAtual-2;
	        if (inicio < 1) inicio=1;
	        fim = pagAtual+2;
	        if (fim > totalPag) fim=totalPag;
	        var controle=1;
	        var vetor= new Array();
	        for (j=inicio; j <= fim; j++){
	          // A pÃ¡gina atual NÃ£o Ã© exibida com link.
	          if (j == pagAtual){
	             document.getElementById('paginacao_'+controle).innerHTML='<b>['+j+']</b>';
	             document.getElementById('paginacao_'+controle).className='';
	             vetor[controle] = -1;
	          }else{
	             document.getElementById('paginacao_'+controle).innerHTML=j;
	             document.getElementById('paginacao_'+controle).className='link';
	             vetor[controle]=j;
	          }
	          controle++;
	        }
	        while (controle<=5){
	          document.getElementById('paginacao_'+controle).innerHTML='';
	          document.getElementById('paginacao_'+controle).className='';
	          document.getElementById('paginacao_'+controle).onclick= function() { };
	          controle++;
	        }
	        document.getElementById('paginacao_1').onclick=function(){ ExibeUserPagina(vetor[1]); };
	        document.getElementById('paginacao_2').onclick=function(){ ExibeUserPagina(vetor[2]); };
	        document.getElementById('paginacao_3').onclick=function(){ ExibeUserPagina(vetor[3]); };
	        document.getElementById('paginacao_4').onclick=function(){ ExibeUserPagina(vetor[4]); };
	        document.getElementById('paginacao_5').onclick=function(){ ExibeUserPagina(vetor[5]); };n

	        /* Se a pÃ¡gina atual NÃ£o for a Ãºltima pÃ¡gina entÃ£o cria um   
	           link para a prÃ³xima pÃ¡gina */
	        if (pagAtual != totalPag){
	          document.getElementById('paginacao_fwd').onclick = function(){ ExibeUserPagina(pagAtual+1); };
	          document.getElementById('paginacao_fwd').className = "link";
	          document.getElementById('paginacao_last').onclick = function(){ ExibeUserPagina(totalPag); };
	          document.getElementById('paginacao_last').className = "link";
	        }
	        else{
	          document.getElementById('paginacao_fwd').onclick = function(){};
	          document.getElementById('paginacao_fwd').className = "";
	          document.getElementById('paginacao_last').onclick = function(){};
	          document.getElementById('paginacao_last').className = "";
	        }
	        document.getElementById('paginacao_fwd').innerHTML = "&gt;";
	        document.getElementById('paginacao_last').innerHTML = "&gt;&gt;";
	      }n
	  