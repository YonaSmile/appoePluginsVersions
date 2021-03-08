function getInstagramTimelineFile() {
    return $.getJSON('/app/plugin/instagram/timeline.json');
}

function getInstagramTimeline(preferences) {
    let timeline = [];
    let options = {
        count: 5
    }
    $.extend(options, preferences);

    getInstagramTimelineFile().done(function(data){
        let instaJson = data.graphql.user.edge_owner_to_timeline_media.edges;
        for (let c = 0; c < options.count; c++) {
            let obj = instaJson[c].node;
            obj.link = data.link + obj.shortcode + '/';
            timeline.push(obj);
        }
        return timeline;
    });
}
