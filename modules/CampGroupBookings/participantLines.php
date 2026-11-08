<?php
/**
 * Advanced OpenReports, SugarCRM Reporting.
 * @package Advanced OpenReports for SugarCRM
 * @copyright SalesAgility Ltd http://www.salesagility.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with this program; if not, see http://www.gnu.org/licenses
 * or write to the Free Software Foundation,Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA 02110-1301  USA
 *
 * @author SalesAgility <info@salesagility.com>
 */


function display_participant_lines($focus, $field, $value, $view){

    global $locale, $app_list_strings, $mod_strings;

    $html = '';

    if (!is_file('cache/jsLanguage/CampGroupBookings/' . $GLOBALS['current_language'] . '.js')) {
        require_once ('include/language/jsLanguage.php');
        jsLanguage::createModuleStringsCache('CampGroupBookings', $GLOBALS['current_language']);
    }
    $html .= '<script src="cache/jsLanguage/CampGroupBookings/'. $GLOBALS['current_language'] . '.js"></script>';

    if($view == 'EditView'){

        $html .= '<script src="modules/CampGroupBookings/participantLines.js"></script>';
        $html .= "<table border='0' cellspacing='4' width='100%' id='participantLines'></table>";

        $html .= "<div style='padding-top: 10px; padding-bottom:10px;'>";
        $html .= "<input type=\"button\" tabindex=\"116\" class=\"button\" value=\"".$mod_strings['LBL_ADD_PARTICIPANT']."\" id=\"btn_ParticipantLine\" onclick=\"insertParticipantLine()\" disabled/>";
        $html .= "</div>";

        $html .= "<script>";
        $html .= "document.getElementById('btn_ParticipantLine').disabled = '';";
        if($focus->id != ''){
            $sql = "SELECT id FROM campparticipants WHERE campgroupbooking_id = '".$focus->id."' AND deleted = 0 ORDER BY date_entered ASC";
            $result = $focus->db->query($sql);
            while ($row = $focus->db->fetchByAssoc($result)) {
                $participant = BeanFactory::getBean('CampParticipants',$row['id']);
                $participant_item = json_encode($participant->toArray());
                $html .= "loadParticipantLine(".$participant_item.");";
            }
        }
        $html .= "</script>";
    }

    return $html;
}

?>
