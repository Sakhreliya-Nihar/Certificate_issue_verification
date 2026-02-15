document.addEventListener('DOMContentLoaded', function() {
  const sidebarToggle = document.querySelector('.sidebar-toggle');
  const sidebar = document.querySelector('.sidebar');
  const sidebarheader = document.querySelector('.sidebar-header');

  sidebarToggle.addEventListener('click', function() {
      sidebar.classList.toggle('active');      
  });
});




