<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/conversor_texto.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a DistÃ¢ncia
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

    Nied - NÃºcleo de InformÃ¡tica Aplicada Ã  EducaÃ§Ã£o
    Unicamp - Universidade Estadual de Campinas
    Cidade UniversitÃ¡ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : bibliotecas/conversor_texto.inc
  ========================================================== */

/* *********************************************************************
   Enter2BR - Converte Enter para <br>
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function Enter2BR($linha)
{
  return(implode("<br>",explode("\n",$linha)));
}

/* *********************************************************************
   BR2Enter - Converte <br> para enter
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function BR2Enter($linha)
{
  return(implode("\n",explode("<br>",$linha)));
}

/* *********************************************************************
   Space2Nbsp - Converte " " para "&nbsp;"
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function Space2Nbsp($linha)
{
  return(implode("&nbsp;",explode(" ",$linha)));
}

/* *********************************************************************
   AjustaParagrafo - Converte "  " para "&nbsp;&nbsp;"
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function AjustaParagrafo($linha)
{
  return(implode("&nbsp;&nbsp;",explode("  ",$linha)));
}


/* *********************************************************************
   Nbsp2Space - Converte "&nbsp;" para " "
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function Nbsp2space($linha)
{
  return(implode(" ",explode("&nbsp;",$linha)));
}

/* *********************************************************************
   EliminaScript - Elimina a tag <script>
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function EliminaScript($linha)
{
  $linha=VerificaTagTable($linha);
  return eregi_replace("<script","&lt;script",$linha);
}

/* *********************************************************************
   ConverteAspas2BarraAspas - troca ' e " por \' e \"
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function ConverteAspas2BarraAspas($linha)
{
  $linha1=implode("\\'",explode("'",$linha));
  return implode('\\"',explode('"',$linha1));
}

/* *********************************************************************
   ConverteBarraAspas2Aspas - troca \' e \" por ' e "
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function ConverteBarraAspas2Aspas($linha)
{
  $linha1=implode("'",explode("\\'",$linha));
  return implode('"',explode('\\"',$linha1));
}

/* *********************************************************************
   ConverteAspas2Html - troca ' e " por &#39; e &#34;
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function ConverteAspas2Html($linha)
{
  $linha1=eregi_replace("'","&#39;",$linha);
  return eregi_replace('"',"&#34;",$linha1);
}

/* *********************************************************************
   ConverteHtml2Aspas- troca &#39 e &#34 por ' e ";
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function ConverteHtml2Aspas($linha)
{
  $linha1=eregi_replace("&#39;","'",$linha);
  return eregi_replace("&#34;",'"',$linha1);
}

/* *********************************************************************
   ConverteSpace2Mais- troca " " (espaÃ§o) por + 
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function ConverteSpace2Mais($linha)
{
  return eregi_replace(' ','+',$linha);
}

/* *********************************************************************
   ConverteSpace2Html- troca " " (espaÃ§o) por %20
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function ConverteSpace2Html($linha)
{
  return eregi_replace(' ','%20',$linha);
}


/* *********************************************************************
   VerificaTagTable - Verifica se a um /table para cada table. Se nÃ£o tiver
                      completa com o que faltar 
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function VerificaTagTable($texto)
{
  $tbl1=explode("<table",strtolower($texto));
  $tbl2=explode("</table",strtolower($texto));
  $dif=count($tbl1)-count($tbl2);
  while ($dif>0)
  {
    $dif--;
    $texto.="</table>";
  }
  return $texto;
}

/* *********************************************************************
   LimpaTitulo - Limpa todas as Tags indesejÃ¡veis do titulo
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function LimpaTitulo($texto)
{
  $texto=eregi_replace("<","&lt;",$texto);
  $texto=eregi_replace("&lt;i>","<i>",$texto);
  $texto=eregi_replace("&lt;b>","<b>",$texto);
  $texto=eregi_replace("&lt;font","<font",$texto);
  $texto=eregi_replace("&lt;s>","<s>",$texto);
  $texto=eregi_replace("&lt;sub>","<sub>",$texto);
  $texto=eregi_replace("&lt;sup>","<sup>",$texto);
  $texto=eregi_replace("&lt;u>","<u>",$texto);
  return $texto;
}

/* *********************************************************************
   LimpaConteudo - Limpa todas as Tags indesejÃ¡veis do conteudo
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function LimpaConteudo($texto)
{
  $texto=eregi_replace("<script","&lt;script",$texto);
  $texto=eregi_replace("<html","&lt;html",$texto);
  $texto=eregi_replace("<head","&lt;head",$texto);
  $texto=eregi_replace("<body","&lt;body",$texto);
  $texto=eregi_replace("<title","&lt;title",$texto);
  $texto=eregi_replace("<style","&lt;style",$texto);
  $texto=eregi_replace("<frame","&lt;frame",$texto);
  $texto=eregi_replace("<frameset","&lt;frameset",$texto);
  $texto=eregi_replace("<form","&lt;form",$texto);
  $texto=eregi_replace("<iframe","&lt;iframe",$texto);
    
  return $texto;
}

/* *********************************************************************
   LimpaTag - Limpa todas as Tags 
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function LimpaTags($texto)
{
  $texto=eregi_replace("<","&lt;",$texto);
  return $texto;
}


/* *********************************************************************
   TruncaString - Reduz o tamanho da string ao tamanho passado.
   Entrada: $texto- string a ser corrigida
            $limite - limite de caracteres
   Saida: string corrigida
*/
function TruncaString($texto,$limite)
{
  if (strlen($texto)>$limite)
    $texto = substr($texto, 0, $limite-3)."...";
  return $texto;
}

/* *********************************************************************
   ConverteUrl2Html- troca " " (espaÃ§o) por %20, e acentos por especiais
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function ConverteUrl2Html($linha)
{
  $substituir=" ������������������������������������������������";
  $saida="";
  for ($i=0;$i<strlen($substituir);$i++)
  {
    $array_sub[substr($substituir,$i,1)]="%".dechex(ord(substr($substituir,$i,1)));
  } 
  for ($i=0; $i < strlen ($linha); $i++)
  {
    if (isset($array_sub[substr($linha,$i,1)]))
      $saida.=$array_sub[substr($linha,$i,1)];
    else
      $saida.=substr($linha,$i,1);
  }
  return $saida; 
}

/* *********************************************************************
   RetiraEspacoEAcentos - Ajusta a frase passada para se adequar as necessidades da Ajuda 
   Entrada: $linha - string a ser corrigida
   Saida: string corrigida
*/
function RetiraEspacoEAcentos($linha)
{
  $origem="������������������������������������������������";
  $destin="aeiouaeiouaoaeiouaeiouncAEIOUAEIOUAOAEIOUAEIOUNC"; 
  $saida="";
  for ($i=0;$i<strlen($origem);$i++)
  {
    $array_sub[substr($origem,$i,1)]=substr($destin,$i,1);
  }
  for ($i=0; $i < strlen ($linha); $i++)
  {
    $s=substr($linha,$i,1);
    if (isset($array_sub[$s]))
      $saida.=$array_sub[$s];
    else if ($s!=" " && $s!="-" && $s!="(" && $s!=")" && $s!="/")
      $saida.=$s;
  }
  return $saida;
}

/* ********************************************************************************************
   RetornaURLValida - Caso necessario, coloca http:// ou ftp:// na frente do link para exibicao
   Entrada: $endereco - endereco a ser verificado se necessita da modificacao
   Saida: endereco corrigido
*/
function RetornaURLValida($endereco)
{
  $partes = explode(":", $endereco);
  $partes2 = explode(".", $endereco);

  if ( (strcmp($partes[0], "http") != 0) && (strcmp($partes[0], "ftp") != 0) ) {
    if ( strcmp($partes2[0], "www") == 0 ) {
      $endereco = "http://".$endereco;
    } elseif ( strcmp($partes2[0], "ftp") == 0) {
      $endereco = "ftp://".$endereco;
    } else {
      $endereco = "http://".$endereco;
    }
  }
  
  return $endereco; 
}

/* *********************************************************************
   DecodificaString - decodifica a string passada por parametro utilizando 'html_entity_decode', dinamicamente
   Entrada: $id - id do elemento a ser retornado
          $texto - texto a ser decodificado
          $local - local a ser retornado (innerHTML, value, etc.)

    Saida: XML da função Ajax
 */
function DecodificaString($id, $texto, $local){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $objResponse->addAssign($id, $local, html_entity_decode($texto));
 
  return $objResponse->getXML();
}

/* *********************************************************************
   VerificaStringQuery - Neutraliza qualquer tipo de SQL Injection em uma dada string
   Entrada: $string - string que será usada em uma query
   Saida: retorna a string tratada contra SQL Injection
 */
function VerificaStringQuery($string)
{
  if (get_magic_quotes_gpc()) 
  {
    $string = stripslashes($string);
  }

  $string = mysql_real_escape_string($string);

  return $string;
}

/* *********************************************************************
   VerificaNumeroQuery - Verifica se uma variavel contem mesmo um valor numerico
   Entrada: $num - variavel a ser verificada
   Saida: Retorna a propria variavel se a mesma contiver um valor numerico; "" caso contrario.
 */
function VerificaNumeroQuery($num)
{
  if(is_numeric($num))
    return $num;
  else
    return "";
}

/* *********************************************************************
   str2num - Converte "valor_inteiro,valor_decimal" para "valor_inteiro.valor_decimal"
   Entrada: $str - string a ser corrigida.
   Saida: string corrigida
*/
function str2num($str)
{
	if(strpos($str, '.') < strpos($str,',')) {
		$str = str_replace('.','',$str);
		$str = strtr($str,',','.');           
	}
	else {
		$str = str_replace(',','',$str);           
	}
	return (float)$str;
}

?>