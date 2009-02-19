/*
 * Grupo.java
 * Versão: 2004-08-26
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml;

/**
 * Classe para leitura e escrita de tags XGMML relativas a um grupo.
 */
public class Grupo implements Io {
    
    /**
     * Escreve as tags XGMML que representam o grupo obj.
     * @obj Nó cujos atributos serão escritos.
     * @param obj Grupo que deve ser descrito.
     * @param o Arquivo em que devem ser escritas as tags.
     * @throws IOException Acontece se "o" não estiver bem definido.
     */

    public void escreverAtributosXGMML(java.io.FileWriter o, Object obj) throws java.io.IOException {
        if (obj instanceof grafo.Grupo) {
            grafo.Grupo grupo = (grafo.Grupo)obj;
            o.write("  <att name=\"group\" value=\""+ grupo.nome +"\">\n");
            Util.escreverAtributosXGMML(o, grupo.cor);
            o.write("  </att>\n");
        }
    }
    
    /**
     * Lê tags de atributos XGMML de um nó DOM e ajusta as propriedades de um objeto
     * grafo.Grupo de acordo com esses atributos.
     * @param obj Objeto grafo.Grupo cujos atributos serão ajustados.
     * @param raiz Nó DOM contendo as tags a serem lidas.
     */
    public void lerAtributosXGMML(org.w3c.dom.Node raiz, Object obj) {
        AtributoXGMML atr = new AtributoXGMML(raiz);
        if (obj instanceof grafo.Grupo && atr.nome.equals("group")) {
            grafo.Grupo grupo = (grafo.Grupo)obj;
            Util.lerAtributosXGMML(raiz, grupo.cor);
        }
    }
    
}
