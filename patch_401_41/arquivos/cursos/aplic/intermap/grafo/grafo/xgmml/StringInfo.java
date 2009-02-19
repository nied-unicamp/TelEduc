/*
 * StringInfo.java
 * Versão: 2004-08-26
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml;

/**
 * Classe abstrata para leitura e escrita de tags XGMML relativas a uma informação
 * no formato de String (grafo.StringInfo).
 */
public class StringInfo implements Info, Constantes {
    
    /**
     * Escreve as tags XGMML que representam o objeto StringInfo.
     * @param obj Objeto grafo.StringInfo a ser descrito.
     * @param o Arquivo no qual as tags devem ser escritas.
     * @throws IOException Acontece se "o" não estiver bem definido.
     */
    public void escreverAtributosXGMML(java.io.FileWriter o, Object obj) throws java.io.IOException {
        if (obj instanceof grafo.StringInfo) {
            String texto=((grafo.StringInfo)obj).texto;
            o.write("    <att name=\"information\" value=\"StringInfo\">\n");
            o.write("      <att name=\"text\" value = \"" + grafo.Util.ascii2xml(texto) + "\" />\n");
            o.write("    </att>\n");
        }
    }
    
    /**
     * Lê tags de atributos XGMML de um nó DOM e ajusta as propriedades de um objeto
     * grafo.StringInfo de acordo com esses atributos.
     * @param obj Objeto grafo.StringInfo cujos atributos serão ajustados.
     * @param raiz Nó DOM a partir do qual serão lidas as tags.
     */
    public void lerAtributosXGMML(org.w3c.dom.Node raiz, Object obj) {
        if (obj instanceof grafo.StringInfo) {
            grafo.StringInfo info =(grafo.StringInfo)obj;
            
            org.w3c.dom.Node noDom;
            AtributoXGMML atr;
            for (int i = 0; i<raiz.getChildNodes().getLength(); i++) {
                noDom = raiz.getChildNodes().item(i);
                if (noDom.getNodeType() == nodeTypeElement && noDom.getNodeName() == "att") {
                    atr = new AtributoXGMML(noDom);
                    if (atr.nome.equals("text")) {
                        info.texto = atr.valor;
                    }
                }
            }
        }
    }
    
}