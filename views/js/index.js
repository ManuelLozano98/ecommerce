$(document).ready(() => {
  const form = getById("filter-form");
  const search = document.querySelector(".search-product");
  const sortInput = getById("sort-input");
  const advancedFilters = document.querySelectorAll(".dropdown-toggle");
  const sortButtons = document.querySelectorAll(".sort-btn");
  const productsContainer = getById("content-products");
  const paginationContainer = getById("pagination-card");
  const url = new URLSearchParams(window.location.search);
  const page = url.get("page") || 1;
  const reset = getById("reset");
  const debounceLoadProducts = debounce(function () {
    loadProducts(1);
    syncUIFromURL();
  }, 1000);
  const debounceSearchProducts = debounce(function () {
    loadProducts(1);
    syncUIFromURL();
  }, 1000);

  function syncUIFromURL() {
    const params = new URLSearchParams(window.location.search);
    form.querySelectorAll("input[type='checkbox']").forEach((input) => {
      if (params.getAll(input.name).includes(input.value)) {
        console.log("?");
        input.checked = true;
      } else {
        input.checked = false;
      }
    });

    const currentSearch = params.get("search");
    if (currentSearch) {
      search.value = currentSearch;
    }
    const currentSort = params.get("sort");
    if (currentSort) {
      sortInput.value = currentSort;

      sortButtons.forEach((btn) => {
        btn.classList.remove("bg-primary");
        btn.classList.add("bg-secondary");

        if (btn.dataset.sort === currentSort) {
          btn.classList.remove("bg-secondary");
          btn.classList.add("bg-primary");
        }
      });
    }
    advancedFilters.forEach((btn) => {
      btn.classList.remove("bg-primary");
      btn.classList.add("bg-secondary");
    });

    if (params.getAll("categories[]").length > 0) {
      advancedFilters[0].classList.remove("bg-secondary");
      advancedFilters[0].classList.add("bg-primary");
    }
    if (params.getAll("range_price[]").length > 0) {
      advancedFilters[1].classList.remove("bg-secondary");
      advancedFilters[1].classList.add("bg-primary");
    }
    if (params.getAll("scores[]").length > 0) {
      advancedFilters[2].classList.remove("bg-secondary");
      advancedFilters[2].classList.add("bg-primary");
    }
  }

  function loadProducts(page = null, pushState = true) {
    const formData = new FormData(form);
    const slider = $("#slider").data("ionRangeSlider");
    if (slider) {
      const defaultFrom = 0;
      const defaultTo = 5000;
      if (slider.result.from == defaultFrom && slider.result.to == defaultTo) {
        formData.delete("range_price[]");
      }
    }

    if (!formData.get("search")) {
      formData.delete("search");
    }

    if (!formData.get("sort")) {
      formData.delete("sort");
    }

    if (page && page > 1) {
      formData.set("page", page);
    } else {
      formData.delete("page");
    }

    const params = new URLSearchParams(formData);
    console.log(params.toString());
    const query = params.toString();
    const newUrl = query
      ? `${window.location.pathname}?${query}`
      : window.location.pathname;

    if (pushState) {
      history.pushState({}, "", newUrl);
    }

    fetch(newUrl, {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then((res) => res.json())
      .then((data) => {
        renderProducts(data);
        renderPagination(
          data.totalPages,
          data.currentPage,
          data.totalProducts,
          data.records,
        );
      })
      .catch((err) => console.error(err));
  }

  function renderProducts(data) {
    productsContainer.innerHTML = "";

    if (!data.products.length) {
      productsContainer.innerHTML = "<p>No products found</p>";
      return;
    }

    data.products.forEach((product) => {
      let starsHTML = "";

      const avg = parseFloat(product.average) || 0;

      for (let i = 1; i <= 5; i++) {
        if (i <= Math.floor(avg)) {
          starsHTML += `<i class="fas fa-star"></i>`;
        } else if (i - 1 < avg && i > Math.floor(avg)) {
          starsHTML += `<i class="fas fa-star-half-alt"></i>`;
        } else {
          starsHTML += `<i class="far fa-star"></i>`;
        }
      }

      const imageHTML = product.image
        ? `<img src="/Ecommerce/uploads/images/${product.image}"
              alt="${product.name}"
              class="card-img-center h-100 w-100">`
        : "";

      productsContainer.innerHTML += `
      <div class="col-sm-3">
        <div class="product-card w-75 h-100">
          <a href="/Ecommerce/${product.category.toLowerCase()}/${product.slug}" class="product-link">
            <div class="card-header h-20 bg-light">
              ${imageHTML}
            </div>

            <div class="product-header">
              <div class="product-price">
                <span>${product.price}€</span>
              </div>

              <div class="ratings">
                <span class="stars">
                  ${starsHTML}
                </span>
                <span class="reviews-count">
                  ${data.reviews[product.id].total || 0} reviews
                </span>
              </div>
            </div>

            <div class="card-body bg-white">
              <div class="product-title">
                ${product.name}
              </div>
            </div>
          </a>
        </div>
      </div>
    `;
    });
  }

  function renderPagination(totalPages, currentPage, totalProducts, records) {
    if (totalPages < 2) {
      paginationContainer.innerHTML = "";
      return;
    }

    let end = currentPage !== 1 ? records * currentPage : records;
    let start = currentPage !== 1 ? end - records + 1 : 1;

    if (totalPages === currentPage) {
      end = totalProducts;
      start = totalProducts - records + 1;
    }

    let html = `<div class="col-md-6">
                  <div class="dataTables_info" id="tableProducts_info" role="status" aria-live="polite">
                    Showing ${start} to ${end} of ${totalProducts} entries
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="dataTables_paginate paging_simple_numbers" id="tableProducts_paginate">
                    <ul class="pagination">
                      <!-- Previous page -->`;

    if (currentPage > 1) {
      html += `<li class="paginate_button page-item previous">
                  <a href="#" data-page="${currentPage - 1}" aria-controls="tableProducts" class="page-link">Previous</a>
                </li>`;
    } else {
      html += `<li class="paginate_button page-item previous disabled">
                  <a href="#" aria-controls="tableProducts" class="page-link">Previous</a>
                </li> <!-- Numerated pages -->`;
    }

    for (let i = 1; i <= totalPages; i++) {
      html += `<li class="paginate_button page-item ${i === currentPage ? "active" : ""}">
                  <a href="#" data-page="${i}" aria-controls="tableProducts" class="page-link">${i}</a>
                </li>`;
    }

    html += `<!-- Next page -->`;

    if (currentPage < totalPages) {
      html += `<li class="paginate_button page-item next">
                  <a href="#" data-page="${currentPage + 1}" aria-controls="tableProducts" class="page-link">Next</a>
              </li>`;
    } else {
      html += `<li class="paginate_button page-item next disabled">
                  <a href="#" aria-controls="tableProducts" class="page-link">Next</a>
                </li>`;
    }

    html += `</ul></div></div>`;
    paginationContainer.innerHTML = html;
  }

  search.addEventListener("input", function (e) {
    e.preventDefault();
    debounceSearchProducts();
  });

  sortButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      sortInput.value = this.dataset.sort;

      sortButtons.forEach((btn) => {
        btn.classList.remove("bg-primary");
        btn.classList.add("bg-secondary");
      });

      this.classList.remove("bg-secondary");
      this.classList.add("bg-primary");

      loadProducts(1);
    });
  });

  form.querySelectorAll("input[type='checkbox']").forEach((input) => {
    input.addEventListener("change", function (e) {
      e.preventDefault();
      loadProducts(1);
      syncUIFromURL();
    });
  });

  $("#slider").on("change", function (e) {
    e.preventDefault();
    debounceLoadProducts();
  });

  paginationContainer.addEventListener("click", function (e) {
    if (e.target.matches("[data-page]")) {
      e.preventDefault();
      loadProducts(e.target.dataset.page);
    }
  });

  reset.addEventListener("click", function (e) {
    e.preventDefault();
    window.location.href = window.location.pathname;
  });

  window.addEventListener("popstate", function () {
    syncUIFromURL();
    loadProducts(null, false);
  });

  setupIonSlider();
  focusDropdown();
  syncUIFromURL();
  loadProducts(page, false);
});

function focusDropdown() {
  $(document).on("click", ".dropdown-menu", function (e) {
    e.stopPropagation();
  });
}

function setupIonSlider() {
  /* ION SLIDER */
  $("#slider").ionRangeSlider({
    min: 0,
    max: 5000,
    from: 0,
    to: 5000,
    type: "double",
    step: 1,
    prefix: "€",
    prettify: false,
    hasGrid: true,
  });
}

function debounce(func, delay) {
  let timeout;
  return function () {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, arguments), delay);
  };
}

// function toggleClassFilters() {
//   $("#filter-form button").on("click", function () {
//     $(this).toggleClass("bg-secondary bg-primary");
//   });
// }
