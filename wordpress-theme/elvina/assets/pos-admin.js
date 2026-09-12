(function () {
  "use strict";

  var config = window.ElvinaPOS || { products: [], ajaxUrl: "", nonce: "", i18n: {} };
  var products = config.products || [];
  var sales = [];
  var openProductId = null;
  var selection = {};

  function formatPrice(v) {
    return v.toLocaleString("fr-FR", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + " €";
  }

  function stockBadge(qty) {
    if (qty === null) return { text: "En stock", cls: "elvina-pos-badge--in" };
    if (qty <= 0) return { text: "Rupture", cls: "elvina-pos-badge--out" };
    if (qty === 1) return { text: "Dernière pièce", cls: "elvina-pos-badge--last" };
    if (qty <= 3) return { text: "Stock limité", cls: "elvina-pos-badge--low" };
    return { text: "En stock", cls: "elvina-pos-badge--in" };
  }

  function totalStock(product) {
    var known = product.variants.filter(function (v) { return v.stock !== null; });
    if (!known.length) return null;
    return known.reduce(function (sum, v) { return sum + v.stock; }, 0);
  }

  function findProduct(id) {
    return products.find(function (p) { return String(p.id) === String(id); });
  }

  function render() {
    var list = document.getElementById("elvina-pos-list");
    var searchEl = document.getElementById("elvina-pos-search");
    var query = searchEl ? searchEl.value.trim().toLowerCase() : "";
    var items = products.filter(function (p) {
      return !query || p.name.toLowerCase().indexOf(query) !== -1;
    });

    if (!items.length) {
      list.innerHTML = "<p>" + config.i18n.noResults + "</p>";
      return;
    }

    list.innerHTML = items.map(function (p) {
      var isOpen = String(p.id) === String(openProductId);
      var badge = stockBadge(totalStock(p));
      var selected = selection[p.id];

      var variantsHtml = p.variants.map(function (v) {
        var isSel = String(selected) === String(v.variationId);
        var vb = stockBadge(v.stock);
        var disabled = v.stock !== null && v.stock <= 0;
        return '<button type="button" class="elvina-pos-variant-btn' + (isSel ? " is-selected" : "") + '" data-product="' + p.id + '" data-variation="' + v.variationId + '"' + (disabled ? " disabled" : "") + ">" +
          "<strong>" + v.label + "</strong>" +
          '<span class="elvina-pos-badge ' + vb.cls + '">' + vb.text + (v.stock !== null ? " · " + v.stock : "") + "</span>" +
        "</button>";
      }).join("");

      var selectedVariant = p.variants.find(function (v) { return String(v.variationId) === String(selected); });
      var confirmVisible = selectedVariant && (selectedVariant.stock === null || selectedVariant.stock > 0);

      return "" +
        '<div class="elvina-pos-item' + (isOpen ? " is-open" : "") + '">' +
          '<button type="button" class="elvina-pos-item-head" data-toggle="' + p.id + '">' +
            '<div class="elvina-pos-item-name"><strong>' + p.name + "</strong></div>" +
            '<span class="elvina-pos-badge ' + badge.cls + '">' + badge.text + "</span>" +
            '<span class="elvina-pos-chevron" aria-hidden="true">›</span>' +
          "</button>" +
          '<div class="elvina-pos-variants">' +
            '<div class="elvina-pos-variant-grid">' + variantsHtml + "</div>" +
            '<div class="elvina-pos-confirm' + (confirmVisible ? " is-visible" : "") + '">' +
              '<label for="qty-' + p.id + '">Qté<input type="number" min="1" value="1" id="qty-' + p.id + '"' + (selectedVariant && selectedVariant.stock !== null ? ' max="' + selectedVariant.stock + '"' : "") + "></label>" +
              '<button type="button" class="button button-primary elvina-pos-sell-btn" data-sell="' + p.id + '">Vendu en boutique</button>' +
            "</div>" +
          "</div>" +
        "</div>";
    }).join("");

    list.querySelectorAll("[data-toggle]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var id = btn.getAttribute("data-toggle");
        openProductId = String(openProductId) === String(id) ? null : id;
        render();
      });
    });

    list.querySelectorAll(".elvina-pos-variant-btn").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var id = btn.getAttribute("data-product");
        var variationId = btn.getAttribute("data-variation");
        selection[id] = String(selection[id]) === String(variationId) ? null : variationId;
        render();
      });
    });

    list.querySelectorAll("[data-sell]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        sell(btn.getAttribute("data-sell"));
      });
    });
  }

  function sell(productId) {
    var product = findProduct(productId);
    var variationId = selection[productId];
    var variant = product && product.variants.find(function (v) { return String(v.variationId) === String(variationId); });
    if (!product || !variant) {
      return;
    }

    var qtyInput = document.getElementById("qty-" + productId);
    var qty = Math.max(1, parseInt(qtyInput.value, 10) || 1);

    var btn = document.querySelector('[data-sell="' + productId + '"]');
    if (btn) btn.disabled = true;

    var body = new URLSearchParams();
    body.set("action", "elvina_pos_sell");
    body.set("nonce", config.nonce);
    body.set("product_id", product.id);
    body.set("variation_id", variant.variationId || 0);
    body.set("qty", qty);

    fetch(config.ajaxUrl, { method: "POST", credentials: "same-origin", body: body })
      .then(function (r) { return r.json(); })
      .then(function (json) {
        if (btn) btn.disabled = false;

        if (!json.success) {
          flash((json.data && json.data.message) || config.i18n.error, true);
          if (json.data && typeof json.data.stock === "number") {
            variant.stock = json.data.stock;
            render();
          }
          return;
        }

        variant.stock = json.data.newStock;
        sales.unshift({ name: json.data.name, variant: variant.label, qty: json.data.qty, total: json.data.total });
        selection[productId] = null;

        render();
        renderTicket();
        flash(json.data.qty + " × " + json.data.name + " (" + variant.label + ") " + config.i18n.sold);
      })
      .catch(function () {
        if (btn) btn.disabled = false;
        flash(config.i18n.error, true);
      });
  }

  function renderTicket() {
    var list = document.getElementById("elvina-pos-ticket-list");
    if (!sales.length) {
      list.innerHTML = '<li class="elvina-pos-ticket-empty">' + config.i18n.noSales + "</li>";
    } else {
      list.innerHTML = sales.map(function (s) {
        return '<li class="elvina-pos-ticket-line"><span>' + s.qty + " × " + s.name + " (" + s.variant + ")</span><span>" + formatPrice(s.total) + "</span></li>";
      }).join("");
    }
    var total = sales.reduce(function (sum, s) { return sum + s.total; }, 0);
    document.getElementById("elvina-pos-ticket-total").textContent = formatPrice(total);
  }

  var flashTimer = null;
  function flash(message, isError) {
    var el = document.getElementById("elvina-pos-flash");
    el.textContent = message;
    el.classList.add("is-visible");
    el.classList.toggle("is-error", !!isError);
    window.clearTimeout(flashTimer);
    flashTimer = window.setTimeout(function () { el.classList.remove("is-visible"); }, 5000);
  }

  document.addEventListener("DOMContentLoaded", function () {
    var search = document.getElementById("elvina-pos-search");
    if (!search) {
      return;
    }
    search.addEventListener("input", render);
    render();
    renderTicket();
  });
})();
