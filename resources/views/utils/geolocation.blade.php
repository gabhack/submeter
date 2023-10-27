<div id="geo" class=".d-none">
	<input type="hidden" name="lat" id="lat">
	<input type="hidden" name="lon" id="lon">
	<input type="hidden" name="address" id="address">
</div>

<script>
	
	var lat = document.getElementById("lat");
	var lon = document.getElementById("lon");
	var address = document.getElementById("address");

	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(showPosition, showError);
	}
	else { 
		console.log("Geolocation is not supported by this browser.");
	}
	
	/*Si acepta compartir su ubicación se sobreescriben los valores, de lo contrario se quedan los primeros*/
	function showPosition(position) {
		lat.value = position.coords.latitude;
		lon.value = position.coords.longitude;

		//Create query for the API.
		var latitude = "latitude=" + position.coords.latitude;
		var longitude = "&longitude=" + position.coords.longitude;
		var query = latitude + longitude + "&localityLanguage=en";

		const Http = new XMLHttpRequest();

		var bigdatacloud_api = "https://api.bigdatacloud.net/data/reverse-geocode-client?";

		bigdatacloud_api += query;

		Http.open("GET", bigdatacloud_api);
		Http.send();

		Http.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var myObj = JSON.parse(this.responseText);
				console.log(myObj);
				address.value= myObj.locality + ", " + myObj.city + ", " + myObj.principalSubdivision + ", " + myObj.countryName;
			}
		};
	}

	function showError(error) {
	  switch(error.code) {
		case error.PERMISSION_DENIED:
		case error.POSITION_UNAVAILABLE:
		case error.TIMEOUT:
			/*Obtiene una coordenadas por IP en base al ISP*/
			$.ajax({
				url: "https://extreme-ip-lookup.com/json/?callback=callback",
				jsonpCallback: "callback",
				dataType: "jsonp",
				success: function(location) {
					$('#address').val(location.city + ', ' + location.region + ', ' + location.country);
					$('#lat').val(location.lat);
					$('#lon').val(location.lon);
				}
			});
		break;
		case error.UNKNOWN_ERROR:
			lat.value = 0;
			lon.value = 0;
			address.value="Error al obtener la geolocalización"
		break;
	  }
	}

</script>