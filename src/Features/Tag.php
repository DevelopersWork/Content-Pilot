<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

class Tag {
    public static function inputTag ( $args ) {
        $field = '<div class="'. $args['args']['col'] .' my-2">';
            $field .= '<label for="'.$args['key'].'" class="form-label">'.$args['title'].'</label>';
            $field .= '<input type="'.$args['args']['type'].'" class="form-control" id="'.$args['key'].'" placeholder="'.$args['args']['placeholder'].'" aria-describedby="'.$args['key'].'" name="'.$args['id'].'"/>';
        $field .= '</div>';

        return $field;
    }

    public static function inputCheckboxTag ( $args ) {
        $field = '<div class="'. $args['args']['col'] .' my-2"><div class="form-check form-switch">';

            $field .= '<input class="form-check-input" type="'.$args['args']['type'].'" value="" id="'.$args['key'].'" name="'.$args['id'].'"/>';

            $field .= '<label for="'.$args['key'].'" class="form-check-label">'.$args['title'].'</label>';
            
        $field .= '</div></div>';

        return $field;
    }

    public static function selectTag ( $args ) {
        $field = '<div class="'. $args['args']['col'] .' my-2"><div class="input-group">';
            $field .= '<label for="'.$args['key'].'" class="input-group-text">'.$args['title'].'</label>';

            $field .= '<select class="form-select" id="'.$args['key'].'" name="'.$args['id'].'">';
                $field .= '<option selected>Choose...</option>';
            
                $options = $args['options'];

                foreach($options as $key => $value) {
                    $field .= '<option value="'.$key.'">';
                    $field .= $value;
                    $field .= "</option>";
                }
            $field .= '</select>';
            
        $field .= '</div></div>';

        return $field;
    }
}