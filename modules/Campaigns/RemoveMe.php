<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/




require_once('modules/Campaigns/utils.php');

if (!empty($_REQUEST['remove'])) clean_string($_REQUEST['remove'], "STANDARD");
if (!empty($_REQUEST['from'])) clean_string($_REQUEST['from'], "STANDARD");

if(!empty($_REQUEST['identifier'])) {
    global $beanFiles, $beanList, $current_user;

    //user is most likely not defined, retrieve admin user so that team queries are bypassed
    if(empty($current_user) || empty($current_user->id)){
            $current_user = new User();
            $current_user->retrieve('1');
    }
    
    $keys=log_campaign_activity($_REQUEST['identifier'],'removed');
    global $current_language;
    $mod_strings = return_module_language($current_language, 'Campaigns');

    
    if (!empty($keys) && $keys['target_type'] == 'Users'){
        //Users cannot opt out of receiving emails, print out warning message.
        echo $mod_strings['LBL_USERS_CANNOT_OPTOUT'];       
     }elseif(!empty($keys) && isset($keys['campaign_id']) && !empty($keys['campaign_id'])){
        //we need to unsubscribe the user from this particular campaign
        $beantype = $beanList[$keys['target_type']];
        require_once($beanFiles[$beantype]);
        $focus = new $beantype();
        $focus->retrieve($keys['target_id']);
        unsubscribe($keys['campaign_id'], $focus); 
    
    }elseif(!empty($keys)){
		$id = $keys['target_id'];
		$module = trim($keys['target_type']);
		$class = $beanList[$module];
		require_once($beanFiles[$class]);
		$mod = new $class();
		$db = DBManagerFactory::getInstance();

		$id = $db->quote($id);

		//no opt out for users.
		if(preg_match('/^[0-9A-Za-z\-]*$/', $id) && $module != 'Users'){
            //record this activity in the campaing log table..
			$query = "UPDATE email_addresses SET email_addresses.opt_out = 1 WHERE EXISTS(SELECT 1 FROM email_addr_bean_rel ear WHERE ear.bean_id = '$id' AND ear.deleted=0 AND email_addresses.id = ear.email_address_id)";
			$status=$db->query($query);
			if($status){
				echo "*";
			}
		}
    }
		//Print Confirmation Message.
		//echo


    echo'
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Company Template</title>
  <style>
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --red: #AB2422;
      --red-dark: #96131f;
      --red-shadow: rgba(192, 25, 44, 0.35);
      --grey-bg: #e8e8e8;
      --white: #ffffff;
      --text-dark: #1a1a1a;
      --text-mid: #444444;
      --text-light: #888888;
      --footer-bg: #1F3A58;
      --footer-text: #cccccc;
    }

    html, body {
      height: 100%;
    }

    body {
      font-family: Georgia, \'Times New Roman\', serif;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: var(--grey-bg);
    }

    /* ── HEADER ── */
    header {
      background-color: var(--red); !important;
      position: relative;
      overflow: hidden;
      padding: 0 2rem;
      height: 110px;
      min-height: 110px;
      display: flex !important;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 20px rgba(192, 25, 44, 0.35);
      flex-shrink: 0;
    }

    /* Subtle geometric accent lines */
    header::before,
    header::after {
      content: "";
      position: absolute;
      top: 0;
      bottom: 0;
      width: 3px;
    }
    header::before { left: 48px; }
    header::after  { right: 48px; }

    .logo-wrapper {
      display: flex;
      align-items: center;
      gap: 16px;
      z-index: 1;
    }

    /* Placeholder logo mark – replace the SVG with your own <img> if preferred */
    .logo-mark svg {
      display: block;
      filter: drop-shadow(0 2px 8px rgba(0,0,0,0.25));
    }

    .logo-text {
      font-family: \'Playfair Display\', serif;
      color: #ffffff;
      line-height: 1.1;
    }

    .logo-text .name {
      font-size: 1.9rem;
      letter-spacing: 0.04em;
    }

    .logo-text .tagline {
      font-family: Arial, Helvetica, sans-serif;
      font-weight: 300;
      font-size: 0.72rem;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.75);
    }

    /* ── BODY ── */
    main {
      flex: 1;
      background: var(--grey-bg);
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding: 60px 24px;
    }

    .content-card {
      background: var(--white);
      max-width: 760px;
      width: 100%;
      border-radius: 4px;
      box-shadow:
        0 2px 4px rgba(0,0,0,0.06),
        0 12px 40px rgba(0,0,0,0.08);
      padding: 56px 64px;
    }

    .content-card .eyebrow {
      font-size: 0.7rem;
      font-weight: 600;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: var(--red);
      margin-bottom: 12px;
    }

    .content-card h1 {
      font-family: Georgia, \'Times New Roman\', serif;
      font-size: 2rem;
      color: var(--text-dark);
      margin-bottom: 20px;
      line-height: 1.25;
    }

    .content-card .divider {
      width: 48px;
      height: 3px;
      background: var(--red);
      border-radius: 2px;
      margin-bottom: 24px;
    }

    .content-card p {
      font-size: 1rem;
      line-height: 1.8;
      color: var(--text-mid);
      margin-bottom: 16px;
    }

    .content-card p:last-child {
      margin-bottom: 0;
    }

    /* ── FOOTER ── */
    footer {
      background: var(--footer-bg);
      color: var(--footer-text);
      padding: 40px 48px;
    }

    .footer-inner {
      max-width: 960px;
      margin: 0 auto;
      display: flex;
      flex-wrap: wrap;
      gap: 32px;
      justify-content: space-between;
      align-items: flex-start;
    }

    .footer-brand .brand-name {
      font-family: Georgia, \'Times New Roman\', serif;
      font-size: 1.2rem;
      color: #ffffff;
      margin-bottom: 6px;
    }

    .footer-brand p {
      font-size: 0.82rem;
      color: var(--footer-text);
      line-height: 1.6;
      max-width: 260px;
    }

    .footer-info {
      font-size: 0.82rem;
      line-height: 1.9;
      color: var(--footer-text);
      text-align: right;
    }

    .footer-info strong {
      color: #ffffff;
      font-weight: 600;
    }

    .footer-bottom {
      max-width: 960px;
      margin: 28px auto 0;
      padding-top: 20px;
      border-top: 1px solid rgba(255,255,255,0.08);
      font-size: 0.75rem;
      color: rgba(255,255,255,0.35);
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      justify-content: space-between;
    }

    @media (max-width: 600px) {
      .content-card { padding: 36px 28px; }
      .footer-inner { flex-direction: column; }
      .footer-info  { text-align: left; }
      .footer-bottom { flex-direction: column; }
    }
  </style>
</head>
<body>

  <!-- ═══════════════ HEADER ═══════════════ -->
  <header>
    <div class="logo-wrapper">

      <!-- Logo mark – swap this SVG for an <img src="logo.png"> if you have one -->
      <div class="logo-mark">
      <img src="https://test.cricketcoaching.com.au/wp-content/uploads/2024/11/SB-200x100-3.png">

      </div>

    </div>
  </header>

  <!-- ═══════════════ BODY ═══════════════ -->
  <main>
    <div class="content-card">
       <h3>Campaign Opt-Out</h3>
        <p>
            You have opt-out of receiving emails from this campaign.
        </p>
    </div>
  </main>

  <!-- ═══════════════ FOOTER ═══════════════ -->
  <footer>
    <div class="footer-inner">
      <div class="footer-brand">
        <div class="brand-name">Shaun Browns Cricket Coaching</div>
        59 Mercedes Drive<br>
        Thomastown VIC 3074, Australia<br>
        (03) 9464 6664<br>
        sbcc@cricketcoaching.com.au
      </div>
    </div>
  </footer>
</body>
</html>';

	
}
sugar_cleanup();
?>
