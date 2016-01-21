/**
 * Interaction for the addresses module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 * @author Nick Vandevenne <nick@comsa.be>
 */
jsFrontend.addresses =
{
    map: {},
    markers: [],
    latLng: [],

    // constructor
    init: function () {
        $('#groups').multiselect({
            buttonWidth: '100%',
            numberDisplayed: 2,
            nonSelectedText: 'Selecteer een opleiding',
            nSelectedText: 'opleidingen geselecteerd',
            allSelectedText: 'Alle opleiding geselecteerd',
        });

        $('#search').submit(function (e) {
            e.preventDefault();

            var address = $(this).find('input#search').val();
            var geocoder = new google.maps.Geocoder();
            var form = $(this);
            geocoder.geocode({'address': address, 'region': 'BE'}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var lat = results[0].geometry.location.lat();
                    var lng = results[0].geometry.location.lng();
                    form.find('input#lat').val(lat);
                    form.find('input#lng').val(lng);
                }
                form.unbind('submit').submit();
            });
        });

        var items = jsFrontend.data.get('Addresses.items');

        jsFrontend.addresses.initMap();
        $("#googleMaps").width($("#googleMaps").parent().width());

        //--Check if there are addresses found
        if (items != undefined && items.length > 0) {
            var lat = jsFrontend.data.get('Addresses.lat') || 0;
            var lng = jsFrontend.data.get('Addresses.lng') || 0;
            var search = jsFrontend.data.get('Addresses.search') || undefined;
            if (lat > 0 && lng > 0) {
                jsFrontend.addresses.addMarker(0, lat, lng, search, true);
            }
            jsFrontend.addresses.centerMap(true);
            if(items.length == 1){
                jsFrontend.addresses.map.setZoom(1);
                setTimeout(function () {
                    jsFrontend.addresses.map.setZoom(15);
                }, 300);
            }
        } else {
            var lat = jsFrontend.data.get('Addresses.lat') || "50.85";
            var lng = jsFrontend.data.get('Addresses.lng') || "4.38";
            var search = jsFrontend.data.get('Addresses.search') || undefined;
            if (lat > 0 && lng > 0) {
                jsFrontend.addresses.addMarker(0, lat, lng, search, true);
            }
            jsFrontend.addresses.centerMap(true);
            jsFrontend.addresses.map.setZoom(1);
            setTimeout(function () {
                jsFrontend.addresses.map.setZoom(8);
                $.each(jsFrontend.addresses.markers, function(t,v){
                    v.setMap(null);
                });
            }, 300);
        }
    },
    initMap: function () {
        // build the options
        var options = {
            center: {lat: 50.85, lng: 4.38},
            zoom: 1,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        // create map
        jsFrontend.addresses.map = new google.maps.Map(document.getElementById('googleMaps'), options);

        // get the items
        var items = jsFrontend.data.get('Addresses.items');

        google.maps.event.addDomListener(window, 'resize', function () {
            $("#googleMaps").width($("#googleMaps").parent().width());
            jsFrontend.addresses.centerMap(true);
            if (items == undefined || items.length <= 0) {
                setTimeout(function () {
                    jsFrontend.addresses.map.setZoom(8);

                }, 300);
            }
        });

        $(items).each(function (e, o) {
            jsFrontend.addresses.addMarker(o.id, o.lat, o.lng, (o.firstname || "") + " " + (o.name || ""), false, o);

        });

        //var clusterStyles = [
        //    {
        //        url: '/src/Frontend/Themes/theme/Core/Layout/Images/marker-clusterer-25.png',
        //        height: 25,
        //        width: 25,
        //        textColor: '#FFFFFF'
        //    },
        //    {
        //        url: '/src/Frontend/Themes/theme/Core/Layout/Images/marker-clusterer-40.png',
        //        height: 40,
        //        width: 40,
        //        textColor: '#FFFFFF'
        //    },
        //    {
        //        url: '/src/Frontend/Themes/theme/Core/Layout/Images/marker-clusterer-50.png',
        //        height: 50,
        //        width: 50,
        //        textColor: '#FFFFFF'
        //    }
        //];
        //
        //
        //markerClusterer = new MarkerClusterer(jsFrontend.addresses.map, jsFrontend.addresses.markers, {styles: clusterStyles});
    },
    // add a marker
    addMarker: function (id, lat, lng, title, dif) {
        //--Create objLatLng
        var objLatLng = new google.maps.LatLng(lat, lng);

        //--Add lat/lng to the array
        jsFrontend.addresses.latLng.push(objLatLng);

        // add the marker
        var marker = new google.maps.Marker(
            {
                position: objLatLng,
                map: jsFrontend.addresses.map,
                title: title,
                locationId: id,
                icon: (dif ? 'http://maps.google.com/mapfiles/ms/icons/green-dot.png' : 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'),
            }
        );

        // show infowindow on click
        if (title != undefined) {
            google.maps.event.addListener(marker, 'click', function () {
                $.each(jsFrontend.addresses.markers, function (index, value) {
                    if (value.infowindow != undefined) {
                        value.infowindow.close();
                    }
                });
                if (marker.infowindow == undefined) {

                    $markerText = $('#address-' + marker.locationId);
                    $markerTextExtra = $('#contact-' + marker.locationId);

                    // apparently JS goes bananas with multiline HTMl, so we grab it from the div, this seems like a good idea for SEO
                    if ($markerText.length > 0) {
                        text = $markerText.html();
                    } else {
                        text = "";
                    }
                    if ($markerTextExtra.length > 0) {
                        textExtra = $markerTextExtra.html();
                    } else {
                        textExtra = "";
                    }

                    var content = '<h4>' + title + '</h4>';
                    if (typeof text != 'undefined') content += text;
                    if (typeof textExtra != 'undefined') content += "<dl class='dl-horizontal'>" + textExtra + "</dl>";

                    marker.infowindow = new google.maps.InfoWindow(
                        {
                            content: content
                        }
                    );
                }
                marker.infowindow.open(jsFrontend.addresses.map, marker);
            });
        }

        //--Push marker to the markers
        jsFrontend.addresses.markers.push(marker);

    },
    centerMap: function (fitBounds) {
        var objLatLngBounds = new google.maps.LatLngBounds();

        //--Loop each LatLng
        $.each(jsFrontend.addresses.latLng, function (intCounter, objObject) {
            objLatLngBounds.extend(objObject);
        });

        //--Center map
        jsFrontend.addresses.map.setCenter(objLatLngBounds.getCenter());

        if (fitBounds == true) {
            jsFrontend.addresses.map.fitBounds(objLatLngBounds);
        }

    },
};

$(jsFrontend.addresses.init);