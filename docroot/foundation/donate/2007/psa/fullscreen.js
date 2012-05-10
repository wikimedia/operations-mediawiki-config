/*
 * Default FlowPlayer fullscreen opener.
 * http://flowplayer.sourceforge.net
 */

function flowPlayerOpenFullScreen(config) {
  var winWidth = window.screen.availWidth;
  var winHeight = window.screen.availHeight;
  var fullScreenWindow = window.open('fullscreen.html', 'Wikimedia - Watch Jimmy\'s video...', 'left=0,top=0,width='+winWidth+',height='+winHeight+',status=no,resizable=yes');
}

function flowPlayerExitFullScreen(config) {
  self.close();
}

