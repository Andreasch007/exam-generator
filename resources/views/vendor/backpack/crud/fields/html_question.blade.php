<!-- used for heading, separators, etc -->
@include('crud::fields.inc.wrapper_start')
	<a href="#">{!!  $field['value']  !!}</a>
@include('crud::fields.inc.wrapper_end')

<!-- <script>
    function editQuestion(id){
        alert('exam_Id'+id)
    }
</script> -->