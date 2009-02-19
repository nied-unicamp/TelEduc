/*
 * ObservableBoolean.java
 * Versão: 2004-07-04
 * Autor: Celmar Guimarães da Silva
 */

package grafo.polar;
import java.util.Observable;

/** Classe responsável pela criação de um objeto que registre um valor booleano.
 */
public class ObservableBoolean extends Observable {
    
    /** Valor booleano armazenado pelo objeto.
     */    
    private boolean valor = false;
    
    /** Cria uma nova instância de ObservableBoolean com o valor false.
     */
    public ObservableBoolean() {
        setValue(false);
    }
    
    /** Cria uma nova instância de ObservableBoolean com um valor especificado.
     * @param valor Valor booleano inicial do objeto.
     */    
    public ObservableBoolean(boolean valor) {
        setValue(valor);
    }
    
    /** Retorna o valor booleano armazenado.
     * @return Valor booleano.
     */    
    public boolean getValue() {
        return valor;
    }
    
    /** Ajusta o valor booleano do objeto e notifica seus observadores.
     * @param valor Novo valor a ser armazenado pelo objeto.
     */    
    public void setValue(boolean valor) {
        this.valor = valor;
        setChanged();
        notifyObservers();
    }
    
    /** Inverte o valor booleano do objeto e notifica seus observadores. 
     */    
    public void change() {
        valor = !valor;
        setChanged();
        notifyObservers();
    }
    
}
