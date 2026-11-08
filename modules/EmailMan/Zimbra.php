<?php
/**
 * Created by IDEA.
 * User: Ben
 * Date: 10/09/2010
 * Time: 10:08:33 AM
 * To change this template use File | Settings | File Templates.
 */
 
class Zimbra {

    function getMailToURL($toAddress = null) {
        global $current_user;
        $compose_path = '';
        if($current_user->getPreference('zimbra_client') == 'html'){
            $compose_path = 'h/search?action=compose';
        } else {
            $compose_path = '?nss=1&view=compose';
        }
        $systemOutboundEmail = new OutboundEmail();
        $userOutboundEmail = new OutboundEmail();
        $systemOutboundEmail = $systemOutboundEmail->getSystemMailerSettings();
        $userOutboundEmail = $userOutboundEmail->getUserMailerSettings($current_user);
        if(empty($userOutboundEmail->mail_webserverclient)){
            if(empty($systemOutboundEmail->mail_webserverclient)){
                $mail_webserverclient = 'mail.eintelcollab.com.au';
            }else{
                $mail_webserverclient = $systemOutboundEmail->mail_webserverclient;
            }
        }else{
            $mail_webserverclient = $userOutboundEmail->mail_webserverclient;
        }

        if( $toAddress ) {
            return "http://{$mail_webserverclient}/zimbra/{$compose_path}&to={$toAddress}";
        } else {
            return "http://{$mail_webserverclient}/zimbra/{$compose_path}&to=";
        }


    }

}
