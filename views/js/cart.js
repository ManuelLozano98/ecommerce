const BASE_URL = "/Ecommerce";

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
});

function checkout(products) {
  fetch(`${BASE_URL}/checkout`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
    method: "POST",
    body: JSON.stringify(products),
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

function removeProduct() {}
