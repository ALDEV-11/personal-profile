  <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; 2025 Personal Profile. All rights reserved.</p>
                <div class="footer-social">
                    <?php foreach ($socialMedia as $social): ?>
                        <a href="<?php echo htmlspecialchars($social['url']); ?>" target="_blank" title="<?php echo htmlspecialchars($social['platform']); ?>">
                            <i class="<?php echo htmlspecialchars($social['icon']); ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </footer>