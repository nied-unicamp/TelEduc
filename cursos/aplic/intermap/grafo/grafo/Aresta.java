/*
 * Aresta.java
 * Versão: 2004-08-26
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo;

import java.io.*;
import java.awt.*;

/** Classe básica para criar arestas de um grafo.
 */
public abstract class Aresta 
{
    
    /** Um dos vértices atingidos pela aresta.
     */
    public No no1;
    
    /** O outro vértice atingido pela aresta.
     */
    public No no2;

    /** Peso da aresta.
     */
    public int peso;

    /** Informação sobre aresta.
     */
    public Info info;

    /** Raio da aresta circular.
     */
    public static int raioArestaCircular = 10;
    
//    /** Informa se a aresta é marcada.
//     */
//    private boolean marcada;

    /** Informa se a aresta é atingida por um nó selecionado.
     */
    private boolean atingida;
    
    /** Diz se a aresta está escondida ou não
     */
    private boolean escondida;
    
    /** Construtor de aresta sem peso (peso = 0) e sem informação (info = null).
     * @param no1 Primeiro nó da aresta
     * @param no2 Segundo nó da aresta
     */
    public Aresta(No no1, No no2) {
        this(no1, no2, 0, null);
    }
    
    /** Construtor de aresta sem informação (info = null) e com peso.
     * @param no1 Primeiro nó da aresta.
     * @param no2 Segundo nó da aresta.
     * @param peso Peso da aresta. 
     */
    public Aresta(No no1, No no2, int peso) {
        this(no1, no2, peso, null);
    }
    
    /** Construtor de aresta com informação e sem peso (peso = 0).
     * @param no1 Primeiro nó da aresta.
     * @param no2 Segundo nó da aresta.
     * @param info Informação relacionada à aresta.
     */
    public Aresta(No no1, No no2, Info info) {
        this(no1, no2, 0, info);
    }
    
    /** Construtor de aresta com informação e peso.
     * @param no1 Primeiro nó da aresta.
     * @param no2 Segundo nó da aresta.
     * @param peso Peso da aresta. 
     * @param info Informação relacionada à aresta.
     */
    public Aresta(No no1, No no2, int peso, Info info) {
        this.no1 = no1; 
        this.no2 = no2;
        this.peso = peso;
        this.info = info;
//        marcada = false;
        atingida = false;
        escondida = false;        
    }

    /** Desenha aresta com no1 == no2 (self-edge).
     * @param grafo Grafo ao qual a aresta pertence
     * @param g Objeto em que a aresta será desenhada.
     * @param no Nó para o qual a aresta aponta.
     */
    public void desenharArestaParaOProprioNo(Grafo grafo, Graphics g, No no) {
        no.atualizarDimensoesTexto(g, no.nomeAMostrar());
        Point centroAutoAresta = retornarCentroAutoAresta(no, grafo);
        g.drawOval((int)centroAutoAresta.x-raioArestaCircular,(int)centroAutoAresta.y-raioArestaCircular,(int)2*raioArestaCircular,(int)2*raioArestaCircular);
    }

    /** Calcula a coordenada do centro do círculo que compõem a auto-aresta 
     * de um nó informado.
     * @param no Nó do qual se deseja saber o centro da auto-aresta.
     * @param g Grafo do qual o nó faz parte.
     * @return Retorna o ponto no centro da auto-aresta.
     */
    public abstract Point retornarCentroAutoAresta(No no, Grafo g);
    
    /** Desenha aresta para o grafo.
     * @param grafo Grafo ao qual a aresta pertence.
     * @param g Objeto em que a aresta será desenhada.
     */
    public abstract void desenhar(Grafo grafo, Graphics g) ;

    /** Retorna a cor que deve ser usada para desenhar a aresta, de acordo com as
     * especificações de cor estabelecidas no grafo em questão.
     * @param grafo Grafo cuja estrutura de cores a aresta deve seguir.
     * @return Cor da aresta.
     */
    public Color retornarCor(Grafo grafo) {
        //if (eMarcada() || eAtingida()) {
        Color resposta;
        if (eAtingida()) {
            resposta = grafo.corAresta.corMarcada;
        } else {
            resposta = grafo.corAresta.corNormal;
        }
        return resposta;
    }
    
    
    /** Marca internamente como atingidos a aresta e os nós a ela conectados.
     */
    public void atingir() {
        if (!atingida) { // Previne que ela seja atingida duas vezes, o que 
                         // poderia causar problemas nos nós. 
            atingida = true;
            if (!escondida) {
                no1.atingir();
                no2.atingir();
            }
        }
    }

    /** Desmarca como atingidos a aresta e os nós a ela conectados.
     */
    public void desfazerAtingirSePossivel() {
        if (atingida) { // Previne que ela seja "desatingida" duas vezes, o que 
                        // poderia causar problemas nos nós.
            if (escondida) {
                atingida = false;
            } else if (!no1.eMarcado() && !no1.eEscolhido() && !no2.eMarcado() && !no2.eEscolhido()) {
                desfazerAtingir();
            }
        }
    }
    
    /** Desmarca como atingida uma aresta, bem como os nós a ela conectados.
     */
    public void desfazerAtingir() {
        if (atingida) {
            atingida = false;
            no1.desfazerAtingir();
            no2.desfazerAtingir();
        }
    }
    
    /** Informa se a aresta é atingida.
     * @return True se a aresta é atingida, false caso contrário.
     */
    public boolean eAtingida() {
        return atingida;
    }

    /** Informa se a aresta está escondida (oculta).
     * @return True se a aresta está escondida, false caso contrário.
     */
    public boolean eEscondida() {
        return escondida;
    }
    
    /** "Esconde" uma aresta, marcando a aresta como escondida.
     */
    public void esconder() {
        if (!escondida) {
            if (atingida) {
                desfazerAtingir();
            } 
            escondida=true;            
        }
    }
    
    /** "Mostra" uma aresta, marcando a aresta como não escondida.
     */
    public void mostrar() {
        if (escondida) {
            escondida=false;
            if (no1.eMarcado() || no1.eEscolhido() || no2.eMarcado() || no2.eEscolhido()) {
                atingir();
            }
        }
    }
    
    /** Verifica se um objeto O é igual à aresta instanciada A.
     * A igualdade acontece se todos os itens abaixo forem verdadeiros:
     * - O é uma aresta
     * - Os nós de O sao iguais aos de A;
     * - O peso de O é igual ao de A;
     * @param o Objeto a ser comparado com a aresta instanciada.
     * @return True se o objeto é igual à aresta, false caso contrário.
     */    
    public boolean equals(Object o) {
        boolean ok = false;
        if (o instanceof Aresta) {
            Aresta a=(Aresta)o;
            ok = (a.no1 == no1 && a.no2 == no2) || (a.no1 == no2 && a.no2 == no1);
        }
        return ok;
    }
    
    /** Retorna uma string que caracteriza a aresta. 
     * @return String no formato "{a,b}", onde a e b são os nós conectados pela
     * aresta.
     */
    public String toString() {
        return "{"+no1+","+no2+"}";
    }

    /** Retorna o hashCode da aresta.
     * @return hashCode.
     */
    public int hashCode() {
        return(53 * no1.hashCode() * no2.hashCode());
        // Assim, uma aresta (a,b) e uma aresta (b,a) retornarao o mesmo HashCode.
    }

}


