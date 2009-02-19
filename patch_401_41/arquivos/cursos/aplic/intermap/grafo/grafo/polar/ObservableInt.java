/*
 * ObservableInt.java
 * Versão: 2004-07-04
 * Autor: Celmar Guimarães da Silva
 */

package grafo.polar;
import java.util.Observable;

/** Classe responsável pela criação de um objeto que registre um valor inteiro.
 */
public class ObservableInt extends Observable {
    

    /** Valor inteiro armazenado pelo objeto. */    
    private int valor = 0;
    
    /** Cria uma nova instância de ObservableInt. */
    public ObservableInt() {
        this(0);
    }
    
    /** Cria uma nova instância de ObservableInt, armazenando o valor especificado.
     * @param valor Valor a ser armazenado.
     */    
    public ObservableInt(int valor) {
        this.valor = valor;
    }
    
    /** Retorna o valor inteiro armazenado pelo objeto.
     * @return Valor armazenado pelo objeto.
     */    
    public int getValue() {
        return valor;
    }
    
    /** Modifica o valor inteiro armazenado pelo objeto.
     * @param valor Novo valor a ser armazenado.
     */    
    public void setValue(int valor) {
        this.valor = valor;
        setChanged();
        notifyObservers();
    }
    
    /** Adiciona um valor ao número inteiro armazenado no objeto.
     * @param delta Valor a ser adicionado ao número armazenado.
     */    
    public void add(int delta) {
        setValue(valor + delta);
    }

    /** Subtrai um valor do número inteiro armazenado no objeto.
     * @param delta Valor a ser subtraído do número armazenado.
     */    
    public void sub(int delta) {
        setValue(valor - delta);
    }
    
}
