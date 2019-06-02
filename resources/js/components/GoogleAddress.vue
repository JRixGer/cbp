<template>
  <input ref="autocomplete" 
         placeholder="Enter your address" 
         type="text"
         :value="value"
  />
</template>
<style type="text/css">
    .pac-container{
        z-index: 99999;
    }
</style>
<script>
  export default {
    props: ['value'],
    mounted() {
        var returnData = {
             street_number: '',
                route: '',
                locality: '',
                administrative_area_level_1: '',
                administrative_area_level_5: '',
                country: '',                
                postal_code: ''
        };
        var autocomplete = new google.maps.places.Autocomplete(
        (this.$refs.autocomplete),
        {types: ['geocode']});

        this.$refs.autocomplete.addEventListener('keydown', (e) => {
          if (e.keyCode === 13) { 
            e.preventDefault(); 
          }
        })

        this.$refs.autocomplete.addEventListener('blur', (e) => {

            returnData.route = e.target.value;
            returnData.street_number = '';

            this.$emit('placechanged', returnData);

        });

        autocomplete.addListener('place_changed', () => {
            let place = autocomplete.getPlace();
            let returnData = [];


            if (!place.geometry) {
                return;
            }

            let addressComponents = {
                street_number: 'short_name',
                route: 'long_name',
                locality: 'long_name',
                postal_town: 'long_name',
                administrative_area_level_1: 'short_name',
                administrative_area_level_5: 'short_name',
                country: 'short_name',                
                postal_code: 'short_name'
            };
        
            if (place.address_components !== undefined) {
                console.log("place.address_components")
                console.log(place.address_components)
                for (let i = 0; i < place.address_components.length; i++) {
                  
                  let addressType = place.address_components[i].types[0];
                  

                  if (addressComponents[addressType]) {

                    let val = place.address_components[i][addressComponents[addressType]];
                        returnData[addressType] = val;
                  }
                }

                returnData['latitude'] = place.geometry.location.lat();
                returnData['longitude'] = place.geometry.location.lng();
            }

            this.$emit('placechanged', returnData, place);

        })
    },

    method: {

        geolocate() {
            if (this.enableGeolocation) {
                if (navigator.geolocation) {
                  navigator.geolocation.getCurrentPosition(position => {
                    let geolocation = {
                      lat: position.coords.latitude,
                      lng: position.coords.longitude
                    };
                    let circle = new google.maps.Circle({
                      center: geolocation,
                      radius: position.coords.accuracy
                    });
    
                  });
                }
            }
        }


    }
  }
</script>