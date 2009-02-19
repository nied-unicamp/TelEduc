/*
 * ObservableBooleanXY.java
 * Versão: 2004-07-04
 * Autor: Celmar Guimarães da Silva
 */

package grafo.polar;
import java.util.Observable;

/** Classe responsável pela criação de um objeto que registre as coordenadas
 * X e Y de um ponto e um valor booleano associado a elas.
 */
public class ObservableBooleanXY extends Observable {
    
    /** Valor booleano armazenado pelo objeto.
     */
    private boolean bool = false;
    
    /** Abscissa armazenada pelo objeto.
     */
    private int x = -1;
    
    /** Ordenada armazenada pelo objeto.
     */
    private int y = -1;
    
    /** Cria uma nova instância de ObservableBooleanXY.
     */
    public ObservableBooleanXY() {
        setValue(false,-1,-1);
    }
    
    /** Cria uma nova instância de ObservableBooleanXY, com os valores
     * especificados.
     * @param bool Valor booleano.
     * @param x Abscissa.
     * @param y Ordenada.
     */
    public ObservableBooleanXY(boolean bool, int x, int y) {
        setValue(bool, x, y);
    }
    
    /** Retorna o valor booleano armazenado pelo objeto.
     * @return Valor booleano.
     */
    public boolean getBoolean() {
        return bool;
    }
    
    /** Retorna a abscissa armazenada pelo objeto.
     * @return Abscissa.
     */
    public int getX() {
        return x;
    }
    
    /** Retorna a ordenada armazenada pelo objeto.
     * @return Ordenada.
     */
    public int getY() {
        return y;
    }
    
    /** Ajusta o valor booleano armazenado pelo objeto e notifica seus
     * observadores.
     * @param bool Novo valor booleano.
     */
    public void setBoolean(boolean bool) {
        this.bool = bool;
        setChanged();
        notifyObservers();
    }

    /** Ajusta a abscissa e a ordenada armazenadas pelo objeto e notifica seus
     * observadores.
     * @param x Nova abscissa.
     * @param y Nova ordenada.
     */
    public void setXY(int x, int y) {
        this.x = x;
        this.y = y;
        setChanged();
        notifyObservers();
    }
    
    /** Ajusta o valor booleano, a abscissa e a ordenada armazenadas pelo objeto
     * e notifica seus observadores.
     * @param bool Novo valor booleano.
     * @param x Nova abscissa.
     * @param y Nova ordenada.
     */
    public void setValue(boolean bool, int x, int y) {
        this.bool = bool;
        this.x = x;
        this.y = y;
        setChanged();
        notifyObservers();
    }
}
