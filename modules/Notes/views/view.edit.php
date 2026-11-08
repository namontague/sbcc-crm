<?php
require_once('include/MVC/View/views/view.edit.php');
require_once('include/json_config.php');

class NotesViewEdit extends ViewEdit {


    function NotesViewEdit(){
        parent::ViewEdit();
        $this->useForSubpanel = true;
    }

    function display() {
//        $this->view = 'edit';
//        $GLOBALS['view'] = $this->view;
        
        //get parents category
        if(empty($this->bean->id)){
            if($_REQUEST['return_module'] == 'Tasks'){
                $query = "SELECT category
                        FROM tasks
                        WHERE id = '{$_REQUEST['return_id']}' 
                        AND tasks.deleted is false;";
                $result = $this->bean->db->query($query,true,"Error: ");
                $row = $this->bean->db->fetchByAssoc($result);
                $parent->category = isset($row['category'])?$row['category']:'';
            }else{
                $parent = BeanFactory::getBean($_REQUEST['return_module'], $_REQUEST['return_id']);
            }
            $this->ss->assign('parent_category',$parent->category);
        }
      
        parent::display();
    }


   

}
?>