</main>

<footer id="footer" class="footer">
  <div class="copyright">
    &copy; Copyright <strong><span>QuickQuery</span></strong>. All Rights Reserved
  </div>
  <div class="credits">
    Designed by <a href="#" style="color: var(--gold); font-weight: bold;">Abilex</a>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const toggleBtn = document.getElementById('toggleSidebar');

  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      document.body.classList.toggle('sidebar-hidden');
    });
  }
</script>

</body>
</html>