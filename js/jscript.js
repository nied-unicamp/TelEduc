function mudafonte(tipo) 
{
  if ( tipo == 0 ) {
    document.getElementById("tabelaExterna").style.fontSize="1.0em";
    tipo='';
  } 
  if ( tipo == 1 ) {
    document.getElementById("tabelaExterna").style.fontSize="1.2em";
    tipo=''; 
  }
  if ( tipo == 2 ) { 
    document.getElementById("tabelaExterna").style.fontSize="1.4em";
    tipo=''; 
  }
}

function startList() 
{
  if (document.all && document.getElementById) {
    nodes = document.getElementsByTagName("span");
    for (i=0; i<nodes.length; i++) {
      node = nodes[i];
      node.onmouseover = function() {
        this.className += "Hover";
      }
      node.onmouseout = function() {
        this.className = this.className.replace("Hover", "");
      }
    }
    nodes = document.getElementsByTagName("li");
    for (i=0; i<nodes.length; i++) {
      node = nodes[i];
      node.onmouseover = function() {
        this.className += "Hover";
      }
      node.onmouseout = function() {
        this.className = this.className.replace("Hover", "");
      }
    }
  }

}

var feedback_count = 0;

var fb_array_colors_success = new Array();
if(navigator.userAgent.indexOf("MSIE")!=-1){
  fb_array_colors_success[0] = 0xFFFFF;
  fb_array_colors_success[1] = 0xECF5FA;
  fb_array_colors_success[2] = 0xD6EBF3;
  fb_array_colors_success[3] = 0xC2E0ED;
  fb_array_colors_success[4] = 0xAED7E7;
  fb_array_colors_success[5] = 0x9BCDE1;
  fb_array_colors_success[6] = 0xA9D7E1;
  fb_array_colors_success[7] = 0xB8E0E1;
  fb_array_colors_success[8] = 0xC7EBE0;
  fb_array_colors_success[9] = 0xD5F5DF;
  fb_array_colors_success[10] = 0xE4FFDE;
}else{

  fb_array_colors_success[0] = "#FFFFF";
  fb_array_colors_success[1] = "#ECF5FA";
  fb_array_colors_success[2] = "#D6EBF3";
  fb_array_colors_success[3] = "#C2E0ED";
  fb_array_colors_success[4] = "#AED7E7";
  fb_array_colors_success[5] = "#9BCDE1";
  fb_array_colors_success[6] = "#A9D7E1";
  fb_array_colors_success[7] = "#B8E0E1";
  fb_array_colors_success[8] = "#C7EBE0";
  fb_array_colors_success[9] = "#D5F5DF";
  fb_array_colors_success[10] = "#E4FFDE";
}

function mudaBackgroundFeedback(i, success){
  if(success){
    document.getElementById('span_feedback').style.backgroundColor = fb_array_colors_success[i];
  }
  if(i<10){
    i = i+1;
    setTimeout("mudaBackgroundFeedback("+i+", "+success+")", 100);
  }
}

function mostraFeedback(string, success){

  document.getElementById('feedback').className="";
  if((success == 'true') || (success===true)){
    mudaBackgroundFeedback(0, true);
  }else if((success=='false') || (success===false)){
    document.getElementById('span_feedback').className="feedback_error";
  }

  document.getElementById('span_feedback').innerHTML=string;

  setTimeout("removeFeedback()", 30000);  //30 seg
  feedback_count++;
  window.location.hash="topo";
}

function removeFeedback(){
  feedback_count--;
  if(feedback_count > 0) return;
  document.getElementById('feedback').className="feedback_hidden";
}