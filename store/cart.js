// cart.js
document.addEventListener('DOMContentLoaded', () => {
    const cartItemsContainer = document.getElementById('cart-items');
    const totalPriceElement = document.getElementById('total-price');

    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    function renderCartItems() {
        cartItemsContainer.innerHTML = '';
        let total = 0;

        cart.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.classList.add('cart-item');
            itemElement.innerHTML = `
                <h3>${item.name} <img src="img/urn.png" alt="${item.name}"></h3>
                <p>Fiyat: ${item.price} TL</p>
                <p>Miktar: ${item.quantity}</p>
                <div>
                    <button class="increase-qty" data-name="${item.name}">+</button>
                    <button class="decrease-qty" data-name="${item.name}">-</button>
                    <button class="remove-item" data-name="${item.name}">Sil</button>
                </div>
            `;

            cartItemsContainer.appendChild(itemElement);
            total += item.price * item.quantity;
        });

        totalPriceElement.textContent = `Toplam: ${total} TL`;

        attachEventListeners();
    }

    function attachEventListeners() {
        const increaseButtons = document.querySelectorAll('.increase-qty');
        const decreaseButtons = document.querySelectorAll('.decrease-qty');
        const removeButtons = document.querySelectorAll('.remove-item');

        increaseButtons.forEach(button => {
            button.addEventListener('click', increaseQuantity);
        });

        decreaseButtons.forEach(button => {
            button.addEventListener('click', decreaseQuantity);
        });

        removeButtons.forEach(button => {
            button.addEventListener('click', removeItem);
        });
    }

    function increaseQuantity(event) {
        const productName = event.target.getAttribute('data-name');
        const productIndex = cart.findIndex(item => item.name === productName);

        if (productIndex > -1) {
            cart[productIndex].quantity += 1;
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCartItems();
        }
    }

    function decreaseQuantity(event) {
        const productName = event.target.getAttribute('data-name');
        const productIndex = cart.findIndex(item => item.name === productName);

        if (productIndex > -1) {
            if (cart[productIndex].quantity > 1) {
                cart[productIndex].quantity -= 1;
            } else {
                cart.splice(productIndex, 1);
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            renderCartItems();
        }
    }

    function removeItem(event) {
        const productName = event.target.getAttribute('data-name');
        cart = cart.filter(item => item.name !== productName);

        localStorage.setItem('cart', JSON.stringify(cart));
        renderCartItems();
    }

    renderCartItems();
});
