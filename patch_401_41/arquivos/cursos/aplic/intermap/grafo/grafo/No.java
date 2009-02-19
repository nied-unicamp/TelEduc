/*
 * No.java
 * Versão: 2004-07-22
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */
package grafo;

import java.util.*;
import java.io.*;
import java.awt.*;

/** Classe para armazenar um nó de um grafo.
 * Da forma como está implementada, essa classe não sabe desenhar o nó.
 * Um elemento desta classe não conhece o grafo ao qual pertence, embora
 * saiba quais arestas incidem sobre ele.
 */
public abstract class No
{

    /** Indica a área externa ao nó.
     */
    public static int AREA_EXTERIOR = 0;

    /** Indica a área interna de um nó. 
     */
    public static int AREA_INTERIOR = 1;
    
    /** Conjunto de cores do nó. */
    public CorNo cor;

    /** Conjunto default de cores. */
    private CorNo corDefault = new CorNo();
    
    /** Indica se o nó deve ou não usar seu conjunto individual de cores. */
    private boolean usarCorIndividual;

    /** Indica se o nó está marcado, ou seja, se ele foi selecionado pelo usuário por
     * meio de um clique.
     */
    private boolean marcado;
    
    /** Indica se o nó está escondido, ou seja, se ele nao deve ser mostrado
     */
    private boolean escondido;

    /** Indica se o nó foi "escolhido", ou seja, se o usuário está sobre o nó. */
    private boolean escolhido;

    /** Informa quantas arestas selecionadas atingem o nó. */
    private int atingido;

    /** Grupo ao qual o nó pertence. */
    public Grupo grupo;
    
    /** Abscissa atual do nó. */
    public double x = 0;
    
    /** Ordenada atual do nó. */
    public double y = 0;
    
    /** Largura do texto mostrado pelo nó. */
    private int larguraTexto;
    
    /** Altura do texto mostrado pelo nó. */    
    private int alturaTexto;

    /** Largura do nó. */
    private int larguraNo;
    
    /** Altura do nó. */    
    private int alturaNo;
    
    /** Margem horizontal entre o texto do nó e sua borda. */    
    private final int margemX = 4;
    
    /** Margem vertical entre o texto do nó e sua borda. */    
    private final int margemY = 2;
    
    /** Lista das arestas que incidem neste nó ou saem dele.
     */
    public ArrayList arestas;

    /** Peso do nó.
     */
    public int peso;
    
    /** Informação sobre este nó.
     */
    public Info info;

    /** Nome do nó.
     */
    public String nome;

    /** Determina se o nome do nó deve ou não ser abreviado quando o nó estiver 
     * marcado.
     */
    public boolean abreviarSeNaoMarcado;
    
    /** Informa se o nó está sendo movido. */
    private boolean movendo;
    
    /** Código identificador do nó */
    private String id = null;
    
//    private static final Font fonte = new Font("Arial", Font.PLAIN, 14);
    
    /** Cria uma nova instância de No, criando um nó sem peso nem informação 
     * associada.
     * @param nome Nome do nó (nome mostrado no nó).
     */
    public No(String nome) {
        this(nome, 0, null);
    }

    /** Cria uma nova instância de No, criando um nó com peso mas sem informação
     * associada.
     * @param nome Nome do nó (nome mostrado no nó).
     * @param peso Peso do nó.
     */
    public No(String nome, int peso) {
        this(nome, peso, null);
    }

    /** Cria uma nova instância de No, criando um nó sem peso mas com informação
     * associada.
     * @param nome Nome do nó (nome mostrado no nó).
     * @param info Informação associada ao nó.
     */
    public No(String nome, Info info) {
        this(nome, 0, info);
    }

    /** Cria uma nova instância de No, criando um nó com peso e com informação
     * associada.
     * @param nome Nome do nó (nome mostrado no nó).
     * @param peso Peso do nó.
     * @param info Informação associada ao nó.
     */
    public No(String nome, int peso, Info info) {
        arestas = new ArrayList();
        this.nome = nome;
        this.peso = peso;
        this.info = info;
        cor = new CorNo(); // Usa o padrão de cores definido em CorNo.
        marcado = false;
        atingido = 0;
        escondido = false;
        removerCorIndividual();
        movendo = false;        
        abreviarSeNaoMarcado = true;
    }
    
    /** Cria uma nova instância de No, duplicando uma instância já existente.
     * @param no Nó a ser duplicado.
     */    
    public No(No no) {
        this(no.nome, no.peso, no.info);
        movendo = no.movendo;
        usarCorIndividual = no.usarCorIndividual;
        
    }

    /** Informa se o nó é o nó que está em movimento ou não.
     * @return True se o nó é o no que está em movimento (noMovendo), false 
     * caso contrário.
     */    
    public boolean eMovendo() {
        return movendo;
    }

    /** Registra nó que está em movimento e marca as arestas das quais ele é
     * extremidade.
     */
    public void marcarComoMovendo() {
        movendo = true;
        if (!escolhido && !marcado) {
            ListIterator li = arestas.listIterator();
            while(li.hasNext()) {
                Aresta aresta = (Aresta) li.next();
                aresta.atingir();
            }
        }
    }

    /** Desfaz o registro de qual nó está em movimento e tenta desmarcar as arestas das
     * quais ele é extremidade.  As arestas somente são desmarcadas se ambos os seus
     * nós não estão marcados.
     */
    public void desmarcarComoMovendo() {
        movendo = false;
        if (!escolhido && !marcado) {
            ListIterator li = arestas.listIterator();
            while(li.hasNext()) {
                Aresta aresta = (Aresta) li.next();
                aresta.desfazerAtingirSePossivel();
            }
        }
    }
    
    
    /** Retorna o conjunto de cores utilizado por este nó.
     * @return Conjunto de cores utilizado pelo nó.
     */    
    public CorNo retornarConjuntoDeCores() {
        CorNo corno = null;
        if (usarCorIndividual) {
            corno = cor;
        } else {
            if (grupo != null) {
                corno = grupo.cor;
            } else {
                corno = corDefault;
            }
        }
        return corno;
    }    
    
    /** Retorna cor do texto do nó.
     * @return Cor do texto do nó.
     */
    public Color retornarCorDeTexto() {
        CorNo corNo = retornarConjuntoDeCores();
        Color cor;
        if (corNo!=null) {
            if (escolhido || marcado || movendo || atingido > 0) {
                cor = corNo.corMarcado;
            } else {
                cor = corNo.corNormal;
            }
        } else {
            System.out.println("Erro em grafo.retornarCorDeTexto().");
            cor = Color.red; // problemas...
        }
        return cor;
    }
    
    /** Retorna cor de fundo do nó.
     * @return Cor de fundo do nó.
     */
    public Color retornarCorDeFundo() {
        CorNo corNo = retornarConjuntoDeCores();
        Color cor;
        if (corNo!=null) {
            if (escolhido || marcado || movendo || atingido > 0) {
                cor = corNo.corDeFundoMarcado;
            } else {
                cor = corNo.corDeFundoNormal;
            }
        } else {
            System.out.println("Erro em no.retornarCorDeFundo().");
            cor = Color.red; // problemas...
        }
        return cor;
    }

    /** Retorna o nome a ser mostrado no display.
     * Decide se o nome vai ser mostrado completamente ou abreviado.
     * @return Nome a ser mostrado.
     */
    public String nomeAMostrar() {
        String resposta;
        if (eMarcado() || nome.length()<7 || !abreviarSeNaoMarcado) {
            resposta = nome;
        } else {
            resposta = nome.substring(0,3) + "...";
        }
        return resposta;
    }
    
    /** Desenha o nó.
     * @param grafo Grafo ao qual o nó pertence. O nó precisa saber disso, uma vez que ele precisa
     * saber qual é o centro do grafo para, assim, poder se desenhar.
     * @param g Onde o nó será desenhado.
     */
    public void desenhar(Grafo grafo, Graphics g) {
        //Point2D.Double centro = new Point2D.Double((int)grafo.centro.x, (int)grafo.centro.y);
        String textoaux = nomeAMostrar();
        desenharNo(x, y, textoaux, g);
    }

    /** Atualiza os indicadores de altura e largura do texto de um nó.
     * @param g Onde o nó será desenhado.
     * @param texto Texto a ser desenhado no nó.
     */    
    public void atualizarDimensoesTexto(Graphics g, String texto) {
        FontMetrics fontMetrics = g.getFontMetrics();
        larguraTexto = (int) fontMetrics.stringWidth(texto);
        alturaTexto = (int) fontMetrics.getHeight();
    }
    
    /** Método interno para desenho do nó.
     * @param x1 Abscissa do meio do nó.
     * @param y1 Ordenada do meio do nó.
     * @param texto Texto do nó.
     * @param g Onde o nó será desenhado.
     */    
    public void desenharNo(double x1, double y1, String texto, Graphics g) {
        //larguraNo = 10; 
        larguraNo = larguraTexto + 2*margemX;
        //alturaNo = 10; 
        alturaNo = alturaTexto + 2*margemY;

        int x = (int)x1;
        int y = (int)y1;
        if (!escondido) {

//            Font font = g.getFont();
            atualizarDimensoesTexto(g, texto);
            g.setColor(retornarCorDeFundo());
            g.fillRect(x-larguraTexto/2-margemX,y-alturaTexto/2-margemY,larguraTexto+2*margemX,alturaTexto+2*margemY);
            if (marcado) {
                g.setColor(Color.black);
                g.drawRect(x-larguraTexto/2-margemX,y-alturaTexto/2-margemY,larguraTexto+2*margemX,alturaTexto+2*margemY);
                g.setColor(Color.white);
                g.drawRect(x-larguraTexto/2-margemX+1,y-alturaTexto/2-margemY+1,larguraTexto+2*margemX-2,alturaTexto+2*margemY-2);
                g.setColor(Color.black);
                g.drawRect(x-larguraTexto/2-margemX+2,y-alturaTexto/2-margemY+2,larguraTexto+2*margemX-4,alturaTexto+2*margemY-4);
            }
            g.setColor(retornarCorDeTexto());
            g.drawString(texto, x-larguraTexto/2, y+alturaTexto/2-3);

            

//            g.setColor(retornarCorDeFundo());
//            g.fillRect(x-5,y-5,10,10);
//            g.setColor(Color.black);
//            g.drawRect(x-5,y-5,10,10);
//            
//            if (marcado) {
//                Font font = g.getFont();
//                atualizarDimensoesTexto(g, texto);
//                g.setColor(Color.black);
//                g.drawRect(x-5,y-5,10,10);
//                g.setColor(Color.white);
//                g.drawRect(x-6,y-6,12,12);
//                g.setColor(Color.black);
//                g.drawRect(x-7,y-7,14,14);
//                g.setFont(fonte);
//                g.setColor(Color.white);
//                g.drawString(texto, x+larguraNo+3-1, y+alturaTexto/2+1);
//                g.setColor(retornarCorDeTexto());
//                g.drawString(texto, x+larguraNo+3, y+alturaTexto/2);
//            }
//            
        }
        
    }
    
    /** Testa se o ponto (x,y) informado se encontra dentro do nó.
     * @param x Abscissa do ponto a ser verificado.
     * @param y Ordenada do ponto a ser verificado.
     * @return Retorna AREA_INTERIOR se o ponto está dentro do nó, e AREA_EXTERIOR caso
     * contrário.
     */
    public int areaEm(int x, int y) {
        //double tx = this.x-larguraTexto/2-margemX;
        //double ty = this.y-alturaTexto/2-margemY;
        double tx = this.x-larguraNo/2;
        double ty = this.y-alturaNo/2;
        int resposta;
        //if (x >= tx && y >= ty && (x-tx) < (larguraTexto+2*margemX) && (y-ty) < (alturaTexto+2*margemY)) {
        if (x >= tx && y >= ty && (x-tx) < (larguraNo) && (y-ty) < (alturaNo)) {        
            resposta = AREA_INTERIOR;
        } else {
            resposta = AREA_EXTERIOR;
        }
        return resposta;
    }
    
    /** Informa se o nó está marcado ou não.
     * @return True se o nó está marcado, false caso contrário.
     */    
    public boolean eMarcado() {
        return marcado;
    }

    /** Informa se o nó é um nó "escolhido" (mouse sobre nó) ou não.
     * @return True se o nó está sob o mouse, false caso contrário.
     */
    public boolean eEscolhido() {
        return escolhido;
    }

    /** Informa se o nó está escondido ou não.
     * @return True se o nó está escondido, false caso contrário.
     */
    public boolean eEscondido() {
        return escondido;
    }

    /** Informa se o nó é um nó atingido ou não.
     * @return True se o nó está sendo atingido, false caso contrário.
     */    
    public boolean eAtingido() {
        return atingido > 0;
    }

    /** Informa se o nó pertence ou não ao anel central.
     * @return True se o nó pertence ao anel central, false caso contrário.
     */    
    public boolean usandoCorIndividual() {
        return usarCorIndividual;
    }
    
    /** Marca nó e as arestas das quais ele é extremidade. */
    public void marcar() {
        marcado = true;
        if (!escolhido && !movendo) {
            ListIterator li = arestas.listIterator();
            while(li.hasNext()) {
                Aresta aresta = (Aresta) li.next();
                aresta.atingir();
            }
        }
    }
    /** Desmarca nó e as arestas das quais ele é extremidade.
     * As arestas somente são desmarcadas se ambos os seus nós não estão marcados.
     */
    public void desmarcar() {
        marcado = false;
        if (!escolhido && !movendo) {
            ListIterator li = arestas.listIterator();
            while(li.hasNext()) {
                Aresta aresta = (Aresta) li.next();
                aresta.desfazerAtingirSePossivel();
            }
        }
    }
    
    /** Marca o nó como "escolhido" (mouse está sobre ele) e as arestas das quais ele é
     * extremidade.
     */
    public void escolher() {
        escolhido = true;
        if (!marcado && !movendo) {
            ListIterator li = arestas.listIterator();
            while(li.hasNext()) {
                Aresta aresta = (Aresta) li.next();
                aresta.atingir();
            }
        }
    }
    /** Desmarca nó como "escolhido" (mouse já não está sobre ele) e tenta desmarcar as
     * arestas das quais ele é extremidade. As arestas somente são desmarcadas se ambos
     * os seus nós não estão marcados.
     */
    public void desfazerEscolher() {
        escolhido = false;
        if (!marcado && !movendo) {
            ListIterator li = arestas.listIterator();
            while(li.hasNext()) {
                Aresta aresta = (Aresta) li.next();
                aresta.desfazerAtingirSePossivel();
            }
        }
    }

    /** Marca o nó como escondido. */
    public void esconder() {
        escondido = true;
        // esconder arestas relacionadas a esse nó
        ListIterator li = arestas.listIterator();
        Aresta aresta;
        while(li.hasNext()) {
            aresta = (Aresta)li.next();
            aresta.esconder();
        }
    }
    
    /** Marca o nó como não escondido. */
    public void mostrar() {
        escondido = false;
        ListIterator li = arestas.listIterator();
        while(li.hasNext()) {
            Aresta aresta = (Aresta) li.next();
            // Se ambos os nós da aresta não são escondidos
            // então posso mostrar a aresta.
            if (!aresta.no1.eEscondido() && !aresta.no2.eEscondido()) {
                aresta.mostrar();
            }
        }
    }

    /** Marca o nó como atingido, aumentando o contador de número de arestas
     * selecionadas das quais ele é extremidade.
     */
    public void atingir() {
        atingido++;
    }
    
    /** Desmarca um nó como atingido, diminuindo o contador de número de arestas
     * selecionadas das quais ele é extremidade.
     */    
    public void desfazerAtingir() {
        atingido--;
        if (atingido<0) {
            System.out.println("ERRO: O número de arestas que atingem o nó "+this+" é tido como negativo: "+atingido+".");
            System.out.println("      Ajustando para zero.");
            atingido = 0;
        }

    }

    /** Zera o contador de arestas selecionadas das quais este nó é extremidade. */    
    public void anularAtingir() {
        atingido = 0;
    }
    
    /** Atribui um conjunto específico de cores para este nó. O conjunto atribuído
     * substitui o conjunto de cores do grupo ao qual este nó pertence.
     * @param cor Conjunto de cores a ser atribuído a este nó.
     */    
    public void ajustarCorIndividual(CorNo cor) {
        usarCorIndividual = true;
        this.cor = cor;
    }
    
    /** Ignora o conjunto de cores atribuído especificamente para este nó, usando então
     * o conjunto de cores do grupo ao qual o nó pertence.
     */    
    public void removerCorIndividual() {
        usarCorIndividual = false;
    }
    
    /** Transfere o nó para um determinado grupo.
     * @param grupo Grupo para o qual o nó está sendo transferido.
     */    
    public void moverParaGrupo(Grupo grupo) {
        this.grupo = grupo;
    }
    
    /** Atribui uma nova informação ao nó, substituindo a anterior.
     * @param info Nova informação a ser associada ao nó.
     */    
    public void ajustarInfo(Info info) {
        this.info = info;
    }
    
    /** Retorna a informação associada atualmente ao nó.
     * @return Retorna a informação associada atualmente ao nó.
     */    
    public Info retornarInfo() {
        return info;
    }

    /** Informa a largura do nó.
     * @return Retorna a largura do nó.
     */    
    public int getWidth() {
        return (larguraTexto + margemX * 2);
    }
    
    /** Informa a altura do nó.
     * @return Retorna a altura do nó.
     */    
    public int getHeight() {
        return (alturaTexto + margemY * 2);
    }
    
    /** Retorna o código do nó .
     * @return código do nó.
     */
    public String retornarId() {
        return id;
    }
    
    /** Registra o código do nó.
     * @param id código do nó.
     */
    public void ajustarId(String id) {
        this.id = id;
    }

    /** Retorna uma string que caracteriza o nó. 
     * @return String no formato "nome(id)", onde nome é o nome do nó e id é
     * seu código.
     */
    public String toString() {
        return nome+"("+id+")";
    }
}

