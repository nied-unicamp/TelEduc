<?php

if ($_SESSION['cod_lingua_s']==3)
	$data_invertida_g=true;
else
	$data_invertida_g=false;

class DataJavaScript{
	
	/* *********************************************************************
	 GeraJSVerificacaoData - Echo que gera funcoes JavaScript de verificacao de campos de data
	entrada: nenhuma
	saida:   nenhuma
	Obs: - a funcao ja gera as tags <script> e </script>
	Funcoes JS geradas:
	- Bissexto(ano):    retorna true se o ano passado e bissexto, false caso contraio.
	- TemCaracteresEstranhos(valor_data) - retorna true se valor_data contiver caracteres que nao sejam digitos ou barra '/', false caso contrario.
	- PadronizaValorData(valor_data) - padroniza valor_data para o formato DD/MM/AAAA.
	- DataValida(campo_data) - verifica se o campo de data passado esta corretamente preenchido.
	*/
	function GeraJSVerificacaoData()
	{
		echo("<script type=\"text/javascript\">\n");
		echo("var ndias=new Array(31,28,31,30,31,30,31,31,30,31,30,31);\n");
		echo("var divisor=20; //divisor de janela de tempo\n");
		echo("var data_g='';\n");
	
		echo("function Bissexto(ano) \n");
		echo("{\n");
		echo("  return ((ano % 4 == 0) && ((ano % 100 != 0) || (ano % 400 ==0))) ;\n");
		echo("}\n");
	
		echo("function TemCaracteresEstranhos(valor_data) \n");
		echo("{\n");
		echo("  var bool_erro=false;\n");
		echo("  var i=0;\n");
		echo("  var c='';\n");
		echo("  while (i<valor_data.length && !bool_erro)\n");
		echo("  {\n");
		echo("    c=valor_data.charAt(i);\n");
		echo("    if ((c<'0' || c>'9') && c!='/') \n");
		echo("      bool_erro=true;\n");
		echo("    i++; \n");
		echo("  }\n");
		echo("  return(bool_erro);\n");
		echo("}\n");
	
		// Data em DD/MM/AAAA
		// Retorna array [0] == D, [1] == M, [2] == A
		echo("function RetornaArrayData(data)\n");
		echo("{\n");
		echo("  var array_ret = new Array(3);\n");
		echo("  b1=data.indexOf('/');\n");
		echo("  b2=data.indexOf('/',b1+1);\n");
		if ($data_invertida_g)
		{
			echo("  array_ret[1]=data.substring(0,b1)*1;  // mes eh o primeiro pedaco da string passada\n");
			echo("  array_ret[0]=data.substring(b1+1,b2)*1;  // dia eh o segundo pedaco da string passada\n");
		}
		else
		{
			echo("  array_ret[0]=data.substring(0,b1)*1;  // dia eh o primeiro pedaco da string passada\n");
			echo("  array_ret[1]=data.substring(b1+1,b2)*1;  // mes eh o segundo pedaco da string passada\n");
		}
		echo("  array_ret[2]=data.substring(b2+1,data.length)*1;\n");  // ano
		echo("  return array_ret;\n");
		echo("}\n");
	
		echo("function PadronizaCampoData(campo_data)\n");
		echo("{\n");
		echo("  var valor_data=campo_data.value;\n");
		echo("  var i=0;\n");
		echo("  var novovalor_data='';\n");
		echo("  var tmpvalor_data='';\n");
		echo("  var numbarras=0;\n");
		echo("  while (i<valor_data.length) \n");
		echo("  {\n");
		echo("    c=valor_data.charAt(i);\n");
		echo("    if (c=='-')\n");
		echo("      c='/';\n");
		echo("    if (c=='/')\n");
		echo("      numbarras++;\n");
		echo("    if (c!=' ')\n");
		echo("      tmpvalor_data+=c;\n");
		echo("    i++; \n");
		echo("  }\n");
		echo("  if (numbarras==2)\n");
		echo("  {\n");
		echo("    tmp_ar=RetornaArrayData(tmpvalor_data);\n");
		echo("    dia=tmp_ar[0];\n");
		echo("    mes=tmp_ar[1];\n");
		echo("    ano=tmp_ar[2]\n");
		echo("    if (ano>=0 && ano<divisor)\n");
		echo("      ano+=2000;\n");
		echo("    if (ano>=divisor && ano<=100)\n");
		echo("      ano+=1900;\n");
		echo("    if (dia>=1 && dia<10)\n");
		echo("      dia='0'+dia;\n");
		echo("    if (mes>=1 && mes<10)\n");
		echo("      mes='0'+mes;\n");
		if ($data_invertida_g)
		{
			echo("    novovalor_data=mes+'/'+dia+'/'+ano;\n");
		}
		else
		{
			echo("    novovalor_data=dia+'/'+mes+'/'+ano;\n");
		}
		echo("    data_g=novovalor_data;\n");
		echo("    return true;\n");
		echo("  }\n");
		echo("  else \n");
		echo("  {\n");
		echo("    data_g=valor_data;\n");
		echo("    return false;\n");
		echo("  }\n");
		echo("}\n");
	
		/*
		 Esta funcao nao gera retorno, ela verifica a variavel check e chama a funcao auxiliar
		Se vc precisar do retorno (true ou false) da funcao, chame DataValidaAux(campo)
		Exemplo:
		if (!DataValida(campo))  { Erro(); } ---> errado !
	
		if (!DataValidaAux(campo)) {Erro();} ---> certo !
		*/
		echo("var check=false;\n");
		echo("function DataValida(campo_data)\n");
		echo("{\n");
		echo("  if (check)\n");
		echo("    if (!DataValidaAux(campo_data))\n");
		echo("    {\n");
		echo("      check=false;\n");
		echo("      campo_data.select();\n");
		echo("      campo_data.focus();\n");
		echo("    }\n");
		echo("}\n");
	
		echo("function DataValidaAux(campo_data) \n");
		echo("{\n");
		echo("  var data = campo_data.value;\n");
		echo("  if (data=='') \n");
		echo("  {\n");
		// 17 - Voce deixou um campo de data vazio.
		// 18 - Por favor preencha-o com uma data.
		echo("    alert('Voce deixou um campo de data vazio. Por favor preencha-o com uma data.');\n");
		echo("    return(false);\n");
		echo("  }\n");
		echo("  else \n");
		echo("  {\n");
		echo("    if(PadronizaCampoData(campo_data))\n");
		echo("      data=data_g;\n");
		echo("    else\n");
		echo("    {\n");
		// 19 - Data invalida:
		// 20 - Por favor retorne e corrija.
		echo("      alert('Data invalida:'+data+'.Por favor retorne e corrija. ');\n");
		echo("      return false;\n");
		echo("    }\n");
		echo("    if (TemCaracteresEstranhos(data)) \n");
		echo("    {\n");
		// 21 - Voce digitou caracteres estranhos nesta data.
		// 22 - Use apenas digitos de 0 a 9 e a barra ( / ) para separar dia, mes e ano (exemplo: 31/01/2000).
		// 20 - Por favor retorne e corrija.
		echo("      alert('Voce digitou caracteres estranhos nesta data.\\nUse apenas digitos de 0 a 9 e a barra ( / ) para separar dia, mes e ano (exemplo: 31/01/2000).\\nPor favor retorne e corrija.');\n");
		echo("      return(false);\n");
		echo("    }\n");
		echo("    else \n");
		echo("    {\n");
		echo("      campo_data.value=data;\n");
		echo("      tmp_ar=RetornaArrayData(data);\n");
		echo("      dia=tmp_ar[0];\n");
		echo("      mes=tmp_ar[1];\n");
		echo("      ano=tmp_ar[2]\n");
		echo("      if (ano>=0 && ano<divisor)   ano=ano+2000;\n");
		echo("      if (ano>=divisor && ano<=99) ano+=1900;\n");
		echo("      if (Bissexto(ano)) \n");
		echo("        ndias[1]=29;\n");
		echo("      else \n");
		echo("        ndias[1]=28;\n");
		echo("      if (ano<1900 || ano>2100 || mes<1 || mes>12) \n");
		echo("      {\n");
		// 19 - Data invalida:
		// 20 - Por favor retorne e corrija.
		echo("      alert('Data invalida:'+data+'.Por favor retorne e corrija. ');\n");
		echo("        return(false);\n");
		echo("      } \n");
		echo("      else \n");
		echo("      {\n");
		echo("        if (dia<1 || dia>ndias[mes-1]) \n");
		echo("        {\n");
		// 19 - Data invalida:
		// 20 - Por favor retorne e corrija.
		echo("      alert('Data invalida:'+data+'.Por favor retorne e corrija. ');\n");
		echo("          return(false);\n");
		echo("        }\n");
		echo("      }\n");
		echo("    }\n");
		echo("  } \n");
		echo("  return(true);\n");
		echo("}\n");
	
		echo("  function padroniza_hora(hora)\n");
		echo("  {\n");
		echo("    var tmphora=hora.value;\n");
		echo("    var tmphora2='';\n");
	
		/***********************************************/
		/*Caso o usuario nao tenha digitado a hora e o
		 objeto perca o foco*/
		/***********************************************/
		echo("    if (tmphora=='')");
		echo("    {\n");
		echo("       tmphora='23:59';\n");
		echo("    }\n");
		/***********************************************/
		echo("    i=tmphora.indexOf(':');\n");
		echo("    if (i==-1)\n");
		echo("    {\n");
		echo("      tmphora2=tmphora;\n");
		echo("      tmphora=tmphora2.substring(0,2)+':'+tmphora2.substring(2,4);\n");
		echo("      i=tmphora.indexOf(':');\n");
		echo("    }\n");
		echo("    if (i==1)\n");
		echo("      tmphora='0'+tmphora;\n");
		echo("    while (tmphora.length<5)\n");
		echo("      tmphora=tmphora+'0';\n");
		echo("    hora.value=tmphora;\n");
		echo("  }\n");
	
		echo("  function hora_valida(hora)\n");
		echo("  {\n");
		echo("    var tmphora=hora.value;\n");
		echo("    var tmphora0=tmphora.substring(0,1);\n");
		echo("    var tmphora1=tmphora.substring(1,2);\n");
		echo("    var tmphora2=tmphora.substring(2,3);\n");
		echo("    var tmphora3=tmphora.substring(3,4);\n");
		echo("    var tmphora4=tmphora.substring(4,5);\n");
		echo("    if (tmphora0<'0' || tmphora0>'2' || tmphora1<'0' || tmphora1>'9' || tmphora2!=':' || tmphora3<'0' || tmphora3>'5' || tmphora4<'0' || tmphora4>'9')\n");
		echo("    {\n");
		/* 374 - Hora invalida */
		// echo("      alert('".RetornaFraseDaLista($lista_frases, 374)."' + ':' + tmphora + '(hh:mm)');\n");
		echo("      return  false;\n");
		echo("    }\n");
		echo("    if (tmphora0=='2' && tmphora1>'3')\n");
		echo("    {\n");
		/* 374 - Hora invalida */
		// echo("      alert('".RetornaFraseDaLista($lista_frases, 374)."' + ':' + tmphora + '(hh:mm)');\n");
		echo("      return  false;\n");
		echo("    }\n");
		echo("    return true;\n");
		echo("  }\n");
	
		echo("</script>\n");
	
	}
	
	/* *********************************************************************
	 GeraJSComparacaoDatas - Echo que gera pacote JS de funcoes de comparacao de datas
	entrada: nenhuma
	saida:   nenhuma
	Funcoes geradas:
	- AnoMesDia(data) - retorna a data passada no formato AAAA/MM/DD, para fazer comparacoes cronologicas.
	- ComparaData(campo_data1,campo_data2) - retorna -1 se 1 < 2, 0 se 1 == 2, 1 se 1 > 2
	*/
	
	function GeraJSComparacaoDatas()
	{
		echo("<script type=\"text/javascript\">\n");
		echo("function AnoMesDia(data)\n");
		echo("{\n");
		echo("  tmp_ar=RetornaArrayData(data);\n");
		echo("  dia=tmp_ar[0];\n");
		echo("  mes=tmp_ar[1];\n");
		echo("  ano=tmp_ar[2];\n");
		echo("  if (dia < 10)\n");
		echo("    dia = '0'+dia;\n");
		echo("  if (mes < 10)\n");
		echo("    mes = '0'+mes;\n");
		echo("  return(ano+'/'+mes+'/'+dia);\n");
		echo("}\n\n");
	
		echo("function HoraMinuto(hora)\n");
		echo("{\n");
		echo("  tmp_ar=hora.split(\":\");\n");
		echo("  hora=tmp_ar[0];\n");
		echo("  minuto=tmp_ar[1];\n");
		echo("  if (hora < 10)\n");
		echo("    hora = '0'+hora;\n");
		echo("  if (minuto < 10)\n");
		echo("    minuto = '0'+minuto;\n");
		echo("  return(hora+':'+minuto);\n");
		echo("}\n\n");
	
		echo("function ComparaData(campo_data1,campo_data2)\n");
		echo("{\n");
		echo("  var data1 = AnoMesDia(campo_data1.value);\n");
		echo("  var data2 = AnoMesDia(campo_data2.value);\n");
		echo("  if (data1 < data2)\n");
		echo("    return -1;\n");
		echo("  else if (data1 > data2)\n");
		echo("    return 1;\n");
		echo("  else if (data1 == data2)\n");
		echo("    return 0;\n");
		echo("}\n\n");
	
		echo("function ComparaHora(campo_hora1,campo_hora2)\n");
		echo("{\n");
		echo("  var hora1 = HoraMinuto(campo_hora1.value);\n");
		echo("  var hora2 = HoraMinuto(campo_hora2.value);\n");
		echo("  if (hora1 < hora2)\n");
		echo("    return -1;\n");
		echo("  else if (hora1 > hora2)\n");
		echo("    return 1;\n");
		echo("  else if (hora1 == hora2)\n");
		echo("    return 0;\n");
		echo("}\n");
	
		echo("function ComparaDataHora(campo_data1,campo_hora1,campo_data2,campo_hora2)\n");
		echo("{\n");
		echo("  if ( (ComparaData(campo_data1, campo_data2) == -1) ||\n");
		echo("      ((ComparaData(campo_data1, campo_data2) == 0) && (ComparaHora(campo_hora1, campo_hora2) == -1))) //data1<data2\n");
		echo("    return -1;\n");
		echo("  if ( (ComparaData(campo_data1, campo_data2) == 1) ||\n");
		echo("      ((ComparaData(campo_data1, campo_data2) == 0) && (ComparaHora(campo_hora1, campo_hora2) == 1))) //data1<data2\n");
		echo("    return 1;\n");
		echo("  else\n");
		echo("    return 0;\n");
		echo("}\n");
		echo("</script>\n");
	}
	
}