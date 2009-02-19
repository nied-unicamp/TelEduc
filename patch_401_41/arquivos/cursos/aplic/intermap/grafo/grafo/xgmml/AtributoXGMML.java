/*
 * AtributoXGMML.java
 * Versão: 2004-07-04
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml;

/**
 * Classe para leitura e escrita de tags XGMML <CODE>att</code>, ou seja,
 * tags com o formato:
 * <CODE>&lt;att name="atrNome" value="atrValor"&gt;...&lt;/att&gt;</CODE>
 */
public class AtributoXGMML implements Constantes {
    
    /**
     * Conteúdo do atributo "nome" da tag att.
     */    
    public String nome="";
    
    /**
     * Conteúdo do atributo "valor" da tag att.
     */    
    public String valor="";
    
    /** Cria uma nova instância de AtributoXGMML
     * @param noDom Nó DOM que contém uma tag att do XGMML.
     */
    public AtributoXGMML(org.w3c.dom.Node noDom) {
        org.w3c.dom.Node atr;
        if (noDom.getNodeType() == nodeTypeElement && noDom.getNodeName() == "att") {
            for (int i = 0; i<noDom.getAttributes().getLength(); i++) {
                // Um atributo aqui é 'name="atrNome" ' ou 'value = "atrValor" '
                // Qualquer outro atributo encontrado será ignorado.
                atr = noDom.getAttributes().item(i);
                if (atr.getNodeType() == nodeTypeAttr) {
                    if (atr.getNodeName() == "name") {
                        nome = atr.getNodeValue();
                    } else if (atr.getNodeName() == "value") {
                        valor = atr.getNodeValue();
                    }
                }
            }
        }
    }
    
}
