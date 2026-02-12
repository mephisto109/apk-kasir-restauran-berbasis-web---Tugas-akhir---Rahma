<?php
include 'koneksi_rahma.php';
?><?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Famiresu Rahma | Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
    --primary: #d62e02;
    --accent: #fd9855;
    --pink: #d161a2;
    --deep: #a20160;
    --gradient-main: linear-gradient(135deg, #d62e02, #a20160);
    --gradient-soft: linear-gradient(135deg, #fd9855, #d161a2);
}

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #fff5f0, #ffffff, #ffeaf4);
}

/* SIDEBAR */
.sidebar {
    min-height: 100vh;
    background: #ffffff;
    border-right: 1px solid rgba(0,0,0,0.05);
    padding: 40px 25px;
}

.sidebar-brand {
    font-size: 22px;
    font-weight: 700;
    background: var(--gradient-main);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.sidebar-menu li {
    list-style: none;
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: 0.3s;
}

.sidebar-menu li.active,
.sidebar-menu li:hover {
    background: linear-gradient(90deg, rgba(214,46,2,0.1), rgba(162,1,96,0.1));
    font-weight: 600;
}

/* MENU CARD */
.menu-card {
    border-radius: 20px;
    background: #ffffff;
    border: 1px solid rgba(214,46,2,0.08);
    padding: 25px;
    transition: 0.35s ease;
    box-shadow: 0 10px 30px rgba(162,1,96,0.1);
}

.menu-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 50px rgba(162,1,96,0.25);
}

.menu-price {
    font-weight: 700;
    background: var(--gradient-main);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* BUTTON */
.btn-professional {
    background: var(--gradient-main);
    border: none;
    border-radius: 50px;
    font-weight: 600;
}

.btn-professional:hover {
    background: var(--gradient-soft);
}

/* ORDER PANEL */
.order-panel {
    background: #ffffff;
    border-left: 1px solid rgba(0,0,0,0.05);
    padding: 30px;
}

.order-box {
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.cart-item {
    font-size: 14px;
    margin-bottom: 8px;
}

.qty-btn {
    border: none;
    background: #f1f1f1;
    padding: 2px 8px;
    border-radius: 6px;
    cursor: pointer;
}

.checkout-btn {
    background: var(--gradient-soft);
    border: none;
    border-radius: 50px;
    padding: 12px;
    font-weight: 600;
    color: white;
}

.search-box {
    border-radius: 50px;
    padding: 10px 15px;
    border: 1px solid #ddd;
}
.btn-danger {
    background: var(--deep);
    border: none;
    border-radius: 8px;
}

.btn-danger:hover {
    background: var(--pink);
}

.btn-outline-danger {
    border-radius: 50px;
}

</style>

</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->
<div class="col-md-2 sidebar">
    <div class="sidebar-brand">Famiresu Rahma</div>

    <ul class="sidebar-menu mt-4 p-0">
        <li>Makanan</li>
        <li>Minuman</li>
    </ul>
</div>

<!-- MENU AREA -->
<div class="col-md-7 p-5">
    <div class="mb-4">
    <input type="text" id="searchMenu" class="form-control search-box" placeholder="Cari menu...">
</div>

    <h4 class="page-title mb-4">Daftar Menu</h4>

    <div class="row g-4">

    <?php
    $query = "SELECT * FROM tbl_menu_rahma ORDER BY kategori_rahma";
    $sql = mysqli_query($koneksiRahma, $query);
    while($data = mysqli_fetch_assoc($sql)) :
    ?>

    <div class="col-md-4">
        <div class="menu-card" data-kategori="<?= strtolower($data['kategori_rahma']) ?>">


            <?php if($data['status_menu_rahma']=='tersedia'): ?>
                <span class="badge-status available">Tersedia</span>
            <?php else: ?>
                <span class="badge-status unavailable">Tidak Tersedia</span>
            <?php endif; ?>

            <div class="menu-title mt-3">
                <?= $data['nama_menu_rahma'] ?>
            </div>

            <div class="menu-category">
                <?= ucfirst($data['kategori_rahma']) ?>
            </div>

            <div class="menu-price mt-2">
                Rp <?= number_format($data['harga_rahma'],0,',','.') ?>
            </div>

            <?php if($data['status_menu_rahma']=='tersedia'): ?>
            <button class="btn btn-professional w-100 mt-3"
                data-bs-toggle="modal"
                data-bs-target="#order<?= $data['id_menu_rahma'] ?>">
                Pesan
            </button>
            <?php else: ?>
            <button class="btn btn-light w-100 mt-3" disabled>
                Tidak Tersedia
            </button>
            <?php endif; ?>

        </div>
    </div>

    <?php endwhile; ?>

    </div>
</div>
<div class="col-md-3 order-panel position-relative">
    <div class="order-box position-relative">
        <div class="order-title d-flex justify-content-between align-items-center">
            Keranjang
            <div class="position-relative">
                <i class="bi bi-cart3 fs-5"></i>
                <span class="cart-counter d-none" id="cartCount">0</span>
            </div>
        </div>

        <div id="cartItems" class="small text-muted">
            Belum ada pesanan
        </div>

        <hr>

        <div class="fw-bold mb-2">
            Total: <span id="cartTotal">Rp 0</span>
        </div>

        <button class="btn checkout-btn w-100">
            <i class="bi bi-credit-card me-2"></i>Proses Pembayaran
        </button>
        <button class="btn btn-outline-danger w-100 mt-2" onclick="clearCart()">
    <i class="bi bi-x-circle me-2"></i>Kosongkan Keranjang
</button>

    </div>
</div>

</div>
</div>
<script>

let cart = JSON.parse(localStorage.getItem("cart")) || [];

function saveCart() {
    localStorage.setItem("cart", JSON.stringify(cart));
}

function renderCart() {
    let cartItems = document.getElementById("cartItems");
    let cartTotal = document.getElementById("cartTotal");
    let cartCount = document.getElementById("cartCount");

    cartItems.innerHTML = "";
    let total = 0;
    let count = 0;

    if(cart.length === 0){
        cartItems.innerHTML = "<div class='text-muted'>Belum ada pesanan</div>";
    }

    cart.forEach((item, index) => {
        total += item.price * item.qty;
        count += item.qty;

        cartItems.innerHTML += `
            <div class="cart-item d-flex justify-content-between align-items-center mb-2">
                
                <div>
                    <div class="fw-semibold">${item.name}</div>
                    <small>Rp ${item.price.toLocaleString("id-ID")}</small>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button class="qty-btn" onclick="changeQty(${index}, -1)">-</button>
                    <span>${item.qty}</span>
                    <button class="qty-btn" onclick="changeQty(${index}, 1)">+</button>

                    <!-- DELETE BUTTON -->
                    <button class="btn btn-sm btn-danger"
                        onclick="removeItem(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });

    cartTotal.innerText = "Rp " + total.toLocaleString("id-ID");
    cartCount.innerText = count;
    cartCount.classList.toggle("d-none", count === 0);

    saveCart();
}

function addToCart(name, price) {
    let existing = cart.find(item => item.name === name);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ name, price, qty: 1 });
    }
    renderCart();
}

function changeQty(index, change) {
    cart[index].qty += change;
    if (cart[index].qty <= 0) {
        cart.splice(index, 1);
    }
    renderCart();
}

/* DELETE PER ITEM */
function removeItem(index) {
    cart.splice(index, 1);
    renderCart();
}

/* KOSONGKAN SEMUA */
function clearCart() {
    cart = [];
    renderCart();
}

/* BUTTON ADD */
document.querySelectorAll(".btn-professional").forEach(btn => {
    btn.addEventListener("click", function() {
        const card = this.closest(".menu-card");
        const name = card.querySelector(".menu-title").innerText;
        const priceText = card.querySelector(".menu-price").innerText;
        const price = parseInt(priceText.replace(/[^0-9]/g, ''));

        addToCart(name, price);
    });
});

/* FILTER KATEGORI */
document.querySelectorAll(".sidebar-menu li").forEach(item => {
    item.addEventListener("click", function() {
        document.querySelectorAll(".sidebar-menu li").forEach(i => i.classList.remove("active"));
        this.classList.add("active");

        let kategori = this.innerText.toLowerCase();
        document.querySelectorAll(".menu-card").forEach(card => {
            card.parentElement.style.display =
                card.dataset.kategori === kategori ? "block" : "none";
        });
    });
});

/* SEARCH */
document.getElementById("searchMenu").addEventListener("keyup", function() {
    let keyword = this.value.toLowerCase();
    document.querySelectorAll(".menu-card").forEach(card => {
        let name = card.querySelector(".menu-title").innerText.toLowerCase();
        card.parentElement.style.display =
            name.includes(keyword) ? "block" : "none";
    });
});

/* INIT */
renderCart();

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
