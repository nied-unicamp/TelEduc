/*
Copyright (C) 2010 Jonnas Fonini <contato@fonini.net>

Este programa é um software livre; você pode redistribui-lo e/ou 
modifica-lo dentro dos termos da Licença Pública Geral GNU como 
publicada pela Fundação do Software Livre (FSF); na versão 2 da 
Licença, ou (na sua opnião) qualquer versão.

Este programa é distribuido na esperança que possa ser  util, 
mas SEM NENHUMA GARANTIA; sem uma garantia implicita de ADEQUAÇÂO a qualquer
MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.

Você deve ter recebido uma cópia da Licença Pública Geral GNU
junto com este programa, se não, escreva para a Fundação do Software Livre(FSF) Inc., 
51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

CKEDITOR.dialog.add( 'youtube', function( editor )
{
	return {
		title : 'Inserir vídeo do YouTube',
		minWidth : 390,
		minHeight : 230,
		contents : [
		{
			id : 'urlTab',
			label : 'URL do Vídeo',
			title : 'URL do Vídeo',
			elements :
			[
				{
					id : 'url',
					type : 'text',
					label : 'Cole a URL do vídeo do YouTube'
				},
				{
					id : 'xhtml',
					type : 'checkbox',
					label : 'XHTML válido'
				},
				{
					id : 'width',
					type : 'text',
					label : 'Largura',
					width : '40'
				},
				{
					id : 'height',
					type : 'text',
					label : 'Altura',
					width : '40'
				}
			]
		},
		{
			id : 'embedTab',
			label : 'Código Embed',
			title : 'Código Embed',
			elements :
			[
				{
					id : 'embed',
					type : 'textarea',
					label : 'Cole o código gerado pelo YouTube (embed)'
				}
			]
		}
        ],
		onOk : function() {
			var editor = this.getParentEditor();
			var contentUrl = this.getValueOf( 'urlTab', 'url' );
			var contentEmbed = this.getValueOf( 'embedTab', 'embed' );
			var xhtml = this.getValueOf( 'urlTab', 'xhtml' );
			var width = this.getValueOf( 'urlTab', 'width' );
			var height = this.getValueOf( 'urlTab', 'height' );

			width = width ? width : 450;
			height = height ? height : 366;
					
			if ( contentUrl.length > 0 ) {
				if (xhtml == true){
					contentUrl = contentUrl.replace(/^[^v]+v.(.{11}).*/,"$1");
					editor.insertHtml('<object type="application/x-shockwave-flash" style="width:' + width + 'px; height:' + height + 'px;" data="http://www.youtube.com/v/'+contentUrl+'"><param name="movie" value="http://www.youtube.com/v/'+contentUrl+'" /></object>');
				}
				else {
					contentUrl = contentUrl.replace(/^[^v]+v.(.{11}).*/, "$1");
					editor.insertHtml('<object width="' + width + '" height="' + height + '"><param name="movie" value="http://www.youtube.com/v/'+contentUrl+'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'+contentUrl+'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="' + width + '" height="' + height + '"></embed></object>');
				}
			}	
			else
			if ( contentEmbed.length > 0 ) {
				editor.insertHtml(contentEmbed);						
			}
		},
	buttons : [ CKEDITOR.dialog.okButton, CKEDITOR.dialog.cancelButton ]
	};
} );

CKEDITOR.plugins.add( 'youtube',
{
	init : function( editor )
	{
		var command = editor.addCommand( 'youtube', new CKEDITOR.dialogCommand( 'youtube' ) );
		command.modes = { wysiwyg:1, source:1 };
		command.canUndo = false;

		editor.ui.addButton( 'YouTube',
		{
			label : 'Inserir vídeo do YouTube',
			command : 'youtube',
			icon : this.path + 'youtube.png'
		});

		CKEDITOR.dialog.add( 'youtube', 'youtube' );
	}
});
