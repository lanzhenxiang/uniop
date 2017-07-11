var vmware = (typeof vmware == "undefined" || !vmware) ? {} : vmware;

vmware.log = (function(){
	var print = function (){};
	
	if (window.console && window.console.log) {
        print = window.console.log;
    }
	
	var  format = function (level, component, string){
		return "{0} [{1}] {2}: {3}".format((new Date()).toLocaleTimeString(), level, component, string);
	};
	
	var setPrinter = function (printer) {
		var failure = false;
		if (!printer) {
            print = function (){};
        } else if (typeof(printer) == "function") {
            print = printer;
        } else if (printer === "popout") {
            var popout = window.open('','','width=600,height=400,resizable=1,location=0');
            var container = $('<ul/>', popout.document).css('font-family','monospace').appendTo($('body', popout.document));

            print = function (stringValue) {
                container.append($('<li/>', popout.document).append(stringValue));
            };
        }
	}
	
	var that = function (level, component, string){
		// 修正原脚本错误
		if (print === window.console.log){
			window.console.log(format(level, component, string));
		}else{
			print(format(level, component, string));
		}
	};
	that.setPrinter = setPrinter;
	return that;
}());