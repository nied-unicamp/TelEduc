/*
 * No.java
 * Versão: 2004-08-26
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml;


/**
 * Classe abstrata para leitura e escrita de tags XGMML relativas a um nó.
 */
public abstract class No implements Io, Constantes {
    
    /** Escreve as tags XGMML que representam o nó obj.
     * @param o Arquivo em que devem ser escritas as tags.
     * @param obj Nó cujos atributos serão escritos.
     * @throws IOException Acontece se "o" não estiver bem definido.
     */
    public void escreverAtributosXGMML(java.io.FileWriter o, Object obj) throws java.io.IOException {
        if (obj instanceof grafo.No) {
            grafo.No no = (grafo.No)obj;
            o.write("  <node id=\""+no.retornarId()+"\" label=\""+grafo.Util.ascii2xml(no.nome)+"\" weight=\""+no.peso+"\">\n");
            o.write("    <att name=\"x\" value = \""+no.x+"\"/>\n");
            o.write("    <att name=\"y\" value = \""+no.y+"\"/>\n");
            String selected = no.eMarcado() ? "Y" : "N";
            o.write("    <att name=\"selected\" value = \""+selected+"\"/>\n");
            String hidden = no.eEscondido() ? "Y" : "N";
            o.write("    <att name=\"hidden\" value = \""+hidden+"\"/>\n");
            if (no.grupo!=null) {
                o.write("    <att name=\"group\" value = \""+no.grupo.nome+"\"/>\n");
            }
            if (no.info!=null) {
                Util.escreverAtributosXGMML(o, no.info);
            }
            escreverAtributosExtrasXGMML(o, obj);
            o.write("  </node>\n");
        }
    }
    
    /**
     * Escreve as tags XGMML que contêm atributos extras do nó obj.
     * @param o Arquivo em que devem ser escritas as tags.
     * @param obj Nó cujos atributos serão escritos.
     * @throws IOException Acontece se "o" não estiver bem definido.
     */
    public void escreverAtributosExtrasXGMML(java.io.FileWriter o, Object obj) throws java.io.IOException {
        // Aqui não faz nada. Pode ser estendida por outras classes derivadas.
    }
    
    /**
     * Lê tags de atributos XGMML de um nó DOM e ajusta as propriedades de um objeto
     * grafo.No de acordo com esses atributos.
     * @param raiz Nó DOM do qual as tags &lt;ATT&gt; serão lidas.
     * @param obj Nó cujas propriedades serão ajustadas.
     */
    public void lerAtributosXGMML(org.w3c.dom.Node raiz, Object obj) {
        if (obj instanceof grafo.No && raiz.getNodeType() == nodeTypeElement && raiz.getNodeName() == "node") {
            grafo.No no = (grafo.No)obj;

            org.w3c.dom.Node noDom;
            AtributoXGMML a;
            for (int i = 0; i<raiz.getChildNodes().getLength(); i++) {
                noDom = raiz.getChildNodes().item(i);
                if (noDom.getNodeType() == nodeTypeElement && noDom.getNodeName() == "att") {
                    // Atributos internos à tag <att> : x, y, selected, group e hidden
                    // O atributo information é tratado pela classe Info.
                    // O atributo group é tratado pela classe Grafo.
                    a = new AtributoXGMML(noDom);
                    if (a.nome.equals("x")) {
                        //no.x = Integer.parseInt(a.valor);
                        no.x = Double.parseDouble(a.valor);
                    } else if (a.nome.equals("y")) {
                        //no.y = Integer.parseInt(a.valor);
                        no.y = Double.parseDouble(a.valor);
                    } else if (a.nome.equals("selected")) {
                        // Y para sim; N para não. Outro valor => não.
                        if (a.valor.equals("Y")) {
                            no.marcar();
                        };
                    } else if (a.nome.equals("hidden")) {
                        // Y para sim; N para não. Outro valor => não.
                        if (a.valor.equals("Y")) {
                            no.esconder();
                        };
                    } else if (a.nome.equals("information")) {
                        no.info = null;
                        try {
                            no.info = (grafo.Info)Class.forName("grafo."+a.valor).getConstructor(null).newInstance(null);
                            Util.lerAtributosXGMML(noDom, no.info);
                        } catch (Exception e) {
                            System.out.println("Não consegui acessar a classe \"grafo."+a.valor+"\"");
                        }
                    }
                    
                }
            }
            
        }
        
    }
    
}