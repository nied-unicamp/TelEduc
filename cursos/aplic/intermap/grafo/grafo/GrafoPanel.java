/*
 * GrafoPanel.java
 * Versão: 2004-07-04
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo;
import javax.swing.*;
import java.io.*;
import java.awt.event.*;
import java.awt.Dimension;
import java.util.List;

/** Cria um panel em que o grafo e seus respectivos controles são desenhados. */
public abstract class GrafoPanel extends JPanel
implements ComponentListener 
{
    
    /** Grafo a ser desenhado. */    
    private Grafo grafo;
    
    /** Display em que o grafo é mostrado. */    
    private GrafoDisplay display;
    
    /** Textos a serem utilizados pelo panel e pelo seu display. */
    public String[] textos;
    
    /** Cria uma nova instância GrafoPanel.
     * @param grafo Grafo a ser mostrado pelo objeto.
     */
    public GrafoPanel(Grafo grafo) {
        definirTextosDefault();
        
        //usarGrafo(grafo);
        this.grafo = grafo;
        
        // definindo a interface
        display = criarDisplay(grafo);
        if (display!=null) {
            
            display.setAlignmentX(0.0F);
            display.setAlignmentY(0.0F);
            inserirLayoutEControles(display, grafo);
            
        } else {
            System.out.println("Erro. Display não pôde ser inicializado.");
            System.out.println("Error. Display could not be initialized.");            
        }
        addComponentListener(this);

    }

    /**
     * Cria e insere layout e controles (se existirem) no GrafoPanel.
     * @param display Display em que o grafo será mostrado.
     * @param grafo Grafo a ser mostrado.
     */    
    public abstract void inserirLayoutEControles(GrafoDisplay display, Grafo grafo);
    
    /**
     * Cria um display para mostrar o grafo informado.
     * @param grafo Grafo a ser mostrado.
     * @return Display, cujo tipo varia de acordo com o tipo do grafo informado.
     */    
    public abstract GrafoDisplay criarDisplay(Grafo grafo);
    
    /** Modifica textos utilizados em menus e diálogos. Útil para definir um novo
     * conjunto de textos, como seria necessário no caso de textos em outro idioma.
     * @param textosNovos Textos que irão substituir os 
     * textos atuais, tanto no panel quanto em seu display.
     */
    public void modificarTextos(List textosNovos) {
        // O GrafoPanel em si não utiliza nenhum texto.
        // Logo, repassa para o display todos os textos recebidos.
        if (textosNovos!=null) {
            if (textosNovos.size() >= textos.length) {
                List textosNovosDoPanel = textosNovos.subList(0, textos.length);
                textos = (String[])textosNovosDoPanel.toArray(textos);
                if (display!=null) {
                    List textosNovosDoDisplay = textosNovos.subList(textos.length, textosNovos.size());
                    display.modificarTextos(textosNovosDoDisplay);
                } else {
                    System.out.println("Erro. Não foi possível modificar os textos do display, porque o display do grafo ainda não estava pronto.");
                    // System.out.println("Error. The display texts could not be modified because the graph display was not ready.");
                }
            } else {
                System.out.println("Erro. Não foi possível modificar os textos do panel, porque a lista contendo os novos textos é de tamanho diferente da lista original de textos.");
            }
        } else {
            System.out.println("Não foi possível modificar os textos do panel, porque a lista contendo os novos textos é nula.");
        }
        
    }

    /** Modifica o nome que descreve o peso dos nós. Inicialmente esse nome é "Peso",
     * mas pode haver interesse em modificá-lo para especificar que esse peso se refere
     * a uma determinada medida (por exemplo, número de mensagens recebidas).
     * Esse nome é independente do nome do peso das arestas.
     * @param nome Novo nome a ser utilizado.
     */        
    public void renomearPesoDosNosPara(String nome) {
        display.renomearPesoDosNosPara(nome);
    }
    
    /** Modifica o nome que descreve o peso das arestas. Inicialmente esse nome é "Peso",
     * mas pode haver interesse em modificá-lo para especificar que esse peso se refere
     * a uma determinada medida (por exemplo, número de mensagens representadas pela
     * aresta).
     * Esse nome é independente do nome do peso dos nós.
     * @param nome Novo nome a ser utilizado.
     */    
    public void renomearPesoDasArestasPara(String nome) {
        display.renomearPesoDasArestasPara(nome);
    }
    
    
    /** Salva um arquivo contendo a imagem do display mostrando o grafo.
     * @param nomeArquivo Nome do arquivo
     * @param tipoArquivo Tipo do arquivo (ao menos jpeg e png são suportados).
     * @return True se a operação foi bem sucedida, False caso contrário.
     */    
    public boolean salvarDisplay(String nomeArquivo, String tipoArquivo) {
        boolean resposta = false;
        if (display!=null) {
            File arquivo = new File(nomeArquivo);
            resposta = display.salvarImagem(arquivo, tipoArquivo);
        } 
        return resposta;
    }

    /**
     * Informa se já foi definido algum display para este GrafoPanel.
     * @return True se um display foi definido, False caso contrário.
     */    
    public boolean displayEstaDefinido() {
        return (display!=null);
    }
    
    /** Define quais textos serão utilizados pelo panel. */
    public void definirTextosDefault() {
        textos = new String[0];
    }
    
    /** Sorteia a posição dos nós de acordo com o tamanho atual do panel.
     */
    synchronized public void disporNosAleatoriamente() {
        if (display != null) {
            display.disporNosAleatoriamente();
        }
    }
    
    /** Sorteia a posição dos nós de acordo com uma dimensão especificada.
     * @param d Dimensão dentro da qual as posições dos nós devem ser sorteadas.
     */
    synchronized public void disporNosAleatoriamente(Dimension d) {
        if (display != null) {
            display.disporNosAleatoriamente(d);
        }
    }    
    
    /** Permite que os nós executem animações.
     */
    public void habilitarAnimacao() {
        display.habilitarAnimacao();
    }

    /** Não permite que os nós executem animações.
     */
    public void desabilitarAnimacao() {
        display.desabilitarAnimacao();
    }

    /** Ajusta propriedades do grafo quando ele é exibido. 
     * @param componentEvent Evento.
     */
    public void componentShown(java.awt.event.ComponentEvent componentEvent) {
        display.ajustarLimitesDeEspacoDoGrafo();
    }
    
    /** Não está sendo utilizado.
     * @param componentEvent Evento.
     */    
    public void componentMoved(java.awt.event.ComponentEvent componentEvent) {
    }
    
    /** Não está sendo utilizado.
     * @param componentEvent Evento.
     */    
    public void componentResized(java.awt.event.ComponentEvent componentEvent) {
    }

    /** Não está sendo utilizado.
     * @param componentEvent Evento.
     */    
    public void componentHidden(java.awt.event.ComponentEvent componentEvent) {
    }
        
}