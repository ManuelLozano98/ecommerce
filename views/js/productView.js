const BASE_URL = "/Ecommerce";

$(document).ready(function () {
  changeImage();
  $(".btn-cart").on("click", function () {
    const productId = parseInt(this.dataset.id);
    const product = JSON.parse(this.dataset.product);
    addToCart(productId, product);
  });
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
