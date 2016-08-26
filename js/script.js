var map;                                                        // Google map
var minZoomLevel = 15;                                          // Minimal zoom level
var bounds;
var start_latitude;
var start_longitude;
var end_latitude;
var end_longitude;
var $providers_container;
var loading = false;
var $loading;

$(document).ready(function(){
    $providers_container = $(".providers");
    $loading = $(".loading");

    $("#request").on("submit", function(e){
        e.preventDefault();
        if(!loading) {
            startLoading();
            $providers_container.html('');
            getAddrCoords($("#start_address").val(), 'from');
        } else {
            alert('Previous is loading, please wait.')
        }
    });
});

/**
 * Adds marker to map
 * @param position
 * @param contentString
 */
function addMarker(position, contentString) {
    var infowindow = new google.maps.InfoWindow({
        content: contentString
    });
    var marker = new google.maps.Marker({
        position: position,
        map: map,
        title: contentString,
        // This marker is 32 pixels wide by 29 pixels high.
        size: new google.maps.Size(32, 29),
        // The origin for this image is (0, 0).
        origin: new google.maps.Point(0, 0),
        // The anchor for this image is the base of the flagpole at (16, 15).
        anchor: new google.maps.Point(16, 15)
    });
    marker.addListener('click', function () {
        infowindow.open(map, marker);
    });
    bounds.extend(marker.getPosition());
}

/**
 * Converts Address to coordinates
 * @param location
 * @param type
 */
function getAddrCoords(location, type) {
    console.log(location);
    if(!location.trim()) {
        endLoading();
        alert('Address ' + type + ' is empty! Please, fill it.')
    } else {
        $.ajax({
            url: "api.php",
            method: 'GET',
            data: {
                request: 'getAddrCoords',
                location: location,
            },
            dataType: 'json'
        }).done(function (data) {
            console.log(data);
            if (!$.isEmptyObject(data)) {
                if(!apiError(data)) {
                    if (type == 'from') {
                        start_latitude = data.lat;
                        start_longitude = data.lng;
                        getAddrCoords($("#end_address").val(), 'to');
                    } else if (type == 'to') {
                        end_latitude = data.lat;
                        end_longitude = data.lng;
                        bounds = new google.maps.LatLngBounds();
                        addMarker({lat: parseFloat(start_latitude), lng: parseFloat(start_longitude)}, 'From')
                        addMarker({lat: parseFloat(end_latitude), lng: parseFloat(end_longitude)}, 'To')
                        map.fitBounds(bounds);
                        APIgetEstimate(start_latitude, start_longitude, end_latitude, end_longitude);
                    }
                }
            } else {
                endLoading()
                alert('Sorry, there was an error, please try again later. CODE: 101');
                return null;
            }
        });
    }
}

/**
 * Get estimate for user route
 *
 * @param icao
 * @constructor
 */
function APIgetEstimate(start_latitude, start_longitude, end_latitude, end_longitude) {
    $.ajax({
        url: "api.php",
        method: 'GET',
        data: {
            request: 'getEstimate',
            start_latitude: start_latitude,
            start_longitude: start_longitude,
            end_latitude: end_latitude,
            end_longitude: end_longitude,
        },
        dataType: 'json'
    }).done(function(data) {
        console.log(data);
        if(!$.isEmptyObject(data)) {
            if(!apiError(data)) {
                if(data.result.length > 0) {
                    $.each(data.result, function (index, value) {
                        var $provider = $('<div id="provider-' + index + '" class="col-md-3">' +
                            '<div class="panel panel-success">' +
                            '<div class="panel-heading">' + value.name +'</div>' +
                            '<div class="panel-body"> ' + value.estimate + '</div>' +
                            '</div>' +
                            '</div>');
                        console.log($provider.html());
                        $providers_container.append($provider);
                    });
                    endLoading();
                } else {
                    endLoading()
                    alert('Sorry, no results found');
                }
            }
        } else {
            endLoading()
            alert('Sorry, there was an error, please try again later. CODE: 102');
        }
    });
}

/**
 * Initiate Google Map
 */
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: -34.397, lng: 150.644},
        zoom: 8
    });
    /**
     * If zoom level from bounds is very low, zoom out to see the map
     */
    google.maps.event.addListener(map, 'zoom_changed', function() {
        zoomChangeBoundsListener =
            google.maps.event.addListener(map, 'bounds_changed', function(event) {
                if (this.getZoom() > minZoomLevel) {
                    // Change max/min zoom here
                    this.setZoom(minZoomLevel);
                    this.initialZoom = false;
                }
                google.maps.event.removeListener(zoomChangeBoundsListener);
            });
    });
}

/**
 * Shows api error
 *
 * @param data
 */
function apiError(data) {
    if(data.result == 'error') {
        alert(data.message);
        endLoading();
        return true;
    }
    return false;
}

/**
 * Hide loading object
 * Enable more searches
 */
function endLoading() {
    loading = false;
    $loading.hide();
    $("#start_address").prop('disabled', false);
    $("#end_address").prop('disabled', false);
}

/**
 * Show loading object
 * Block more searches
 */
function startLoading() {
    loading = true;
    $loading.show();
    $("#start_address").prop('disabled', true);
    $("#end_address").prop('disabled', true);
}