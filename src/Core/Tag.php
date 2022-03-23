<?php
/**
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

class Tag
{

    public static function inputTag($args)
    {
        $field = '<tr>';
            $field .= '<th scope="row"><label for="'.$args['id'].'" >'.$args['title'].'</label></th>';
            $field .= '<td><input type="'.$args['args']['type'].'" class="regular-text" placeholder="'.$args['args']['placeholder'].'" name="'.$args['id'].'"/>';
        $field .= '</tr>';

        return $field;
    }

    public static function inputCheckboxTag($args)
    {
        $field = '<tr>';

            $field .= '<th scope="row"><label for="'.$args['id'].'" >'.$args['title'].'</label></th>';
            $field .= '<td><input type="'.$args['args']['type'].'" value="1" id="'.$args['key'].'" name="'.$args['id'].'"/></td>';

        $field .= '</tr>';

        return $field;
    }

    public static function selectTag($args)
    {
        $field = '<tr>';
            $field .= '<th scope="row"><label for="'.$args['id'].'" >'.$args['title'].'</label></th>';

            $field .= '<td><select id="'.$args['key'].'" name="'.$args['id'].'">';
                $field .= '<option selected value="">Choose...</option>';
            
                $options = $args['options'];

        foreach ($options as $key => $value) {
            $field .= '<option value="'.$key.'">';
            $field .= $value;
            $field .= "</option>";
        }
            $field .= '</select></td>';
            
        $field .= '</tr>';

        return $field;
    }

    public static function textAreaTag($args)
    {
        $field = '<tr>';

            $field .= '<th scope="row"><label for="'.$args['id'].'" >'.$args['title'].'</label></th>';
        
            $field .= '<td><textarea type="'.$args['args']['type'].'" col="5" value="" id="'.$args['key'].'" name="'.$args['id'].'"></textarea></td>';

        $field .= '</tr>';

        return $field;
    }
}
