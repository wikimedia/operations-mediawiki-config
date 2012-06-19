/*
 * SimpleMenu
 * (Because I need it to work)
 */

var SimpleMenu = function()
{
  var menuPlugin = this;
  document.addEventListener('deviceready', function() {  
    var menus = document.getElementsByTagName('menu');
    if(menus.length > 0)
    {
      var firstMenu = menus[0];
      menuPlugin.loadMenu(menus[0], function() {
        console.log('Menu loaded');
      },
      function()
      {
      });
    }
  });
}

SimpleMenu.prototype.setMenuState = function(action, state, win, fail) {
	PhoneGap.exec(function(result) {
		win(result);
	},
	function(ex)
	{
		fail(ex);
	},
	"SimpleMenu", "setMenuState", [action, state]);
}

SimpleMenu.prototype.loadMenu = function(menu, triggers, win, fail)
{
  this.menu = [];
  this.triggers = triggers;
  var commands = menu.getElementsByTagName('command');
  for(var i = 0; i < commands.length; ++i)
  {
    var item = {};
    item.icon = commands[i].getAttribute('icon');
    item.action = commands[i].getAttribute('action');
    item.label = commands[i].getAttribute('label');
    var disabled = commands[i].getAttribute('disabled');
    if(disabled == 'disabled')
    	disabled = true;
    if(disabled == 'null')
    	disabled = false;    
    item.disabled = disabled;
    this.menu.push(item);
  }
  var setRefresh = this.setRefresh;
  var winning = function() {
    setRefresh(win, fail);
  }
  this.createMenu(winning, fail);
}

/*
 * This is a method that we use to actual create the menu.  NOTE: This will update
 * the internal menu structure, but we won't create a new menu until we actually call refresh
 */

SimpleMenu.prototype.createMenu = function(win, fail)
{
  var menu = this.menu || [];
  var menuString = JSON.stringify(menu);
  PhoneGap.exec(function(result) {
    win(result);
  },
  function(ex)
  {
    fail(ex);
  }, 
  "SimpleMenu", "create", [menuString]);
}

SimpleMenu.prototype.setRefresh = function(win, fail)
{
  PhoneGap.exec(function(result) {
    win(result);
  },
  function(ex)
  {
    fail(ex);
  },
  "SimpleMenu", "refresh", []);
}

SimpleMenu.prototype.fireCallback = function(index)
{
  if(this.menu != null && this.menu.length > index)
  {
    var action = this.menu[index].action;
	this.triggers[action].call(this);
  }
}

PhoneGap.addConstructor(function() 
{
  PhoneGap.addPlugin("SimpleMenu", new SimpleMenu());
});

