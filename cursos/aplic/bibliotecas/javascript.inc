<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/bibliotecas/JavaScript.inc

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

/* ==========================================================
   ARQUIVO : cursos/aplic/bibliotecas/JavaScript.inc
   ========================================================== */

/*
  biblioteca de fun��es JavaScript e cria��o de campos de data HTML
  Autor: Leonel Aguilar Gayard
*/

/* *********************************************************************
   GeraJSVerificacaoData - Echo que gera fun��es JavaScript de verificacao de campos de data
   entrada: nenhuma
   saida:   nenhuma
   Obs: - a fun��o j� gera as tags <script> e </script>
     Fun��es JS geradas:
       - Bissexto(ano):    retorna true se o ano passado � bissexto, false caso contr�rio.
       - TemCaracteresEstranhos(valor_data) - retorna true se valor_data contiver caracteres que nao sejam digitos ou barra '/', false caso contrario.
       - PadronizaValorData(valor_data) - padroniza valor_data para o formato DD/MM/AAAA. 
       - DataValida(campo_data) - verifica se o campo de data passado est� corretamente preenchido.
*/
function GeraJSVerificacaoData()
{
  echo("<script language=JavaScript>\n");
  echo("var ndias=new Array(31,28,31,30,31,30,31,31,30,31,30,31);\n");
  echo("var divisor=20; //divisor de janela de tempo\n");

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

  echo("function PadronizaValorData(campo_data)\n");
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
  echo("    b1=tmpvalor_data.indexOf('/');\n");
  echo("    b2=tmpvalor_data.indexOf('/',b1+1);\n");
  echo("    dia=tmpvalor_data.substring(0,b1);\n");
  echo("    intdia=dia*1;\n");
  echo("    ano=tmpvalor_data.substring(b2+1,valor_data.length)*1;\n");
  echo("    mes=tmpvalor_data.substring(b1+1,b2)*1;\n");
  echo("    intmes=mes*1;\n");
  echo("    if (ano>=0 && ano<divisor)\n");
  echo("      ano+=2000;\n");
  echo("    if (ano>=divisor && ano<=100)\n");
  echo("      ano+=1900;\n");
  echo("    if (intdia>=1 && intdia<10)\n");
  echo("      dia='0'+intdia;\n");
  echo("    if (intmes>=1 && intmes<10)\n");
  echo("      mes='0'+intmes;\n");
  echo("    novovalor_data=dia+'/'+mes+'/'+ano;\n");
  echo("    campo_data.value=novovalor_data;\n");
  echo("    return(novovalor_data);\n");
  echo("  }\n");
  echo("  else \n");
  echo("  {\n");
  echo("    campo_data.value=valor_data;\n");
  echo("    return(valor_data);\n");
  echo("  }\n");
  echo("}\n");

  echo("function DataValida(campo_data) \n");
  echo("{\n");
  echo("  var data = campo_data.value;\n");
  echo("  if (data=='') \n");
  echo("  {\n");
  //  Voce deixou um campo_data de data vazio. Por favor preencha-o com uma data.\n");
  //  alert('".RetornaFraseDaLista($lista_frases,??)."');\n");
  echo("    alert('Voc� deixou um campo de data vazio. Por favor preencha-o com uma data.');\n");
  echo("    return(false);\n");
  echo("  }\n");
  echo("  else \n");
  echo("  {\n");
  echo("    data=PadronizaValorData(campo_data);\n");
  echo("    if (TemCaracteresEstranhos(data)) \n");
  echo("    {\n");
  // Voc� digitou caracteres estranhos nesta data.\nUse apenas d�gitos de 0 a 9 e a barra ( / ) para separar dia, m�s e ano (exemplo: 31/01/2000).\nPor favor retorne e corrija.\n");
  // alert('".RetornaFraseDaLista($lista_frases,??)."');\n");
  echo("      alert('Voc� digitou caracteres estranhos nesta data.\\nUse apenas d�gitos de 0 a 9 e a barra ( / ) para separar dia, m�s e ano (exemplo: 31/01/2000).\\nPor favor retorne e corrija.');\n");
  echo("      return(false);\n");
  echo("    }\n");
  echo("    else \n");
  echo("    {\n");
  echo("      campo_data.value=data;\n");
  echo("      b1=data.indexOf('/');\n");
  echo("      b2=data.indexOf('/',b1+1);\n");
  echo("      dia=data.substring(0,b1)*1;\n");
  echo("      mes=data.substring(b1+1,b2)*1;\n");
  echo("      ano=data.substring(b2+1,data.length)*1;\n");
  echo("      if (ano>=0 && ano<divisor)   ano=ano+2000;\n");
  echo("      if (ano>=divisor && ano<=99) ano+=1900;\n");
  echo("      if (Bissexto(ano)) \n");
  echo("        ndias[1]=29;\n");
  echo("      else \n");
  echo("        ndias[1]=28;\n");
  echo("      if (ano<1900 || ano>2100 || mes<1 || mes>12) \n");
  echo("      {\n");
  // Data inv�lida: \n");
  // . Por favor retorne e corrija.\n");
  // alert('".RetornaFraseDaLista($lista_frases,??)."'+data+'".RetornaFraseDaLista($lista_frases,??)."');\n");
  echo("        alert('Data inv�lida: '+data+'. Por favor retorne e corrija.');\n");
  echo("        return(false);\n");
  echo("      } \n");
  echo("      else \n");
  echo("      {\n");
  echo("        if (dia<1 || dia>ndias[mes-1]) \n");
  echo("        {\n");
  // Data inv�lida: \n");
  // . Por favor retorne e corrija.\n");
  // alert('".RetornaFraseDaLista($lista_frases,??)."'+data+'".RetornaFraseDaLista($lista_frases,??)."');\n");
  echo("          alert('Data inv�lida: '+data+'. Por favor retorne e corrija.');\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("      }\n");
  echo("    }\n");
  echo("  } \n");
  echo("  return(true);\n");
  echo("}\n");
  echo("</script>\n");
}

/* *********************************************************************
   GeraJSComparacaoDatas - Echo que gera pacote JS de fun��es de compara��o de datas
   entrada: nenhuma
   saida:   nenhuma
   Funcoes geradas:
     - AnoMesDia(data) - retorna a data passada no formato AAAA/MM/DD, para fazer comparacoes cronologicas.
     - ComparaData(campo_data1,campo_data2) - retorna -1 se 1 < 2, 0 se 1 == 2, 1 se 1 > 2
*/

function GeraJSComparacaoDatas()
{
  echo("<script language=JavaScipt>\n");
  echo("function AnoMesDia(data)\n");
  echo("{\n");
  echo("  alert('estou em anomesdia');");
  echo("  b1=data.indexOf('/');\n");
  echo("  b2=data.indexOf('/',b1+1);\n");
  echo("  dia=data.substring(0,b1);\n");
  echo("  ano=data.substring(b2+1,data.length);\n");
  echo("  mes=data.substring(b1+1,b2);\n");
  echo("  return(ano+'/'+mes+'/'+dia);\n");
  echo("}\n");
  echo("function ComparaData(campo_data1,campo_data2)\n");
  echo("{\n");
  echo("  var data1 = AnoMesDia(campo_data1.value);\n");
  echo("  var data2 = AnoMesDia(campo_data2.value);\n");
  echo("  alert('compara data');\n");
  echo("  if (data1 < data2)\n");
  echo("    return -1;\n");
  echo("  else if (data1 > data2)\n");
  echo("    return 1;\n");
  echo("  else if (data1 == data2)\n");
  echo("    return 0;\n");
  echo("}\n");
  echo("</script>\n");
}
?>
