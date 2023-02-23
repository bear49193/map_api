<!DOCTYPE html>
<html>
<head>
  <script type="text/javascript"
    src="http://maps.google.com/maps/api/js?key=AIzaSyATQfgciiyXbclz4wv0b4h3Qrfw7wmuJi0">
  </script>

  <script type="text/javascript">
  var geocoder;
  var map;
  var infowindow = new google.maps.InfoWindow();
  function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = {
      zoom: 4,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    
  }

  function codeAddress() {
    var address = document.getElementById("address").value;
    var bounds = new google.maps.LatLngBounds();

    var node  = document.getElementById("searched_marks");
    if(node!==null) {document.body.removeChild(node);}
    node  = document.getElementById("added_marks");
    if(node!==null) {document.body.removeChild(node);}
    //var btn = document.getElementById("add_mark");
    //btn.removeAttribute('disabled');
    
    var newNode = document.createElement("p");
    newNode.setAttribute('id', 'searched_marks');

    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);

        for(var i=0; i<results.length; i++) {
          var addr = results[i].formatted_address + "<br />lat = " + results[i].geometry.location.lat()+ "  lng = " + results[i].geometry.location.lng() + "<br /><br />";
          
          createMarker(results[i].geometry.location, results[i].formatted_address, addr);

          bounds.extend(results[i].geometry.location);
          
          newNode.innerHTML += addr;
        }

        map.fitBounds(bounds);
        document.body.appendChild(newNode);
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }

  function toggleBounce(marker) {
    if (marker.getAnimation() !== null) {
      marker.setAnimation(null);
    } else {
      marker.setAnimation(google.maps.Animation.BOUNCE);
    }
  }

  function addInfo(marker) {
    var newNode  = document.getElementById("added_marks");
    if(!newNode) {
      newNode = document.createElement("div");
      newNode.setAttribute('id', 'added_marks');
    }
    var childNode = document.getElementById('added_marks' + marker.__gm_id);
    if(!childNode) {
      childNode = document.createElement("p");
      childNode.setAttribute('id', 'added_marks' + marker.__gm_id);
    }
    
    newNode.appendChild(childNode);

    geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
      if(status == google.maps.GeocoderStatus.OK) {
        if(results[1]) {
          var addr = results[1].formatted_address + "<br />lat = " +  marker.getPosition().lat() + "  lng = " + marker.getPosition().lng();
          infowindow.setContent(addr);
          infowindow.open(map, marker);

          childNode.innerHTML = "<hr/>" + addr;
          document.body.appendChild(newNode);
        }
      } else {
        alert("Geocoder failed due to: " + status);
      }
    });
  }

  function createMarker(position, title, html) {
    var marker = new google.maps.Marker({
      map: map,
      position: position,
      title: title
    });
    google.maps.event.addListener(marker, 'click', function () {
      infowindow.setContent(html);
      infowindow.open(map, marker);
    });
  }

  function addMark() {
    marker = new google.maps.Marker({
      map: map,
      draggable: true,
      animation: google.maps.Animation.DROP,
      position: map.center,
      title: 'drag me!'
    });
    listenerMaker(marker);
  }

  function listenerMaker(marker) {
    google.maps.event.addListener(marker, 'click', function() { return toggleBounce(marker); });
    google.maps.event.addListener(marker, 'dragend', function() { return addInfo(marker); });
  }
  </script>

  <body style="background: linear-gradient(to right, #3a1c71, #d76d77, #ffaf7b); color: green;" onload="initialize()">
  <div style="padding-left: 20px;">
    <h2> Business information</h2>
  </div>
  <div style="padding: 20px;">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name">
    <label style="padding-left: 10px;" for="address">Address:</label>
    <input id="address" type="textbox" value="">
    <label style="padding-left: 10px;"for="phone">Phone:</label>
    <input type="text" id="phone" name="phone">
    <input style="border-width: 1px; background: linear-gradient(to bottom, #4ed5bd, #41b799, #1c704d);" type="button" value="submit" onclick="codeAddress()">
    <button style="margin-left: 200px; border-width: 1px; background: linear-gradient(to bottom, #7e4ed5, #d76d77, #ffaf7b);" id="add_mark" onclick="addMark()" type="button">add mark</button>
  </div>
 
  <div id="map_canvas" style="width: 100%; height: 450px;"></div>
  </body>
  </head>
</html>