(function() {
    if (document.getElementById("map") !== null) {
        var initialize = function initialize() {
            //create an array with points each contains [0 => description, 1 => latitude, 2 => longitude]
            var points = [],
                mapCanvas = document.getElementById("map"),
                counter = 0;
            while (mapCanvas.getAttribute("data-description-" + (++counter))) {
                points.push([
                    mapCanvas.getAttribute("data-description-" + counter),
                    mapCanvas.getAttribute("data-latitude-" + counter),
                    mapCanvas.getAttribute("data-longitude-" + counter)
                ]);
            };
            
            //create map with and initial properties
            var mapBounds = new google.maps.LatLngBounds(),
                mapProp = {
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    minZoom: 3,
                    maxZoom: 15,
                    scrollwheel: false
                },
                map = new google.maps.Map(mapCanvas, mapProp);
            
            //set markers with infowindows and extend boundaries
            var markers = [],
                coords,
                myinfowindow,
                length = points.length,
                content;
            while (length--) {
                //create coords
                coords = new google.maps.LatLng(points[length][1], points[length][2]);
                
                //create content for myinfowindow
                content = "<div style='max-width:150px;'>" + points[length][0] + "</div>";
                
                //create infowindow and put content
                myinfowindow = new google.maps.InfoWindow({
                    content: content
                });
                
                //extend bounds
                mapBounds.extend(coords);
                
                //create marker and put it on map
                markers[length] = new google.maps.Marker({
                    position: coords,
                    infowindow: myinfowindow
                });
                markers[length].setMap(map);
                
                //when one of infowindows is clicked close the rest
                google.maps.event.addListener(markers[length], 'click', function() {
                    length = points.length;
                    while (length--){
                        markers[length].infowindow.close();
                    }
                    this.infowindow.open(map, this);
                });
            }
            
            //apply extended map boundaries
            map.fitBounds(mapBounds);
        }();
    }
})();
