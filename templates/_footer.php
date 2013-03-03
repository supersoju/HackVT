</div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    
    <script src="/hackvt/templates/assets/js/bootstrap-transition.js"></script>
    <script src="/hackvt/templates/assets/js/bootstrap-alert.js"></script>
    <script src="/hackvt/templates/assets/js/bootstrap-modal.js"></script>
    <script src="/hackvt/templates/assets/js/bootstrap-dropdown.js"></script>
    <script src="/hackvt/templates/assets/js/bootstrap-scrollspy.js"></script>
    <script src="/hackvt/templates/assets/js/bootstrap-tab.js"></script>
    <script src="/hackvt/templates/assets/js/bootstrap-tooltip.js"></script>
    <script src="/hackvt/templates/assets/js/bootstrap-popover.js"></script>
    <script src="/hackvt/templates/assets/js/bootstrap-button.js"></script>
    <script src="/hackvt/templates/assets/js/bootstrap-collapse.js"></script>
    <script src="/hackvt/templates/assets/js/bootstrap-carousel.js"></script>
    <script src="/hackvt/templates/assets/js/bootstrap-typeahead.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
    <script type="text/javascript" src="/hackvt/templates/js/jquery.ui.map.js"></script>
    <script type="text/javascript">
    window.setTimeout(function() {
	    $(".alert").fadeTo(500, 0).slideUp(500, function(){
	        $(this).remove(); 
	    });
	}, 5000);
	
	function showPosition(position) {
    $('#gps-lat').val(position.coords.latitude);
    $('#gps-long').val(position.coords.longitude);
    $('#gps-heading').val(position.coords.heading);
    $('#gps-speed').val(position.coords.speed);
    $('#gps-altitude').val(position.coords.altitude);
    console.log('set location to ' + position.coords.latitude + ' ' + position.coords.longitude);
    refreshEntries();
    $('#gps-lat').hide();
    $('#gps-long').hide();
}
$(document).ready(function() {
    //$('#state').hide();
    //$('#city').hide();
    $('#gps-lat').hide();
    $('#gps-long').hide();

    console.log('document ready');
    if (navigator.geolocation) {
        console.log('got position!');
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        console.log('could not get position');
        refreshEntries();
        $('#gps-lat').hide();
        $('#gps-long').hide();
        $('#state').show();
        $('#city').show();
    };

           /*
            $(document).bind('deviceready', function(){

           onDeviceReady();
           refreshEntries();
           });
         */
});

$(document).bind("mobileinit", function(){
    console.log('mobileinit');
    refreshEntries();
});
$('#refresh').live('click', function() {
    console.log('clearing markers'); 
    $('#gps-lat').val('');
    $('#gps-long').val('');
    $('#search').val($('#searchvis').val());
    console.log("search vis: " + $('#searchvis').val());
    console.log('turning off lat/long');
    //$('#map_canvas').gmap('destroy');
    
    $('#map_canvas').gmap('clear', 'markers');
    $('#map_canvas').gmap('set', 'bounds', null);
    console.log('refreshing');

    refreshEntries();
});

$('#ingred a').live('click', function(){
    $('#search').val($(this).attr('id'));
    $('#map_canvas').gmap('clear', 'markers');
    $('#map_canvas').gmap('set', 'bounds', null);
    console.log($('#search').val());
    refreshEntries();
});

function refreshEntries() {
    console.log('refreshing entries');
    var latitude = $('#gps-lat').val();
    var longitude = $('#gps-long').val();
    console.log('lat is ' + latitude + ' long is ' + longitude);
    var state = $('#state').val();
    var city = $('#city').val();
    var search = $('#search').val();
    console.log("search: " + search);
    if(state == undefined){
        state = "VT";
    };
    if(city == undefined){
        city = "Winooski";
    };
    if(latitude == '') {
        var jsonstring = "/hackvt/json?city=" + city + "&search=" + search;
        console.log('using city / state');
        console.log('state is ' + state + ' city is ' + city);
    } else {
        var jsonstring = "/hackvt/json?latitude=" + latitude + "&longitude=" + longitude + "&search=" + search;
        console.log('using lat/long');
        console.log(jsonstring);
    };
    
    //$.getJSON("/json?city=" + city + "&state=" + state,function(data) {
    $.getJSON(jsonstring,function(data) {

        //$.getJSON("sample.json?callback=?", function(data) {
        //self.clear('markers');
        //			self.set('bounds', null);
        //
        console.log('we got data');
        console.log(data);
        if(data.count == 0){
            console.log("Sorry, no results found");
            //$('#map_canvas').html('<h3>Sorry no results found</h3>');
        } else {
            $.each(data, function(i,data) {
                var item_title = data.title;
                var item_lat = data.latitude;
                var item_long = data.longitude;
                var item_id = i;
                var item_distance = data.distance;
                var item_product = data.product;
                console.log("Drawing " + item_title);
                //$('#map_canvas').gmap().bind('init', function(ev, map) {
                $('#map_canvas').gmap('addMarker', {'position': item_lat + ',' + item_long, 'bounds': true}).click(function() {
                    $('#map_canvas').gmap('openInfoWindow', {'content': '<p><strong>' + item_title + '</strong><br />' + item_product + '<br />' + item_distance + ' miles away</p>'}, this);
                });
            });
            //});
            $('#map_canvas').gmap('refresh');
        };
    });
};

    </script>
  </body>
</html>
