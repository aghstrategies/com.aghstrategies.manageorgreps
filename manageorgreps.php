<?php

require_once 'manageorgreps.civix.php';

/**
 * Implementation of hook_civicrm_post
 */
function manageorgreps_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName=='Profile' && $op=='create'){
    $orgrep_profile_id = get_orgrep_profile_id();
    if ($objectRef['uf_group_id']==$orgrep_profile_id){
      $org_id = $objectRef['organizationalaffiliation'];
       $contact_id = $objectId;
       $relationship_type_id = get_organizational_relationship_id();
       $start_date = date('Y-d-m');
       $params = array(
         'version' => 3,
         'relationship_type_id' => $relationship_type_id,
         'contact_id_a' => $contact_id,
         'contact_id_b' => $org_id, //get org id
         'start_date' => $start_date,
       );
    }
    $result = civicrm_api('Relationship', 'Create', $params);
    if ($result['is_error']){
      print_r($result['error_message']);
    }
    //$op=='edit'
  }
}

/**
 * Implementation of hook_civicrm_buildForm
 */
function manageorgreps_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Profile_Form_Edit'){
    $gid = $form->getVar('_gid');
    $orgrep_profile_id = get_orgrep_profile_id();
    if ($gid==$orgrep_profile_id){//profile id
    $form->add('text', 'organizationalaffiliation', ts('Organization Affiliation'));
    $org_id = '';
    if (array_key_exists('org_id', $_GET)){
      $org_id = $_GET['org_id'];
    }
    $form->assign('org_id', $org_id);
    // $ the field element in the form
    $form->add('text', 'testfield', ts('Test field'));
    // dynamically insert a template block in the page
    $templatePath = realpath(dirname(__FILE__)."/templates");
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "{$templatePath}/organizationalaffiliation.tpl"
     ));
    }
    CRM_Core_Resources::singleton()->addScriptFile('com.aghstrategies.manageorgreps', 'js/getorgname.js');

  }
}

/**
 * Implementation of hook_civicrm_token
 */
function manageorgreps_civicrm_tokens( &$tokens ) {
  $tokens['org_reps'] = array(
    'org_reps.list' => ts("Organizational Representatives List"),
    'org_reps.link' => ts("Add Organizational Representatives Link"),
  );
}

/**
 * Implementation of hook_civicrm_tokenValues
 */
function manageorgreps_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
  $orgrep_profile_id = get_orgrep_profile_id();
  $gid = $orgrep_profile_id;

  if (!empty($tokens['org_reps'])){
    $relationship_type_id = get_organizational_relationship_id();
    foreach($cids as $cid){
      $cid_cs = CRM_Contact_BAO_Contact_Utils::generateChecksum( $cid );
      try{
         $relationships = civicrm_api3('Relationship', 'get', array(
            'contact_id_b'   =>  $cid,
            'relationship_type_id' => $relationship_type_id,
            'is_active' => 1,
         ));
      }
      catch (CiviCRM_API3_Exception $e) {
        $error = $e->getMessage();
      }
      $list = '';
      if ($relationships['count']>0){
        $list .= '<table>
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>';
        foreach ($relationships['values'] as $relationship){
          $contact_a = $relationship['contact_id_a'];
          try{
           $contact = civicrm_api3('Contact', 'getSingle', array(
              'id'   =>  $contact_a,
           ));
          }
          catch (CiviCRM_API3_Exception $e) {
            $error = $e->getMessage();
          }
          $contact_profile_link = '';

          $contact_a_cs = CRM_Contact_BAO_Contact_Utils::generateChecksum( $contact_a );

          $contact_profile_link = CRM_Utils_System::url('civicrm/profile/edit', $query = 'reset=1&gid='.$gid.'&id='.$contact_a.'&org_id='.$cid.'&cs='.$contact_a_cs,  true, null, true, true);
          $contact_delete_link = CRM_Utils_System::url('civicrm/delete_relationship', $query = 'relationship_id='.$relationship['id'].'&cs='.$contact_a_cs,  true, null, true, true);

          $list .= '<tr><td>'.$contact['display_name'] . '</td><td> '.$contact['email'] .'</td><td> <a href="' .$contact_profile_link.'">Update User</a> | <a href="'.$contact_delete_link.'">Remove Representative</a></td><tr>';
        }
        $list .='</tbody></table>';
      }
      $profile_link = CRM_Utils_System::url('civicrm/profile/create', $query = 'reset=1&gid='.$gid.'&org_id='.$cid.'&cs='.$cid_cs,  true, null, true, true);
      $token = array('org_reps.list' => $list, 'org_reps.link' => $profile_link);
      $values[$cid] = empty($values[$cid]) ? $token : $values[$cid] + $token;
    }
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
  $params = array(
      'version' => '3',
      'group_type' => 'Contact,Individual',
      'name' => 'update_organizational_contacts',
      'title' => 'Update Organizational Contacts',
      'is_reserved' => 1,
      'is_active' => 1,
      );
  $ufgroup = civicrm_api('UFGroup', 'create', $params);
  if (!$ufgroup['is_error']){
    $params = array(
      'version' => '3',
      'uf_group_id' => $ufgroup['id'],
      'module' => 'Profile',
      'is_active' => '1',
      'weight' => '1',
    );
    $ufjoin = civicrm_api('UFJoin', 'create', $params);
    if ($ufjoin['is_error']){
      print_r($ufjoin['error_message']); die();
    }
  }
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function manageorgreps_civicrm_uninstall() {
  $profile_id =  get_orgrep_profile_id();
  $params= array('version' => '3', 'id' => $profile_id);
  civicrm_api('UFGroup', 'delete', $params);
  $params= array('version' => '3', 'uf_group_id' => $profile_id);
  civicrm_api('UFJoin', 'delete', $params);
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
  $profile_id =  get_orgrep_profile_id();
  $params= array('version' => '3', 'id' => $profile_id, 'is_active' => 1);
  civicrm_api('UFGroup', 'create', $params);
  try{
    $ufjoin = civicrm_api3('UFJoin', 'getSingle', array(
      'uf_group_id' => $profile_id,
     ));
  }
  catch (CiviCRM_API3_Exception $e) {
    $error = $e->getMessage();
  }
  $params= array('version' => '3', 'uf_group_id' => $profile_id, 'id' => $ufjoin['id'], 'is_active' => 1);
  civicrm_api('UFJoin', 'create', $params);
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

function get_organizational_relationship_id(){
  try{
     $relationshiptype = civicrm_api3('RelationshipType', 'getSingle', array(
        'name_a_b'   =>  'Organizational Representative of',
        'name_b_a' => 'Organizational Representative is',
     ));
    }
  catch (CiviCRM_API3_Exception $e) {
    $error = $e->getMessage();
  }
  return $relationshiptype['id'];
}

function get_orgrep_profile_id(){
  try{
     $ufgroup = civicrm_api3('UFGroup', 'getSingle', array(
      'title' => 'Update Organizational Contacts',
      'is_reserved' => 1,
     ));
    }
  catch (CiviCRM_API3_Exception $e) {
    $error = $e->getMessage();
  }
  return $ufgroup['id'];
}
