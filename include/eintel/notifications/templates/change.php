<!--
/*********************************************************************************
 * (C) 2009 Enterpris Intelligence Systems
 * Not to be used without the express permission of the author
 ********************************************************************************/
-->

<!-- BEGIN: Task_Change_Subject -->
SugarCRM Task: {EMAIL_SUBJECT}
<!-- END: Task_Change_Subject -->
<!-- BEGIN: Task_Change -->
To {CONTACT_NAME},<br />
{HEADER}<br />

<table border="0">
  <tr>
    <th scope="col" align="left">Subject</th>
    <th scope="col" align="left">Status</th>
    <th scope="col" width="300" align="left">Description</th>

  </tr>
<!-- BEGIN: TASK_ROWS -->
  <tr>
    <td valign="top">{TASK_SUBJECT}</td>
    <td valign="top">{STATUS}</td>
    <td valign="top">{DESCRIPTION}</td>
  </tr>
  <tr>
      <td valign="top" colspan="3"><br /></td>
      <td valign="top" colspan="3">If you have any questions about this task call <b>Eintel</b> on <b>1300 854 880</b></td>
  </tr>
  <!-- END: TASK_ROWS -->
</table>

<!-- END: Task_Change -->

