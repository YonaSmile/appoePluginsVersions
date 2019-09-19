function leaflet_getMap(lngLat, zoom) {

    //Create Map
    var map = new L.Map('mapOSM', {
        scrollWheelZoom: false,
        zoom: 16,
        maxZoom: 22,
        gestureHandling: true
    }).setView(lngLat, zoom);

    //Scroll protection

    //Add tiles
    map.addLayer(new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '<a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
        maxZoom: 22,
        maxNativeZoom:22
    }));

    return map;
}

function leaflet_getGps(map) {

    new L.Control.Gps({

        //autoActive:true,
        autoCenter: true

    }).addTo(map);
}

function leaflet_developpement(map) {

    var popup = L.popup();

    function onMapClick(e) {
        popup
            .setLatLng(e.latlng)
            .setContent("Position : " + e.latlng.toString())
            .openOn(map);
    }

    map.on('click', onMapClick);
}

function leaflet_showImg(map, imgSrc = '/app/lib/template/images/logo_app.png', imgWidth = '128px', onclickUrl = 'https://aoe-communication.com', position = 'bottomleft') {

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

function leaflet_aoe(map) {

    var marker = new L.Marker([48.585863, 7.763], {title: 'Art Of Event - Communication'}).addTo(map);

    //marker.bindPopup("<b>Art Of Event</b><br>Communication").openPopup();
    marker.bindTooltip("<b>Art Of Event</b><br>Communication");

    var circle = new L.Circle([48.585863, 7.763], {
        color: '#fff',
        fillColor: '#fff',
        fillOpacity: 0.5,
        radius: 2500
    }).addTo(map);

    var latlngs = [
        [
            [48.565703, 7.63588],
            [48.603177, 7.659249],
            [48.565703, 7.695325]
        ],
        [
            [48.57973, 7.644644],
            [48.580383, 7.68042]
        ],
        [
            [48.603177, 7.858318],
            [48.603177, 7.825506],
            [48.566384, 7.825506],
            [48.566157, 7.857414]
        ],
        [
            [48.584784, 7.825117],
            [48.584784, 7.856727]
        ]
    ];
    map.addLayer(new L.Polyline(latlngs, {color: '#FFF'}));
}