</main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $baseUrl ?>/assets/js/main.js?v=<?= time() ?>"></script>
<script src="<?= $baseUrl ?>/assets/js/layout.js?v=<?= time() ?>"></script>
<?php if (!empty($script)): ?>
    <script src="<?= $baseUrl ?>/assets/js/<?= htmlspecialchars($script, ENT_QUOTES, 'UTF-8') ?>?v=<?= time() ?>"></script>
<?php endif; ?>
</body>
</html>
