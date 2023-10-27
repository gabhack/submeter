<script type="text/javascript">
  
  $(document).ready(function(){
    $("#option_interval").change(changeIntervalOption);
    // changeIntervalOption();

    $( "#datepicker" ).datepicker({
      dateFormat:'yy-mm-dd',
      changeMonth: true,
      changeYear: true
    });
  
    $( "#datepicker2" ).datepicker({
      dateFormat:'yy-mm-dd',
      changeMonth: true,
      changeYear: true
    });
  });
  
  function changeIntervalOption(){
    var val = $("#option_interval").val();
    if(val == "9"){
      $("#div_datatimes").show();
    }
    else{
      $("#div_datatimes").hide();
    }
  }
</script>

