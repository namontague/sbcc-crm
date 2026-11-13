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

/*********************************************************************************
 * SBCC customised opt-out page.
 *
 * GET  index.php?entryPoint=removeme&identifier=<tracker key>
 *      Shows a confirmation page. Nothing is changed, so link scanners that
 *      pre-open email links (e.g. Outlook Safe Links) can't opt people out.
 * POST identifier=<tracker key>&confirm=1
 *      Records the opt-out:
 *        - NewsLetter campaign with a suppression (exempt) list: the person is
 *          added to that list, so only that newsletter stops.
 *        - Anything else (or a newsletter with no suppression list): every
 *          email address on the person is marked opted out, so all campaign
 *          emails stop.
 ********************************************************************************/

require_once('modules/Campaigns/utils.php');

if (!empty($_REQUEST['remove'])) clean_string($_REQUEST['remove'], "STANDARD");
if (!empty($_REQUEST['from'])) clean_string($_REQUEST['from'], "STANDARD");

if (!function_exists('sbcc_removeme_h')) {
    function sbcc_removeme_h($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

global $beanFiles, $beanList, $current_user, $current_language;
$mod_strings = return_module_language($current_language, 'Campaigns');

//user is most likely not defined, retrieve admin user so that team queries are bypassed
if (empty($current_user) || empty($current_user->id)) {
    $current_user = new User();
    $current_user->retrieve('1');
}

$db = DBManagerFactory::getInstance();

$identifier   = isset($_REQUEST['identifier']) ? trim($_REQUEST['identifier']) : '';
$state        = 'invalid';   // invalid | users | confirm | done
$scope        = 'all';       // all | newsletter
$campaignName = '';
$target       = null;

// Tracker keys are GUIDs; anything else can't be one of our links.
if ($identifier !== '' && preg_match('/^[0-9A-Za-z\-]{1,36}$/', $identifier)) {
    $qIdentifier = $db->quote($identifier);
    $result = $db->query(
        "SELECT cl.target_id, cl.target_type, cl.campaign_id, c.campaign_type, c.name AS campaign_name"
        . " FROM campaign_log cl"
        . " LEFT JOIN campaigns c ON c.id = cl.campaign_id AND c.deleted = 0"
        . " WHERE cl.target_tracker_key = '$qIdentifier' AND cl.activity_type = 'targeted'"
    );
    $target = $db->fetchByAssoc($result);

    if (!empty($target) && !empty($target['target_id'])) {
        if (strtolower($target['target_type']) == 'users') {
            $state = 'users';
        } else {
            $state = 'confirm';

            // Newsletters unsubscribe via their suppression list, if they have one.
            if ($target['campaign_type'] == 'NewsLetter') {
                $qCampaign = $db->quote($target['campaign_id']);
                $exempt = $db->fetchByAssoc($db->query(
                    "SELECT pl.id FROM prospect_lists pl"
                    . " INNER JOIN prospect_list_campaigns plc ON plc.prospect_list_id = pl.id AND plc.deleted = 0"
                    . " WHERE plc.campaign_id = '$qCampaign' AND pl.list_type = 'exempt' AND pl.deleted = 0"
                ));
                if (!empty($exempt)) {
                    $scope = 'newsletter';
                    $campaignName = from_html($target['campaign_name']);
                }
            }
        }
    }
}

if ($state == 'confirm'
    && isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'
    && !empty($_POST['confirm'])) {

    // Adds the 'removed' entry shown on the campaign status page.
    log_campaign_activity($identifier, 'removed');

    if ($scope == 'newsletter') {
        $focus = null;
        if (isset($beanList[$target['target_type']])) {
            $beanType = $beanList[$target['target_type']];
            require_once($beanFiles[$beanType]);
            $focus = new $beanType();
            $focus->retrieve($target['target_id']);
        }
        if (!empty($focus) && !empty($focus->id)) {
            unsubscribe($target['campaign_id'], $focus);
        } else {
            $scope = 'all';
        }
    }

    if ($scope == 'all') {
        $qTarget = $db->quote($target['target_id']);
        $db->query(
            "UPDATE email_addresses SET email_addresses.opt_out = 1"
            . " WHERE EXISTS(SELECT 1 FROM email_addr_bean_rel ear"
            . " WHERE ear.bean_id = '$qTarget' AND ear.deleted = 0 AND email_addresses.id = ear.email_address_id)"
        );
    }

    $state = 'done';
}

$contactEmail = 'sbcc@cricketcoaching.com.au';
$contactPhone = '(03) 9464 6664';
$contactLine  = 'email <a href="mailto:' . $contactEmail . '">' . $contactEmail . '</a> or call ' . $contactPhone;
$listName     = $scope == 'newsletter' && $campaignName !== ''
    ? 'the &ldquo;' . sbcc_removeme_h($campaignName) . '&rdquo; mailing list'
    : 'this mailing list';

?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex, nofollow" />
  <title>Unsubscribe | Shaun Brown&rsquo;s Cricket Coaching</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind:wght@400;700&amp;family=Work+Sans:wght@400;600&amp;display=swap" />
  <style>
    /* Matches cricketcoaching.com.au: Hind headings, Work Sans body,
       SBCC red header, navy-bordered rounded cards, red pill buttons,
       navy footer with red copyright bar. */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --red: #AB2422;
      --button-red: #B80000;
      --button-red-dark: #8f0000;
      --eyebrow-red: #fc4444;
      --navy: #1F3A58;
      --navy-light: #2d5076;
      --black: #0d0d0d;
      --page-bg: #f4f4f4;
      --white: #ffffff;
      --muted: #5b6572;
      --heading-font: 'Hind', Arial, Helvetica, sans-serif;
      --body-font: 'Work Sans', Arial, Helvetica, sans-serif;
    }

    .topbar, header, footer { flex-shrink: 0; }

    body {
      font-family: var(--body-font);
      font-size: 16px;
      line-height: 1.6;
      color: var(--black);
      background: var(--page-bg);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    a { color: var(--red); }

    /* Top strip */
    .topbar {
      background: var(--black);
      color: var(--white);
      font-size: 0.85rem;
      padding: 8px 24px;
    }
    .topbar-inner {
      max-width: 1140px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      gap: 16px;
    }
    .topbar a { color: var(--white); text-decoration: none; }

    /* Header */
    header {
      background: var(--red);
      position: relative;
      overflow: hidden;
      padding: 22px 24px;
      display: flex;
      justify-content: center;
    }
    header::before {
      content: "";
      position: absolute;
      top: 0;
      left: -60px;
      width: 220px;
      height: 100%;
      background: #c42b28;
      transform: skewX(-30deg);
    }
    header a { position: relative; display: block; }
    header img { display: block; width: 180px; max-width: 100%; height: auto; }

    /* Hero band + card */
    main {
      flex: 1 0 auto;
      background: linear-gradient(to bottom, var(--navy) 0, var(--navy-light) 200px, var(--page-bg) 200px);
      padding: 64px 20px 72px;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }

    .card {
      background: var(--white);
      border: 2px solid var(--navy);
      border-radius: 16px;
      box-shadow: 0 12px 40px rgba(13, 13, 13, 0.12);
      max-width: 680px;
      width: 100%;
      padding: 48px 56px;
    }

    .eyebrow {
      font-weight: 600;
      font-size: 1rem;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      color: var(--eyebrow-red);
      margin-bottom: 10px;
    }

    h1 {
      font-family: var(--heading-font);
      font-weight: 700;
      font-size: 2.25rem;
      line-height: 1.15;
      color: var(--red);
      margin-bottom: 18px;
    }

    .card p { margin-bottom: 14px; }
    .card p.note { font-size: 0.9rem; color: var(--muted); }
    .card p:last-child { margin-bottom: 0; }

    .actions { margin: 28px 0 22px; }

    .button {
      display: inline-block;
      font-family: var(--body-font);
      font-weight: 600;
      font-size: 1rem;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      text-decoration: none;
      color: var(--white);
      background: var(--button-red);
      border: 0;
      border-radius: 50px;
      padding: 15px 34px;
      cursor: pointer;
    }
    .button:hover, .button:focus { background: var(--button-red-dark); color: var(--white); }

    /* Footer */
    footer { background: var(--navy); color: var(--white); }
    .footer-main {
      max-width: 1140px;
      margin: 0 auto;
      padding: 56px 24px 40px;
      display: flex;
      flex-wrap: wrap;
      gap: 40px;
      justify-content: space-between;
    }
    .footer-brand h2 {
      font-family: var(--heading-font);
      font-weight: 700;
      font-size: 1.7rem;
      line-height: 1.2;
      margin-bottom: 8px;
    }
    footer h3 {
      font-weight: 600;
      font-size: 1rem;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 12px;
    }
    footer ul { list-style: none; font-size: 0.95rem; line-height: 1.9; }
    footer a { color: var(--white); text-decoration: none; }
    footer a:hover, footer a:focus { color: var(--eyebrow-red); }
    .footer-bottom {
      background: var(--button-red);
      font-size: 0.85rem;
      padding: 14px 24px;
    }
    .footer-bottom-inner {
      max-width: 1140px;
      margin: 0 auto;
      display: flex;
      flex-wrap: wrap;
      gap: 8px 24px;
      justify-content: space-between;
    }

    @media (max-width: 600px) {
      .topbar-address { display: none; }
      .topbar-inner { justify-content: center; }
      header { padding: 16px; }
      header img { width: 140px; }
      main { padding: 32px 16px 48px; background: linear-gradient(to bottom, var(--navy) 0, var(--navy-light) 140px, var(--page-bg) 140px); }
      .card { padding: 32px 24px; }
      h1 { font-size: 1.8rem; }
      .button { display: block; width: 100%; text-align: center; }
      .footer-main { padding: 40px 20px 32px; gap: 28px; }
    }
  </style>
</head>
<body>

  <div class="topbar">
    <div class="topbar-inner">
      <span class="topbar-address">59 Mercedes Drive, Thomastown</span>
      <a href="tel:+61394646664"><?php echo $contactPhone; ?></a>
    </div>
  </div>

  <header>
    <a href="https://cricketcoaching.com.au/"><img src="https://cricketcoaching.com.au/wp-content/uploads/2024/11/SB-200x100-3.png" width="200" height="100" alt="Shaun Brown's Cricket Coaching" /></a>
  </header>

  <main>
    <div class="card">
      <div class="eyebrow">Email preferences</div>
<?php if ($state == 'confirm') { ?>
      <h1>Unsubscribe from our emails?</h1>
<?php if ($scope == 'newsletter') { ?>
      <p>Click the button below to unsubscribe from <?php echo $listName; ?>.</p>
<?php } else { ?>
      <p>Click the button below and we&rsquo;ll stop sending you marketing emails from Shaun Brown&rsquo;s Cricket Coaching.</p>
<?php } ?>
      <form class="actions" method="post" action="index.php?entryPoint=removeme">
        <input type="hidden" name="identifier" value="<?php echo sbcc_removeme_h($identifier); ?>" />
        <input type="hidden" name="confirm" value="1" />
        <button class="button" type="submit">Unsubscribe</button>
      </form>
      <p class="note">Clicked the link by mistake? Just close this page and nothing will change.</p>
<?php } elseif ($state == 'done') { ?>
      <h1>You&rsquo;ve been unsubscribed</h1>
<?php if ($scope == 'newsletter') { ?>
      <p>You won&rsquo;t receive any more emails from <?php echo $listName; ?>.</p>
<?php } else { ?>
      <p>You won&rsquo;t receive any more marketing emails from Shaun Brown&rsquo;s Cricket Coaching.</p>
<?php } ?>
      <div class="actions"><a class="button" href="https://cricketcoaching.com.au/">Visit our website</a></div>
      <p class="note">Changed your mind, or still getting emails? Please <?php echo $contactLine; ?> and we&rsquo;ll sort it out.</p>
<?php } elseif ($state == 'users') { ?>
      <h1>Unable to unsubscribe</h1>
      <p><?php echo sbcc_removeme_h($mod_strings['LBL_USERS_CANNOT_OPTOUT']); ?></p>
<?php } else { ?>
      <h1>This link isn&rsquo;t working</h1>
      <p>We couldn&rsquo;t match this unsubscribe link to one of our emails. It may have been cut short or copied incorrectly.</p>
      <p>To stop receiving our emails, please <?php echo $contactLine; ?> and we&rsquo;ll remove you.</p>
<?php } ?>
    </div>
  </main>

  <footer>
    <div class="footer-main">
      <div class="footer-brand">
        <h2>Shaun Browns Cricket Coaching</h2>
        59 Mercedes Drive, Thomastown VIC 3074
      </div>
      <div>
        <h3>Quick links</h3>
        <ul>
          <li><a href="https://cricketcoaching.com.au/coaching/">Coaching</a></li>
          <li><a href="https://cricketcoaching.com.au/cricket-camps/">Cricket Camps</a></li>
          <li><a href="https://cricketcoaching.com.au/overseas-tours/">Overseas Tours</a></li>
          <li><a href="https://cricketcoaching.com.au/contact-us/">Contact</a></li>
        </ul>
      </div>
      <div>
        <h3>Need help?</h3>
        <ul>
          <li><a href="tel:+61412382558">0412 382 558</a></li>
          <li><a href="tel:+61394646664"><?php echo $contactPhone; ?></a></li>
          <li><a href="mailto:<?php echo $contactEmail; ?>"><?php echo $contactEmail; ?></a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="footer-bottom-inner">
        <span>&copy; <?php echo date('Y'); ?> All Rights Reserved. Shaun Brown Cricket Coaching</span>
        <a href="https://cricketcoaching.com.au/privacy-policy/">Privacy Policy</a>
      </div>
    </div>
  </footer>

</body>
</html>
<?php
sugar_cleanup();
