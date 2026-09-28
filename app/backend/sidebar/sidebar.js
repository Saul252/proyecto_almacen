
document.addEventListener('click', function(e) {
    // Lógica para el botón de Toggle del Sidebar
    if (e.target.closest('#toggleSidebar')) {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('hidden');
            document.body.classList.toggle('sidebar-hidden');
        }
    }
});
