<?php
/**
 * Rodapé Compartilhado
 */
?>
            </div> <!-- .content-wrapper -->
        </main> <!-- .main-content -->
    </div> <!-- .app-container -->
    
    <!-- Scripts Principais -->
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
    <?php if (isAdmin()): ?>
    <script src="<?= BASE_URL ?>/assets/js/admin.js"></script>
    <?php endif; ?>
</body>
</html>
