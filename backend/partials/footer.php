    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Admin JS -->
    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.querySelector('.navbar-toggler');
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 768) {
                    if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
            
            // Auto dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
            
            // Confirm delete
            const deleteButtons = document.querySelectorAll('.btn-delete, .delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Apakah Anda yakin ingin menghapus data ini? Data yang dihapus tidak dapat dikembalikan.')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Preview image before upload
            const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
            imageInputs.forEach(input => {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const preview = document.getElementById('imagePreview');
                            if (preview) {
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
            
            // Character counter for textareas
            const textareas = document.querySelectorAll('textarea[maxlength]');
            textareas.forEach(textarea => {
                const maxLength = textarea.getAttribute('maxlength');
                const counter = document.createElement('div');
                counter.className = 'text-muted mt-1';
                counter.style.fontSize = '0.875rem';
                counter.innerHTML = `<span class="char-count">0</span> / ${maxLength} karakter`;
                textarea.parentNode.appendChild(counter);
                
                textarea.addEventListener('input', function() {
                    const count = this.value.length;
                    counter.querySelector('.char-count').textContent = count;
                    
                    if (count > maxLength * 0.9) {
                        counter.classList.add('text-warning');
                    } else {
                        counter.classList.remove('text-warning');
                    }
                });
            });
            
            // Form validation
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
            
            // Loading state for buttons
            const submitButtons = document.querySelectorAll('button[type="submit"]');
            submitButtons.forEach(button => {
                button.closest('form').addEventListener('submit', function() {
                    button.disabled = true;
                    const originalText = button.innerHTML;
                    button.innerHTML = '<span class="loading"></span> Loading...';
                    
                    // Re-enable after 5 seconds (in case of error)
                    setTimeout(() => {
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }, 5000);
                });
            });
        });
        
        // Helper function: Format number
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Helper function: Truncate text
        function truncateText(text, length) {
            if (text.length > length) {
                return text.substring(0, length) + '...';
            }
            return text;
        }
        
        // Helper function: Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Berhasil disalin ke clipboard!');
            }).catch(err => {
                console.error('Gagal menyalin:', err);
            });
        }
    </script>
    
    <!-- Autocomplete Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing autocomplete...');
            
            // Autocomplete functionality for search inputs
            const autocompleteInputs = document.querySelectorAll('input[data-autocomplete-url]');
            console.log('Found ' + autocompleteInputs.length + ' autocomplete inputs');
            
            autocompleteInputs.forEach(input => {
                console.log('Initializing autocomplete for:', input.name, 'URL:', input.dataset.autocompleteUrl);
                
                let debounceTimer;
                let resultsContainer;
                
                // Create results container
                const createResultsContainer = () => {
                    if (!resultsContainer) {
                        resultsContainer = document.createElement('div');
                        resultsContainer.className = 'autocomplete-results';
                        resultsContainer.style.cssText = `
                            position: absolute;
                            top: 100%;
                            left: 0;
                            right: 0;
                            background: white;
                            border: 1px solid #ddd;
                            border-top: none;
                            border-radius: 0 0 4px 4px;
                            max-height: 300px;
                            overflow-y: auto;
                            z-index: 1050;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                            display: none;
                        `;
                        input.parentElement.style.position = 'relative';
                        input.parentElement.appendChild(resultsContainer);
                    }
                    return resultsContainer;
                };
                
                // Handle input
                input.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    const searchTerm = this.value.trim();
                    
                    if (searchTerm.length < 2) {
                        if (resultsContainer) resultsContainer.style.display = 'none';
                        return;
                    }
                    
                    debounceTimer = setTimeout(() => {
                        const apiUrl = input.dataset.autocompleteUrl;
                        console.log('Fetching autocomplete:', apiUrl, 'term:', searchTerm);
                        
                        fetch(`${apiUrl}?term=${encodeURIComponent(searchTerm)}`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Autocomplete results:', data);
                                const container = createResultsContainer();
                                container.innerHTML = '';
                                
                                if (!data || data.length === 0) {
                                    container.style.display = 'none';
                                    return;
                                }
                                
                                data.forEach(item => {
                                    const div = document.createElement('div');
                                    div.className = 'autocomplete-item';
                                    div.style.cssText = `
                                        padding: 12px 15px;
                                        cursor: pointer;
                                        border-bottom: 1px solid #f0f0f0;
                                        transition: background-color 0.2s;
                                    `;
                                    div.textContent = item.label || item.value;
                                    
                                    div.addEventListener('mouseenter', function() {
                                        this.style.backgroundColor = '#f8f9fa';
                                    });
                                    
                                    div.addEventListener('mouseleave', function() {
                                        this.style.backgroundColor = 'white';
                                    });
                                    
                                    div.addEventListener('click', function() {
                                        input.value = item.value;
                                        container.style.display = 'none';
                                        input.form.submit();
                                    });
                                    
                                    container.appendChild(div);
                                });
                                
                                container.style.display = 'block';
                            })
                            .catch(error => {
                                console.error('Autocomplete error:', error);
                            });
                    }, 300);
                });
                
                // Handle keyboard navigation
                input.addEventListener('keydown', function(e) {
                    if (!resultsContainer || resultsContainer.style.display === 'none') return;
                    
                    const items = resultsContainer.querySelectorAll('.autocomplete-item');
                    let currentIndex = -1;
                    
                    items.forEach((item, index) => {
                        if (item.style.backgroundColor === 'rgb(248, 249, 250)') {
                            currentIndex = index;
                        }
                    });
                    
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (currentIndex < items.length - 1) {
                            if (currentIndex >= 0) items[currentIndex].style.backgroundColor = 'white';
                            items[currentIndex + 1].style.backgroundColor = '#f8f9fa';
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (currentIndex > 0) {
                            items[currentIndex].style.backgroundColor = 'white';
                            items[currentIndex - 1].style.backgroundColor = '#f8f9fa';
                        }
                    } else if (e.key === 'Enter') {
                        if (currentIndex >= 0) {
                            e.preventDefault();
                            items[currentIndex].click();
                        }
                    } else if (e.key === 'Escape') {
                        resultsContainer.style.display = 'none';
                    }
                });
                
                // Close on click outside
                document.addEventListener('click', function(e) {
                    if (resultsContainer && !input.contains(e.target) && !resultsContainer.contains(e.target)) {
                        resultsContainer.style.display = 'none';
                    }
                });
            });
        });
    </script>
    
    <!-- Admin Footer -->
    <footer class="admin-footer">
        <div class="footer-content">
            <div class="footer-left">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
            <div class="footer-right">
                <p>Developer</p>
            </div>
        </div>
    </footer>
    
</body>
</html>
