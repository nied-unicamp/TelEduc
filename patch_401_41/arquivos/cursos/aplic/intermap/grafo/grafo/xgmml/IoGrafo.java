/*
 * IoGrafo.java
 * Versão: 2004-07-04
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml;

/**
 * Interface que define métodos de leitura e escrita de grafos.
 */
public interface IoGrafo {
    
    /**
     * Escreve as tags XGMML que representam um grafo.
     * @param g Grafo cujas propriedades serão descritas.
     * @param o Instância de FileWriter (arquivo) na qual os atributos serão
     * escritos.
     * @throws IOException Ocorre exceção se houver problemas com o arquivo.
     */  
    public void escreverXGMML(java.io.FileWriter o, grafo.Grafo g) throws java.io.IOException;
    
    /**
     * Lê um arquivo XGMML especificado e armazena o grafo por ele descrito.
     * @param g Grafo cujo conteúdo será definido.
     * @param arquivo Arquivo XGMML
     * @throws IOException Ocorre quando o arquivo informado não existir.
     */      
    public void lerXGMML(java.io.File arquivo, grafo.Grafo g) throws java.io.IOException;
    
}
