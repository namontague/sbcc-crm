<!--
/*********************************************************************************
 * (C) 2009 Enterpris Intelligence Systems
 * Not to be used without the express permission of the author
 ********************************************************************************/
-->

<!-- BEGIN: Task_Complete_Subject -->
SugarCRM Task - Completed : {DESCRIPTION}
<!-- END: Task_Complete_Subject -->
<!-- BEGIN: Task_Complete -->
To {USER},<br />
{HEADER}<br />

<table border="0">
  <tr>
    <th scope="col" align="left">Subject</th>
    <th scope="col" width="300" align="left">Description</th>
    <th scope="col" align="left">Priority</th>
    <th scope="col" align="left">Date Due</th>
  </tr>
<!-- BEGIN: TASK_ROWS -->
  <tr>
    <td valign="top">{SUBJECT}</td>
    <td valign="top">{DESCRIPTION}</td>
    <td valign="top">{PRIORITY}</td>
    <td valign="top">{DATE_DUE}</td>
  </tr>
  <tr>
    <th colspan="4" align="left">You may review this Task at</th>
  </tr>
  <tr>
    <td colspan="4"><a href="{URL}">SugarCRM Task: {SUBJECT} - Click to view</a></td>
  </tr>
  <!-- END: TASK_ROWS -->
</table>
<!-- END: Task_Complete -->


