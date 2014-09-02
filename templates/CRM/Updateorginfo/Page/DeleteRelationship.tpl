{$contact_delete_link}
{$relationship_id}
<script>
var relationshipId = {$relationship_id};
var endDate = {$end_date};
{literal}
if (confirm("Do you want to delete this Organizational Representative?")){
  CRM.api('Relationship', 'create', {'relationship_id': relationshipId, 'is_active': 0, 'end_date':  endDate},
    {success: function(data) {
        cj.each(data, function(key, value) {
           console.log(data);
      });
    }
  });
}
else{  // window.location.replace('http://youwilldobetter.blogspot.com');
}
{/literal}
</script>
