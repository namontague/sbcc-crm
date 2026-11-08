<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/*
 * Entry point: index.php?entryPoint=sbccSubpanelEmailAddresses
 * Returns the email addresses of all records related to a parent record, for the
 * "load addresses" button in the quick compose window opened by
 * SugarWidgetSubPanelTopComposeEmailButton_SBCC.
 *
 * Params: parent_type, parent_id, linked_field
 */

function sbccSubpanelEmailAddressesRespond($data) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
}

function sbccSubpanelEmailAddresses() {
    global $beanList;

    $parent_type = isset($_REQUEST['parent_type']) ? $_REQUEST['parent_type'] : '';
    $parent_id = isset($_REQUEST['parent_id']) ? $_REQUEST['parent_id'] : '';
    $linked_field = isset($_REQUEST['linked_field']) ? $_REQUEST['linked_field'] : '';

    if(empty($parent_id) || empty($linked_field) || empty($beanList[$parent_type])) {
        return array('error' => 'Missing or invalid parameters');
    }

    $bean = BeanFactory::getBean($parent_type, $parent_id);
    if(empty($bean) || empty($bean->id) || !$bean->ACLAccess('view')) {
        return array('error' => 'Record not found or access denied');
    }

    if(!$bean->load_relationship($linked_field)) {
        return array('error' => 'Relationship not found');
    }

    $related_module = $bean->$linked_field->getRelatedModuleName();
    $related = BeanFactory::getBean($related_module);
    if(empty($related) || !$related->ACLAccess('list')) {
        return array('error' => 'Access denied');
    }

    $related_ids = $bean->$linked_field->get();
    $db = $related->db;

    // Bug63174: Email address is not shown in the list view for employees
    $email_module = ($related->module_dir == "Employees") ? "Users" : $related->module_dir;
    $is_person = isset($related->field_defs['first_name']) && isset($related->field_defs['last_name']);
    $name_cols = $is_person ? "first_name, last_name" : "name";

    $names = array();
    $emails = array();

    foreach(array_chunk($related_ids, 500) as $chunk) {
        $in = array();
        foreach($chunk as $id) {
            $in[] = $db->quoted($id);
        }
        $in = implode(",", $in);

        $r = $db->query("SELECT id, $name_cols FROM {$related->table_name} WHERE id IN ($in) AND deleted = 0");
        while($a = $db->fetchByAssoc($r)) {
            $names[$a['id']] = $is_person ? trim($a['first_name'] . ' ' . $a['last_name']) : $a['name'];
        }

        // primary address first, so the first row seen for a bean is the one to use
        // does not return invalid or opted out addresses
        $q = "SELECT ear.bean_id, ea.email_address FROM email_addr_bean_rel ear
                INNER JOIN email_addresses ea ON ea.id = ear.email_address_id
                WHERE ear.bean_module = " . $db->quoted($email_module) . "
                AND ear.bean_id IN ($in)
                AND ear.deleted = 0
                AND ea.deleted = 0
                AND ea.invalid_email = 0
                AND ea.opt_out = 0
                ORDER BY ear.primary_address DESC";
        $r = $db->query($q);
        while($a = $db->fetchByAssoc($r)) {
            if(!isset($emails[$a['bean_id']]) && !empty($a['email_address'])) {
                $emails[$a['bean_id']] = $a['email_address'];
            }
        }
    }

    $list = array();
    $seen = array();
    foreach($related_ids as $id) {
        if(!isset($names[$id]) || !isset($emails[$id])) {
            //only add those records with email addresses
            continue;
        }
        $key = strtolower($emails[$id]);
        if(isset($seen[$key])) {
            //same address on more than one record (eg siblings), only send once
            continue;
        }
        $seen[$key] = true;
        $list[] = from_html($names[$id]) . " <" . from_html($emails[$id]) . ">";
    }

    return array(
        'addresses' => implode("; ", $list),
        'count' => count($list),
        'total' => count($related_ids),
    );
}

sbccSubpanelEmailAddressesRespond(sbccSubpanelEmailAddresses());
