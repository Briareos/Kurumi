$(document).ready(function () {
    "use strict";

    var labels = [], mapped = {}, timezone;
    $('#user_register_profile_city_fullName').typeahead({
        source:function (query, process) {
            $.getJSON('http://ws.geonames.org/searchJSON', {
                username:"grayfox",
                lang:"en",
                style:"full",
                featureClass:"P",
                maxRows:8,
                country:"RS",
                name_startsWith:query.split(',').shift()
            }, function (data) {
                labels = [];
                mapped = {};
                $.each(data.geonames,
                    function (i, item) {
                        if (item.countryCode === "US") {
                            item.label = item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", US";
                        } else {
                            item.label = item.name + ", " + item.countryName;
                        }
                        labels.push(item.label);
                        mapped[item.label] = item;
                    }).sort(function (a, b) {
                        if (a.population === b.population) {
                            return 0;
                        } else {
                            return (a.population < b.population) ? 1 : -1;
                        }
                    });
                process(labels);
            });
        }, updater:function (item) {
            var data = mapped[item];
            return item;
        }
    });

});