/*
 * Info.java
 * Versão: 2004-07-04
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml;

/**
 * Classe abstrata para leitura e escrita de tags XGMML relativas a uma informação.
 */
public interface Info extends Io  {
    
    /**
     * Lê tags de atributos XGMML de um nó DOM e ajusta as propriedades de um objeto
     * grafo.Info de acordo com esses atributos.
     * @param raiz Nó DOM contendo as tags a serem lidas.
     * @param obj Objeto grafo.Info cujos atributos serão ajustados.
     */
    //public Info() {
    //}
    
    public void lerAtributosXGMML(org.w3c.dom.Node raiz, Object obj);
    
    /** Escreve as tags XGMML que representam uma informação definida em obj.
     * @param o Instância de FileWriter (arquivo) na qual as tags serão 
     * escritas.
     * @param obj Objeto a ser descrito pelas tags.
     * @throws IOException Ocorre exceção se houver problemas com o arquivo.
     */  
    public void escreverAtributosXGMML(java.io.FileWriter o, Object obj) throws java.io.IOException;
    
}
