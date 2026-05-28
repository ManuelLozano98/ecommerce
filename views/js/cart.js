$(document).ready(function () {
  const el = document.querySelectorAll(".product-data");
  const products = Array.from(el).map((product) => product.dataset);
  if (!products.length) {
    const btn = getById("checkout");
    btn.disabled = true;
  }

  $("#checkout").on("click", function () {
    checkout(products);
  });

  $(".delete").on("click", function (e) {
    const productDiv = e.target.closest('[class^="product-data"]');
    const product = parseInt(productDiv.dataset.id);
    removeProduct(product);
  });
});

function checkout(products) {
  fetch(`${BASE_URL}/checkout/start`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
    method: "POST",
    body: JSON.stringify(products),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.redirect) {
        window.location.href = data.redirect;
        return;
      }
      if (data.message) {
        notifyErrorResponse(data);
      }
    })
    .catch((err) => console.error(err));
}

function removeProduct(product) {
  fetch(`${BASE_URL}/cart/${product}`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
    method: "DELETE",
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        notifySuccessResponse(data.message);
        setTimeout(() => {
          location.reload();
        }, 500);
        // const products = Array.from(document.querySelectorAll(".product-data"));
        // const productDiv = products.find(
        //   (element) => element.dataset.id == product,
        // );
        // productDiv.parentNode.removeChild(productDiv);
      } else {
        notifyErrorResponse(data);
      }
    })
    .catch((err) => console.error(err));
}
