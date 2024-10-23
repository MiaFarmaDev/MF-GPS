<div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <div id="map" style="height: 300px;"></div>

    <!-- Botón para verificar la ubicación -->
    <button type="button" id="verificar-ubicacion" class="btn btn-primary" onclick="getLocation()">Verificar ubicación</button>

    <!-- Inputs que mostrarán la latitud y longitud -->
    <div style="margin-top: 10px;">
        <label>Latitud:</label>
        <input type="text" id="latitud" wire:model.live="latitud" >

        <label>Longitud:</label>
        <input type="text" id="longitud" wire:model.live="longitude" >
    </div>
    @push('scripts')
    <script>
        // Inicializa el mapa
        var map = L.map('map').setView([0, 0], 0); // Coordenadas iniciales

        // Carga las capas del mapa (puedes cambiar la fuente si prefieres otra)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Inicializa un marcador en el mapa
        var marker = L.marker([0, 0]).addTo(map);

        // Función para obtener la ubicación y actualizar el marcador
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;

                    // Asigna las coordenadas a los inputs
                    document.getElementById('latitud').value = lat;
                    document.getElementById('longitud').value = lng;

                    // Mueve el mapa y el marcador a las nuevas coordenadas
                    map.setView([lat, lng], 19); // Cambia el zoom según prefieras
                    marker.setLatLng([lat, lng]);

                    // Despacha el evento con las coordenadas a Livewire
                    console.log('Emitir evento location-updated con coordenadas:', { lat, lng });
                    document.addEventListener('livewire:init', () => {
                        console.log('Emitir evento location-updated con coordenadas:', { lat, lng });
                        $wire.dispatch('location-updated', {
                            lat: lat,
                            lng: lng
                        });
                    });
                });
            } else {
                alert("No es posible verificar la ubicación.");
            }
        }

        // Verificar si se recibe el evento en JavaScript
        document.addEventListener('livewire:init', () => {
            Livewire.on('location-updated', (event)=>{
                console.log('Evento location-updated recibido en JavaScript:', { lat, lng });});
                    });
      
        // $wire.on('location-updated', (lat, lng) => {
        //     console.log('Evento location-updated recibido en JavaScript:', { lat, lng });
        // });
    </script>
    @endpush
</div>
