</main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Toggle Sidebar
  const sidebar = document.getElementById('sidebar');
  document.getElementById('btnToggleSidebar').addEventListener('click', () => {
    if (window.innerWidth < 992) {
      sidebar.classList.toggle('buka-hp');
    } else {
      sidebar.classList.toggle('tutup');
    }
  });

  // Toggle Dark / Light Mode
  const btnTema = document.getElementById('btnTema');
  const ikonTema = document.getElementById('ikonTema');

  function perbaruiIkon(tema) {
    ikonTema.className = tema === 'dark' ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill';
  }
  perbaruiIkon(document.documentElement.getAttribute('data-bs-theme'));

  btnTema.addEventListener('click', () => {
    const sekarang = document.documentElement.getAttribute('data-bs-theme');
    const baru = sekarang === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-bs-theme', baru);
    localStorage.setItem('tema_eco', baru);
    perbaruiIkon(baru);
  });
</script>
</body>
</html>