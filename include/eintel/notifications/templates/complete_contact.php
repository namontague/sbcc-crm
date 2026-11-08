<!--
/*********************************************************************************
 * (C) 2009 Enterpris Intelligence Systems
 * Not to be used without the express permission of the author
 ********************************************************************************/
-->

<!-- BEGIN: Task_Contact_Subject -->
SugarCRM Task - Completed : {DESCRIPTION}
<!-- END: Task_Contact_Subject -->
<!-- BEGIN: Task_Contact -->
To {USER},<br />
{HEADER}<br />

<table  border="0">
  <tr>
    <th scope="col" align="left">Subject</th>
    <th scope="col" width="300" align="left">Description</th>
    <th scope="col" align="left">Assigned User</th>
  </tr>
<!-- BEGIN: TASK_ROWS -->
  <tr>
    <td valign="top">{SUBJECT}</td>
    <td valign="top">{DESCRIPTION}</td>
    <td valign="top">{ASSIGNED_USER}</td>
  </tr>
  <tr>
      <td valign="top" colspan="3"><br /></td>
  </tr>
  <tr>
      <td valign="top" colspan="3">If you have any questions about this task call <b>Eintel</b> on <b>1300 854 880</b></td>
  </tr>
  <!-- END: TASK_ROWS -->
</table>
<!-- END: Task_Contact -->


