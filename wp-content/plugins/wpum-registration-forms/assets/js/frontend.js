(function($) {

	var MultiStepForm = function(el){
		this.form  = $(el);
		this.steps = $('form', this.form).find('.step');

		if( this.steps.length ){
			this.steps.not(':first').hide();
		} else {
			this.form.find('[name="submit_registration"]').show();
		}

		var instance = this;

		$('.step-breadcrumb', this.form).on( 'click', function(){
			var index = $(this).index() + 1;
			instance.setUpStep(index);
		});

		$('.step-button-wrappers button').on( 'click', function(){

			var step 	 = parseInt( instance.steps.filter(':visible').data('step') );

			if( step < 1 || instance.steps.length < step || !$(this).hasClass('available') ){
				return;
			}

			var nextStep = $(this).is('.step-previous') ? -1 : 1;
			instance.setUpStep( step + nextStep );
		});

		$(':input', this.form).on( 'invalid', function(e){
			var stepEl = $(this).parents('.step');
			var step   = stepEl.data('step');

			var invalidFields = instance.form.find(':invalid').filter(':input');

			// show only the first invalid element step
			if( $(e.target).is( invalidFields[0] ) ){
				instance.setUpStep(step);
			}
		});

		this.toggleActionsAvailability(1);
		this.toggleActiveStep(1);
	};

	MultiStepForm.prototype.setUpStep = function(step){
		this.showStep(step);
		this.showProgress(step);
		this.toggleSubmitButton(step);
		this.toggleActionsAvailability(step);
		this.toggleActiveStep(step);
	}

	MultiStepForm.prototype.showStep = function(step){
		var activeSelector = '[data-step='+step+']';

		if( !this.steps.filter(':visible').is(activeSelector) ){
			this.steps.fadeOut(200);
		}
		this.steps.filter('[data-step='+step+']').fadeIn(250);
	}

	MultiStepForm.prototype.showProgress = function(step){
		var percentage = this.steps.length > 1 ? ( 100 / this.steps.length ) * parseInt(step) : 100;
		this.form.find('.step-progress-bar').width(percentage + '%');
	}

	MultiStepForm.prototype.toggleSubmitButton = function(step){
		this.form.find('[name="submit_registration"]').toggle( this.steps.last().data('step') == step );
	}

	MultiStepForm.prototype.toggleActionsAvailability = function(step){
		this.form.find('.step-previous').toggleClass( 'available', this.steps.first().data('step') != step );
		this.form.find('.step-next').toggleClass( 'available', this.steps.last().data('step') != step );
	}

	MultiStepForm.prototype.toggleActiveStep = function(step){
		this.form.find('.step-breadcrumbs button').removeClass('active').eq(step - 1).addClass('active');
	}

	$(function() {

		$('.wpum-registration-form').each(function(){
			new MultiStepForm($(this));
		});

	});

}(window.jQuery));
