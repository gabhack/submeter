<div id="local" class=".d-none">
    <input type="hidden" name="timezoneoffset" id="timezoneoffset">
</div>

<script>
    var d = new Date();
    document.getElementById("timezoneoffset").value = d.getTimezoneOffset();
</script>