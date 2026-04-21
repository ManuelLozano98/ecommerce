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
      for (let i = 0; i < sortButtons.length; i++) {
        if (sortButtons.item(i).getAttribute("data-sort") === currentSort) {
          const outlineClass = [...sortButtons.item(i).classList].find((c) =>
            c.startsWith("btn-outline-"),
          );
          if (outlineClass) {
            const color = outlineClass.replace("btn-outline-", "");
            sortButtons.item(i).classList.remove(outlineClass);
            sortButtons.item(i).classList.add(`bg-${color}`);
          }
        }
      }
    }
    advancedFilters.forEach((btn) => {
      btn.classList.remove("bg-dark");
      btn.classList.add("btn-outline-dark");
    });

    if (params.get("categories[]")) {
      advancedFilters[0].classList.remove("btn-outline-dark");
      advancedFilters[0].classList.add("bg-dark");
    }
    if (params.get("range_price[]")) {
      advancedFilters[1].classList.remove("btn-outline-dark");
      advancedFilters[1].classList.add("bg-dark");
    }
    if (params.get("scores[]")) {
      advancedFilters[2].classList.remove("btn-outline-dark");
      advancedFilters[2].classList.add("bg-dark");
    }
    if (params.get("range_price[]")) {
      let sliderVal = params.get("range_price[]").split(";");
      const slider = $("#slider").data("ionRangeSlider");
      slider.update({
        from: parseInt(sliderVal[0]),
        to: parseFloat(sliderVal[1]),
      });
    }
  }

  function loadProducts(page = null, pushState = true) {
    const formData = new FormData(form);
    const slider = $("#slider").data("ionRangeSlider");
    if (slider) {
      const defaultFrom = 0;
      const defaultTo = parseInt(
        $("#slider").data("ionRangeSlider").options.max,
      );
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

    if (
      pushState &&
      newUrl !== window.location.pathname + window.location.search
    ) {
      history.pushState({}, "", newUrl);
    }

    fetch(newUrl, {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json",
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
      let html = "";

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
              class="card-img-top object-fit-cover">`
        : "";

      html = `
        <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-4">
          <div class="card h-100 shadow-sm border-0">                          
            <a href="/Ecommerce/${product.category.toLowerCase()}/${product.slug}">
              <div class="ratio ratio-1x1 bg-light">
                  ${imageHTML}
              </div>
            </a>
            <div class="card-body d-flex flex-column">
               <h5 class="fw-bold mb-1">
                  ${product.price}€
                </h5>
                <div class="text-warning mb-1">
                  <span class="stars">
                    ${starsHTML}
                   </span>                           
                  <span class="text-muted small">
                    (${data.reviews[product.id].total || 0})
                  </span>
                </div>
               <p class="card-text text-muted flex-grow-1">
                  ${product.name}
                </p>
                <a href="/Ecommerce/${product.category.toLowerCase()}/${product.slug}" class="btn btn-dark w-100 btn-sm rounded-pill">
                  View product
                </a>
              </div>
            </div>
          </div>
    `;
      productsContainer.innerHTML += html;
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
      sortButtons.forEach((btn) => {
        const bgClass = [...btn.classList].find((c) => c.startsWith("bg-"));
        if (bgClass) {
          const color = bgClass.replace("bg-", "");
          btn.classList.remove(bgClass);
          btn.classList.add(`btn-outline-${color}`);
        }
      });

      const outlineClass = [...this.classList].find((c) =>
        c.startsWith("btn-outline-"),
      );
      if (outlineClass) {
        const color = outlineClass.replace("btn-outline-", "");
        this.classList.remove(outlineClass);
        this.classList.add(`bg-${color}`);
      }
      sortInput.value = this.dataset.sort;
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

  $slider = $("#slider").data("ionRangeSlider");
  fetch(`api/products?sort=price_desc&limit=1`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
  })
    .then((res) => res.json())
    .then((data) => {
      $slider.update({
        max: parseFloat(data.data[0].price),
      });
    })
    .catch((err) => console.error(err));
}

function debounce(func, delay) {
  let timeout;
  return function () {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, arguments), delay);
  };
}
