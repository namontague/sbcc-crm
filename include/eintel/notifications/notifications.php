<?php
/*
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * Not to be used without the permission of the author
 *
 * Utilities for email notifications
 *
 */


/**
 * Handles sending out email notifications to users
 *
 * @param string $notify_user user to notify
 * @param string $admin the admin user that sends out the notification
 */
function send_notification($sendToEmail, $nofityFullName, $template_name, $xtpl) {
    global $sugar_version;
    global $sugar_config;
    global $app_list_strings;
    global $current_user;
    global $locale;
    
    require_once("XTemplate/xtpl.php");
    require_once("include/SugarPHPMailer.php");
    require_once("modules/Administration/Administration.php");

    $admin = new Administration();
    $admin->retrieveSettings();
    // Check to see if notifications have been enabled in the "Email Settings" screen of admin.
    if ($admin->settings['notify_on']) {
        global $current_user;
        // Call function to create the message
        //Replace in this function
        //$notify_mail = create_notification_email($sendToEmail, $nofityFullName, $moduleName, $template_file, $template_name, $record_id);
        $notify_mail = new SugarPHPMailer();
        $notify_mail->AddAddress($sendToEmail, $nofityFullName);

        if(empty($_SESSION['authenticated_user_language'])) {
            $current_language = $sugar_config['default_language'];
        } else {
            $current_language = $_SESSION['authenticated_user_language'];
        }

        $port = '';

        if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
            $port = $_SERVER['SERVER_PORT'];
        }

        $httpHost = $_SERVER['HTTP_HOST'];

        if($colon = strpos($httpHost, ':')) {
            $httpHost    = substr($httpHost, 0, $colon);
        }

        $parsedSiteUrl = parse_url($sugar_config['site_url']);
        $host = ($parsedSiteUrl['host'] != $httpHost) ? $httpHost : $parsedSiteUrl['host'];

        if(!isset($parsedSiteUrl['port'])) {
            $parsedSiteUrl['port'] = 80;
        }

        $port= ($parsedSiteUrl['port'] != 80) ? ":".$parsedSiteUrl['port'] : '';
        $path= !empty($parsedSiteUrl['path']) ? $parsedSiteUrl['path'] : "";
        $cleanUrl= "{$parsedSiteUrl['scheme']}://{$host}{$port}{$path}";

        $xtpl->assign("SUGAR", "Sugar v{$sugar_version}");
        $xtpl->parse($template_name);
        $xtpl->parse($template_name . "_Subject");

        $notify_mail->Body = to_html(trim($xtpl->text($template_name)));
        $notify_mail->Subject = to_html($xtpl->text($template_name . "_Subject"));

        // cn: bug 8568 encode notify email in User's outbound email encoding
        $notify_mail->prepForOutbound();

        $notify_mail->IsHTML(TRUE);
        // Prepare for sending
        $notify_mail->setMailerForSystem();
        $notify_mail->From = $admin->settings['notify_fromaddress'];
        $notify_mail->FromName = $admin->settings['notify_fromname'];
        // Log the result of send attempt
        if(!$notify_mail->Send()) {
            $GLOBALS['log']->warn("Notifications: error sending e-mail (method: {$notify_mail->Mailer}), (error: {$notify_mail->ErrorInfo})");
        } else {
            $GLOBALS['log']->info("Notifications: e-mail successfully sent");
        }
    }
    else {
        $GLOBALS['log']->info("Notifications: not sending e-mail, notify_on is set to OFF");
    }

}

function send_attachment_notification($sendToEmail, $nofityFullName, $template_name, $xtpl, $attachments = null) {
    global $sugar_version;
    global $sugar_config;
    global $app_list_strings;
    global $current_user;
    global $locale;

    require_once("XTemplate/xtpl.php");
    require_once("include/SugarPHPMailer.php");
    require_once("modules/Administration/Administration.php");

    $admin = new Administration();
    $admin->retrieveSettings();

    global $current_user;
    // Call function to create the message

    $notify_mail = new SugarPHPMailer();
    $notify_mail->AddAddress($sendToEmail, $nofityFullName);

    if(empty($_SESSION['authenticated_user_language'])) {
        $current_language = $sugar_config['default_language'];
    } else {
        $current_language = $_SESSION['authenticated_user_language'];
    }

    $port = '';

    if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
        $port = $_SERVER['SERVER_PORT'];
    }

    $httpHost = $_SERVER['HTTP_HOST'];

    if($colon = strpos($httpHost, ':')) {
        $httpHost    = substr($httpHost, 0, $colon);
    }

    $parsedSiteUrl = parse_url($sugar_config['site_url']);
    $host = ($parsedSiteUrl['host'] != $httpHost) ? $httpHost : $parsedSiteUrl['host'];

    if(!isset($parsedSiteUrl['port'])) {
        $parsedSiteUrl['port'] = 80;
    }

    $port= ($parsedSiteUrl['port'] != 80) ? ":".$parsedSiteUrl['port'] : '';
    $path= !empty($parsedSiteUrl['path']) ? $parsedSiteUrl['path'] : "";
    $cleanUrl= "{$parsedSiteUrl['scheme']}://{$host}{$port}{$path}";

    //$xtpl->assign("SUGAR", "Sugar v{$sugar_version}");
    $xtpl->parse($template_name);
    $xtpl->parse($template_name . "_Subject");

    $notify_mail->Body = to_html(trim($xtpl->text($template_name)));
    $notify_mail->Subject = to_html($xtpl->text($template_name . "_Subject"));

    // cn: bug 8568 encode notify email in User's outbound email encoding
    $notify_mail->prepForOutbound();

    $notify_mail->IsHTML(TRUE);
    // Prepare for sending
    $notify_mail->setMailerForSystem();
    $notify_mail->From = $admin->settings['notify_fromaddress'];
    $notify_mail->FromName = $admin->settings['notify_fromname'];
    if(isset($attachments)){
        foreach($attachments as $attachment){
            $notify_mail->AddStringAttachment($attachment->path, $attachment->filename, $attachment->encoding, $attachment->type);
        }
    }
    // Log the result of send attempt
    if(!$notify_mail->Send()) {
        $GLOBALS['log']->warn("Notifications: error sending e-mail (method: {$notify_mail->Mailer}), (error: {$notify_mail->ErrorInfo})");
    } else {
        $GLOBALS['log']->info("Notifications: e-mail successfully sent");
    }

}



?>
