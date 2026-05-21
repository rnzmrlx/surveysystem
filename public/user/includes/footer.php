</main>

<footer id="footer" class="footer" style="
  background: var(--paper);
  border-top: 1px solid var(--paper-3);
  color: var(--ink-3);
">
  <div class="copyright" style="text-align:center; color: var(--ink-2);">
    &copy; Copyright <strong><span>QuickQuery</span></strong>. All Rights Reserved
  </div>
  <div class="credits" style="text-align:center; font-size:13px; color: var(--ink-2);">
    Designed by <a href="#" style="color: var(--gold); font-weight: bold;">Abilex</a>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const toggleBtn = document.getElementById('toggleSidebar');

  if (toggleBtn) {
    toggleBtn.addEventListener('click', function() {
      document.body.classList.toggle('sidebar-hidden');
    });
  }
</script>
<?php
if (isset($_SESSION['message']) && isset($_SESSION['code']) && $_SESSION['code'] != '') {
?>
  <script>
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer
        toast.onmouseleave = Swal.resumeTimer
      }
    });
    Toast.fire({
      icon: "<?php echo $_SESSION['code']; ?>",
      title: "<?php echo $_SESSION['message']; ?>"
    });
  </script>
<?php
  unset($_SESSION['message']);
  unset($_SESSION['code']);
}
?>
</body>

</html>