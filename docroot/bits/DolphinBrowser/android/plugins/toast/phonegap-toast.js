var ToastPlugin = function() {
};

ToastPlugin.prototype.show_long = function(message, win, fail) {
  PhoneGap.exec(win, fail, "ToastPlugin", "show_long", [message]);
};

ToastPlugin.prototype.show_short = function(message, win, fail) {
  PhoneGap.exec(win, fail, "ToastPlugin", "show_short", [message]);
};

/**
 * <ul>
 * <li>Register the ToastPlugin Javascript plugin.</li>
 * <li>Also register native call which will be called when this plugin runs</li>
 * </ul>
 */
PhoneGap.addConstructor(function() { 
  // Register the javascript plugin with PhoneGap
  PhoneGap.addPlugin('ToastPlugin', new ToastPlugin());

  // Register the native class of plugin with PhoneGap
  // navigator.app.addService("ToastPlugin", "com.chariotsolutions.toast.plugin.ToastPlugin"); 
});
