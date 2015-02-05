<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
        html { height: 100% }
        body { height: 100%; margin: 0; padding: 0 }
        #map-canvas { height: 75% }
        #youtube-container { height: 25% }
        #youtube-container iframe { margin-right: 2px; }
    </style>
	<script type="text/javascript" src="js/jquery-1.11.2.js">
	</script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQedy4azLGDiAKWCVzZ36t4OKYORp8ki4">
	</script>
	<script type="text/javascript">
		// Méthodes de recherche de données freebase via localisation du pays
		var geocoder;

		// Recherche de données freebase
		function makeRequestFreebase(searchValue) {

			var service_url = 'https://www.googleapis.com/freebase/v1/search';
			var params = { query : searchValue,  filter : "(all type:/location/country)" };

			$.getJSON(service_url + '?callback=?', params, function (topic) { createFreebaseIframe(topic); });
		}

		// Retrouve le pays associé aux coordonnées GPS et effectue la recherche freebase associée
		function codeLatLng(latlng) {

			geocoder = new google.maps.Geocoder();

			geocoder.geocode({ 'latLng' : latlng }, 
			function (results, status) {

				// Existe-t-il des informations associées aux coordonnées GPS
				if (status == google.maps.GeocoderStatus.OK) {

					if (results[0]) {
						var elt = results[0].address_components;

						for (i in elt) {

							if (elt[i].types[0] == 'country') {

								makeRequestFreebase(elt[i].long_name);

								makeRequestYoutube(elt[i].long_name);
							}
						}
					}
				} else {

					alert("Geocoder failed due to: " + status);
				}
			});
		}
    </script>
    <script type="text/javascript">
		// Méthodes de recherche de vidéos Youtube
		function makeRequestYoutube(searchValue) {

			var request = gapi.client.youtube.search.list({q : searchValue,	part : 'snippet', type:  'video' });

			request.execute(
			function (response) {

				createYoutubeIframe(response);
			});
		}

		// Chargement de l'API Google
		function load() {

			gapi.client.setApiKey("AIzaSyAQedy4azLGDiAKWCVzZ36t4OKYORp8ki4");
			gapi.client.load('youtube', 'v3');
		}
    </script>
    <script src="https://apis.google.com/js/client.js?onload=load">
	</script>
    <script type="text/javascript">
		// Méthodes d'affichage de la map
		var map;

		// Déclaration de la fenêtre d'informations
		var infowindow = new google.maps.InfoWindow({ maxWidth : 300, maxHeight : 400 });

		function initialize() {

			var mapOptions = { center : new google.maps.LatLng(0,0), zoom : 2 };

			map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

            // Create a <script> tag and set the USGS URL as the source.
            var script = document.createElement('script');

            script.src = 'http://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/2.5_month.geojsonp';

            document.getElementsByTagName('head')[0].appendChild(script);
		}

		function eqfeed_callback(results) {

			// On parcours les résultats JSON
			for (var i = 0; i < results.features.length; i++) {

				// On créer un marqueur représentant le tremblement de terre
				var earthquake = results.features[i];
				var coords = earthquake.geometry.coordinates;
				var latLng = new google.maps.LatLng(coords[1], coords[0]);
				var marker = new google.maps.Marker({ position : latLng, map : map, icon : styleFeature(earthquake.properties.mag) });

				// Contenue de la fenêtre d'informations
				var html = "<table>" +
				"<tr><td><b>Lieu:</b></td><td>" + earthquake.properties.place + "</td></tr>" +
				"<tr><td><b>Date:</b></td><td>" + new Date(earthquake.properties.time).toLocaleString() + "</td></tr>" +
				"<tr><td><b>Fuseau horaire:</b></td><td>" + earthquake.properties.tz/60 + "</td></tr>" +
				"<tr><td><b>Magnitude:</b></td><td>" + earthquake.properties.mag + "</td></tr>" +
				"<tr><td><b>Alerte:</b></td><td>" + earthquake.properties.alert + "</td></tr>" +
				"<tr><td><b>Infos:</b><td><a href=\"" + earthquake.properties.url + "\">" + earthquake.properties.url + "</a></td></tr>" +
				"</table>";

				google.maps.event.addListener(marker, 'click', openWindow(map, marker, html, earthquake, latLng));
			}
		}

		function openWindow(map, marker, html, earthquake, latLng){

			function ajoute() {

				// Affichage de la fenêtre d'informations
				infowindow.setContent(html);

				infowindow.open(map, marker);

				// Affichage des données freebase
				codeLatLng(latLng);
			}

			return ajoute;
		}

		// Affichage des marqueurs de couleur en fonction de la magnitude
		function styleFeature(feature) {

			var low = [151, 83, 34];   // color of mag 1.0
			var high = [5, 69, 54];  // color of mag 6.0 and above
			var minMag = 1.0;
			var maxMag = 6.0;

			// Fraction represents where the value sits between the min and max
			var fraction = (Math.min(feature, maxMag) - minMag) / (maxMag - minMag);

			var color = interpolateHsl(low, high, fraction);

			var circle = {
				path: google.maps.SymbolPath.CIRCLE,
				strokeWeight: 0.5,
				strokeColor: '#fff',
				fillColor: color,
				fillOpacity: 2 / feature,
				scale: Math.pow(feature, 2)
			};

			return circle;
		}

		function interpolateHsl(lowHsl, highHsl, fraction) {

			var color = [];

			for (var i = 0; i < 3; i++) {

				// Calculate color based on the fraction.
				color[i] = (highHsl[i] - lowHsl[i]) * fraction + lowHsl[i];
			}

			return "hsl(" + color[0] + ", " + color[1] + "%, " + color[2] + "%)";
		}

		// Affichage des vidéos Youtube trouvées
		function createYoutubeIframe(request){

			var height = ($(window).height() * 25) / 100;
			var width = height * (560 / 315);
			var items = request.items;

			if (items != null) {

				// On renouvelle la liste de vidéos
				$("#youtube-container").html("");
				$("#youtube-container").append("<h1>Youtube</h1>");

				for (var i = 0; i < items.length; i++) {

					$("#youtube-container").append("<iframe width=\"" + width + "\" height=\""+height+"\" src=\"//www.youtube.com/embed/" + items[i].id.videoId + "\" frameborder=\"0\" allowfullscreen></iframe>");
				}
			} else {

				$("#youtube-container").html("<p>La recherche de vidéos a échoué</p>");
			}
		}

		// Affichage des données freebase
		function createFreebaseIframe(request) {

			var res = request.result;

			if (request != null) {

				// On renouvelle la liste de résultats
				$("#freebase-container").html("");
				$("#freebase-container").append("<h1>Freebase</h1>");

				for (var i = 0; i < res.length; i++) {

					$("#freebase-container").append("<p>" + res[i].name + "</p>");
				}
			} else {

				$("#freebase-container").html("<h1>Freebase</h1>");
				$("#freebase-container").append("<p>La recherche de pays a échoué</p>");
			}
		}

		google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</head>
<body>
	<!-- Le conteneur permettant d'afficher la map -->
	<div id="map-canvas">
	</div>
	<!-- Le conteneur permettant d'afficher les donnÃ©es freebase -->
	<div id="freebase-container">
	</div>
	<!-- Le conteneur permettant d'afficher les vidÃ©os youtube -->
	<div id="youtube-container">
	</div>
</body>
</html>