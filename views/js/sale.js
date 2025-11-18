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
  $(document).on("click", "[id^='btn-edit']", async function (e) {
    e.preventDefault();
    const { data: dataUsername, error: errorUsername } = await getUsernames();
    if (dataUsername) {
      loadUsernameData("edit-users", dataUsername);
    }
    if (errorUsername) {
      notifyErrorResponse(errorUsername);
    }
  });

  getSales();
  showFullText();
  loadDeleteButton();
  loadEditForm();
  setupSelect2();
  setupDateTimePicker();
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
  const users = formData.getAll("user_id[]");
  const products = formData.getAll("product_id[]");
  const formattedDate = moment(formData.get("created_at")).format(
    "YYYY-MM-DD HH:mm:ss"
  );
  formData.set("created_at", formattedDate);
  for (let i = 0; i < users.length; i++) {
    formData.set("user_id", parseInt(users[i]));
    for (let x = 0; x < products.length && i !== users.length; x++) {
      formData.set("product_id", parseInt(products[x]));
      const { data, error } = await apiRequest("api/sales", {
        method: "POST",
        body: JSON.stringify(Object.fromEntries(formData.entries())),
      });
      if (data) {
        notifySuccessResponse(API_MSGS.Created);
        getDatatable("tableSales").ajax.reload(null, false);
      }
      if (error) {
        notifyErrorResponse(error);
      }
    }
  }
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
  const response = await getDeleteMsg(msg);
  if (response.isConfirmed) {
    const { data, error } = await apiRequest(
      `api/sales/${idSale}/items/${idProduct}`,
      {
        method: "DELETE",
      }
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
    "MM/DD/YYYY h:mm A"
  ).format("YYYY-MM-DD HH:mm:ss");
  console.log(formattedDate);
  form.set("created_at", formattedDate);
  let obj = Object.fromEntries(form.entries());
  const { data, error } = await apiRequest(`api/sales/${id}`, {
    method: "PUT",
    body: JSON.stringify(obj),
  });
  if (data) {
    notifySuccessResponse(API_MSGS.Created);
    getDatatable("tableSales").ajax.reload(null, false);
  }
  if (error) {
    notifyErrorResponse(error);
  }
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
        url: "api/sales/detailed",
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
      { data: "id" },
      {
        data: "username",
        render: function (data, type, row) {
          return `<span id="username-${row.user_id}" data-user_id="${row.user_id}">${data}</span>`;
        },
      },
      { data: "created_at" },
      { data: "payment_method" },
      { data: "status" },
      {
        data: "total_amount",
        render: function (data) {
          return data + "€";
        },
      },
      {
        data: null,
        title: "Actions",
        orderable: false,
        searchable: false,
        className: "no-export",
        render: function (data, type, row) {
          let id = Object.values(data)[0];
          let viewBtn = `<button id="btn-view${id}" type="button" class="btn btn-primary btn-sm rounded-0 view-button" data-toggle="modal" data-target="#modal-view" data-placement="top" title="View info"><i class="fa fa-eye"></i></button>`;
          let editBtn = `<button id="btn-edit${id}" class="btn btn-success btn-sm rounded-0 edit-button" type="button" data-toggle="modal" data-target="#modal-edit-default" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>`;
          let deleteBtn = `<button id="btn-delete_${id}" class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button>`;
          return viewBtn + editBtn + deleteBtn;
        },
      },
    ],
  });
}

function loadEditForm() {
  $("#tableSales").on("click", ".edit-button", function () {
    let row = $(this).closest("tr");
    let table = $("#tableSales").DataTable();
    if (row.hasClass("child")) {
      row = row.prev(); // needed for responsive tables
    }
    let data = table.row(row).data();
    let paymentMethod = data.payment_method;
    paymentMethod = paymentMethod.toLowerCase().replace(/\s+/g, "_");
    // $("#edit-tab2").empty();
    $("#edit-tab2")
      .find("div.card.mb-3")
      .each(function () {
        $(this).remove();
      });
    $("#edit-idsale").val(data.id);
    $("#edit-users").val(data.user_id);
    $("#edit-payment_method").val(paymentMethod);
    $("#edit-status").val(data.status.toLowerCase());
    $("#edit-total").val(data.total_amount);
    $("#edit-datetime").datetimepicker(
      "date",
      moment(data.created_at, "YYYY-MM-DD HH:mm:ss")
    );
    for (let i = 0; i < data.items.length; i++) {
      let saleItem = {
        data: {
          id: data.items[i].id,
          price: data.items[i].price,
          name: data.items[i].product_name,
          subtotal: data.items[i].subtotal,
          quantity: data.items[i].quantity,
        },
      };
      loadFormProducts(saleItem, $("#edit-tab2"));
    }

    $(`#edit-tab2 div[id^="product-"]`).each(function () {
      $(this)
        .find(`div.card-header`)
        .append(
          `<button id="btn-delete-${$(this).attr(
            "id"
          )}" class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete" style="display: flex; float: right;"><i class="fa fa-trash"></i></button>`
        );
      $(`#btn-delete-${$(this).attr("id")}`).on("click", function () {
        let productId = $(this).attr("id").split("-")[3];
        deleteProduct(data.id, productId);
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
          $("#edit-tab2 input[name='subtotal']")
        );
      });
      const $container = $(this).closest(".row");
      $(this).val(data.items[i].quantity);
      $container.find("input[name='subtotal']").val(data.items[i].subtotal);
    });
  });
}

function loadFormProducts(product, dataTarget = $("#tab2")) {
  product = product.data;

  // Card container
  const $card = $(
    `<div class="card mb-3 bg-light" id="product-${product.id}"></div>`
  );
  const $cardHeader = $(
    `<div class="card-header" id="header-${product.id}">${product.name}</div>`
  );
  const $cardBody = $('<div class="card-body"></div>');
  const $row = $('<div class="row"></div>');

  // Quantity
  const $quantityCol = $('<div class="col-sm-4"></div>')
    .append(
      `<label for="quantity-${product.id}" class="form-label">Quantity</label>`
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
        })
      )
    );

  // Subtotal
  const $subtotalCol = $('<div class="col-sm-4"></div>')
    .append(
      `<label for="subtotal-${product.id}" class="form-label">Subtotal</label>`
    )
    .append(
      $('<div class="input-group"></div>').append(
        $("<input>", {
          type: "text",
          class: "form-control",
          name: "subtotal",
          id: `subtotal-${product.id}`,
          value: product.price,
        }),
        $('<span class="input-group-text">€</span>')
      )
    );

  // Price
  const $priceCol = $('<div class="col-sm-4"></div>')
    .append(`<label for="price-${product.id}" class="form-label">Price</label>`)
    .append(
      $('<div class="input-group"></div>').append(
        $("<input>", {
          type: "text",
          class: "form-control",
          name: "price",
          id: `price-${product.id}`,
          value: product.price,
        }),
        $('<span class="input-group-text">€</span>')
      )
    );

  $row.append($quantityCol, $subtotalCol, $priceCol);
  $cardBody.append($row);
  $card.append($cardHeader, $cardBody);
  dataTarget.append($card);

  $(`#add-sale #quantity-${product.id}`).on("change", function (e) {
    let subTotal =
      $(`#price-${product.id}`).val() * $(`#quantity-${product.id}`).val();
    $(`#subtotal-${product.id}`).val(subTotal.toFixed(2));
    calculateTotal($("#total"), $("#add-sale input[name=subtotal]"));
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
  $totalElement.val($allSubtotalVal);
}
