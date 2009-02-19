/*
 * Io.java
 * Versão: 2004-07-04
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml;

import java.io.*;

/** Estipula métodos de leitura e escrita de arquivos XGMML.
 */
public interface Io {
    
    /** Escreve as tags XGMML que representam o objeto obj.
     * @param o Instância de FileWriter (arquivo) na qual as tags serão 
     * escritas.
     * @param obj Objeto a ser descrito pelas tags.
     * @throws IOException Ocorre exceção se houver problemas com o arquivo.
     */
    public void escreverAtributosXGMML(FileWriter o, Object obj) throws IOException;
    
    /**
     * Lê tags de atributos XGMML de um nó DOM e ajusta as propriedades do objeto
     * obj de acordo com esses atributos.
     * @param raiz Nó DOM do qual as tags serão lidas.
     * @param obj Objeto cujas caracteristicas serão estabelecidas pelas tags.
     */
    public void lerAtributosXGMML(org.w3c.dom.Node raiz, Object obj);
    
}
