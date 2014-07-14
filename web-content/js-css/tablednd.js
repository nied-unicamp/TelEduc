/**
 * Encapsulate table Drag and Drop in a class. We'll have this as a Singleton
 * so we don't get scoping problems.
 */
function TableDnD() {
    /** Keep hold of the current drag object if any */
    this.dragObject = null;
    /** Keep hold of the current drag object's brother if any */
    this.dragObjectBrother = null;
    /** The current mouse offset */
    this.mouseOffset = null;
    /** The current table */
    this.table = null;
    /** The current tbody */
    this.tbody = null;
    /** Remember the old value of Y so that we don't do too much processing */
    this.oldY = 0;

    this.alteracao = 0;

    this.linhas = new Array();
    this.linhas_string;

    /** Initialise the drag and drop by capturing mouse move events */
    this.init = function(table,tbody,hasBrother) {
    	var rows;
    	
    	if(table != null){
        	this.table = table;
         	rows = table.tBodies[0].rows; //getElementsByTagName("tr")
    	}
    	else{
    		this.tbody = tbody;
    		rows = tbody.rows;
    	}
    	
        for (var i=0; i<rows.length; i++){
        	if(hasBrother){
        		if(i%2 == 0)
        			this.makeDraggable(rows[i],hasBrother);
        	}
        	else
            	this.makeDraggable(rows[i],hasBrother);
        }
        
        this.linhas = rows;

        var self = this;
        // Now make the onmousemove method in the context of "self" so that we can get back to tableDnD
        document.onmousemove = function(ev){
        	
        	// Compatibilidade com os layers!
        	Ypos = (isMinNS4) ? ev.pageY : event.clientY;
        	Xpos = (isMinNS4) ? ev.pageX : event.clientX;
        	
        	
            if (self.dragObject) {
                ev   = ev || window.event;
                var mousePos = self.mouseCoords(ev);
                var y = mousePos.y - self.mouseOffset.y;
                if (y != self.oldY) {
                    // work out if we're going up or down...
                    var movingDown = y > self.oldY;
                    // update the old value
                    self.oldY = y;
                    // update the style to show we're dragging
                    self.dragObject.style.backgroundColor = "#eee";
                    // If we're over a row then move the dragged row to there so that the user sees the
                    // effect dynamically
                    var currentRow = self.findDropTargetRow(y);
                    if (currentRow) {
                        if (movingDown && self.dragObject != currentRow) {
//                              self.linhas_string = self.linhas_string.replace(self.dragObject.id, '*');
//                              self.linhas_string = self.linhas_string.replace(currentRow.id, self.dragObject.id);
//                              self.linhas_string = self.linhas_string.replace('*', currentRow.id);
                            self.dragObject.parentNode.insertBefore(self.dragObject, currentRow.nextSibling);
                        } else if (! movingDown && self.dragObject != currentRow) {
                            self.dragObject.parentNode.insertBefore(self.dragObject, currentRow);
//                              self.linhas_string = self.linhas_string.replace(self.dragObject.id, '*');
//                              self.linhas_string = self.linhas_string.replace(currentRow.id, self.dragObject.id);
//                              self.linhas_string = self.linhas_string.replace('*', currentRow.id);
                        }
                        self.alteracao = 1;
                        if(hasBrother)
                        	self.dragObject.parentNode.insertBefore(self.dragObjectBrother, self.dragObject.nextSibling);           	
                    }
                }

                return false;
            }
        };

        // Similarly for the mouseup
        document.onmouseup   = function(ev){
            if (self.dragObject != null) {
                var droppedRow = self.dragObject;
                // If we have a dragObject, then we need to release it,
                // The row will already have been moved to the right place so we just reset stuff
                droppedRow.style.backgroundColor = 'transparent';
                self.dragObject = null;
                // And then call the onDrop method in case anyone wants to do any post processing
                self.onDrop(self.table, droppedRow);
                if(self.alteracao==1){
                  var ids = new Array();
                  var j = 0;
                  for (i=0; i<self.linhas.length; i++){
                  	if(hasBrother){
                  		if(i%2 == 0)
                  			ids[j] = self.linhas[i].id;
                  			j++;
                  	}
                  	else
                    	ids[i] = self.linhas[i].id;
                  } 
                  self.alteracao=0;
                  
                  SoltaMouse(ids);
                }
//                 xajax_AtualizaPosicoes(cod_curso, cod_usuario, cod_topico);
            }
        };
    }
    
    /** Terminate the drag and drop by capturing mouse move events */
    this.term = function() {
    	document.onmousemove = null;
    	document.onmouseup = null;
    	
    	if(this.table != null)
         	rows = this.table.tBodies[0].rows; //getElementsByTagName("tr")
    	else
    		rows = this.tbody.rows;
    	
    	for (var i=0; i<rows.length; i++) {
            this.unmakeDraggable(rows[i]);
        }
    }

    /** This function is called when you drop a row, so redefine it in your code
        to do whatever you want, for example use Ajax to update the server */
    this.onDrop = function(table, droppedRow) {
        // Do nothing for now
    }

	/** Get the position of an element by going up the DOM tree and adding up all the offsets */
    this.getPosition = function(e){
        var left = 0;
        var top  = 0;
		/** Safari fix -- thanks to Luis Chato for this! */
		if (e.offsetHeight == 0) {
			/** Safari 2 doesn't correctly grab the offsetTop of a table row
			    this is detailed here:
			    http://jacob.peargrove.com/blog/2006/technical/table-row-offsettop-bug-in-safari/
			    the solution is likewise noted there, grab the offset of a table cell in the row - the firstChild.
			    note that firefox will return a text node as a first child, so designing a more thorough
			    solution may need to take that into account, for now this seems to work in firefox, safari, ie */
			e = e.firstChild; // a table cell
		}

        while (e.offsetParent){
            left += e.offsetLeft;
            top  += e.offsetTop;
            e     = e.offsetParent;
        }

        left += e.offsetLeft;
        top  += e.offsetTop;

        return {x:left, y:top};
    }

	/** Get the mouse coordinates from the event (allowing for browser differences) */
    this.mouseCoords = function(ev){
        if(ev.pageX || ev.pageY){
            return {x:ev.pageX, y:ev.pageY};
        }
        return {
            x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
            y:ev.clientY + document.body.scrollTop  - document.body.clientTop
        };
    }

	/** Given a target element and a mouse event, get the mouse offset from that element.
		To do this we need the element's position and the mouse position */
    this.getMouseOffset = function(target, ev){
        ev = ev || window.event;

        var docPos    = this.getPosition(target);
        var mousePos  = this.mouseCoords(ev);
        return {x:mousePos.x - docPos.x, y:mousePos.y - docPos.y};
    }

	/** Take an item and add an onmousedown method so that we can make it draggable */
    this.makeDraggable = function(item,hasBrother){
        if(!item) return;
        var self = this; // Keep the context of the TableDnd inside the function
        item.onmousedown = function(ev){
            self.dragObject = this;
            if(hasBrother){
            	self.dragObjectBrother = document.getElementById("trAltGab_"+self.dragObject.id.split("_")[1]);
            }
            self.mouseOffset = self.getMouseOffset(this, ev);
            return false;
        }
        item.style.cursor = "move";
    }
    
    /** Take an item and make it undraggable */
    this.unmakeDraggable = function(item){
        if(!item) return;
        item.onmousedown = null;
        item.style.cursor = "default";
    }

    /** We're only worried about the y position really, because we can only move rows up and down */
    this.findDropTargetRow = function(y) {
    	var rows;
        if(this.table != null)
        	rows = this.table.tBodies[0].rows;
        else
        	rows = this.tbody.rows;
        
        for (var i=0; i<rows.length; i++) {
            var row = rows[i];
            var rowY    = this.getPosition(row).y;
            var rowHeight = parseInt(row.offsetHeight)/2;
			if (row.offsetHeight == 0) {
				rowY = this.getPosition(row.firstChild).y;
				rowHeight = parseInt(row.firstChild.offsetHeight)/2;
			}
            // Because we always have to insert before, we need to offset the height a bit
            if ((y > rowY - rowHeight) && (y < (rowY + rowHeight))) {
                // that's the row we're over
                return row;
            }
        }
        return null;
    }

}
