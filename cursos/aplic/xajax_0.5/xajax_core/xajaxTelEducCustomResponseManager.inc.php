<?php
/*
	File: xajaxTelEducCustomResponseManager.inc.php

	Contains the response manager class, with a different behaviour for error handling.
	
	Title: xajax TelEduc custom response manager class
*/

/*
	Class: xajaxTelEducCustomResponseManager
	
	Overrides xajaxResponseManager from the xajax core.
	
*/
class xajaxTelEducCustomResponseManager extends xajaxResponseManager
{

	/*
		String: sErrorHandlerCallback
		
		The name of the function which serves as callback to erros when they occur.
	*/
	var $sErrorHandlerCallback;

	/*
		Function: getInstance
		
		Implementation of the singleton pattern: provide a single instance of the <xajaxResponseManager>
		to all who request it.
	*/
	function &getInstance()
	{
		static $obj;
		if (!$obj) {
			$obj = new xajaxTelEducCustomResponseManager();
		}
		return $obj;
	}

	/*
		Function: configure
		
		Parameters:
		$sName - (string): Setting name
		$mValue - (mixed): Value
	*/
	function configure($sName, $mValue)
	{
		if ('ErrorHandlerCallback' == $sName)
		{
			$this->sErrorHandlerCallback = $mValue;
		}
		else {
			parent::configure($sName, $mValue);
		}
	}
	/*
		Function: getErrorHandlerCallback
	*/
	function getErrorHandlerCallback()
	{
		return $this->sErrorHandlerCallback;
	}

	function sendError($sMessage)
	{
		if ($this->objResponse == NULL)
			$this->objResponse = new xajaxResponse();
		if ($this->sErrorHandlerCallback) {
			$this->objResponse->call($this->sErrorHandlerCallback, $sMessage);
		}
		else {
			/* Erro personalizado TelEduc */
			$sock = Conectar("");
			$lista_frases = RetornaListaDeFrases($sock, -1);
			/* 80 (geral) - Ocorreu um erro interno. */
			$this->objResponse->call('mostraFeedback', RetornaFraseDaLista($lista_frases, 80));
		}
	}
}