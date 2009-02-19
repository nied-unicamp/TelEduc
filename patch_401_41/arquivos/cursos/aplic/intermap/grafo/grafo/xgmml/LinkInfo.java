/*
 * LinkInfo.java
 * Versão: 2004-08-26
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml;

/**
 * Classe abstrata para leitura e escrita de tags XGMML relativas a uma informação
 * no formato de link (grafo.LinkInfo).
 */
public class LinkInfo implements Info, Constantes {
    
    /**
     * screve as tags XGMML que representam o objeto LinkInfo.
     * @param obj Objeto grafo.LinkInfo a ser descrito.
     * @param o Arquivo no qual as tags devem ser escritas.
     * @throws IOException Acontece se "o" não estiver bem definido.
     */
    public void escreverAtributosXGMML(java.io.FileWriter o, Object obj) throws java.io.IOException {
        if (obj instanceof grafo.LinkInfo) {
            String nome=((grafo.LinkInfo)obj).nome;
            String url=((grafo.LinkInfo)obj).url;
            o.write("    <att name=\"information\" value=\"LinkInfo\">\n");
            o.write("      <att name=\"name\" value = \"" + grafo.Util.ascii2xml(nome) + "\" />\n");
            o.write("      <att name=\"url\" value = \"" + grafo.Util.ascii2xml(url) + "\" />\n");
            o.write("    </att>\n");
        }
    }
    
    /**
     * Lê tags de atributos XGMML de um nó DOM e ajusta as propriedades de um objeto
     * grafo.LinkInfo de acordo com esses atributos.
     * @param obj Objeto grafo.LinkInfo cujos atributos serão ajustados.
     * @param raiz Nó DOM a partir do qual serão lidas as tags.
     */
    public void lerAtributosXGMML(org.w3c.dom.Node raiz, Object obj) {
        if (obj instanceof grafo.LinkInfo) {
            grafo.LinkInfo info =(grafo.LinkInfo)obj;
            
            org.w3c.dom.Node noDom;
            AtributoXGMML atr;
            for (int i = 0; i<raiz.getChildNodes().getLength(); i++) {
                noDom = raiz.getChildNodes().item(i);
                if (noDom.getNodeType() == nodeTypeElement && noDom.getNodeName() == "att") {
                    atr = new AtributoXGMML(noDom);
                    if (atr.nome.equals("name")) {
                        info.nome = atr.valor;
                    } else if (atr.nome.equals("url")) {
                        info.url = atr.valor;
                    }

                }
            }
        }
    }
    
}