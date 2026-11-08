<!--
/*********************************************************************************
 * (C) 2009 Enterpris Intelligence Systems
 * Not to be used without the express permission of the author
 ********************************************************************************/
-->

<!-- BEGIN: ProjectTask_Subject -->
SugarCRM Project Task {SUBJECT}
<!-- END: ProjectTask_Subject -->
<!-- BEGIN: ProjectTask -->
To {USER},<br />
{MESSAGE}<br />
<table border="0">
  <tr>
    <th scope="col" align="left">Task</th>
    <th scope="col" width="300" align="left">Description</th>
    <th scope="col" align="left">Assigned To</th>
    <th scope="col" align="left">Status</th>
    <th scope="col" align="left">Priority</th>
    <th scope="col" align="left">Date Due</th>
  </tr>
<!-- BEGIN: PROJECTTASK_ROWS -->
  <tr>
    <td valign="top">{TASK}</td>
    <td valign="top">{DESCRIPTION}</td>
    <td valign="top">{ASSIGNED_USER}</td>
    <td valign="top">{STATUS}</td>
    <td valign="top">{PRIORITY}</td>
    <td valign="top">{DATE_DUE}</td>
  </tr>
  <tr>
    <th colspan="6" align="left">You may review this Project Task at</th>
  </tr>
  <tr>
    <td colspan="6"><a href="{URL}">SugarCRM Project Task: {TASK} - Click to view</a></td>
  </tr>
  <!-- END: PROJECTTASK_ROWS -->
</table>
<!-- END: ProjectTask -->


