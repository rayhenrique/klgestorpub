@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: @json(html_entity_decode(session('success'), ENT_QUOTES)),
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção!',
                text: @json(html_entity_decode(session('warning'), ENT_QUOTES)),
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: @json(html_entity_decode(session('error'), ENT_QUOTES)),
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif
