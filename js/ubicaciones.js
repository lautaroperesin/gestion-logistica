document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado');
    // Evento para cuando cambia el país
    document.getElementById('id_pais').addEventListener('change', function() {
        const paisId = this.value;
        console.log('paisId', paisId);
        const provinciaSelect = document.getElementById('id_provincia');
        const localidadSelect = document.getElementById('id_localidad');

        // Limpiar selects de provincia y localidad
        provinciaSelect.innerHTML = '<option value="">Seleccione una provincia</option>';
        localidadSelect.innerHTML = '<option value="">Seleccione una localidad</option>';

        if (paisId) {
            // Cargar provincias
            fetch(`?route=getProvinciasByPais&id_pais=${paisId}`, {
                method: 'GET',
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(provincias => {
                    console.log('Provincias recibidas:', provincias);
                    if (Array.isArray(provincias)) {
                        provincias.forEach(provincia => {
                            const option = document.createElement('option');
                            option.value = provincia.id_provincia;
                            option.textContent = provincia.provincia;
                            provinciaSelect.appendChild(option);
                        });
                    } else {
                        console.error('La respuesta no es un array:', provincias);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    provinciaSelect.innerHTML = '<option value="">Error al cargar provincias</option>';
                });
        }
    });

    // Evento para cuando cambia la provincia
    document.getElementById('id_provincia').addEventListener('change', function() {
        const provinciaId = this.value;
        const localidadSelect = document.getElementById('id_localidad');

        // Limpiar select de localidad
        localidadSelect.innerHTML = '<option value="">Seleccione una localidad</option>';

        if (provinciaId) {
            // Cargar localidades
            fetch(`?route=getLocalidadesByProvincia&id_provincia=${provinciaId}`, {
                method: 'GET',
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(localidades => {
                    console.log('Localidades recibidas:', localidades);
                    if (Array.isArray(localidades)) {
                        localidades.forEach(localidad => {
                            const option = document.createElement('option');
                            option.value = localidad.id_localidad;
                            option.textContent = localidad.localidad;
                            localidadSelect.appendChild(option);
                        });
                    } else {
                        console.error('La respuesta no es un array:', localidades);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    localidadSelect.innerHTML = '<option value="">Error al cargar localidades</option>';
                });
        }
    });
});
