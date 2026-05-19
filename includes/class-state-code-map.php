<?php
/**
 * US State / Territory Code Map
 *
 * holds the static map of 2-letter codes to full names used by gravity forms
 * address field state dropdowns. labels match exactly what GF emits so any
 * downstream consumer (e.g. another form populated via URL params) recognizes
 * them as a valid option.
 *
 * @package FSN_GF
 */

// dont let anyone access this directly
defined( 'ABSPATH' ) || exit;

/**
 * FSN_GF_State_Map class
 *
 * pure data class - no state, no instantiation needed
 * gives ya a lookup from "NJ" -> "New Jersey"
 */
class FSN_GF_State_Map {

    /**
     * mappin a US state / territory 2-letter code to its full label
     *
     * labels match the option values produced by Gravity Forms' Address field
     * state dropdown so downstream consumers (e.g. another Gravity Form
     * populated via URL params) recognize them.
     *
     * @param string $da_code uppercase 2-letter code
     * @return string|null full state name, or null when unknown
     */
    public static function code_to_name( $da_code ) {
        $da_map = self::get_map();

        return isset( $da_map[ $da_code ] ) ? $da_map[ $da_code ] : null;
    }

    /**
     * grabbin the full code -> name map
     *
     * filterable via `fsn_gf_state_map` for sites that need to override or
     * extend the list (e.g. adding non-US regions for a customized Address
     * field).
     *
     * @return array<string,string> code -> name lookup
     */
    public static function get_map() {
        static $da_map = null;

        if ( null === $da_map ) {
            $da_map = array(
                'AL' => 'Alabama',
                'AK' => 'Alaska',
                'AS' => 'American Samoa',
                'AZ' => 'Arizona',
                'AR' => 'Arkansas',
                'CA' => 'California',
                'CO' => 'Colorado',
                'CT' => 'Connecticut',
                'DE' => 'Delaware',
                'DC' => 'District of Columbia',
                'FL' => 'Florida',
                'GA' => 'Georgia',
                'GU' => 'Guam',
                'HI' => 'Hawaii',
                'ID' => 'Idaho',
                'IL' => 'Illinois',
                'IN' => 'Indiana',
                'IA' => 'Iowa',
                'KS' => 'Kansas',
                'KY' => 'Kentucky',
                'LA' => 'Louisiana',
                'ME' => 'Maine',
                'MD' => 'Maryland',
                'MA' => 'Massachusetts',
                'MI' => 'Michigan',
                'MN' => 'Minnesota',
                'MS' => 'Mississippi',
                'MO' => 'Missouri',
                'MT' => 'Montana',
                'NE' => 'Nebraska',
                'NV' => 'Nevada',
                'NH' => 'New Hampshire',
                'NJ' => 'New Jersey',
                'NM' => 'New Mexico',
                'NY' => 'New York',
                'NC' => 'North Carolina',
                'ND' => 'North Dakota',
                'MP' => 'Northern Mariana Islands',
                'OH' => 'Ohio',
                'OK' => 'Oklahoma',
                'OR' => 'Oregon',
                'PA' => 'Pennsylvania',
                'PR' => 'Puerto Rico',
                'RI' => 'Rhode Island',
                'SC' => 'South Carolina',
                'SD' => 'South Dakota',
                'TN' => 'Tennessee',
                'TX' => 'Texas',
                'UT' => 'Utah',
                'VI' => 'U.S. Virgin Islands',
                'VT' => 'Vermont',
                'VA' => 'Virginia',
                'WA' => 'Washington',
                'WV' => 'West Virginia',
                'WI' => 'Wisconsin',
                'WY' => 'Wyoming',
                'AA' => 'Armed Forces Americas',
                'AE' => 'Armed Forces Europe',
                'AP' => 'Armed Forces Pacific',
            );

            /**
             * filterin the code -> name map
             *
             * use this to add/replace entries for non-US regions or to localize
             * the labels if your form uses translated state options.
             *
             * @param array<string,string> $da_map default US states / territories map
             */
            $da_map = apply_filters( 'fsn_gf_state_map', $da_map );
        }

        return $da_map;
    }
}
