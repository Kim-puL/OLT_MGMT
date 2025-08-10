</div> <!-- Close container div -->
<footer class="bg-dark text-white py-3 mt-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5><i class="bi bi-router"></i> OLT Monitoring</h5>
                <p class="mb-0">Sistem monitoring OLT berbasis web untuk manajemen jaringan GPON.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">&copy; <?= date('Y') ?> Monitoring OLT. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    setTimeout(() => {
        location.reload();
    }, 300000); // 5 menit dalam milidetik
</script>
</body>
</html>