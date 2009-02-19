/*
 * NoPolar.java
 * Versão: 2004-08-24
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo.polar;

import java.awt.*;
import java.awt.geom.Point2D;
import java.util.*;
import java.io.*;
import grafo.*;

/** Estende classe No para implementar características de um nó utilizado em um
 * grafo polar.
 * Dentre essas características, destacam-se: ser ou não marcado, atingido ou
 * escolhido, estar ou não sendo movido, grupo do nó, e cor individual.
 * É responsável também por desenhar o nó e por retornar informações sobre sua
 * conexão com o grafo polar.
 */
public class NoPolar extends No implements Aneis {
    
    /** Raio - posição do nó. */
    public double raio;   // coordenadas polares

    /** Ângulo - posição do nó. */
    public double angulo; // coordenadas polares

    /** Informa em que anel do grafo polar o nó está posicionado. */
    private boolean anel;
    
    /** Cria uma nova instância de NoPolar, sem peso nem informação associada.
     * @param nome Nome do nó a ser criado.
     * @param anel Anel em que o nó deve ser criado.
     */
    public NoPolar(String nome, boolean anel) {
        this(nome, anel, 0, null);
    }
    
    /** Cria uma nova instância de NoPolar, com peso e sem informação associada.
     * @param nome Nome do nó a ser criado.
     * @param anel Anel em que o nó deve ser criado.
     * @param peso Peso do nó.
     */
    public NoPolar(String nome, boolean anel, int peso) {
        this(nome, anel, peso, null);
    }
    
    /** Cria uma nova instância de NoPolar, sem peso mas com informação associada.
     * @param nome Nome do nó a ser criado.
     * @param anel Anel em que o nó deve ser criado.
     * @param info Informação associada.
     */
    public NoPolar(String nome, boolean anel, Info info) {
        this(nome, anel, 0, info);
    }
    
    /** Cria uma nova instância de NoPolar, com peso e informação associada.
     * @param nome Nome do nó a ser criado.
     * @param anel Anel em que o nó deve ser criado.
     * @param peso Peso do nó.
     * @param info Informação associada.
     */
    public NoPolar(String nome, boolean anel, int peso, Info info) {
        super(nome, peso, info);
        this.anel = anel;
        raio = 0.0;
        angulo = 0.0;
        cor = new CorNo(GrafoPolar.defaultCorNo);
        grupo = GrafoPolar.defaultGrupo;
        //abreviado = false;
        removerCorIndividual();
   }
    
    /** Cria uma nova instância de NoPolar, copiando um nó informado.
     * @param no Nó a ser copiado
     */
    public NoPolar(NoPolar no) {
        this(no.nome, no.anel, no.peso, no.info);
        raio = no.raio;
        angulo = no.angulo;
        cor = no.cor;
        grupo = no.grupo;
    }
    
    /** Desenha o nó.
     * @param grafo Grafo ao qual o nó pertence. O nó precisa saber disso, uma vez que ele precisa
     * saber qual é o centro do grafo para, assim, poder se desenhar.
     * @param g Onde o nó será desenhado.
     */
    public void desenhar(Grafo grafo, Graphics g) {
        if (grafo instanceof GrafoPolar) {
            GrafoPolar gr = (GrafoPolar)grafo;
            Point2D.Double centro = new Point2D.Double((int)gr.centro.x, (int)gr.centro.y);
            x = (int) PolarCartesiano.polar2Cartesiano_x(raio, angulo, centro);
            y = (int) PolarCartesiano.polar2Cartesiano_y(raio, angulo, centro);
            String textoaux = nomeAMostrar();
            desenharNo(x,y, textoaux, g);
        } 
    }

    /** Informa se o nó pertence ou não ao anel central.
     * @return True se o nó pertence ao anel central, false caso contrário.
     */    
    public boolean eCentral() {
        return anel == ANEL_CENTRAL;
    }
    
    /** Informa se o nó pertence ou não ao anel periférico.
     * É exatamente o contrário de eCentral().
     * @return True se o nó pertence ao anel periférico, false caso contrário.
     */    
    public boolean ePeriferico() {
        return anel == ANEL_PERIFERICO;
    }
    
    
    
    /** Transfere o nó para o anel periférico. */    
    public void moverParaAnelPeriferico() {
        anel = ANEL_PERIFERICO;
    }
    
    /** Transfere o nó para o anel central. */    
    public void moverParaAnelCentral() {
        anel = ANEL_CENTRAL;
    }

    /** Retorna o anel ao qual o nó pertence.
     * @return Retorna ANEL_CENTRAL se o nó pertence ao anel central, ou ANEL_PERIFERICO se
     * pertence ao periférico.
     */    
    public boolean retornarAnel() {
        return anel;
    }
    

    
}

