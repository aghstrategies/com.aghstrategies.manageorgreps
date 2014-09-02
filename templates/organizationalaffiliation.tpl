{* template block that contains the new field *}
<div id="organizationalaffiliation-tr" class="crm-section editrow form-item">
  <div class="label">Organizational Affiliation</div>
  <div class="edit-value content">{$form.organizationalaffiliation.html}</div>
</div>
{* reposition the above block after #someOtherBlock *}
<script type="text/javascript">
  cj('#organizationalaffiliation-tr').insertBefore('.crm-submit-buttons');
  cj('#organizationalaffiliation').val("{$org_id}");
  cj('#organizationalaffiliation-tr').css("visibility", "hidden");
</script>
