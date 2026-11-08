<?php

    require_once('include/MVC/View/views/view.list.php');

    class TimesheetViewList extends ViewList {

        var $actual_time;
        var $billed_time;


        function TimesheetList() {
            parent::ViewList();
        }
        function listViewProcess(){
            $this->processSearchForm();
            $this->lv->searchColumns = $this->searchForm->searchColumns;

            if(!$this->headers)
                return;
            if(empty($_REQUEST['search_form_only']) || $_REQUEST['search_form_only'] == false){
                $this->lv->setup($this->seed, 'modules/Timesheet/tpls/ListViewGeneric.tpl', $this->where, $this->params);
                if(isset($this->lv->data)){
                    foreach($this->lv->data[data] as $d){
                        $this->actual_time = $this->actual_time + $d['ACTUAL'];
                        $this->billed_time = $this->billed_time + $d['BILLABLE'];
                    }
                    $this->lv->ss->assign('actual_time', $this->actual_time);
                    $this->lv->ss->assign('billed_time', $this->billed_time);
                }
                $savedSearchName = empty($_REQUEST['saved_search_select_name']) ? '' : (' - ' . $_REQUEST['saved_search_select_name']);
                echo get_form_header($GLOBALS['mod_strings']['LBL_LIST_FORM_TITLE'] . $savedSearchName, '', false);
                echo $this->lv->display();
            }


        }
       
        function listViewPrepare() {
            if(empty($_REQUEST['orderBy'])){
                $_REQUEST['orderBy'] = 'date_booked';
                $_REQUEST['sortOrder'] = 'desc';
            }
            parent::listViewPrepare();
        }
    }
?>
