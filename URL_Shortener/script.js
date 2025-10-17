// URL Shortener JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('urlForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
});

async function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');

    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';

    try {
        const response = await fetch('api.php?action=shorten', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showResult(data);
        } else {
            showError(data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Network error. Please try again.');
    } finally {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.textContent = 'Shorten URL';
    }
}

function showResult(data) {
    const resultDiv = document.getElementById('result');
    const errorDiv = document.getElementById('error');
    const shortUrlInput = document.getElementById('shortUrl');
    const statsLink = document.getElementById('statsLink');

    // Hide error, show result
    errorDiv.classList.add('hidden');
    resultDiv.classList.remove('hidden');

    // Set the short URL
    shortUrlInput.value = data.short_url;

    // Set stats link
    statsLink.href = `stats.php?code=${data.short_code}`;

    // Generate QR code if available
    const qrContainer = document.getElementById('qrContainer');
    if (typeof QRCode !== 'undefined') {
        qrContainer.innerHTML = '';
        QRCode.toCanvas(qrContainer, data.short_url, {
            width: 150,
            height: 150
        });
    }

    // Scroll to result
    resultDiv.scrollIntoView({ behavior: 'smooth' });
}

function showError(message) {
    const resultDiv = document.getElementById('result');
    const errorDiv = document.getElementById('error');

    // Hide result, show error
    resultDiv.classList.add('hidden');
    errorDiv.classList.remove('hidden');
    errorDiv.textContent = message;

    // Scroll to error
    errorDiv.scrollIntoView({ behavior: 'smooth' });
}

function copyToClipboard() {
    const shortUrlInput = document.getElementById('shortUrl');

    shortUrlInput.select();
    shortUrlInput.setSelectionRange(0, 99999); // For mobile devices

    try {
        document.execCommand('copy');
        // Visual feedback
        const originalText = shortUrlInput.value;
        shortUrlInput.value = 'Copied!';
        setTimeout(() => {
            shortUrlInput.value = originalText;
        }, 1000);
    } catch (err) {
        console.error('Failed to copy: ', err);
        alert('Failed to copy to clipboard');
    }
}

// Form validation
function validateAlias(alias) {
    if (!alias) return true; // Optional field
    const regex = /^[a-zA-Z0-9_-]+$/;
    return regex.test(alias);
}

// Add real-time validation
document.addEventListener('DOMContentLoaded', function() {
    const aliasInput = document.getElementById('customAlias');
    if (aliasInput) {
        aliasInput.addEventListener('input', function() {
            const isValid = validateAlias(this.value);
            this.style.borderColor = isValid ? '#ddd' : '#dc3545';

            const errorMsg = this.parentNode.querySelector('.alias-error');
            if (!isValid && this.value) {
                if (!errorMsg) {
                    const msg = document.createElement('small');
                    msg.className = 'alias-error';
                    msg.style.color = '#dc3545';
                    msg.textContent = 'Only letters, numbers, hyphens, and underscores allowed';
                    this.parentNode.appendChild(msg);
                }
            } else if (errorMsg) {
                errorMsg.remove();
            }
        });
    }
});