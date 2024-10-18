<script>
    document.addEventListener('DOMContentLoaded', function () {
        const botonVerificar = document.getElementById('verificar-ubicacion');

        if (botonVerificar) {
            botonVerificar.addEventListener('click', function () {
                if ('geolocation' in navigator) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        var latitude = position.coords.latitude;
                        var longitude = position.coords.longitude;

                        document.getElementById('latitude').value = latitude;
                        document.getElementById('longitude').value = longitude;

                        console.log('Ubicación verificada: ', latitude, longitude);
                    }, function (error) {
                        alert('Error al obtener la ubicación: ' + error.message);
                    });
                } else {
                    alert('Geolocalización no está disponible en este navegador');
                }
            });
        }
    });
</script>
