/*
 * GerenciadorXGMML.java
 * Versão: 2004-03-16
 * Autor: Celmar Guimarães da Silva
 */

package grafo;

import java.io.*;

/** Estipula métodos de leitura e escrita de arquivos XGMML.
 */
public interface GerenciadorXGMML {
    
    // Constantes de tipos de nós de um documento DOM.
    /** Nó DOM do tipo atributo */    
    final int nodeTypeAttr = 2;
    /** Nó DOM do tipo documento. */    
    final int nodeTypeDocument = 9;
    /** Nó DOM do tipo elemento. */    
    final int nodeTypeElement = 1;
    /** Nó DOM do tipo texto. */    
    final int nodeTypeText = 3;

    
    /** Retorna tags de atributos para gravação de arquivo XGMML.
     * @param o Instância de FileWriter (arquivo) na qual os atributos serão 
     * escritos.
     * @throws IOException Ocorre exceção se houver problemas com o arquivo.
     */
    public void escreverAtributosXGMML(FileWriter o) throws IOException;
    
    /** Lê tags de atributos XGMML de um elemento, ajustando suas propriedades
     * de acordo com esses atributos.
     * @param raiz Nó DOM do qual as tags &lt;ATT&gt; serão lidas
     */
    public void lerAtributosXGMML(org.w3c.dom.Node raiz);
    
}
