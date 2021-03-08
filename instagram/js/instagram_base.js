function getInstagramTimelineFile() {
    return $.getJSON('/app/plugin/instagram/timeline.json');
}

function showInstagramTimeline(preferences = {}) {

    let options = {
        count: 5,
        container: '#instagramTimelineContainer',
        thumbnail: false
    }
    $.extend(options, preferences);
    let obj;
    let $timelineContainer = $(options.container);

    getInstagramTimelineFile().done(function (timeline) {
        if (timeline) {
            for (let c = 0; c < options.count; c++) {
                if (timeline.data[c]) {
                    obj = timeline.data[c];
                    if (obj.media_type !== 'VIDEO') {
                        let imgUrl = (options.thumbnail && obj.thumbnail_url) ? obj.thumbnail_url : obj.media_url;
                        let img = '<img src="' + imgUrl + '" alt="' + obj.caption + '">';
                        let item = '<a href="' + obj.permalink + '" target="_blank">' + img + '</a>';
                        $timelineContainer.append(item);
                    }
                }
            }
        }
    });
}