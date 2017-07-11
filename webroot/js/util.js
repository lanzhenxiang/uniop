+function($){
	'use strict';

	var NUMVERIFY = /^[1-9][0-9]*$/;
	var NumInput = function(element){
		$(element).on('blur',this.verify);
	}

	NumInput.prototype.verify = function(target){
		var $this = $(this);
		var value = $this.val();
		if(!value.match(NUMVERIFY)){
			$this.val(1);
		}else{
			value = parseInt(value);
			if(value>=99){
				$this.val(99);
			}
		}
	}

	function Plugin(){
		return this.each(function(){
			var $this = $(this);
			var data = $this.data('bs.numinput');
			if(!(data)) $(this).data('bs.numinput', (data = new NumInput(this)));

		});
	}

	$.fn.numinput = Plugin;

	$('input[type="number"]').numinput();
}(jQuery);
