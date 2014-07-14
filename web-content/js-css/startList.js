function startList() {
  if (document.all && document.getElementById) {
    nodes = document.getElementsByTagName("span"); /* aqui ele transforma os span's da tela */
    for (i=0; i<nodes.length ; i++) {
      node = nodes[i];
      node.onmouseover = function() {
        this.className += "Hover";
      }
      node.onmouseout = function() {
        this.className = this.className.replace ("Hover", "");
      }
    }
//     nodes = document.getElementsByTagName("li"); /* aqui ele transforma os li's da tela */
//     for (i=0; i<nodes.length; i++) {
//       node = nodes[i];
//       node.onmouseover = function() {
//         this.className += "Hover";
//       }
//       node.onmouseout = function() {
//         this.className = this.className.replace("Hover", "");
//       }
//     }
  }
}