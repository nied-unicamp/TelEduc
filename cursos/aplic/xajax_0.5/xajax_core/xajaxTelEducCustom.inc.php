<?php
/*
	File: xajaxTelEducCustom.inc.php

	Main custom xajax class, with a different behaviour for error handling.

	Title: xajax TelEduc custom class
*/

/*
	Class: xajaxTelEducCustom
	
	Overrides xajax from the xajax core.
	
*/

require 'xajax.inc.php';

class xajaxTelEducCustom extends xajax
{
	function __wakeup()
	{
		parent::__wakeup();
		
		$sLocalFolder = dirname(__FILE__);

		require $sLocalFolder . '/xajaxTelEducCustomResponseManager.inc.php';
		
		$this->objResponseManager =& xajaxTelEducCustomResponseManager::getInstance();
		$this->sCoreIncludeOutput = ob_get_clean();
	}

	function processRequest()
	{
//SkipDebug
		// Check to see if headers have already been sent out, in which case we can't do our job
		if (headers_sent($filename, $linenumber)) {
			echo "Output has already been sent to the browser at {$filename}:{$linenumber}.\n";
			echo 'Please make sure the command $xajax->processRequest() is placed before this.';
			exit();
		}
//EndSkipDebug

		if ($this->canProcessRequest())
		{
			// Use xajax error handler if necessary
			if ($this->bErrorHandler) {
				$GLOBALS['xajaxErrorHandlerText'] = "";
				set_error_handler("xajaxErrorHandler");
			}
			
			$mResult = true;

			// handle beforeProcessing event
			if (isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_BEFORE]))
			{
				$bEndRequest = false;
				$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_BEFORE]->call(array(&$bEndRequest));
				$mResult = (false === $bEndRequest);
			}

			if (true === $mResult)
				$mResult = $this->objPluginManager->processRequest();

			if (true === $mResult)
			{
				if ($this->bCleanBuffer) {
					$er = error_reporting(0);
					while (ob_get_level() > 0) ob_end_clean();
					error_reporting($er);
				}

				// handle afterProcessing event
				if (isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_AFTER]))
				{
					$bEndRequest = false;
					$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_AFTER]->call(array(&$bEndRequest));
					if (true === $bEndRequest)
					{
						$this->objResponseManager->clear();
						$this->objResponseManager->append($aResult[1]);
					}
				}
			}
			else if (is_string($mResult))
			{
				if ($this->bCleanBuffer) {
					$er = error_reporting(0);
					while (ob_get_level() > 0) ob_end_clean();
					error_reporting($er);
				}

				// $mResult contains an error message
				// the request was missing the cooresponding handler function
				// or an error occurred while attempting to execute the
				// handler.  replace the response, if one has been started
				// and send a debug message.

				$this->objResponseManager->clear();
				$this->objResponseManager->append(new xajaxResponse());

				// handle invalidRequest event
				if (isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_INVALID]))
					$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_INVALID]->call();
				else
					$this->objResponseManager->debug($mResult);
			}

			if ($this->bErrorHandler) {
				$sErrorMessage = $GLOBALS['xajaxErrorHandlerText'];
				if (!empty($sErrorMessage)) {
					if (0 < strlen($this->sLogFile)) {
						$fH = @fopen($this->sLogFile, "a");
						if (NULL != $fH) {
							fwrite(
								$fH, 
								$this->objLanguageManager->getText('LOGHDR:01')
								. strftime("%b %e %Y %I:%M:%S %p") 
								. $this->objLanguageManager->getText('LOGHDR:02')
								. $sErrorMessage 
								. $this->objLanguageManager->getText('LOGHDR:03')
								);
							fclose($fH);
						} else {
							$this->objResponseManager->debug(
								$this->objLanguageManager->getText('LOGERR:01') 
								. $this->sLogFile
								);
						}
					}
					$this->objResponseManager->debug(
						$this->objLanguageManager->getText('LOGMSG:01') 
						. $sErrorMessage
						);
					// This is the tweaked line for calling the callback on error.
					$this->objResponseManager->sendError($sErrorMessage);
				}
			}

			$this->objResponseManager->send();

			if ($this->bErrorHandler) restore_error_handler();

			if ($this->bExitAllowed) exit();
		}
	}
}