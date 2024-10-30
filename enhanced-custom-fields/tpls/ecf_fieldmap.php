<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>

<?php if(!empty($this->value)) : ?>
	<input type="hidden" name="<?php echo $this->name?>" value="<?php echo $this->value; ?>" id="<?php echo $this->name?>" />
<?php else: ?>
	<input type="hidden" name="<?php echo $this->name?>" value="" id="<?php echo $this->name?>" />
<?php endif ?>
<div class="cl">&nbsp;</div>
<div id="map_<?php echo $this->name?>" style="width: 500px; height: 300px; border: solid 2px #dfdfdf; overflow: hidden;"></div>
<div class="cl" style="height: 10px;">&nbsp;</div>
Double click on the map and a marker will appear. Drag &amp; Drop the marker to new position<br />on the map. This is handy if you do not have a specific address for the location, or want to<br />specify a general area rather than a specific one.<br />
Or enter address: <input type="text" id="<?php echo $this->name?>_address" value="" style="width: 335px;"/> <input id="<?php echo $this->name?>_button" type="button" class="button" value="Search" />
<script type="text/javascript" charset="utf-8">
    var geocoder = new google.maps.Geocoder();
    (function($){
        $('#<?php echo $this->name?>_button').click(function() {
            if ($(this).is(':disabled')) {
                return false;
            }

            if (!$('#<?php echo $this->name?>_address').val()) {
                return false;
            }

            $('#<?php echo $this->name?>_button').attr('disabled', 'disabled').val('Searching');
            geocoder.geocode( {'address': $('#<?php echo $this->name?>_address').val() }, function(results, status) {
                if (results && results[0]) {
                    var location = results[0];
                    var lat = location.geometry.location.lat();
                    var lng = location.geometry.location.lng();
                    $('#<?php echo $this->name?>').val(lat.toString() + ',' + lng.toString());
                    set_coords(location.geometry.location);
                    map_<?php echo $this->name?>.setCenter(location.geometry.location);
                } else {
                    alert('Could not locate address.');
                }
                $('#<?php echo $this->name?>_button').removeAttr('disabled').val('Search');
            })
        });
        $('#<?php echo $this->name?>_address').keydown(function(e) {
            if (e.keyCode == 13) {
                $('#<?php echo $this->name?>_button').click();
                return false;
            }
            return true;
        });
    })(jQuery);

	<?php if(!empty($this->value)) : ?>
        v = '<?php echo $this->value; ?>'.split(',');
        var latlng = new google.maps.LatLng(v[0],v[1]);
        var zoom = <?php echo $this->zoom; ?>;
        if (v.length > 2) {
            zoom = v[2];
        }

	<?php else: ?>
        var zoom = <?php echo $this->zoom; ?>;
        var latlng = new google.maps.LatLng(<?php echo $this->lat?>, <?php echo $this->long?>);
	<?php endif; ?>	
    var myOptions = {
        zoom: parseInt(zoom),
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableDoubleClickZoom: true,
        panControl: true,
        zoomControl: true,
        mapTypeControl: true,
        scaleControl: true,
        streetViewControl: false,
        overviewMapControl: true
    };
    var map_<?php echo $this->name?> = new google.maps.Map(document.getElementById("map_<?php echo $this->name?>"), myOptions);

    <?php if(!empty($this->value)) : ?>
        var marker = new google.maps.Marker({
            position: latlng,
            map: map_<?php echo $this->name?>,
            draggable: true
        });
        google.maps.event.addListener(marker, 'dragend', change_coords);     
    <?php else: ?>
        var marker = null;
    <?php endif; ?>  

	function change_coords(point) {
        latLng = marker.getPosition();
        if (point) {
            latLng = point;
        }
        document.getElementById("<?php echo $this->name?>").value = latLng.lat() + "," + latLng.lng() + "," + map_<?php echo $this->name?>.getZoom();
	}
	function set_coords(point) {
        if (marker != null) {
            marker.setMap(null);
        }
        if (point) {
            if (point.latLng) {
                latLng = point.latLng;
            } else {
                latLng = point;
            }
            
            marker = new google.maps.Marker({
                position: latLng,
                map: map_<?php echo $this->name?>,
                draggable: true
            });
            google.maps.event.addListener(marker, 'dragend', change_coords);
            change_coords(latLng);
        }
        return false;
	}
    function get_map() {
        return map_<?php echo $this->name?>;
    }
    google.maps.event.addListener(map_<?php echo $this->name?>, "dblclick", set_coords);
    google.maps.event.addListener(map_<?php echo $this->name?>, "zoom_changed", change_coords);
</script>
