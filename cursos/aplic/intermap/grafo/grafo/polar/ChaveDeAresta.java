/*
 * ChaveDeAresta.java
 * Versão: 2004-08-26
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo.polar;

/** Classe que estabelece uma chave que define uma aresta polar.
 */
public class ChaveDeAresta {
    
    /** Ângulo do primeiro nó da aresta.
     */
    public double angulo1;
    
    /** Ângulo do segundo nó da aresta.
     */    
    public double angulo2;
    
    /** Anel do primeiro nó da aresta.
     */
    public boolean anel1;
    
    /** Anel do segundo nó da aresta.
     */    
    public boolean anel2;

    /** Precisão utilizada na comparação de ângulos.
     */
    private final double precisao = 100000;
    
    /** Cria uma nova instância de ChaveDeAresta
     * @param angulo1 Ângulo do primeiro nó da aresta.
     * @param anel1 Anel do primeiro nó da aresta.
     * @param angulo2 Ângulo do segundo nó da aresta.
     * @param anel2 Anel do segundo nó da aresta.
     */
    public ChaveDeAresta(double angulo1, boolean anel1, double angulo2, boolean anel2) {
        this.angulo1 = ajustaPrecisao(angulo1);
        this.anel1 = anel1;
        this.angulo2 = ajustaPrecisao(angulo2);
        this.anel2 = anel2;
    }

    /** Verifica se um objeto informado é igual à chave instanciada.
     * Nesta verificação, duas chaves de arestas são consideradas iguais se o conjunto
     * de nós de ambos os objetos for igual, independentemente da ordem dos nós.
     * Ou seja, os dois objetos A e B nos exemplos abaixo são iguais:
     * A = { (45, true) , (90, false) }
     * B = { (45, true) , (90, false) }
     *
     * A = { (45, true) , (90, false) }
     * B = { (90, false) , (45, true) }
     *
     * Mas no exemplo abaixo A e B não são iguais:
     * A = { (45, true) , (90, false) }
     * B = { (90, true) , (45, false) }
     * @param obj Objeto a ser comparado com a instância em questão.
     * @return True se os objetos forem iguais, false caso contrário.
     */    
    public boolean equals(Object obj) {
        boolean resposta = false;
        if (obj instanceof ChaveDeAresta) {
            ChaveDeAresta ca = (ChaveDeAresta) obj;
//            double ca1 = ajustaPrecisao(ca.angulo1);
//            double ca2 = ajustaPrecisao(ca.angulo2);
            
            resposta = (angulo1 == ca.angulo1 && anel1 == ca.anel1 && 
                        angulo2 == ca.angulo2 && anel2 == ca.anel2) ||
                       (angulo1 == ca.angulo2 && anel1 == ca.anel2 && 
                        angulo2 == ca.angulo1 && anel2 == ca.anel1);
        } 
        return resposta;
    }

    /** Arredonda um valor informado, mantendo apenas um número fixo de casas decimais.
     * É necessário devido a imprecisões mínimas que impediam dois ângulos de serem
     * considerados iguais.
     * @param d Valor a ser arredondado.
     * @return Valor após o arredondamento.
     */    
    public double ajustaPrecisao(double d) {
        return(Math.round(d * precisao) / precisao);
    }
    
    /** Gera o hashCode da chave.
     * @return hashCode.
     */    
    public int hashCode() {
        double a1 = angulo1;
        if (!anel1) {
            a1 = -a1;
        }
        double a2 = angulo2;
        if (!anel2) {
            a2 = -a2;
        }
        return 23 * (int)a1 + 59 * (int)a2;
    }
    
}
