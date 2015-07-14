/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.language = 'pt-br';
	config.uiColor = '#ffffff';
	config.removePlugins = 'elementspath,scayt';
	config.toolbarCanCollapse = false;
	config.toolbar = 'TelEduc';
	config.disableNativeSpellChecker = true;
	config.extraPlugins = "youtube";

	
	config.toolbar_TelEduc =
		[
		 	['NewPage','Preview'],['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
		 	['Find','Replace','-','SelectAll','RemoveFormat'],
		    ['Bold', 'Italic', 'Underline','Strike', 'Subscript','Superscript', '-', 'NumberedList', 'BulletedList'],
		    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Image','YouTube','SpecialChar'], '/',
		    ['Font','FontSize'], ['TextColor','BGColor'],
		    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
		    ['Link', 'Unlink'],
		    ['Undo','Redo'],
		    ['Source','Preview'] 
		];

};
