<?php
/*
Plugin Name: GWP mail BCC and CC
Plugin URI:
Description: A plugin to add bcc and cc email address to any mail sent by WordPress
Version: 1.0
Author: Ohad Raz
Author URI: http://generatewp.com
*/
/**
* GWP_bcc_cc_mail
*/
class GWP_bcc_cc_mail{
     
    /**
     * __construct
     * class constructor will set the needed filter and action hooks
     *
     */
    function __construct(){
        // Hook into the 'user_contactmethods' filter to add cc and bcc fields
        add_filter( 'user_contactmethods', array($this,'user_contactmethods' ));
        //hook into wp_mail to add cc and bcc as needed
        add_filter('wp_mail',array($this,'wp_mail'));
 
    }
 
    /**
     * wp_mail
     * In this method we actuly add the cc and bcc address to the wp_mail call
     * @param  array $args an associative array of mail arguments(to,subject,message,headers,attachments)
     * @return array      
     */
    function wp_mail($args){
        //get all emails that are already beeing sent to (users)
        $emails = $args['to'];
        //make sure we proccess them one by one
        $emails = explode(',', $emails);
        //loop over the emails and check if we need to add cc and bcc addresses
        foreach ($emails as $em) {
            //try to get the user id by the "to mail"
            $user = get_user_by( 'email', trim($em));
            if ($user){
                //get user ccs if exists
                $cc = get_user_meta( $user->ID, 'cc', true );
                if ( $cc ){
                    //explode by comma "," to allow multiple cc addresses
                    $ccs = explode(",", $cc);
                    foreach ((array)$ccs as $cc_address) {
                        $args['headers'][] = 'Cc: '.trim($cc_address);
                    }
                }
                //get user bccs if exists
                $bcc = get_user_meta( $user->ID, 'bcc', true );
                if ( $bcc ){
                    //explode by comma "," to allow multiple bcc addresses
                    $bccs = explode(",", $bcc);
                    foreach ((array)$bccs as $bcc_address) {
                        $args['headers'][] = 'Bcc: '.trim($bcc_address);
                    }
                }
            }
        }
        return $args;
    }
 
    /**
     * user_contactmethods
     * @param  array $user_contact_method an associative array keyed by form field ids with human-readable text as values.
     * @return array
     */
    function user_contactmethods($user_contact_method){
 
        $user_contact_method['cc'] = __( 'CC email address (multiple: Comma separated emails)', 'GWP' );
        $user_contact_method['bcc'] = __( 'BCC email address (multiple: Comma separated emails)', 'GWP' );
        return $user_contact_method;
    }
}//end class
//instantiate the class
add_action( 'plugins_loaded', 'GWP_bcc_cc_mail_init' );
function GWP_bcc_cc_mail_init() {
    $GLOBALS['GWP_bcc_cc_mail'] = new GWP_bcc_cc_mail();
}