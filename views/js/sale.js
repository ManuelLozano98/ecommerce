$(document).ready(function () {
  $("#add-sale").on("submit", function (e) {
    e.preventDefault();
    const form = getById("add-sale");
    if (checkForm(form)) {
      insert();
    }
  });

  $("#form-edit").on("submit", function (e) {
    e.preventDefault();
    const form = getById("form-edit");
    if (checkForm(form)) {
      edit();
    }
  });
  $("#addBtn").on("click", async function (e) {
    e.preventDefault();
    // Clear form
    getById("add-sale").reset();
    $("#add-sale .card").empty();
    const { data: dataUsername, error: errorUsername } = await getUsernames();
    if (dataUsername) {
      loadUsernameData("users", dataUsername);
    }
    if (errorUsername) {
      notifyErrorResponse(errorUsername);
    }
    const { data: dataProductsName, error: errorProductsName } =
      await getProductNames();
    if (dataProductsName) {
      loadProductNameData("articles", dataProductsName);
    }
    if (errorProductsName) {
      notifyErrorResponse(errorProductsName);
    }
  });

  getSales();
  showFullText();
  loadDeleteButton();
  loadEditForm();
  setupSelect2();
  setupDateTimePicker();
  loadProductView();
  loadProductInfo();
  loadItemForm();
});

function loadProductNameData(selectId, data) {
  $(`#${selectId}`).empty();
  let select = getById(selectId);
  console.log(select);
  data.data.forEach((product) => {
    let option = new Option(product.name, product.id);
    select.append(option);
  });
}
function loadUsernameData(selectId, data) {
  $(`#${selectId}`).empty();
  let select = getById(selectId);
  console.log(select);
  data.data.forEach((user) => {
    let option = new Option(user.username, user.id);
    select.append(option);
  });
}

function setupDateTimePicker() {
  $("#datetime").datetimepicker({ icons: { time: "far fa-clock" } });
  $("#edit-datetime").datetimepicker({ icons: { time: "far fa-clock" } });
}

async function insert() {
  const formElement = getById("add-sale");
  const formData = new FormData(formElement);
  const users = formData.getAll("user_id[]").map(Number);
  const products = formData.getAll("product_id[]");
  const createdAt = moment(formData.get("created_at")).format(
    "YYYY-MM-DD HH:mm:ss",
  );

  const commonSaleData = {
    created_at: createdAt,
    payment_method: formData.get("payment_method"),
    total_amount: formData.get("total_amount"),
    status: formData.get("status"),
  };
  // Insert sale
  for (const userId of users) {
    commonSaleData.user_id = userId;
    const { data, error } = await apiRequest("api/sales", {
      method: "POST",
      body: JSON.stringify(commonSaleData),
    });
    if (data) {
      notifySuccessResponse(API_MSGS.Created);
    }
    if (error) {
      notifyErrorResponse(error);
    }
    // Insert sale items
    for (let i = 0; i < products.length; i++) {
      const item = {
        product_id: parseInt(products[i]),
        quantity: parseInt(formData.getAll("quantity")[i]),
        subtotal: parseFloat(formData.getAll("subtotal")[i]),
        price: parseFloat(formData.getAll("price")[i]),
      };
      const { data: saleItem, error: itemError } = await apiRequest(
        `api/sales/${data.data.id}/items`,
        {
          method: "POST",
          body: JSON.stringify(item),
        },
      );

      if (itemError) {
        notifyErrorResponse(itemError);
      }
    }
  }

  getDatatable("tableSales").ajax.reload(null, false);
}

async function deleteItem(id) {
  const msg =
    "<p style='color: red'>Please note that this will also remove associated products and users.</p>";
  const response = await getDeleteMsg(msg);
  if (response.isConfirmed) {
    const { data, error } = await apiRequest(`api/sales/${id}`, {
      method: "DELETE",
    });
    if (data) {
      notifySuccessResponse(API_MSGS.Deleted);
      getDatatable("tableSales").ajax.reload(null, false);
    }
    if (error) {
      notifyErrorResponse(error);
    }
  }
}
async function deleteProduct(idSale, idProduct) {
  const response = await getDeleteMsg();
  if (response.isConfirmed) {
    const { data, error } = await apiRequest(
      `api/sales/${idSale}/items/${idProduct}`,
      {
        method: "DELETE",
      },
    );
    if (data) {
      notifySuccessResponse(API_MSGS.Deleted);
      getDatatable("tableSales").ajax.reload(null, false);
      $(`#product-${idProduct}`).remove();
    }
    if (error) {
      notifyErrorResponse(error);
    }
  }
}

async function edit() {
  let id = $("#edit-idsale").val();
  let form = new FormData(getById("form-edit"));
  console.log(form.get("created_at"));
  const formattedDate = moment(
    form.get("created_at"),
    "MM/DD/YYYY h:mm A",
  ).format("YYYY-MM-DD HH:mm:ss");
  console.log(formattedDate);
  form.set("created_at", formattedDate);
  let obj = Object.fromEntries(form.entries());
  const { data, error } = await apiRequest(`api/sales/${id}`, {
    method: "PUT",
    body: JSON.stringify(obj),
  });
  if (data) {
    notifySuccessResponse(API_MSGS.Updated);
    getDatatable("tableSales").ajax.reload(null, false);
  }
  if (error) {
    notifyErrorResponse(error);
  }
  await editSaleItems(data.data.id);
  await addSaleItems(data.data.id);
}

async function addSaleItems(id) {
  $(`#edit-tab2`)
    .find('div[id^="card-"]')
    .each(async function () {
      let $card = $(this);
      let item = {
        product_id: parseInt(
          $card.find("select[name='product_id']").val() || 0,
          10,
        ),
        quantity: parseInt($card.find("input[name='quantity']").val() || 0, 10),
        price: parseFloat($card.find("input[name='price']").val() || 0),
        subtotal: parseFloat($card.find("input[name='subtotal']").val() || 0),
      };
      const { data, error } = await apiRequest(`api/sales/${id}/items/`, {
        method: "POST",
        body: JSON.stringify(item),
      });
      if (data) {
        notifySuccessResponse(API_MSGS.Created);
        getDatatable("tableSales").ajax.reload(null, false);
      }
      if (error) {
        notifyErrorResponse(error);
      }
    });
}

async function editSaleItems(saleId) {
  $(`#edit-tab2 div[id^="product-"]`).each(async function () {
    let $card = $(this);
    const divId = $card.attr("id");
    const idSaleItem = divId.split("-")[1];
    if (idSaleItem !== "edit") {
      let item = {
        quantity: parseInt($card.find("input[name='quantity']").val() || 0, 10),
        price: parseFloat($card.find("input[name='price']").val() || 0),
        subtotal: parseFloat($card.find("input[name='subtotal']").val() || 0),
        product_id: parseInt(
          $card.find("input[name='original-product']").val() || 0,
          10,
        ),
      };
      const { data, error } = await apiRequest(
        `api/sales/${saleId}/items/${idSaleItem}`,
        {
          method: "PUT",
          body: JSON.stringify(item),
        },
      );
      if (data) {
        getDatatable("tableSales").ajax.reload(null, false);
      }
      if (error) {
        notifyErrorResponse(error);
      }
    }
  });
}

function getSales() {
  let loaderTimeout;
  let loaderShownAt = null;
  const MIN_VISIBLE_TIME = 400; // ms
  const DEBOUNCE_DELAY = 200; // ms

  $("#tableSales").DataTable({
    serverSide: true,
    processing: false,
    ajax: function (data, callback, settings) {
      loaderTimeout = setTimeout(() => {
        $(".loader").show();
        loaderShownAt = Date.now();
      }, DEBOUNCE_DELAY);

      $.ajax({
        url: "api/sales/detailed/username/",
        method: "GET",
        data: data,
        success: function (response) {
          callback({
            draw: response.draw,
            recordsTotal: response.recordsTotal,
            recordsFiltered: response.recordsFiltered,
            data: response.data,
          });
        },
        error: function (xhr, status, error) {
          console.error("An error occurred while loading data:", error);
          callback({
            draw: data.draw,
            recordsTotal: 0,
            recordsFiltered: 0,
            data: [],
          });
          toastr.error(error);
        },
        complete: function () {
          clearTimeout(loaderTimeout);
          if (!loaderShownAt) return;

          const elapsed = Date.now() - loaderShownAt;
          const remainingTime = MIN_VISIBLE_TIME - elapsed;

          if (remainingTime > 0) {
            setTimeout(() => {
              $(".loader").hide();
              loaderShownAt = null;
            }, remainingTime);
          } else {
            $(".loader").hide();
            loaderShownAt = null;
          }
        },
      });
    },
    ...getSettingsDataTable(),
    buttons: getButtonsDataTable(),
    dom: getDomStyleDataTable(),
    columns: [
      { data: "sales.id" },
      {
        data: "username",
        render: function (data, type, row) {
          return `<span id="username-${row.sales.user_id}" data-user_id="${row.sales.user_id}">${data}</span>`;
        },
      },
      { data: "sales.created_at" },
      { data: "sales.payment_method" },
      { data: "sales.status" },
      {
        data: "sales.total_amount",
        render: function (data) {
          return data + "€";
        },
      },
      {
        data: null,
        orderable: false,
        searchable: false,
        className: "no-export",
        render: function (data) {
          const id = data.sales.id;

          let viewBtn = "";
          if (data.sales.items.length > 0) {
            viewBtn = `<button id="btn-view${id}" type="button" class="btn btn-primary btn-sm rounded-0 view-button" data-toggle="modal" data-target="#modal-view" data-placement="top" title="View info"><i class="fa fa-eye"></i></button>`;
          }
          let editBtn = `<button id="btn-edit${id}" class="btn btn-success btn-sm rounded-0 edit-button" type="button" data-toggle="modal" data-target="#modal-edit-default" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>`;
          let deleteBtn = `<button id="btn-delete_${id}" class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button>`;
          return viewBtn + editBtn + deleteBtn;
        },
      },
    ],
  });
}

async function loadEditForm() {
  const { data: dataUsername, error: errorUsername } = await getUsernames();
  if (dataUsername) {
    loadUsernameData("edit-users", dataUsername);
  }
  if (errorUsername) {
    notifyErrorResponse(errorUsername);
  }
  $("#tableSales").on("click", ".edit-button", function () {
    let row = $(this).closest("tr");
    let table = $("#tableSales").DataTable();
    if (row.hasClass("child")) {
      row = row.prev(); // needed for responsive tables
    }
    let data = table.row(row).data();
    let paymentMethod = data.sales.payment_method;
    paymentMethod = paymentMethod.toLowerCase().replace(/\s+/g, "_");
    $("#edit-tab2")
      .find("div.card.mb-3")
      .each(function () {
        $(this).remove();
      });
    $("#edit-idsale").val(data.sales.id);
    $("#edit-users").val(data.sales.user_id);
    $("#edit-payment_method").val(paymentMethod);
    $("#edit-status").val(data.sales.status.toLowerCase());
    $("#edit-total").val(data.sales.total_amount);
    $("#edit-datetime").datetimepicker(
      "date",
      moment(data.sales.created_at, "YYYY-MM-DD HH:mm:ss"),
    );
    for (let i = 0; i < data.sales.items.length; i++) {
      let saleItem = {
        data: {
          id: data.sales.items[i].id,
          price: data.sales.items[i].price,
          name: data.sales.items[i].product_name,
          subtotal: data.sales.items[i].subtotal,
          quantity: data.sales.items[i].quantity,
        },
      };
      loadFormProducts(saleItem, $("#edit-tab2"));
    }

    // Add input type hidden with product id value
    $(`#edit-tab2 div[id^="product-"]`).each(function (index) {
      $(this).append(
        `<input type='hidden' name='original-product' value='${data.sales.items[index].product_id}'>`,
      );
      $(this)
        .find(`div.card-header`)
        .append(
          `<button id="btn-delete-${$(this).attr(
            "id",
          )}" class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete" style="display: flex; float: right;"><i class="fa fa-trash"></i></button>`,
        );
      $(`#btn-delete-${$(this).attr("id")}`).on("click", function () {
        let productId = $(this).attr("id").split("-")[3];
        deleteProduct(data.sales.id, productId);
      });
    });

    $(`#edit-tab2 input[name='quantity']`).each(function (i) {
      $(this).on("change", function () {
        const $quantityInput = $(this);
        const $row = $quantityInput.closest(".row");
        const quantity = parseFloat($quantityInput.val());
        const price = parseFloat($row.find("input[name='price']").val());
        const subtotal = quantity * price;

        $row.find("input[name='subtotal']").val(subtotal.toFixed(2));
        calculateTotal(
          $("#edit-total"),
          $("#edit-tab2 input[name='subtotal']"),
        );
      });
      const $container = $(this).closest(".row");
      $(this).val(data.sales.items[i].quantity);
      $container
        .find("input[name='subtotal']")
        .val(data.sales.items[i].subtotal);
    });

    $(`#edit-tab2 input[name='price']`).each(function (i) {
      $(this).on("change", function () {
        const $priceInput = $(this);
        const $row = $priceInput.closest(".row");
        const price = parseFloat($row.find("input[name='price']").val());
        const subtotal = price;

        $row.find("input[name='subtotal']").val(subtotal.toFixed(2));
        calculateTotal(
          $("#edit-total"),
          $("#edit-tab2 input[name='subtotal']"),
        );
      });
      const $container = $(this).closest(".row");
      $container
        .find("input[name='subtotal']")
        .val(data.sales.items[i].subtotal);
    });
  });
}

function loadFormProducts(product, dataTarget = $("#tab2")) {
  product = product.data;

  // Card container
  const $card = $(
    `<div class="card mb-3 bg-light mt-4" id="product-${product.id}"></div>`,
  );
  const $cardHeader = $(
    `<div class="card-header" id="header-${product.id}">${product.name}</div>`,
  );
  const $cardBody = $('<div class="card-body"></div>');
  const $row = $('<div class="row"></div>');

  // Quantity
  const $quantityCol = $('<div class="col-sm-4"></div>')
    .append(
      `<label for="quantity-${product.id}" class="form-label">Quantity</label>`,
    )
    .append(
      $('<div class="input-group"></div>').append(
        $("<input>", {
          type: "number",
          step: 1,
          min: 1,
          class: "form-control",
          name: "quantity",
          id: `quantity-${product.id}`,
          value: 1,
        }),
      ),
    );

  // Subtotal
  const $subtotalCol = $('<div class="col-sm-4"></div>')
    .append(
      `<label for="subtotal-${product.id}" class="form-label">Subtotal</label>`,
    )
    .append(
      $('<div class="input-group"></div>').append(
        $("<input>", {
          type: "number",
          class: "form-control",
          name: "subtotal",
          id: `subtotal-${product.id}`,
          value: product.price,
          min: 0,
          step: "0.01",
          inputmode: "decimal",
          pattern: "^d+([.,]d{1,2})?$",
          readonly: true,
        }),
        $('<span class="input-group-text">€</span>'),
      ),
    );

  // Price
  const $priceCol = $('<div class="col-sm-4"></div>')
    .append(`<label for="price-${product.id}" class="form-label">Price</label>`)
    .append(
      $('<div class="input-group"></div>').append(
        $("<input>", {
          type: "number",
          class: "form-control",
          name: "price",
          id: `price-${product.id}`,
          value: product.price,
          min: 0,
          step: "0.01",
          inputmode: "decimal",
          pattern: "^d+([.,]d{1,2})?$",
        }),
        $('<span class="input-group-text">€</span>'),
      ),
    );

  $row.append($quantityCol, $subtotalCol, $priceCol);
  $cardBody.append($row);
  $card.append($cardHeader, $cardBody);
  dataTarget.append($card);

  $(`#add-sale #quantity-${product.id}`).on("change", function (e) {
    let subTotal =
      $(`#price-${product.id}`).val() * $(`#quantity-${product.id}`).val();
    $(`#subtotal-${product.id}`).val(subTotal.toFixed(2));
    calculateTotal($("#total"), $("#add-sale input[name='subtotal']"));
  });

  $(`#add-sale #price-${product.id}`).on("change", function (e) {
    let subTotal =
      $(`#price-${product.id}`).val() * $(`#quantity-${product.id}`).val();
    $(`#subtotal-${product.id}`).val(subTotal.toFixed(2));
    calculateTotal($("#total"), $("#add-sale input[name='subtotal']"));
  });
}

function showFullText() {
  $("#tableSales").on("click", ".view-full-text", function () {
    $("#modal-body").empty();
    const fullText = decodeURIComponent($(this).data("full"));
    $("#modal-body").append(`<p>${fullText}</p>`);
    $("#viewModalText").modal("show");
  });
}

function appendSelectItemForm() {
  let id = crypto.getRandomValues(new Uint32Array(1))[0].toString(36);
  let cardId = `card-${id}`;

  $("#edit-tab2").append(`
         <div class="card mb-3 bg-light mt-4" id="${cardId}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>New Product</span>
                <button type="button" class="btn-close" aria-label="Close" data-target="#${cardId}">
                    <span>&times;</span>
                </button>
            </div>
            <div class="card-body">
                <select name="product_id" class="form-control select-product" id="addProduct-${id}"></select>
                <div class="product-form-container" id="form-${id}"></div>
            </div>
        </div>
    `);

  $("#edit-tab2").on("click", ".btn-close", function () {
    const target = $(this).data("target");
    $(target).remove();
  });
  return { id, cardId };
}

function loadItemForm() {
  $("#addProductBtn").on("click", async function (e) {
    e.preventDefault();
    const { id, cardId } = appendSelectItemForm();
    let loadForm = false;

    const { data: dataProductsName, error: errorProductsName } =
      await getProductNames();
    if (dataProductsName) {
      loadProductNameData("addProduct-" + id, dataProductsName);
    }
    if (errorProductsName) {
      notifyErrorResponse(errorProductsName);
    }
    appendItemForm(loadForm, id, cardId);
  });
}
function appendItemForm(loadForm, id, cardId) {
  $(`#addProduct-${id}`).on("change", async function () {
    const selectedId = $(this).val();
    const { data, error } = await getProduct(selectedId);

    let productCard;

    if (data) {
      if (!loadForm) {
        loadFormProducts(data, $("#edit-tab2"));
        $(`#product-${selectedId}`).attr("id", `product-edit-${id}`);
        productCard = $(`#product-edit-${id}`);

        productCard
          .find(`#quantity-${data.data.id}`)
          .attr("id", `quantity-edit-${id}`);
        productCard
          .find(`#subtotal-${data.data.id}`)
          .attr("id", `subtotal-edit-${id}`);
        productCard
          .find(`#price-${data.data.id}`)
          .attr("id", `price-edit-${id}`);
      }

      loadForm = true;
      productCard = $(`#product-edit-${id}`);

      $(`#${cardId}`).append(productCard);

      productCard.find(".card-header").text(data.data.name);
      $(`#price-edit-${id}`).val(parseFloat(data.data.price));
      $(`#subtotal-edit-${id}`).val(parseFloat(data.data.price));
      $(`#quantity-edit-${id}`).val(1);

      $(`#quantity-edit-${id}`).on("change", function () {
        let subTotal =
          $(`#price-edit-${id}`).val() * $(`#quantity-edit-${id}`).val();
        $(`#subtotal-edit-${id}`).val(subTotal.toFixed(2));

        const $total = $("#edit-total");
        const $allSubtotal = $("#edit-tab2 input[name='subtotal']");
        calculateTotal($total, $allSubtotal);
      });

      $(`#price-edit-${id}`).on("change", function () {
        let subTotal =
          $(`#price-edit-${id}`).val() * $(`#quantity-edit-${id}`).val();
        $(`#subtotal-edit-${id}`).val(subTotal.toFixed(2));

        const $total = $("#edit-total");
        const $allSubtotal = $("#edit-tab2 input[name='subtotal']");
        calculateTotal($total, $allSubtotal);
      });
    }

    if (error) {
      console.error("Error loading product:", error);
    }
  });
}

function loadProductInfo() {
  $("#articles").on("select2:select", async function (e) {
    const { data, error } = await getProduct(e.params.data.id);
    if (data) {
      console.log(data);
      loadFormProducts(data);
      calculateTotal($("#total"), $("#add-sale input[name='subtotal']"));
    }
    if (error) {
      notifyErrorResponse(error);
    }
  });

  $("#articles").on("select2:unselect", function (e) {
    $(`#product-${e.params.data.id}`).remove();
    calculateTotal($("#total"), $("#add-sale input[name='subtotal']"));
  });
}

function loadProductView(productsPerPage = 1) {
  $("#tableSales").on("click", ".view-button", function () {
    let row = $(this).closest("tr");
    let table = $("#tableSales").DataTable();
    if (row.hasClass("child")) {
      row = row.prev(); // needed for responsive tables
    }
    let data = table.row(row).data();
    let items = data.sales.items;
    let modalBody = getById("modal-body");
    let currentPage = 1;
    modalBody.dataset.products = JSON.stringify(items);
    modalBody.dataset.perPage = productsPerPage;
    renderProducts(items, currentPage, productsPerPage);
    renderPagination(items.length, productsPerPage, currentPage);
    initProductsPagination();
  });
}
function initProductsPagination() {
  document
    .getElementById("pagination-container")
    .addEventListener("click", function (e) {
      e.preventDefault();
      let target = e.target;

      if (target.tagName === "A" && target.dataset.page) {
        let page = parseInt(target.dataset.page);
        let modalBody = document.getElementById("modal-body");
        let products = JSON.parse(modalBody.dataset.products);
        let perPage = parseInt(modalBody.dataset.perPage);

        if (
          !isNaN(page) &&
          page >= 1 &&
          page <= Math.ceil(products.length / perPage)
        ) {
          renderProducts(products, page, perPage);
          renderPagination(products.length, perPage, page);
        }
      }
    });
}

function renderProducts(products, currentPage, productPerPage) {
  let modalProduct = getById("modal-body");
  while (modalProduct.firstChild) {
    modalProduct.removeChild(modalProduct.firstChild);
  }
  let startIndex = (currentPage - 1) * productPerPage;
  let endIndex = startIndex + productPerPage;
  let pageProducts = products.slice(startIndex, endIndex);

  pageProducts.forEach((element, index) => {
    let productRow = document.createElement("div");
    productRow.classList.add("row");

    let fields = [
      { label: "Product", value: element.product_name },
      { label: "Quantity", value: element.quantity },
      { label: "Price", value: element.price },
      { label: "Subtotal", value: element.subtotal },
    ];

    fields.forEach((field) => {
      let col = document.createElement("div");
      col.classList.add("col-sm-12");

      let formGroup = document.createElement("div");
      formGroup.classList.add("form-group");

      let label = document.createElement("label");
      label.setAttribute("for", `${field.label}-${index}`);
      label.innerText = field.label;

      let input = document.createElement("input");
      input.classList.add("form-control");
      input.setAttribute("type", "text");
      input.setAttribute("name", `${field.label}-${index}`);
      input.setAttribute("id", `${field.label}-${index}`);
      input.setAttribute("readonly", true);
      input.value = field.value;

      formGroup.appendChild(label);
      formGroup.appendChild(input);
      col.appendChild(formGroup);
      productRow.appendChild(col);
    });

    modalProduct.appendChild(productRow);
  });
}

function renderPagination(totalItems, perPage, currentPage) {
  let totalPages = Math.ceil(totalItems / perPage);
  let container = getById("pagination-container");
  container.innerHTML = "";

  // Prev
  let prev = document.createElement("li");
  prev.classList.add("page-item");
  if (currentPage === 1) prev.classList.add("disabled");
  prev.innerHTML = `<a class="page-link" href="#" data-page="${
    currentPage - 1
  }">Anterior</a>`;
  container.appendChild(prev);
  // Pages
  for (let i = 1; i <= totalPages; i++) {
    let li = document.createElement("li");
    li.classList.add("page-item");
    if (i === currentPage) li.classList.add("active");

    li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
    container.appendChild(li);
  }

  // Next
  let next = document.createElement("li");
  next.classList.add("page-item");
  if (currentPage === totalPages) next.classList.add("disabled");

  next.innerHTML = `<a class="page-link" href="#" data-page="${
    currentPage + 1
  }">Siguiente</a>`;
  container.appendChild(next);
}

function setupCounter(inputId, counterId, limit = 255) {
  getById(inputId).addEventListener("input", function () {
    updateCounter(this, getById(counterId), limit);
  });
}

function loadDeleteButton() {
  document.addEventListener("click", function (e) {
    let deleteBtn = e.target.closest('[id^="btn-delete_"]');
    if (deleteBtn) {
      let id = deleteBtn.id.split("_")[1];
      deleteItem(id);
    }
  });
}
function setupSelect2() {
  $(".select2").select2();
}
async function getProduct(id) {
  return await apiRequest(`api/products/${id}`, {
    method: "GET",
  });
}

async function getProductNames() {
  return await apiRequest(`api/products/name`, {
    method: "GET",
  });
}

async function getUsernames() {
  return await apiRequest("api/users/username", {
    method: "GET",
  });
}

function calculateTotal($totalElement, $subtotalElements) {
  $allSubtotalVal = 0;
  $subtotalElements.each(function () {
    let result = parseFloat($(this).val());
    $allSubtotalVal += result;
  });
  $allSubtotalVal = $allSubtotalVal.toFixed(2);
  $totalElement.val($allSubtotalVal);
}
