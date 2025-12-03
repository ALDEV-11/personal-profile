    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Autocomplete Script -->
    <script>
        class Autocomplete {
            constructor(input, options = {}) {
                this.input = input;
                this.apiUrl = options.apiUrl || input.dataset.autocompleteUrl;
                this.minChars = options.minChars || 2;
                this.debounceDelay = options.debounceDelay || 300;
                this.onSelect = options.onSelect || null;
                
                this.currentFocus = -1;
                this.debounceTimer = null;
                this.resultsContainer = null;
                
                this.init();
            }
            
            init() {
                // Create results container
                const wrapper = document.createElement('div');
                wrapper.className = 'autocomplete-wrapper';
                this.input.parentNode.insertBefore(wrapper, this.input);
                wrapper.appendChild(this.input);
                
                this.resultsContainer = document.createElement('div');
                this.resultsContainer.className = 'autocomplete-results';
                wrapper.appendChild(this.resultsContainer);
                
                // Bind events
                this.input.addEventListener('input', this.handleInput.bind(this));
                this.input.addEventListener('keydown', this.handleKeydown.bind(this));
                document.addEventListener('click', this.handleClickOutside.bind(this));
            }
            
            handleInput(e) {
                const value = e.target.value.trim();
                
                clearTimeout(this.debounceTimer);
                
                if (value.length < this.minChars) {
                    this.hideResults();
                    return;
                }
                
                this.debounceTimer = setTimeout(() => {
                    this.fetchSuggestions(value);
                }, this.debounceDelay);
            }
            
            async fetchSuggestions(term) {
                if (!this.apiUrl) {
                    console.error('Autocomplete API URL not specified');
                    return;
                }
                
                try {
                    this.showLoading();
                    
                    const response = await fetch(`${this.apiUrl}?term=${encodeURIComponent(term)}`);
                    const data = await response.json();
                    
                    if (data.length === 0) {
                        this.showNoResults();
                    } else {
                        this.renderSuggestions(data);
                    }
                } catch (error) {
                    console.error('Autocomplete fetch error:', error);
                    this.hideResults();
                }
            }
            
            showLoading() {
                this.resultsContainer.innerHTML = '<div class="autocomplete-loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
                this.resultsContainer.classList.add('show');
            }
            
            showNoResults() {
                this.resultsContainer.innerHTML = '<div class="autocomplete-no-results">No suggestions found</div>';
                this.resultsContainer.classList.add('show');
            }
            
            renderSuggestions(data) {
                this.currentFocus = -1;
                
                const html = data.map((item, index) => {
                    return `
                        <div class="autocomplete-item" data-index="${index}" data-value="${item.value}">
                            <div class="autocomplete-item-main">${this.highlightMatch(item.label, this.input.value)}</div>
                            ${item.subtitle ? `<div class="autocomplete-item-sub">${item.subtitle}</div>` : ''}
                        </div>
                    `;
                }).join('');
                
                this.resultsContainer.innerHTML = html;
                this.resultsContainer.classList.add('show');
                
                // Add click listeners
                this.resultsContainer.querySelectorAll('.autocomplete-item').forEach(item => {
                    item.addEventListener('click', () => {
                        this.selectItem(item.dataset.value);
                    });
                });
            }
            
            highlightMatch(text, search) {
                const regex = new RegExp(`(${search})`, 'gi');
                return text.replace(regex, '<strong>$1</strong>');
            }
            
            handleKeydown(e) {
                const items = this.resultsContainer.querySelectorAll('.autocomplete-item');
                
                if (!items.length) return;
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    this.currentFocus++;
                    if (this.currentFocus >= items.length) this.currentFocus = 0;
                    this.setActive(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    this.currentFocus--;
                    if (this.currentFocus < 0) this.currentFocus = items.length - 1;
                    this.setActive(items);
                } else if (e.key === 'Enter') {
                    if (this.currentFocus > -1) {
                        e.preventDefault();
                        items[this.currentFocus].click();
                    }
                } else if (e.key === 'Escape') {
                    this.hideResults();
                }
            }
            
            setActive(items) {
                items.forEach(item => item.classList.remove('active'));
                if (this.currentFocus >= 0 && this.currentFocus < items.length) {
                    items[this.currentFocus].classList.add('active');
                }
            }
            
            selectItem(value) {
                this.input.value = value;
                this.hideResults();
                
                if (this.onSelect) {
                    this.onSelect(value);
                } else {
                    // Trigger form submission or page reload with search
                    const form = this.input.closest('form');
                    if (form) {
                        form.submit();
                    } else {
                        // Update URL parameter and reload
                        const url = new URL(window.location);
                        url.searchParams.set('search', value);
                        url.searchParams.set('page', '1');
                        window.location.href = url.toString();
                    }
                }
            }
            
            handleClickOutside(e) {
                if (!this.input.parentElement.contains(e.target)) {
                    this.hideResults();
                }
            }
            
            hideResults() {
                this.resultsContainer.classList.remove('show');
                this.currentFocus = -1;
            }
        }
        
        // Initialize autocomplete on page load
        document.addEventListener('DOMContentLoaded', function() {
            const searchInputs = document.querySelectorAll('[data-autocomplete-url]');
            searchInputs.forEach(input => {
                new Autocomplete(input);
            });
        });
    </script>
    
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
