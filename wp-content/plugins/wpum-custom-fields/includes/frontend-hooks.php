<?php
/**
 * Script to hide conditional fields on initiating
 *
 */
function wpumcf_conditional_field_hide_script( $data ){
	$conditional_fields = array_filter( $data->fields, function ( $field ) {
		return ! empty( $field['id'] ) && (bool) get_metadata( 'wpum_field', $field['id'], 'enable_condition', true );
	} );

	if ( ! count( $conditional_fields ) ) {
		return;
	}

	$rulesets = array();
	$fields   = array_filter( $data->fields, function ( $field ) {
		return isset( $field['id'] ) && $field['id'];
	} );
	$keys     = array_keys( $fields );
	foreach ( $conditional_fields as $cf_key => $conditional_field ) {
		$field_id = absint( $conditional_field['id'] );
		$rules    = (array) unserialize( get_metadata( 'wpum_field', $field_id, 'conditions', true ) );
		foreach ( $rules as $i => $ruleset ) {
			$mapped = array_map( function ( $rule ) use ( $keys, $fields ) {
				if ( isset( $rule['field'] ) ) {
					$index = array_search( $rule['field'], $fields );
					if ( $index === false ) {
						$field = new WPUM_Field( absint( $rule['field'] ) );
						$key   = $field->get_key();
					} else {
						$key = ! empty( $keys[ $index ] ) ? $keys[ $index ] : $rule['field'];
					}
					$rule['field'] = $key;
				}

				return $rule;
			}, $ruleset );

			$rules[ $i ] = $mapped;
		}
		$rulesets[ $cf_key ] = $rules;
	}

    ?>
    <script type="text/javascript">
        (function(){
            var ruleset = <?php echo json_encode( $rulesets ) ?>;
            Object.keys(ruleset).forEach(function(fieldName){
                var field = document.querySelector('.fieldset-' + fieldName);
                if( field ){
                    field.style.display = 'none';
                    field.dataset.condition = JSON.stringify(ruleset[fieldName]);
                }
            });
        })();
    </script>
    <?php
}
add_action( 'wpum_after_registration_form', 'wpumcf_conditional_field_hide_script', 1);
add_action( 'wpum_after_account_form', 'wpumcf_conditional_field_hide_script', 1);
add_action( 'wpum_after_custom_account_form', 'wpumcf_conditional_field_hide_script', 1);


function wpumcf_conditional_field_script(){
    wp_enqueue_script( 'wpumcf-conditional-script', WPUMCF_PLUGIN_URL . 'assets/js/conditional-fields.js', array( 'jquery' ), true );
}
add_action( 'wpum_enqueue_frontend_scripts', 'wpumcf_conditional_field_script' );