document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileNav = document.getElementById('mobileNav');

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileNav.classList.toggle('active');
        });
    }

    updateCartCount();

    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

function updateCartCount() {
    fetch('/cart/count')
        .then(response => response.json())
        .then(data => {
            const cartCountElement = document.getElementById('cartCount');
            if (cartCountElement) {
                cartCountElement.textContent = data.count || 0;
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
}

function addToCart(productId, quantity = 1, isSubscription = false, frequency = null) {
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('is_subscription', isSubscription ? '1' : '0');
    if (frequency) {
        formData.append('frequency', frequency);
    }
    if (csrfToken) {
        formData.append('csrf_token', csrfToken);
    }

    fetch('/cart/add', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            updateCartCount();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('A apărut o eroare. Vă rugăm să încercați din nou.', 'error');
    });
}

function updateCartItem(cartItemId, quantity) {
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;

    const formData = new FormData();
    formData.append('cart_item_id', cartItemId);
    formData.append('quantity', quantity);
    if (csrfToken) {
        formData.append('csrf_token', csrfToken);
    }

    fetch('/cart/update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('A apărut o eroare. Vă rugăm să încercați din nou.', 'error');
    });
}

function removeCartItem(cartItemId) {
    if (!confirm('Sigur doriți să eliminați acest produs din coș?')) {
        return;
    }

    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;

    const formData = new FormData();
    formData.append('cart_item_id', cartItemId);
    if (csrfToken) {
        formData.append('csrf_token', csrfToken);
    }

    fetch('/cart/remove', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('A apărut o eroare. Vă rugăm să încercați din nou.', 'error');
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '1000';
    notification.style.maxWidth = '400px';
    notification.style.animation = 'slideIn 0.3s ease-out';

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);
