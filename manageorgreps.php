<?php

require_once 'manageorgreps.civix.php';

/**
 * Implementation of hook_civicrm_post
 */
function manageorgreps_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName=='Profile' && $op=='create'){
    if ($objectRef['uf_group_id']==13){ //selected profile id
       $contact_id = $objectId;
       $orgrepid = 17;
       $params = array(
         'version' => 3,
         'relationship_type_id' => $orgrepid,
         'contact_id_a' => $contact_id,
         'contact_id_b' => 1, //get org id

       );
    }
    //$op=='edit'
  }
}

/**
 * Implementation of hook_civicrm_buildForm
 */
function manageorgreps_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Profile_Form_Edit'){
    CRM_Core_Resources::singleton()->addScriptFile('com.aghstrategies.manageorgreps', 'js/getorgname.js');

  }
}

/**
 * Implementation of hook_civicrm_config
 */
function manageorgreps_civicrm_config(&$config) {
  _manageorgreps_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function manageorgreps_civicrm_xmlMenu(&$files) {
  _manageorgreps_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function manageorgreps_civicrm_install() {
  return _manageorgreps_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function manageorgreps_civicrm_uninstall() {
  return _manageorgreps_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function manageorgreps_civicrm_enable() {
  return _manageorgreps_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function manageorgreps_civicrm_disable() {
  return _manageorgreps_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function manageorgreps_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _manageorgreps_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function manageorgreps_civicrm_managed(&$entities) {//add uf group and uf fields
  $entities[] = array(
    'module' => 'com.aghstrategies.manageorgreps',
    'name' => 'Organizational Representative Relationship',
    'entity' => 'RelationshipType',
    'params' => array(
      'version' => '3',
      'name_a_b' => 'Organizational Representative of',
      'label_a_b' => 'Organizational Representative of',
      'name_b_a' => 'Organizational Representative is',
      'label_b_a' => 'Organizational Representative is',
      'description' => 'Organization Representative relationship',
      'contact_type_a' => 'Individual',
      'contact_type_b' => 'Organization',
      'is_reserved' => 1,
      'is_active' => 1,
      ),
  );
}
