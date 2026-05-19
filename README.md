# Full State Name for Gravity Forms

[![License: GPL v2](https://img.shields.io/badge/License-GPLv2-blue.svg?style=for-the-badge)](https://www.gnu.org/licenses/gpl-2.0)

A Gravity Forms add-on that fixes an inconsistency where the Address field's "State / Province" sub-input (input `X.4`) sometimes resolves to a 2-letter abbreviation (`NJ`) instead of the full state name (`New Jersey`) when used in merge tags.

---

## The Problem

Depending on the form, the active add-on, and the code path, Gravity Forms' Address "State / Province" sub-input (`X.4`) sometimes resolves to a 2-letter code (`NJ`) and sometimes to the full label (`New Jersey`) when used in:

- `{all_fields}` merge tag
- `{Address (State / Province):X.4}` merge tag
- Notifications & confirmations
- URL query strings used to pre-populate another Gravity Form (the second form's State dropdown won't match `NJ` against its option value `New Jersey`, so the field stays blank)

## The Solution

This plugin hooks into `gform_merge_tag_filter` and, **only when the merge tag targets an Address State sub-input (`X.4`) and the value is exactly two letters**, expands it back to the full state name Gravity Forms uses in its Address dropdown.

---

## Key Features

| Feature | What it does |
|---|---|
| **Targeted** | Only Address `X.4` sub-inputs are touched — nothing else |
| **Safe** | Only acts on exact 2-letter strings; full names + other text pass through |
| **Zero config** | Activate the plugin and the fix is live |
| **Filterable map** | `fsn_gf_state_map` filter to add non-US regions or translated labels |
| **Filterable output** | `fsn_gf_expanded_state_name` filter for per-replacement customization |

## How It Works

```
{Address:3.4} merge tag value = "NJ"
                ↓
gform_merge_tag_filter fires
                ↓
FSN_GF_Expander detects:
   • field is Address
   • input_id ends in .4
   • value matches /^[A-Za-z]{2}$/
                ↓
FSN_GF_State_Map::code_to_name("NJ") → "New Jersey"
                ↓
merge tag now renders as "New Jersey"
```

## Coverage

All US states (50), DC, US territories (PR, GU, VI, AS, MP), and military mailing regions (AA, AE, AP) — **59 entries** that match the default Gravity Forms Address state options exactly.

---

## Requirements

- WordPress 5.8+
- PHP 7.4+
- Gravity Forms 2.5+

## Installation

1. Download or clone this repository
2. Upload the `full-state-name-for-gravity-forms` folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** menu in WordPress
4. Done — no settings page, no configuration

## Project Structure

```
full-state-name-for-gravity-forms/
├── class-fsn-gf-addon.php             # Main GFAddOn subclass
├── full-state-name-for-gravity-forms.php  # Plugin bootstrap
├── includes/
│   ├── class-state-code-map.php       # 2-letter → full name map (filterable)
│   └── class-state-name-expander.php  # gform_merge_tag_filter handler
├── languages/
└── readme.txt
```

## Extending

### Add or replace state entries (e.g. for non-US regions)

```php
add_filter( 'fsn_gf_state_map', function ( $map ) {
    $map['ON'] = 'Ontario';
    $map['QC'] = 'Quebec';
    return $map;
} );
```

### Customize the expanded value before it replaces the merge tag

```php
add_filter( 'fsn_gf_expanded_state_name', function ( $full, $code, $field, $input_id ) {
    if ( $code === 'NY' ) {
        return 'New York State';
    }
    return $full;
}, 10, 4 );
```

## License

GPL-2.0-or-later — same as Gravity Forms and WordPress itself.
