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

  $("#customer-review").on("click", function () {
    fetch(`${BASE_URL}/review`, {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json",
      },
      method: "POST",
      body: JSON.stringify({
        product_id: product.id,
        url: window.location.href,
      }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.redirect) {
          window.location.href = data.redirect;
          return;
        }
        if (data.success) {
          getById("review-card").classList.remove("d-none");
          if (data.review) {
            const reviewCard = getById("review-card");
            if (reviewCard.firstElementChild.nodeName !== "H5") {
              const title = document.createElement("h5");
              title.textContent =
                "You've already reviewed this product, would you like to update your review?";
              title.classList.add("card-title", "mb-2");
              reviewCard.prepend(title);
            }
            getById("title-review").value = data.review.title;
            getById("comment-review").value = data.review.comment;
            getById("rating-review").style.setProperty(
              "--val",
              data.review.rating,
            );
            getById("post-review").innerHTML = "Update Review";
          }
        }
      })
      .catch((err) => console.error(err));
  });
  $("#review-form").on("submit", function (e) {
    e.preventDefault();
    const data = {
      product_id: product.id,
      user_id: getById("post-review").dataset.review,
      title: getById("title-review").value,
      comment: getById("comment-review").value,
      rating: getById("rating-review").value,
    };
    reviewProduct(data);
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

function checkout(product) {
  const qty = getById("quantity").value;
  product.quantity = qty;
  product.checkout_type = "buy_now";
  fetch(`${BASE_URL}/checkout/start`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
    method: "POST",
    body: JSON.stringify([product]),
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

function disableProductOutOfStock(product) {
  if (product.stock <= 0) {
    getById("checkout").disabled = true;
    document.querySelector(".btn-cart").disabled = true;
  }
}

function reviewProduct(review) {
  fetch(`${BASE_URL}/review-product`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
    method: "POST",
    body: JSON.stringify(review),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status) {
        notifySuccessResponse(API_MSGS.Saved);
        setTimeout(() => {
          location.reload();
        }, 1000);
      } else {
        notifyErrorResponse(data.status);
      }
    })
    .catch((err) => console.error(err));
}
