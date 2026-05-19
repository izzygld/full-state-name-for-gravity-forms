<?php
/**
 * Merge Tag State Name Expander
 *
 * intercepts the gform_merge_tag_filter hook and expands a 2-letter state /
 * territory code on the Address state sub-field (X.4) back to its full label.
 *
 * some gravity forms code paths (and certain add-ons / address types) hand
 * back the 2-letter code when replacing merge tags. that breaks downstream
 * forms / notifications that expect the full label.
 *
 * @package FSN_GF
 */

// dont let anyone access this directly
defined( 'ABSPATH' ) || exit;

/**
 * FSN_GF_Expander class
 *
 * does the actual expansion work via gform_merge_tag_filter
 */
class FSN_GF_Expander {

    /**
     * hookin up our merge tag filter
     * this gets called from the main addon init()
     *
     * @return void
     */
    public function hookup() {
        add_filter( 'gform_merge_tag_filter', array( $this, 'expand_state_code' ), 10, 6 );
    }

    /**
     * expandin a 2-letter state code on the Address state sub-input back to
     * the full state name
     *
     * @param string   $value     the merge tag replacement value
     * @param string   $input_id  the input id being replaced (e.g. "3.4")
     * @param string   $modifier  any merge tag modifier (e.g. ":label")
     * @param GF_Field $field     the field the merge tag refers to
     * @param mixed    $raw_value the raw stored value
     * @param string   $format    output format (e.g. "html")
     * @return string the (possibly expanded) merge tag value
     */
    public function expand_state_code( $value, $input_id, $modifier, $field, $raw_value, $format = '' ) {
        // only act on Address field "State / Province" sub-input (X.4)
        if ( ! $field || ! is_object( $field ) || ! method_exists( $field, 'get_input_type' ) ) {
            return $value;
        }

        if ( $field->get_input_type() !== 'address' ) {
            return $value;
        }

        // merge tag must target the state sub-field
        if ( substr( (string) $input_id, -2 ) !== '.4' ) {
            return $value;
        }

        if ( ! is_string( $value ) || $value === '' ) {
            return $value;
        }

        $da_candidate = trim( $value );

        // only attempt expansion when the value looks like a 2-letter code
        if ( ! preg_match( '/^[A-Za-z]{2}$/', $da_candidate ) ) {
            return $value;
        }

        $da_full = FSN_GF_State_Map::code_to_name( strtoupper( $da_candidate ) );

        if ( null !== $da_full ) {
            /**
             * filterin the expanded state name right before it replaces the merge tag
             *
             * @param string   $da_full      the expanded state name
             * @param string   $da_candidate the original 2-letter code
             * @param GF_Field $field        the address field
             * @param string   $input_id     the input id (e.g. "3.4")
             */
            return apply_filters( 'fsn_gf_expanded_state_name', $da_full, $da_candidate, $field, $input_id );
        }

        return $value;
    }
}
