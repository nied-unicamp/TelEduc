/*
 * NoPolar.java
 * Versão: 2004-08-26
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml.polar;
import java.io.*;

/**
 *
 * @author  Celmar
 */
public class NoPolar extends grafo.xgmml.No {

    /** Retorna tags de atributos para gravação de arquivo XGMML.
     * @param o Arquivo no qual as tags devem ser escritas.
     * @throws IOException Acontece se "o" não estiver bem definido.
     */
    public void escreverAtributosXGMML(FileWriter o, Object obj) throws IOException {
        if (obj instanceof grafo.polar.NoPolar) {
            super.escreverAtributosXGMML(o, obj);
        }
    }
    
    /** Escreve as tags <CODE>&lt;att&gt;</CODE> (XGMML) que contêm atributos
     * extras do nó obj.
     * @param Arquivo em que devem ser escritas as tags.
     * @obj Nó cujos atributos serão escritos.
     * @throws IOException Acontece se "o" não estiver bem definido.
     */
    public void escreverAtributosExtrasXGMML(java.io.FileWriter o, Object obj) throws java.io.IOException {
        if (obj instanceof grafo.polar.NoPolar) {
            grafo.polar.NoPolar no = (grafo.polar.NoPolar)obj;        
            String ring = no.eCentral() ? "C" : "P";
            o.write("    <att name=\"ring\" value = \""+ring+"\"/>\n");
        }
    }
    
}
