Extension for CiviCRM to manage additional contacts at organizations.

This extension creates a relationship type called "Organizational Representative". It also creates a profile labeled "Update Organizational Contacts". A user can add fields to this profile through the menu link at the bottom of the administer menu. When this profile is used with the paramater org_id the new user it creates will have a relationship with the organization specified by id.

To access the url for the profile with org id quickly for an email, there is a token listed as "Add Organizational Representive" and is identified in Smarty as {org_reps.link}. Additionally, there is a token called "Organizational Representatives List" which will display a list of names and email address of previously added reps along with links to update or delete them. Deleting them will disable the relationship.
