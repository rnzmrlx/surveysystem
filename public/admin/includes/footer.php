</main>

<footer id="footer" class="footer" style="
  margin-left: 260px;
  transition: all 0.3s;
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

<style>
.toggle-sidebar #footer {
  margin-left: 0 !important;
}
@media (max-width: 1199px) {
  #footer {
    margin-left: 260px;
    transition: all 0.3s;
  }
}
@media (max-width: 1199px) {
  body.sidebar-hidden #footer {
    margin-left: 0 !important;
  }
}
</style>

<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/chart.js/chart.umd.js"></script>
<script src="assets/vendor/echarts/echarts.min.js"></script>
<script src="assets/vendor/quill/quill.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/main.js"></script>

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