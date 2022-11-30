<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core;

class Main{
    public function plugins_loaded(){
        // Adding an action to init
        add_action('init', array($this, 'init'));

        // Adding an action to wp_loaded
        add_action('wp_loaded', array($this, 'wp_loaded'));
    }

    public function init(){

        // "init()" method creates custom hooks 
        // these custom hooks can linked with an action
        // actions later triggered based on event

        // if user is logged in
        if (is_user_logged_in()){
            // if user is admin
            if(is_user_admin()){

            } else{

            }
        }else {
            // No user authenticated
        }

        // everything else
        
    }

    public function wp_loaded(){}
}