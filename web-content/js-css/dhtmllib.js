/*
IMPORTANT: this library is NO LONGER SUPPORTED.  Please DO NOT email Mike
(or Scott) with questions.  Please direct your questions to a favorite DHTML/
JavaScript newsgroup or BBS.
*/


/******************************************************************************
* dhtmllib.js                                                                 *
*                                                                             *
* Copyright 1999 by Mike Hall.                                                *
* Web address: http://www.brainjar.com                                        *
* Last update: November 30, 1999.                                             *
*                                                                             *
* Provides basic functions for DHTML positioned elements which will work on   *
* both Netscape Communicator and Internet Explorer browsers (version 4.0 and  *
* up).                                                                        *
*                                                                             *
* Support for Netscape 6 and Mozilla M18 by Scott Andrew LePera               *
* http://www.scottandrew.com                                                  *
* Added: January 11, 2001 Edited with permission.                             *
*                                                                             *
******************************************************************************/

// Determine browser.

var isMinNS4 = (navigator.appName.indexOf("Netscape") >= 0 && parseFloat(navigator.appVersion) >= 4) ? 1 : 0;
var isMinNS6 = (isMinNS4 && navigator.userAgent.indexOf("Gecko")>=0) ? 1 : 0;
var isMinIE4 = (document.all) ? 1 : 0;
var isMinIE5 = (isMinIE4 && navigator.appVersion.indexOf("5.")) >= 0 ? 1 : 0;
var isDOM = (document.getElementById) ? 1 : 0;

//-----------------------------------------------------------------------------
// Layer visibility.
//-----------------------------------------------------------------------------

function hideLayer(layer) {
  
  if (isMinIE4||isDOM) layer.style.visibility = "hidden";
  if (isMinNS4)layer.visibility = "hide";
}

function showLayer(layer) {
  
  if (isMinIE4||isDOM)layer.style.visibility = "visible";
  if (isMinNS4) layer.visibility = "show";
}

function isVisible(layer) {
  
  if ((isMinIE4||isDOM) && layer.style.visibility == "visible") return(true);
  if (isMinNS4 && layer.visibility == "show") return(true);
  return(false);
}

//-----------------------------------------------------------------------------
// Layer positioning.
//-----------------------------------------------------------------------------

function moveLayerTo(layer, x, y) {

  if (isMinIE4||isDOM) {
    layer.style.left = x + "px";
    layer.style.top  = y + "px";
    return;
  }
  if (isMinNS4)layer.moveTo(x, y);
}

function moveLayerBy(layer, dx, dy) {

  if (isMinIE4 || isDOM){
    layer.style.left = getLeft(layer) + dx + "px";
    layer.style.top  = getTop(layer) + dy + "px";
    return;
  }
  if (isMinNS4) layer.moveBy(dx, dy);
}

function getLeft(layer) {

  if (isDOM) return(parseInt(layer.style.left));
  if (isMinIE4)return(layer.style.pixelLeft);
  if (isMinNS4)return(layer.left);
  return(-1);
}

function getTop(layer) {
  if (isDOM) return(parseInt(layer.style.top));
  if (isMinIE4) return(layer.style.pixelTop);
  if (isMinNS4) return(layer.top);
  return(-1);
}

function getRight(layer) {
  return(getLeft(layer) + getWidth(layer));
}

function getBottom(layer) {
  return(getTop(layer) + getHeight(layer));
}

function getPageLeft(layer) {

  if (isMinIE4||isDOM) return(layer.offsetLeft);
  if (isMinNS4) return(layer.pageX);
  return(-1);
}

function getPageTop(layer) {
  
  if (isMinIE4||isDOM) return(layer.offsetTop);
  if (isMinNS4) return(layer.pageY);
  return(-1);
}

function getWidth(layer) {
  
  if (isDOM){
    if (layer.style.width)
      return(parseInt(layer.style.width));
    else
      return(layer.clientWidth);
  }
  if (isMinIE4) {
    if (layer.style.pixelWidth)
      return(layer.style.pixelWidth);
    else
      return(layer.clientWidth);
  }
  if (isMinNS4) {
    if (layer.width)
      return(layer.width);
    else
      return(layer.clip.right - layer.clip.left);
  }
  
  return(-1);
}

function getHeight(layer) {
  
  if (isDOM){
    if (layer.style.height)
      return(parseInt(layer.style.height));
    else
      return(layer.clientHeight);
  }
  if (isMinIE4) {
    if (false && layer.style.pixelHeight)
      return(layer.style.pixelHeight);
    else
      return(layer.clientHeight);
  }
  if (isMinNS4) {
    if (layer.height)
      return(layer.height);
    else
      return(layer.clip.bottom - layer.clip.top);
  }
  
  return(-1);
}

function getzIndex(layer) {

  if (isMinIE4||isDOM) return(layer.style.zIndex);
  if (isMinNS4) return(layer.zIndex);
  return(-1);
}

function setzIndex(layer, z) {

  if (isMinIE4||isDOM) layer.style.zIndex = z;
  if (isMinNS4) layer.zIndex = z;
}

//-----------------------------------------------------------------------------
// Layer clipping.
//-----------------------------------------------------------------------------

function clipLayer(layer, clipleft, cliptop, clipright, clipbottom) {

  if (isMinIE4||isDOM){
    layer.style.clip = 'rect(' + cliptop + 'px ' +  clipright + 'px ' + clipbottom + 'px ' + clipleft +'px)';
    return;
  }
  if (isMinNS4) {
    layer.clip.left   = clipleft;
    layer.clip.top    = cliptop;
    layer.clip.right  = clipright;
    layer.clip.bottom = clipbottom;
  }
}

function getClipLeft(layer) {

  if (isMinIE4||isDOM) {
    var str =  layer.style.clip;
    if (!str)
      return(0);
    var clip = getDOMClipValues(layer.style.clip);
    return(clip[3]);
  }
  if (isMinNS4)
    return(layer.clip.left);
  return(-1);
}

function getClipTop(layer) {

  if (isMinIE4||isDOM) {
    var str =  layer.style.clip;
    if (!str)
      return(0);
    var clip = getDOMClipValues(layer.style.clip);
    return(clip[0]);
  }
  if (isMinNS4)
    return(layer.clip.top);
  return(-1);
}

function getClipRight(layer) {

  if (isMinIE4||isDOM) {
    var str =  layer.style.clip;
    if (!str)
      return(getWidth(layer));
    var clip = getDOMClipValues(layer.style.clip);
    return(clip[1]);
  }
  if (isMinNS4)
    return(layer.clip.right);
  return(-1);
}

function getClipBottom(layer) {

  if (isMinIE4||isDOM) {
    var str =  layer.style.clip;
    if (!str)
      return(getHeight(layer));
    var clip = getDOMClipValues(layer.style.clip);
    return(clip[2]);
  }
  if (isMinNS4)
    return(layer.clip.bottom);
  return(-1);
}

function getClipWidth(layer) {

  if (isMinIE4||isDOM) {
    var str = layer.style.clip;
    if (!str)
      return(getWidth(layer));
    var clip = getDOMClipValues(layer.style.clip);
    return(clip[1] - clip[3]);
  }
  if (isMinNS4)
    return(layer.clip.width);
  return(-1);
}

function getClipHeight(layer) {

  if (isMinIE4||isDOM) {
    var str =  layer.style.clip;
    if (!str)
      return(getHeight(layer));
    var clip = getDOMClipValues(layer.style.clip);
    return(clip[2] - clip[0]);
  }
  if (isMinNS4)
    return(layer.clip.height);
  return(-1);
}

function getIEClipValues(str) {

  var clip = new Array();
  var i;

  // Parse out the clipping values for IE layers.

  i = str.indexOf("(");
  clip[0] = parseInt(str.substring(i + 1, str.length), 10);
  i = str.indexOf(" ", i + 1);
  clip[1] = parseInt(str.substring(i + 1, str.length), 10);
  i = str.indexOf(" ", i + 1);
  clip[2] = parseInt(str.substring(i + 1, str.length), 10);
  i = str.indexOf(" ", i + 1);
  clip[3] = parseInt(str.substring(i + 1, str.length), 10);
  return(clip);
}

getDOMClipValues = getIEClipValues;

//-----------------------------------------------------------------------------
// Layer scrolling.
//-----------------------------------------------------------------------------

function scrollLayerTo(layer, x, y, bound) {

  var dx = getClipLeft(layer) - x;
  var dy = getClipTop(layer) - y;

  scrollLayerBy(layer, -dx, -dy, bound);
}

function scrollLayerBy(layer, dx, dy, bound) {

  var cl = getClipLeft(layer);
  var ct = getClipTop(layer);
  var cr = getClipRight(layer);
  var cb = getClipBottom(layer);

  if (bound) {
    if (cl + dx < 0)

      dx = -cl;

    else if (cr + dx > getWidth(layer))
      dx = getWidth(layer) - cr;
    if (ct + dy < 0)

      dy = -ct;

    else if (cb + dy > getHeight(layer))
      dy = getHeight(layer) - cb;
  }

  clipLayer(layer, cl + dx, ct + dy, cr + dx, cb + dy);
  moveLayerBy(layer, -dx, -dy);
}

//-----------------------------------------------------------------------------
// Layer background.
//-----------------------------------------------------------------------------

function setBgColor(layer, color) {
  
  if (isMinIE4||isDOM) layer.style.backgroundColor = color;
  if (isMinNS4) layer.bgColor = color;
}

function setBgImage(layer, src) {
  
  if (isMinIE4||isDOM) layer.style.backgroundImage = "url(" + src + ")";
  if (isMinNS4) layer.background.src = src;
  
}

//-----------------------------------------------------------------------------
// Layer utilities.
//-----------------------------------------------------------------------------

function getLayer(name) {
  if (isDOM)
    return document.getElementById(name);
  if (isMinNS4)
    return findLayer(name, document);
  if (isMinIE4)
    return eval('document.all.' + name);
  return null;
}

function findLayer(name, doc) {

  var i, layer;

  for (i = 0; i < doc.layers.length; i++) {
    layer = doc.layers[i];
    if (layer.name == name)
      return layer;
    if (layer.document.layers.length > 0) {
      layer = findLayer(name, layer.document);
      if (layer != null)
        return layer;
    }
  }

  return null;
}

//-----------------------------------------------------------------------------
// Window and page properties.
//-----------------------------------------------------------------------------

function getWindowWidth() {

  if (isMinNS4||isMinNS6)
    return(window.innerWidth);
  if (isMinIE4)
    return(document.body.clientWidth);
  return(-1);
}

function getWindowHeight() {

  if (isMinNS4||isMinNS6)
    return(window.innerHeight);
  if (isMinIE4)
    return(document.body.clientHeight);
  return(-1);
}

function getPageWidth() {

  if (isMinNS6)
    return(document.body.offsetWidth);
  if (isMinNS4)
    return(document.width);
  if (isMinIE4)
    return(document.body.scrollWidth);
  return(-1);
}

function getPageHeight() {

  if (isMinNS4)
    return(document.height);
  if (isMinNS6)
    return(document.body.offsetHeight); 
  if (isMinIE4)
    return(document.body.scrollHeight);
  return(-1);
}

function getPageScrollX() {

  if (isMinNS4)
    return(window.pageXOffset);
  if (isMinIE4)
    return(document.body.scrollLeft);
  return(-1);
}

function getPageScrollY() {

  if (isMinNS4)
    return(window.pageYOffset);
  if (isMinIE4)
    return(document.body.scrollTop);
  return(-1);
}
