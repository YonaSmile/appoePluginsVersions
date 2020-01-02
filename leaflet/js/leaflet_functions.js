function leaflet_getMap(lngLat, zoom, otherTile = '') {

    if ($('#mapOSM').length) {

        //Create Map
        var map = new L.Map('mapOSM', {
            scrollWheelZoom: false,
            zoom: 16,
            maxZoom: 22,
            gestureHandling: true
        }).setView(lngLat, zoom);

        //Add tiles
        map.addLayer(new L.TileLayer(!otherTile.trim() ? 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png' : otherTile, {
            attribution: '<a href="https://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> | <a href="https://aoe-communication.com" target="_blank" title="Art Of Event - Communication">AOE</a>',
            maxZoom: 22,
            maxNativeZoom: 22
        }));

        return map;
    }
}

function leaflet_getGps(map) {

    if ($('#mapOSM').length) {

        new L.Control.Gps({

            //autoActive:true,
            autoCenter: true

        }).addTo(map);
    }
}

function leaflet_developpement(map) {

    if ($('#mapOSM').length) {
        var popup = L.popup();

        function onMapClick(e) {
            popup
                .setLatLng(e.latlng)
                .setContent("Position : " + e.latlng.toString())
                .openOn(map);
        }

        map.on('click', onMapClick);
    }
}

function leaflet_simpleMarker(map, lngLat, title, imgSrc) {

    if ($('#mapOSM').length) {

        var marker = L.marker(lngLat, {title: title}).addTo(map);
        marker.bindPopup('<img src="' + imgSrc + '" alt="' + title + '">', {minWidth: 100}).openPopup();
        map.invalidateSize();
    }
}

function leaflet_showImg(map, imgSrc = '/app/lib/template/images/logo_app.png', imgWidth = '128px', onclickUrl = 'https://aoe-communication.com', position = 'bottomleft') {

    if ($('#mapOSM').length) {

        L.Control.Watermark = L.Control.extend({

            onAdd: function (map) {

                var img = L.DomUtil.create('img');
                img.src = imgSrc;
                img.style.width = imgWidth;

                L.DomEvent.on(img, 'click', function () {
                    window.open(onclickUrl, '_blank');
                });

                return img;
            },

            onRemove: function (map) {
            }
        });

        L.control.watermark = function (opts) {
            return new L.Control.Watermark(opts);
        };

        L.control.watermark({position: position}).addTo(map);
    }
}

function leaflet_aoe(map) {

    if ($('#mapOSM').length) {

        var marker = new L.Marker([48.585863, 7.763], {title: 'Art Of Event - Communication'}).addTo(map);
        marker.bindTooltip("<b>Art Of Event</b><br>Communication");
    }
}