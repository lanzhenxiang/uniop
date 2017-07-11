require.config({
	paths : {
		'jquery' : '../jQuery-2.1.3.min',
		'validator' : '../validator.bootstrap'
	}
});

require(['jquery','validator'],function($,BootstrapValidator){
	$('#department-form').bootstrapValidator({
		fields : {
			name: {
				group: '.col-sm-6',
				validators: {
					notEmpty: {
                        message: '租户名不能为空'
                    },
                    stringLength: {
                    	min: 2,
                    	max: 16,
                    	message: '请保持在2-16位'
                    },
                    regexp: {
                    	regexp: /^\S*$/,
                    	message: '租户名中不能有空格'
                    }
				}	
			},
			dept_code: {
				group: '.col-sm-6',
				validators: {
					notEmpty: {
                        message: '租户code不能为空'
                    },
                    stringLength: {
                    	min: 2,
                    	max: 16,
                    	message: '请保持在2-16位'
                    },
                    regexp: {
                    	regexp: /^\S*$/,
                    	message: '租户名中不能有空格和中文'
                    }
				}	
			},
			email: {
				group: '.col-sm-6',
				validators: {
					notEmpty: {
                        message: '租户code不能为空'
                    },
					emailAddress: {
                        message: '邮箱格式不对'
                    }
				}
			}
		}
	});

	$('#ds').click(function() {
        if($('#department-form').data('bootstrapValidator').isValid()){

        }else{
        	
        }
    });

});
