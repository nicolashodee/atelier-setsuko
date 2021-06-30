<?php

/**
 * Conditions which are used for conditional fields
 *
 * @return Array
 */
function wpumcf_field_conditions(){

    return apply_filters( 'wpumcf_field_conditions', array(
	    'value_equals' => esc_html__( 'Value Is Equal To', '' ),
	    'value_not_equals' => esc_html__( 'Value Is Not Equal To', '' ),
	    'value_contains'=> esc_html__( 'Value Contains', '' ),
        'has_value' => esc_html__( 'Has Value', 'wpum-custom-fields' ),
        'has_no_value' => esc_html__( 'Has No Value', '' ),
        'value_greater' => esc_html__( 'Value Is Greater Than', '' ),
        'value_less' => esc_html__( 'Value Is Less Than', '' )
    ));
}