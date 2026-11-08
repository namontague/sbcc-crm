<!--
/*********************************************************************************
 * (C) 2009 Enterpris Intelligence Systems
 * Not to be used without the express permission of the author
 ********************************************************************************/
-->

<!-- BEGIN: Task_List_Subject -->
Task list
<!-- END: Task_List_Subject -->
<!-- BEGIN: Task_List -->
Dear CRM User,
<br />
    <!-- BEGIN: TASK_HEADER -->
<br />
Listed below are tasks that have been assigned to you.
<table width="686" border="1">
  <tr>
    <th scope="col" width="197">Task Name</th>
    <th scope="col" width="182">Date Due</th>
    <th scope="col" width="136">Importance</th>
    <th scope="col" width="143">Status</th>
  </tr>
    <!-- END: TASK_HEADER -->
    <!-- BEGIN: TASK_ROWS -->
  <tr>
    <td width="197"><a href="{URL}">{NAME}</a></td>
    <td width="182">{DATE_DUE}</td>
    <td width="136">{PRIORITY}</td>
    <td width="143">{STATUS}</td>
  </tr>
    <!-- END: TASK_ROWS -->
</table>
<br />
    <!-- BEGIN: PROJECT_TASK_HEADER -->
List of Project Tasks that have been assigned to you
<table width="686" border="1">
  <tr>
    <th scope="col" width="197">Project Task Name</th>
    <th scope="col" width="182">Date Due</th>
    <th scope="col" width="136">Importance</th>
    <th scope="col" width="143">Status</th>
  </tr>
    <!-- END: PROJECT_TASK_HEADER -->
    <!-- BEGIN: PROJECT_TASK_ROWS -->
  <tr>
    <td width="197"><a href="{PROJECTTASK_URL}">{PROJECTTASK_NAME}</a></td>
    <td width="182">{PROJECTTASK_DATE_DUE}</td>
    <td width="136">{PROJECTTASK_PRIORITY}</td>
    <td width="143">{PROJECTTASK_STATUS}</td>
  </tr>
    <!-- END: PROJECT_TASK_ROWS -->
</table>
<br />
Please update these tasks or action as appropriate.
<br />
Once tasks have been set to "Completed" you will no longer recieve this message.

<!-- END: Task_List -->
