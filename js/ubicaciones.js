document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado');
    
    const paisSelect = document.getElementById('id_pais');
    const provinciaSelect = document.getElementById('id_provincia');
    const localidadSelect = document.getElementById('id_localidad');
    
    // Evento para cuando cambia el país
    if (paisSelect) {
        paisSelect.addEventListener('change', function() {
            const paisId = this.value;
            console.log('paisId', paisId);
            
            // Limpiar selects de provincia y localidad
            if (provinciaSelect) provinciaSelect.innerHTML = '<option value="">Seleccione una provincia</option>';
            if (localidadSelect) localidadSelect.innerHTML = '<option value="">Seleccione una localidad</option>';
            
            // Si no hay país seleccionado, no hacemos nada más
            if (!paisId) return;

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
                            option.value = provincia.id_provincia || provincia.id; // Asegurar compatibilidad con ambos formatos
                            option.textContent = provincia.provincia;
                            // Marcar como seleccionada si coincide con el valor guardado
                            if (provinciaSelect.dataset.selected && provinciaSelect.dataset.selected === option.value) {
                                option.selected = true;
                            }
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
    })};

    // Evento para cuando cambia la provincia
    if (provinciaSelect) {
        provinciaSelect.addEventListener('change', function() {
            const provinciaId = this.value;
            
            // Limpiar select de localidad
            if (localidadSelect) {
                localidadSelect.innerHTML = '<option value="">Seleccione una localidad</option>';
            }

            if (provinciaId && localidadSelect) {
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
                                option.value = localidad.id_localidad || localidad.id; // Asegurar compatibilidad con ambos formatos
                                option.textContent = localidad.localidad;
                                // Marcar como seleccionada si coincide con el valor guardado
                                if (localidadSelect.dataset.selected && localidadSelect.dataset.selected === option.value) {
                                    option.selected = true;
                                }
                                localidadSelect.appendChild(option);
                            });
                        } else {
                            console.error('La respuesta no es un array:', localidades);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (localidadSelect) {
                            localidadSelect.innerHTML = '<option value="">Error al cargar localidades</option>';
                        }
                    });
            }
        });
    }
});
