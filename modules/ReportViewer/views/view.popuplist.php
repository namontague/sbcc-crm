<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

/* * *******************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2010 SugarCRM Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 * ****************************************************************************** */

require_once('include/MVC/View/SugarView.php');

class ReportViewerViewPopupList extends SugarView
{

    function ReportViewerViewPopupList()
    {
        parent::SugarView();
    }

    function process()
    {
        // This will bypass the sugar header and footer so only the required text is sent
        $this->display();
    }

    function display()
    {
        $sql = "SELECT * FROM reportviewer WHERE related_module = '{$_REQUEST['bean']}' AND deleted = 0";
        $result = $this->bean->db->query($sql);
        $countLine = $this->bean->db->getRowCount($result);

        echo '<div id="select_form">';

        if ($countLine != 0)
        {
            echo "<form name='reportSelection' id='reportSelection' action='index.php' method='get' >
            <input id='module' type='hidden' value='ReportViewer' name='module' />
            <input id='action' type='hidden' value='ReportParams' name='action' />
            <input id='format' type='hidden' value='' name='id' />
            <input id='report' type='hidden' value='{$_REQUEST['record_id']}' name='record_id' />";

            echo "<table>";
            while ($row = $this->bean->db->fetchByAssoc($result))
            {
                $js = "var form=document.getElementById('reportSelection');if(form!=null){form.id.value='" . $row['id'] . "';form.submit();}else{alert('Error!');}";
                echo '<td width="17" valign="center"><a href="#" onclick="' . $js . '">'.SugarThemeRegistry::current()->getImage("ReportViewer", "border='0' width='16' height='16'").'</a></td>
					<td scope="row" align="left"><b><a href="#" onclick="' . $js . '">' . $row['name'] . '</a></b></td></tr>';
            }
            echo "</table>";
        }
        else
            echo "No Reports";

        echo "</div>";
    }

}
?>
