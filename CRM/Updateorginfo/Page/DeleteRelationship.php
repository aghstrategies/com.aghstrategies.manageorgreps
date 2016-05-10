<?php

require_once 'CRM/Core/Page.php';

class CRM_Updateorginfo_Page_DeleteRelationship extends CRM_Core_Page {
  public function run() {
    CRM_Utils_System::setTitle(ts('Delete Relationship'));
    $relationship_id = $_GET['relationship_id'];
    $end_date = date('Y-m-d');
    $contact_delete_link = CRM_Utils_System::url('civicrm/ajax/rest', $query = 'entity=Relationship&action=delete&debug=1&sequential=1&json=1&id=' . $relationship_id, TRUE, NULL, TRUE, TRUE);
    $this->assign('contact_delete_link', $contact_delete_link);
    $this->assign('relationship_id', $relationship_id);
    $this->assign('end_date', $end_date);
    //TODO what is this constant CIVICRM_B
    $this->assign('home', CIVICRM_B);
    // Example: Assign a variable for use in a template

    parent::run();
  }

}
