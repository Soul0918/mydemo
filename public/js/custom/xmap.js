var map, geocoder, marker, citylocation = null;

var xMap = {
    loadScript: function () {
        var key = 'IOLBZ-UAPKP-H56DG-VGYTB-UD2FT-FYBDF';
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'http://map.qq.com/api/js?v=2.exp&key=' + key + '&callback=init';
        document.body.appendChild(script);
    },
    getGeocoder: function () {
        return geocoder || {};
    },
    setLocation: function (location) {}
};

window.xMap = xMap;

function init () {
    var myLatlng = new qq.maps.LatLng(39.916527,116.397128);
    var myOptions = {
        zoom: 15,
        center: myLatlng,
        mapTypeId: qq.maps.MapTypeId.ROADMAP
    };
    map = new qq.maps.Map(document.getElementById('map-container'), myOptions);

    geocoder = new qq.maps.Geocoder({
        complete : function(result){
            map.setCenter(result.detail.location);
            xMap.setLocation(result.detail.location);
            if (marker != null) {
                marker.setMap(null);
            }
            marker = new qq.maps.Marker({
                map:map,
                position: result.detail.location
            });
        }
    });

    // 调用城市服务信息
    citylocation = new qq.maps.CityService({
        complete : function(results){
            map.setCenter(results.detail.latLng);
            xMap.setLocation(results.detail.latLng);
            if (marker != null) {
                marker.setMap(null);
            }
            //设置marker标记
            marker = new qq.maps.Marker({
                map: map,
                position: results.detail.latLng
            });
        }
    });

    // 地图绑定click事件
    qq.maps.event.addListener(
        map,
        'click',
        function (event) {
            var lat = event.latLng.getLat();
            var lng = event.latLng.getLng();
            var latLng = new qq.maps.LatLng(lat , lng);
            citylocation.searchCityByLatLng(latLng);
        }
    );
}