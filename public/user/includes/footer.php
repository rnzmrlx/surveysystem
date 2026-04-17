</main>

<footer id="footer" class="footer">

  <style>
    .footer {
      background: var(--paper);
      border-top: 1.5px solid var(--paper-3);
      padding: 18px 20px;
      font-size: 13px;
      color: var(--ink-3);
      text-align: center;
    }

    .footer strong span {
      color: var(--ink);
    }

    .footer a {
      color: var(--gold);
      text-decoration: none;
      font-weight: 600;
    }

    .footer a:hover {
      color: var(--ink);
    }

    .back-to-top {
      position: fixed;
      right: 15px;
      bottom: 15px;
      width: 40px;
      height: 40px;
      background: var(--gold);
      color: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: var(--shadow);
      transition: 0.3s;
      z-index: 999;
    }

    .back-to-top:hover {
      background: var(--ink);
      color: #fff;
    }
  </style>

  <div class="copyright">
    &copy; <?php echo date('Y'); ?>
    <strong><span>QuickQuery</span></strong>. All Rights Reserved
  </div>

  <div class="credits">
    Designed by <a href="#">Abilex</a>
  </div>

</footer>

<!-- Back to top -->
<a href="#" class="back-to-top">
  <i class="bi bi-arrow-up-short"></i>
</a>

<!-- ===== VENDOR JS ===== -->
<script src="../../assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/vendor/chart.js/chart.umd.js"></script>
<script src="../../assets/vendor/echarts/echarts.min.js"></script>
<script src="../../assets/vendor/quill/quill.js"></script>
<script src="../../assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="../../assets/vendor/tinymce/tinymce.min.js"></script>
<script src="../../assets/vendor/php-email-form/validate.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- MAIN JS -->
<script src="../../assets/js/main.js"></script>

<!-- ===== SWEETALERT TOAST ===== -->
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
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    }
  });

  Toast.fire({
    icon: "<?= $_SESSION['code']; ?>",
    title: "<?= $_SESSION['message']; ?>"
  });
</script>
<?php
  unset($_SESSION['message']);
  unset($_SESSION['code']);
}
?>

</body>
</html>