/*
 * Aresta.java
 * Versão: 2004-07-04
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml;

/**
 * Classe abstrata para leitura e escrita de tags XGMML relativas a uma aresta.
 */
public abstract class Aresta implements Io, Constantes {
    
    /**
     * Cria uma nova instância de Aresta.
     */
    //public Aresta() {
    //}
    
    /**
     * Escreve as tags XGMML que representam a aresta obj.
     * @obj Nó cujos atributos serão escritos.
     * @param o Arquivo em que devem ser escritas as tags.
     * @param obj Aresta cujos atributos serão escritos.
     * @throws IOException Acontece se "o" não estiver bem definido.
     */
    public void escreverAtributosXGMML(java.io.FileWriter o, Object obj) throws java.io.IOException {
        if (obj instanceof grafo.Aresta) {
            grafo.Aresta aresta = (grafo.Aresta)obj;
            String idno1 = aresta.no1.retornarId();
            String idno2 = aresta.no2.retornarId();
            o.write("  <edge source=\""+idno1+"\" target=\""+idno2+"\" weight=\""+aresta.peso+"\">\n");
            String hidden = aresta.eEscondida() ? "Y" : "N";
            o.write("    <att name=\"hidden\" value = \""+hidden+"\"/>\n");
            if (aresta.info !=null) {
                Util.escreverAtributosXGMML(o, aresta.info);
            }
            o.write("  </edge>\n");
            // As propriedades marcada, atingida e escolhida não são gravadas no
            // arquivo. Elas são calculadas de acordo com os estados dos nós do
            // grafo.
            //
        }
    }
    
    /**
     * Lê tags de atributos XGMML de um nó DOM e ajusta as propriedades de um objeto
     * grafo.aresta de acordo com esses atributos.
     * @param raiz Nó DOM do qual as tags &lt;ATT&gt; serão lidas.
     * @param obj Aresta cujas propriedades serão ajustadas.
     */
    public void lerAtributosXGMML(org.w3c.dom.Node raiz, Object obj) {
        if (obj instanceof grafo.Aresta && raiz.getNodeType() == nodeTypeElement && raiz.getNodeName() == "edge") {
            grafo.Aresta aresta = (grafo.Aresta)obj;
            org.w3c.dom.Node noDom;
            AtributoXGMML atr;
            for (int i = 0; i<raiz.getChildNodes().getLength(); i++) {
                noDom = raiz.getChildNodes().item(i);
                if (noDom.getNodeType() == nodeTypeElement && noDom.getNodeName() == "att") {
                    // Atributos internos à tag <att> : hidden
                    // O atributo information é tratado pela classe No.
                    atr = new AtributoXGMML(noDom);
                    // Tenho atrNome e atrValor. Posso agora verificar quem é atrNome.
                    if (atr.nome.equals("hidden")) {
                        // Y para sim; N para não. Outro valor => não.
                        if (atr.valor.equals("Y")) {
                            aresta.esconder();
                        };
                    } else if (atr.nome.equals("information")) {
                        aresta.info = null;
                        try {
                            aresta.info = (grafo.Info)Class.forName("grafo."+atr.valor).getConstructor(null).newInstance(null);
                            Util.lerAtributosXGMML(noDom, aresta.info);
                        } catch (Exception e) {
                            System.out.println("Nao consegui acessar a classe \"grafo."+atr.valor+"\"");
                        }
                    }
                    
                }
            }
            
        }
        
    }
    
}
