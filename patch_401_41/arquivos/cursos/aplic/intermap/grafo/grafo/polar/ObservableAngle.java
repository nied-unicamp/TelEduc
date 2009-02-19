/*
 * ObservableAngle.java
 * Versão: 2004-07-04
 * Autor: Celmar Guimarães da Silva
 */

package grafo.polar;
import java.util.Observable;
import grafo.*;

/** Classe responsável pela criação de um objeto que registre o valor de um 
 * ângulo. 
 */
public class ObservableAngle extends Observable {
    
    /** Valor do ângulo em graus ( 0 <= angulo < 360 ). */    
    private double valor = 0;
    
    /** Cria uma nova instância de ObservableAngle. */
    public ObservableAngle() {
        this(0);
    }
    
    /** Cria uma nova instância de ObservableAngle cujo angulo tenha o valor
     * especificado.
     * @param valor Valor do ângulo com o qual o objeto deve ser criado.
     */
    public ObservableAngle(double valor) {
        this.valor = Util.normalizarAngulo(valor);
    }

    /** Retorna o valor do ângulo armazenado.
     * @return Retorna o ângulo armazenado pelo objeto.
     */
    public double getValue() {
        return valor;
    }
    
    /** Ajusta o valor do ângulo armazenado, e notifica seus observadores.
     * @param valor Novo valor do ângulo.
     */
    public void setValue(double valor) {
        this.valor = Util.normalizarAngulo(valor);
        setChanged();
        notifyObservers();
    }
    
}
