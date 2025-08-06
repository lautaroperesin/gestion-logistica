            </main>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Función para mostrar alertas
        function showAlert(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.main-content');
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Confirmación para eliminar
        function confirmDelete(message = '¿Está seguro de que desea eliminar este elemento?') {
            return confirm(message);
        }
    </script>
</body>
</html>

