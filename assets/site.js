(function () {
  "use strict";

  var CART_KEY = "elvina-cart-v1";

  var ICONS = {
    mode: '<svg viewBox="0 0 48 48" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="M24 8a3 3 0 1 1 3 3" stroke-linecap="round"/><path d="M24 11v4" stroke-linecap="round"/><path d="M24 15 8 26l3 3 4-2 1 13h16l1-13 4 2 3-3-16-11z" stroke-linejoin="round"/></svg>',
    bijoux: '<svg viewBox="0 0 48 48" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round" aria-hidden="true"><path d="M14 18h20l6 8-16 14-16-14z"/><path d="M14 18l4-8h12l4 8"/><path d="M24 18v22M18 18l6 22 6-22"/></svg>'
  };

  function getCart() {
    try { return JSON.parse(localStorage.getItem(CART_KEY) || "[]"); }
    catch (e) { return []; }
  }

  function saveCart(cart) {
    try { localStorage.setItem(CART_KEY, JSON.stringify(cart)); }
    catch (e) { /* stockage indisponible : le panier ne persistera pas sur cet appareil */ }
  }

  function cartQtyFor(productId, variantLabel) {
    var line = getCart().find(function (l) { return l.id === productId && l.variant === variantLabel; });
    return line ? line.qty : 0;
  }

  function addToCart(productId, variantLabel, qty) {
    var product = findProduct(productId);
    if (!product) return 0;
    var variant = product.variants.find(function (v) { return v.label === variantLabel; });
    if (!variant) return 0;

    var cart = getCart();
    var line = cart.find(function (l) { return l.id === productId && l.variant === variantLabel; });
    var currentQty = line ? line.qty : 0;
    var nextQty = Math.min(currentQty + qty, variant.stock);
    if (nextQty <= 0) return 0;

    if (line) { line.qty = nextQty; }
    else { cart.push({ id: productId, variant: variantLabel, qty: nextQty }); }

    saveCart(cart);
    updateCartCount();
    return nextQty;
  }

  function setCartQty(productId, variantLabel, qty) {
    var product = findProduct(productId);
    var variant = product ? product.variants.find(function (v) { return v.label === variantLabel; }) : null;
    var cart = getCart();
    var idx = cart.findIndex(function (l) { return l.id === productId && l.variant === variantLabel; });
    if (idx === -1) return;

    if (qty <= 0) {
      cart.splice(idx, 1);
    } else {
      cart[idx].qty = variant ? Math.min(qty, variant.stock) : qty;
    }
    saveCart(cart);
    updateCartCount();
  }

  function cartCount() {
    return getCart().reduce(function (sum, l) { return sum + l.qty; }, 0);
  }

  function updateCartCount() {
    var el = document.getElementById("cart-count");
    if (el) el.textContent = String(cartCount());
  }

  function stockBadge(qty) {
    if (qty <= 0) return { text: "Rupture de stock", cls: "badge--out" };
    if (qty === 1) return { text: "Dernière pièce", cls: "badge--last" };
    if (qty <= 3) return { text: "Stock limité", cls: "badge--low" };
    return { text: "En stock", cls: "badge--in" };
  }

  function categoryLabel(cat) {
    return cat === "mode" ? "Mode" : "Bijoux";
  }

  function productCardHtml(p) {
    var badge = stockBadge(totalStock(p));
    return "" +
      '<a class="product-card" href="produit.html?id=' + encodeURIComponent(p.id) + '">' +
        '<div class="product-media product-media--' + p.category + '">' +
          ICONS[p.category] +
          '<span class="product-media-tag">' + categoryLabel(p.category) + "</span>" +
        "</div>" +
        '<div class="product-info">' +
          '<p class="product-name">' + p.name + "</p>" +
          '<p class="product-price">' + formatPrice(p.price) + "</p>" +
          '<span class="badge ' + badge.cls + '">' + badge.text + "</span>" +
        "</div>" +
      "</a>";
  }

  function initNavToggle() {
    var toggle = document.getElementById("nav-toggle");
    var nav = document.getElementById("main-nav");
    if (!toggle || !nav) return;
    toggle.addEventListener("click", function () {
      var open = nav.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }

  function renderBoutique() {
    var grid = document.getElementById("product-grid");
    if (!grid) return;

    var params = new URLSearchParams(window.location.search);
    var activeCat = params.get("cat") || "all";

    document.querySelectorAll(".filter-tab").forEach(function (tab) {
      tab.classList.toggle("is-active", tab.dataset.cat === activeCat);
    });

    var list = PRODUCTS.filter(function (p) {
      return activeCat === "all" || p.category === activeCat;
    });

    grid.innerHTML = list.length
      ? list.map(productCardHtml).join("")
      : '<p class="empty-note">Aucun article dans cette catégorie pour le moment.</p>';

    document.querySelectorAll(".filter-tab").forEach(function (tab) {
      tab.addEventListener("click", function (e) {
        e.preventDefault();
        var url = new URL(window.location.href);
        if (tab.dataset.cat === "all") url.searchParams.delete("cat");
        else url.searchParams.set("cat", tab.dataset.cat);
        window.history.pushState({}, "", url);
        renderBoutique();
      });
    });
  }

  function renderFeatured() {
    var grid = document.getElementById("featured-grid");
    if (!grid) return;
    var ids = ["robe-satin-ivoire", "collier-fil-or-fin", "trench-leger-camel", "bague-solitaire-zircon"];
    var list = ids.map(findProduct).filter(Boolean);
    grid.innerHTML = list.map(productCardHtml).join("");
  }

  function renderProductDetail() {
    var container = document.getElementById("product-detail");
    if (!container) return;

    var params = new URLSearchParams(window.location.search);
    var product = findProduct(params.get("id"));

    if (!product) {
      container.innerHTML = '<p class="empty-note">Cet article n’existe pas ou n’est plus disponible. <a href="boutique.html">Retour à la boutique</a>.</p>';
      return;
    }

    document.title = product.name + " — Elvina";

    var variantsHtml = product.variants.map(function (v, i) {
      var isOut = v.stock <= 0;
      var checked = i === 0 && !isOut ? " checked" : "";
      var note = isOut ? " (rupture)" : v.stock === 1 ? " (1 restant)" : "";
      return "" +
        '<label class="variant-option' + (isOut ? " is-disabled" : "") + '">' +
          '<input type="radio" name="variant" value="' + v.label + '"' + (isOut ? " disabled" : "") + checked + " />" +
          "<span>" + v.label + note + "</span>" +
        "</label>";
    }).join("");

    container.innerHTML = "" +
      '<div class="product-media product-media--' + product.category + ' product-media--large">' +
        ICONS[product.category] +
        '<span class="product-media-tag">' + categoryLabel(product.category) + "</span>" +
      "</div>" +
      '<div class="product-panel">' +
        '<p class="eyebrow">' + categoryLabel(product.category) + "</p>" +
        "<h1>" + product.name + "</h1>" +
        '<p class="product-price product-price--lg">' + formatPrice(product.price) + "</p>" +
        '<p class="product-desc">' + product.description + "</p>" +
        '<fieldset class="variant-group">' +
          "<legend>" + product.variantLabel + "</legend>" +
          variantsHtml +
        "</fieldset>" +
        '<div class="qty-row">' +
          '<label for="qty">Quantité</label>' +
          '<input type="number" id="qty" min="1" value="1" />' +
        "</div>" +
        '<button type="button" class="btn btn--primary" id="add-to-cart">Ajouter au panier</button>' +
        '<p class="form-note" id="add-to-cart-note" role="status"></p>' +
      "</div>";

    var addBtn = document.getElementById("add-to-cart");
    addBtn.addEventListener("click", function () {
      var selected = container.querySelector('input[name="variant"]:checked');
      var note = document.getElementById("add-to-cart-note");
      if (!selected) {
        note.textContent = "Choisissez une option avant d’ajouter au panier.";
        return;
      }
      var qty = Math.max(1, parseInt(document.getElementById("qty").value, 10) || 1);
      var before = cartQtyFor(product.id, selected.value);
      var after = addToCart(product.id, selected.value, qty);
      if (after > before) {
        note.textContent = "Ajouté au panier (" + after + " au total pour cette option).";
      } else if (after > 0) {
        note.textContent = "Vous avez déjà la quantité maximale disponible (" + after + ") dans votre panier.";
      } else {
        note.textContent = "Stock insuffisant pour cette quantité.";
      }
    });
  }

  function renderCartPage() {
    var container = document.getElementById("cart-lines");
    var summary = document.getElementById("cart-summary");
    if (!container || !summary) return;

    var cart = getCart();
    if (cart.length === 0) {
      container.innerHTML = '<p class="empty-note">Votre panier est vide. <a href="boutique.html">Voir la boutique</a>.</p>';
      summary.hidden = true;
      return;
    }

    summary.hidden = false;
    var subtotal = 0;

    container.innerHTML = cart.map(function (line, i) {
      var product = findProduct(line.id);
      if (!product) return "";
      var lineTotal = product.price * line.qty;
      subtotal += lineTotal;
      var variant = product.variants.find(function (v) { return v.label === line.variant; });
      var maxStock = variant ? variant.stock : line.qty;
      return "" +
        '<div class="cart-line">' +
          '<div class="product-media product-media--' + product.category + ' product-media--sm">' + ICONS[product.category] + "</div>" +
          '<div class="cart-line-info">' +
            '<p class="product-name"><a href="produit.html?id=' + product.id + '">' + product.name + "</a></p>" +
            '<p class="form-note">' + product.variantLabel + " : " + line.variant + "</p>" +
            '<div class="qty-row">' +
              '<label for="qty-' + i + '">Quantité</label>' +
              '<input type="number" id="qty-' + i + '" min="0" max="' + maxStock + '" value="' + line.qty + '" data-index="' + i + '" class="cart-qty" />' +
            "</div>" +
          "</div>" +
          '<p class="cart-line-total">' + formatPrice(lineTotal) + "</p>" +
        "</div>";
    }).join("");

    document.getElementById("cart-subtotal").textContent = formatPrice(subtotal);

    container.querySelectorAll(".cart-qty").forEach(function (input) {
      input.addEventListener("change", function () {
        var idx = parseInt(input.dataset.index, 10);
        var line = cart[idx];
        setCartQty(line.id, line.variant, parseInt(input.value, 10) || 0);
        input.blur();
        renderCartPage();
      });
    });
  }

  function initCheckout() {
    var btn = document.getElementById("checkout-btn");
    if (!btn) return;
    btn.addEventListener("click", function () {
      var note = document.getElementById("checkout-note");
      note.textContent = "Commande simulée ✓ — cette étape se connectera à votre solution de caisse (Square, WooCommerce...) au moment de la mise en ligne réelle.";
      note.hidden = false;
    });
  }

  function initContactForm() {
    var form = document.getElementById("contact-form");
    if (!form) return;
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      document.getElementById("contact-note").hidden = false;
      form.reset();
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    updateCartCount();
    renderFeatured();
    renderBoutique();
    renderProductDetail();
    renderCartPage();
    initCheckout();
    initContactForm();
  });
})();
