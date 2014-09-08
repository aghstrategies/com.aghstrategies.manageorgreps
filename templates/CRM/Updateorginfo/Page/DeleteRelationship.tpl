
<script>
var relationshipId = {$relationship_id};
var endDate = "{$end_date}";
{literal}
var url = CRM.url('', {});
alert(endDate);
if (confirm("Do you want to deactivate this Organizational Representative?")){
  CRM.api('Relationship', 'create', {'relationship_id': relationshipId, 'is_active': 0, 'end_date':  endDate},
    {success: function(data) {
        // cj.each(data, function(key, value) {
          console.log(data);
           if(!data['is_error']){
             alert('Relationship deactivated.');
           }
      // });
    }
  });
}
window.location.replace(url);

{/literal}
</script>
