function getInteractiveMap(filename, element, options = []) {

    var interMapOptions = {
        source: '/app/plugin/interactiveMap/'+filename+'.json',
        sidebar: false, 			// Enable sidebar
        search: false,
        minimap: false, 			// Enable minimap
        markers: false, 		// Disable markers
        fillcolor: '', 		// Disable default fill color
        fullscreen: true, 		// Enable fullscreen
        maxscale: 3, 			// Setting maxscale to 3 times bigger than the original file
        developer: false,
        mapfill: true,
        lightbox: true,
        landmark: true,
        action: 'tooltip', //tooltip, open-link, open-link-new-tab, lightbox and none
        tooltip: {
            thumb: true,
            desc: true,
            link: true
        }
    };

    $.extend( interMapOptions, options );

    $(element).mapplic(interMapOptions);
}