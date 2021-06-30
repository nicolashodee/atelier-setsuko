(function ( $ ) {

    $.wpumcfConditionalFields = function( element, options ){

        var form = $(element),
            self = this;

        this.init = function(){

            this.validateFields();

            form.find(':input').on( 'input change', function(){
                self.validateFields( $(this).parents('fieldset') );
            });
        }

        this.validateField = function(element){
            var rules = element.data('condition');
            element.toggle( this.validateRules(rules) );
        }

        this.validateFields = function(){
            form.find('fieldset[data-condition]').each(function(){
                var rules = $(this).data('condition');
                $(this).toggle( self.validateRules(rules) );
            });
        }

        this.validateRules = function(rules){
            return rules.some(function(andRules){
                return andRules.every(self.validateRule);
            })
        }

        this.validateRule = function(rule){
            return self.hasOwnProperty(self.ruleMethodName(rule.condition)) ? self[self.ruleMethodName(rule.condition)](rule) : false;
        }

        this.ruleMethodName = function(rule){
            return rule.replace(/([-_][a-z])/ig, function($1){
                return $1.toUpperCase()
                .replace('-', '')
                .replace('_', '');
            });
        }

        this.getValue = function(rule){
            var el = $('[name^="'+rule.field+'"]');
            if( el.length ){
                if( el.is('[type="radio"]') ){
                    return el.filter(':checked').val();
                }else if( el.is('[type="checkbox"]') ){
                    return el.filter(':checked').map(function(){
                        return $(this).val();
                    }).toArray();
                }else{
                    return el.first().val();
                }
            }
        }

        this.hasValue = function(rule){
            var value = this.getValue(rule);
            return $.isArray(value) ? value.length : value && $.trim(value) !== '';
        }

        this.hasNoValue = function(rule){
            var value = this.getValue(rule);
            return $.isArray(value) ? !value.length : !value || value === '';
        }

        this.valueContains = function(rule){
            var value = this.getValue(rule);
            return $.isArray(value) ? value.includes(rule.value) : value && value.toLowerCase().indexOf(rule.value.toLowerCase())  > -1;
        }

        this.valueEquals = function(rule){
            var value = this.getValue(rule);
            return $.isArray(value) ? value.includes(rule.value) : value && value.toLowerCase() === rule.value.toLowerCase();
        }

        this.valueNotEquals = function(rule){
            var value = this.getValue(rule);
            return $.isArray(value) ? !value.includes(rule.value) : value && value.toLowerCase() !== rule.value.toLowerCase();
        }

        this.valueGreater = function(rule){
            var value = this.getValue(rule);
            return parseFloat(value) > parseFloat(rule.value);
        }

        this.valueLess = function(rule){
            var value = this.getValue(rule);
            return parseFloat(value) < parseFloat(rule.value);
        }

        this.init();
    }

    $.fn.wpumcfConditionalFields = function( options ) {
        new $.wpumcfConditionalFields(this, options);
    };

    $(document).ready(function(){
        $('.wpum-registration-form, .wpum-account-form, .wpum-custom-account-form').wpumcfConditionalFields({});
    });

}( jQuery ));