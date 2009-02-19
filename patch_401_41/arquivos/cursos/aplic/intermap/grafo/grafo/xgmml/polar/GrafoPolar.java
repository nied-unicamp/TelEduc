/*
 * GrafoPolar.java
 * Versão: 2004-08-26
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml.polar;
import grafo.xgmml.*;
import java.util.Map;

public class GrafoPolar extends grafo.xgmml.Grafo {
    
    /** Escreve tags XGMML contendo atributos do grafo.
     * @param o Instância de FileWriter (arquivo) na qual as tags serão
     * escritas.
     * @throws IOException Ocorre exceção se houver problemas com o arquivo.
     */
    protected void escreverAtributosPadraoXGMML(java.io.FileWriter o, grafo.Grafo g) throws java.io.IOException {
        if (g instanceof grafo.polar.GrafoPolar) {
            grafo.polar.GrafoPolar gp = (grafo.polar.GrafoPolar)g;
            o.write("  <att name=\"peripheral-ring-ray\" value=\""+gp.raioPeriferico.getValue()+"\"/>\n");
            o.write("  <att name=\"central-ring-ray\" value=\""+gp.raioCentral.getValue()+"\"/>\n");
            o.write("  <att name=\"prohibited-area-ray\" value=\""+gp.raioAreaProibida.getValue()+"\"/>\n");
            o.write("  <att name=\"peripheral-ring-starting-angle\" value=\""+gp.anguloInicialNosPerifericos.getValue()+"\"/>\n");
            o.write("  <att name=\"central-ring-starting-angle\" value=\""+gp.anguloInicialNosCentrais.getValue()+"\"/>\n");
        }
    }
    
    /** Retorna um nó especificado pelo elemento DOM informado.
     * @param raiz Nó DOM
     * @param nome Nome do nó, já obtido anteriormente
     * @param peso Peso do nó, já obtido anteriormente
     * @param info Informação sobre o nó, já obtida anteriormente
     * @return Nó a ser adicionado ao grafo.
     */
    protected grafo.No obterNoXGMML(org.w3c.dom.Node raiz, String nome, int peso, grafo.Info info) {
        grafo.polar.NoPolar no = new grafo.polar.NoPolar(nome, grafo.polar.GrafoPolar.ANEL_PERIFERICO, peso, info);
        Util.lerAtributosXGMML(raiz, no);
        //adicionarNo(no);
        return no;
    }
    
    /** Retorna uma aresta especificada pelo elemento DOM informado.
     * @param raiz Nó DOM.
     * @param source Origem da aresta, já descoberta anteriormente.
     * @param target Destino da aresta, já descoberto anteriormente.
     * @param peso Peso da aresta, já descoberto anteriormente.
     * @param info Informaçao sobre a aresta, já descoberta anteriormente.
     * @param nosHash Hash contendo nomes dos nós dentro do XGMML.
     * @return Aresta a ser adicionada ao grafo.
     */
    protected grafo.Aresta obterArestaXGMML(org.w3c.dom.Node raiz, String source, String target, int peso, grafo.Info info, Map nosHash) {
        grafo.polar.NoPolar no1 = (grafo.polar.NoPolar)nosHash.get(source);
        grafo.polar.NoPolar no2 = (grafo.polar.NoPolar)nosHash.get(target);
        grafo.polar.ArestaPolar aresta;
        if (no1 != null && no2 != null) {
            aresta = new grafo.polar.ArestaPolar(no1, no2, peso, info);
            // Atributos especificados por tags <att>: responsabilidade da classe ArestaPolar.
            Util.lerAtributosXGMML(raiz, aresta);
        } else {
            aresta = null;
        }
        //adicionarAresta(aresta);
        return aresta;
    }
    
    /** Processa uma tag XGMML contendo um atributo do grafo.
     * @param raiz Nó DOM.
     * @param atr.nome Nome da característica registrada pela tag.
     * @param atr.valor Valor da característica registrada pela tag.
     * @return True se o atributo foi reconhecido e tratado, False caso
     * contrário.
     */
    protected boolean processaAtributo(org.w3c.dom.Node raiz, AtributoXGMML atr, grafo.Grafo g) {
        boolean resp = false;
        if (g instanceof grafo.polar.GrafoPolar) {
            grafo.polar.GrafoPolar gp = (grafo.polar.GrafoPolar)g;
            if (atr.nome.equals("peripheral-ring-ray")) {
                gp.raioPeriferico.setValue(Integer.parseInt(atr.valor));
            } else if (atr.nome.equals("central-ring-ray")) {
                gp.raioCentral.setValue(Integer.parseInt(atr.valor));
            } else if (atr.nome.equals("prohibited-area-ray")) {
                gp.raioAreaProibida.setValue(Integer.parseInt(atr.valor));
            } else if (atr.nome.equals("peripheral-ring-starting-angle")) {
                gp.anguloInicialNosPerifericos.setValue(Float.parseFloat(atr.valor));
            } else if (atr.nome.equals("central-ring-starting-angle")) {
                gp.anguloInicialNosCentrais.setValue(Float.parseFloat(atr.valor));
            } else {
                resp = super.processaAtributo(raiz, atr, gp);
            }
        }
        return resp;
    }
    
}
