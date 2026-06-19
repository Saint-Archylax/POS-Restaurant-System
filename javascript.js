// Confirmation function (unchanged)
function checker() {
    var result = confirm("Access to this page requires the owner's approval.");
    if (result == false) {
        event.preventDefault();
    }
}

function fetchCurrentStock(itemId) {
    fetch(`get_current_stock.php?item_id=${itemId}`)
        .then(res => res.json())
        .then(data => {
            const stockQty = data.stock_quantity ?? 0;
            document.getElementById(`stockDisplay-${itemId}`).innerText = stockQty;
            document.getElementById(`stockInput-${itemId}`).value = stockQty;
            updateItemStatus(itemId, stockQty);
        })
        .catch(() => {
            alert("Error fetching current stock");
        });
}

function adjustStock(itemId) {
    const newStock = parseInt(document.getElementById(`stockInput-${itemId}`).value);
    if (isNaN(newStock) || newStock < 0) {
        alert("Please enter a valid stock number");
        return;
    }

    fetch('process_stock_adjustment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `item_id=${itemId}&stock_quantity=${newStock}`
    })
    .then(res => res.text())
    .then(response => {
        alert(response);
        fetchCurrentStock(itemId);
    })
    .catch(() => {
        alert("Error updating stock");
    });
}

function updateItemStatus(itemId, stockQty) {
    const statusCell = document.getElementById(`status-${itemId}`);
    if (stockQty === 0) {
        statusCell.innerHTML = '<span style="color:red">Unavailable</span>';
    } else {
        statusCell.innerText = 'Available';
    }
}


// Unified Cart Manager
class CartManager {
    static async addItem(itemId, quantity = 1) {
        return this._sendCartAction('add', itemId, quantity);
    }

    static async removeItem(itemId) {
        return this._sendCartAction('remove', itemId);
    }

    static async updateCart() {
        try {
            const response = await fetch('cart_actions.php?get_cart=1');
            return await response.json();
        } catch (error) {
            console.error('Cart update error:', error);
            return { success: false, message: 'Network error' };
        }
    }

    static async _sendCartAction(action, itemId, quantity = 1) {
        try {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('item_id', itemId);
            if (quantity) formData.append('quantity', quantity);

            const response = await fetch('cart_actions.php', {
                method: 'POST',
                body: formData
            });
            return await response.json();
        } catch (error) {
            console.error('Cart action error:', error);
            return { success: false, message: 'Network error' };
        }
    }
}

// UI Manager
class CartUI {
    static init() {
        this.bindEvents();
        this.loadCart();
    }

    static bindEvents() {
        document.body.addEventListener('click', async (e) => {

            // Remove item
            if (e.target.closest('.remove-item')) {
                const button = e.target.closest('.remove-item');
                const form = button.closest('form');
                const itemId = form?.querySelector('[name="item_id"]')?.value;

                if (itemId) {
                    this.setButtonState(button, 'loading');
                    const result = await CartManager.removeItem(itemId);
                    this.handleCartResponse(result);
                }
            }
        });
    }

    static async loadCart() {
        const cartContainer = document.querySelector('.cart-items');
        if (!cartContainer) return;

        cartContainer.innerHTML = '<div class="loading-cart">Loading...</div>';
        
        const result = await CartManager.updateCart();
        this.renderCart(result);
    }

    static renderCart(data) {
        const cartContainer = document.querySelector('.cart-items');
        const totalElement = document.querySelector('.cart-total');
        
        if (!data.success) {
            cartContainer.innerHTML = `<div class="cart-error">${data.message || 'Error loading cart'}</div>`;
            if (totalElement) totalElement.textContent = '₱0.00';
            return;
        }

        // Update count
        document.querySelectorAll('.cart-count, .cart-icon span').forEach(el => {
            el.textContent = data.count;
        });

        // Render items
        cartContainer.innerHTML = '<h3 id="CS-label">Customer\'s Cart</h3>';
        
        if (data.items && data.items.length > 0) {
            data.items.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'item';
                itemElement.innerHTML = this.createCartItemHTML(item);
                cartContainer.appendChild(itemElement);
            });
        } else {
            cartContainer.innerHTML += '<div class="item">Your cart is empty</div>';
        }

        // Update total
        if (totalElement) {
            totalElement.textContent = `₱${(data.total || 0).toFixed(2)}`;
        }
    }

    static createCartItemHTML(item) {
        const total = item.Price * item.Quantity;
        return `
            <span>${item.Item_Name} (x${item.Quantity})</span>
            <span>₱${total.toFixed(2)}</span>
            <form class="remove-item-form" style="display:inline;">
                <input type="hidden" name="item_id" value="${item.Item_ID}">
                <button type="button" class="remove-item">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </form>
        `;
    }

    static setButtonState(button, state) {
        const states = {
            loading: '<i class="ri-loader-4-line fa-spin"></i>',
            success: '<i class="ri-check-line" style="color:green;"></i>',
            error: '<i class="ri-close-line" style="color:red;"></i>',
            default: '<i class="ri-add-circle-line"></i>'
        };
        
        button.innerHTML = states[state] || states.default;
    }

    static handleCartResponse(result, button = null) {
        if (result.success) {
            this.renderCart(result);
            if (button) {
                this.setButtonState(button, 'success');
                setTimeout(() => this.setButtonState(button, 'default'), 1000);
            }
        } else {
            console.error('Cart error:', result.message);
            if (button) {
                this.setButtonState(button, 'error');
                setTimeout(() => this.setButtonState(button, 'default'), 1000);
            }
            alert(result.message || 'Operation failed');
        }
    }
}

class OrderModal {
    static init() {
        this.bindModalEvents();
    }

    static bindModalEvents() {
        document.body.addEventListener('click', async (e) => {
            if (e.target.closest('.view-items')) {
                const button = e.target.closest('.view-items');
                const orderId = button.dataset.order;
                this.showOrderItems(orderId, button);
            }
        });
    }

    static async showOrderItems(orderId, button) {
        try {
            this.setButtonState(button, 'loading');
            
            const response = await fetch(`get_order_items.php?order_id=${orderId}`);
            if (!response.ok) throw new Error('Network response was not ok');
            
            const itemsHTML = await response.text();
            this.displayModal(itemsHTML);
            this.setButtonState(button, 'default');
        } catch (error) {
            console.error('Error loading order items:', error);
            this.displayModal('<p class="text-danger">Error loading items. Please try again.</p>');
            this.setButtonState(button, 'default');
        }
    }

    static displayModal(content) {
        const modalBody = document.getElementById('itemsModalBody');
        if (modalBody) {
            modalBody.innerHTML = content;
            new bootstrap.Modal(document.getElementById('itemsModal')).show();
        }
    }

    static setButtonState(button, state) {
        const states = {
            loading: '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...',
            default: 'View Items'
        };
        
        if (button && states[state]) {
            button.innerHTML = states[state];
        }
    }
}
class SearchManager {
    static init() {
        this.bindSearchEvents();
    }

    static bindSearchEvents() {
        const searchInput = document.querySelector('.search-box input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.filterMenuItems(e.target.value.toLowerCase());
            });
        }
    }

    static filterMenuItems(searchTerm) {
        const cards = document.querySelectorAll('.card');
        
        cards.forEach(card => {
            const itemName = card.querySelector('.food-name').textContent.toLowerCase();
            if (itemName.includes(searchTerm)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });

        // Show message if no results found
        const visibleCards = document.querySelectorAll('.card[style="display: flex;"]');
        const menuLabel = document.querySelector('.menuLabel');
        const noResults = document.getElementById('no-results-message');
        
        if (visibleCards.length === 0) {
            if (!noResults) {
                const message = document.createElement('div');
                message.id = 'no-results-message';
                message.className = 'no-results';
                message.textContent = 'No items found matching your search';
                menuLabel.insertAdjacentElement('afterend', message);
            }
        } else if (noResults) {
            noResults.remove();
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    CartUI.init();
    OrderModal.init();
    SearchManager.init();
});

// Keep your existing checker function
function checker() {
    var result = confirm("Access to this page requires the owner's approval.");
    if (result == false) {
        event.preventDefault();
    }
}