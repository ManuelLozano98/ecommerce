const BASE_URL = "/Ecommerce";

$(document).ready(function () {
  changeImage();
  const product = JSON.parse(
    document.querySelector(".btn-cart").dataset.product,
  );
  $(".btn-cart").on("click", function () {
    const productId = parseInt(this.dataset.id);
    addToCart(productId, product);
  });
  $("#checkout").on("click", function () {
    checkout(product);
  });
  disableProductOutOfStock(product);
});

function changeImage() {
  $(".product-image-thumb").on("click", function () {
    let $image_element = $(this).find("img");
    $(".product-image").prop("src", $image_element.attr("src"));
    $(".product-image-thumb.active").removeClass("active");
    $(this).addClass("active");
  });
}

function addToCart(id, p) {
  const item = {
    product_id: id,
    quantity: parseInt($("input[type='number']").val()),
    product: p,
  };
  fetch(`${BASE_URL}/cart`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
    method: "POST",
    body: JSON.stringify(item),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        notifySuccessResponse(data.message);
        setTimeout(() => {
          location.reload();
        }, 1000);
      } else {
        notifyErrorResponse(data.message);
      }
    })
    .catch((err) => console.error(err));
}

function checkout(product) {
  const qty = getById("quantity").value;
  product.quantity = qty;
  product.checkout_type = "buy_now";
  fetch(`${BASE_URL}/checkout`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
    method: "POST",
    body: JSON.stringify([product]),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.message) {
        notifyErrorResponse(data);
      }
      if (data.clientSecret) {
        location.href = `${BASE_URL}/checkout?client=${data.clientSecret}`;
      }
    })
    .catch((err) => console.error(err));
}

function disableProductOutOfStock(product) {
  if (product.stock <= 0) {
    getById("checkout").disabled = true;
    document.querySelector(".btn-cart").disabled = true;
  }
}
