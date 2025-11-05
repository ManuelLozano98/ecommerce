$(document).ready(function () {
  getProducts();

  $("#form").on("submit", function (e) {
    e.preventDefault();
    const form = getById("form");
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

  loadCategories("categorySelect");
  showFullText();
  loadEditForm();
  loadDeleteButton();
  setupCounter("description", "counter");
  setupCounter("edit-description", "edit-counter");
  setupGenerateCode("generate-code", "barcode", "code");
  setupGenerateCode("edit-generate-code", "edit-barcode", "edit-code");
  validateUserProductCode("code", "barcode");
  validateUserProductCode("edit-code", "edit-barcode");
});

function getProducts() {
  let loaderTimeout;
  let loaderShownAt = null;
  const MIN_VISIBLE_TIME = 400; // ms
  const DEBOUNCE_DELAY = 200; // ms

  $("#tableProducts").DataTable({
    serverSide: true,
    processing: false,
    ajax: function (data, callback, settings) {
      loaderTimeout = setTimeout(() => {
        $(".loader").show();
        loaderShownAt = Date.now();
      }, DEBOUNCE_DELAY);

      $.ajax({
        url: "api/products/detailed",
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
        data: "product_name",
        render: function (data) {
          return truncateText(data);
        },
      },
      {
        data: "description",
        render: function (data) {
          return truncateText(data);
        },
      },
      {
        data: "code",
        render: function (data) {
          return truncateText(data);
        },
      },
      {
        data: "image",
        render: function (data) {
          let result = "No image found";
          if (data !== "") {
            result = `<img alt="${data.substring(
              0,
              data.indexOf(".")
            )}" src="uploads/images/${data}" width="100px" height="100px" object-fit:cover;/>`;
          }
          return result;
        },
      },
      { data: "stock" },
      {
        data: "price",
      },
      {
        data: "category_name",
        render: function (data) {
          return truncateText(data);
        },
      },
      { data: "created_at" },
      {
        data: "active",
        render: function (data, type, row) {
          return data === 1 ? "Yes" : "No";
        },
      },
      getActionsColumnDataTable(),
    ],
  });
}

async function loadCategories(selectId, id = "") {
  $(`#${selectId}`).empty();
  const { data, error } = await apiRequest(`api/categories/name`, {
    method: "GET",
  });
  let select = getById(selectId);

  setupSelect(select);
  if (data) {
    data.data.forEach((category) => {
      let option = new Option(category.name, category.id);
      if (id && id === category.id) {
        option.selected = true;
      }
      select.append(option);
    });

    if (error) {
      notifyErrorResponse(error);
    }
  }
}

function setupSelect(element) {
  $(element).select2({
    theme: "bootstrap4",
  });
}

async function insert() {
  const form = new FormData(getById("form"));
  form.delete("image");
  const formObj = Object.fromEntries(form.entries());
  const product = JSON.stringify(formObj);
  form.delete("image");
  const { data, error } = await apiRequest("api/products", {
    method: "POST",
    body: product,
  });
  if (data) {
    const imageFile = getById("form").image.files[0];
    if (imageFile) {
      const imageForm = new FormData();
      imageForm.append("image", imageFile);
      const { dataImage, errorImage } = await fetch(
        `api/products/${data.data.id}/image`,
        {
          method: "POST",
          body: imageForm,
        }
      );
      if (dataImage) {
        notifySuccessResponse(API_MSGS.Created);
        getDatatable("tableProducts").ajax.reload(null, false);
        return;
      }
      if (errorImage) {
        notifyErrorResponse(errorImage);
        return;
      }
    }
    notifySuccessResponse(API_MSGS.Created);
    getDatatable("tableProducts").ajax.reload(null, false);
  }
  if (error) {
    notifyErrorResponse(error);
  }
}

function showFullText() {
  $("#tableProducts").on("click", ".view-full-text", function () {
    $("#modal-body").empty();
    const fullText = decodeURIComponent($(this).data("full"));
    $("#modal-body").append(`<p>${fullText}</p>`);
    $("#viewModalText").modal("show");
  });
}

function loadEditForm() {
  $("#tableProducts").on("click", ".edit-button", function () {
    const table = $("#tableProducts").DataTable();
    let row = $(this).closest("tr");

    if (row.hasClass("child")) {
      row = row.prev(); // for responsive rows
    }

    const data = table.row(row).data();
    const descriptionLimit = 255;
    $("#edit-name").val(data.product_name);
    $("#productId").val(data.id);
    $("#edit-description").val(data.description);
    $("#edit-price").val(data.price.substring(0, data.price.length - 1)); // Remove the € symbol
    $("#edit-stock").val(data.stock);
    $("#edit-code").val(data.code);
    generateBarCode("#edit-barcode", data.code);
    loadCategories("edit-categorySelect", data.category_id);
    if (data.image !== "") {
      $("#edit-img").show();
      $("#edit-img").attr("src", "uploads/images/" + data.image);
    } else {
      $("#edit-img").hide();
    }
    $("#customSwitch1").prop("checked", data.active === 1);
    const $description = $("#edit-description");
    const $counter = $("#edit-counter");
    updateCounter($description, $counter, descriptionLimit);
  });
}

function generateBarCode(element, code) {
  JsBarcode(element, code);
  $(element).attr("width", "200px");
}

async function edit() {
  const form = new FormData(getById("form-edit"));
  let active = form.get("active");
  if (active === "on") {
    form.set("active", "1");
  } else {
    form.set("active", "0");
  }
  form.delete("image");
  const formObj = Object.fromEntries(form.entries());
  const product = JSON.stringify(formObj);
  const { data, error } = await apiRequest(`api/products/${formObj.id}`, {
    method: "PUT",
    body: product,
  });
  if (data) {
    const imageFile = getById("form-edit").image.files[0];
    if (imageFile) {
      const imageForm = new FormData();
      imageForm.append("image", imageFile);
      const { dataImage, errorImage } = await fetch(
        `api/products/${data.data.id}/image`,
        {
          method: "POST",
          body: imageForm,
        }
      );
      if (dataImage) {
        notifySuccessResponse(API_MSGS.Updated);
        getDatatable("tableProducts").ajax.reload(null, false);
        return;
      }
      if (errorImage) {
        notifyErrorResponse(errorImage);
        return;
      }
    }
    notifySuccessResponse(API_MSGS.Updated);
    getDatatable("tableProducts").ajax.reload(null, false);
  }
  if (error) {
    notifyErrorResponse(error);
  }
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

async function deleteItem(id) {
  const response = await getDeleteMsg();
  if (response.isConfirmed) {
    const { data, error } = await apiRequest(`api/products/${id}`, {
      method: "DELETE",
    });
    if (data) {
      notifySuccessResponse(API_MSGS.Deleted);
      getDatatable("tableProducts").ajax.reload(null, false);
    }
    if (error) {
      notifyErrorResponse(error);
    }
  }
}

function setupGenerateCode(buttonId, barcodeId, codeId) {
  document.getElementById(buttonId).addEventListener("click", function (event) {
    event.preventDefault();
    generateCode(barcodeId, codeId);
  });
}

function setupCounter(inputId, counterId, limit = 255) {
  getById(inputId).addEventListener("input", function () {
    updateCounter(this, getById(counterId), limit);
  });
}

function generateCode(element, elementId) {
  let code = getById(elementId);
  code.value = Date.now() + Math.floor(Math.random());
  validateCode(element, code.value, elementId);
}

async function validateCode(element, code, inputId) {
  const { data, error } = await apiRequest(`api/products/code/${code}`, {
    method: "GET",
  });
  if (data) {
    $("#" + inputId).addClass("is-invalid");
    $("#" + inputId).removeClass("is-valid");
  }
  if (error) {
    $("#" + inputId).removeClass("is-invalid");
    $("#" + inputId).addClass("is-valid");
    generateBarCode("#" + element, code);
  }
}
function debounce(func, delay) {
  let timeout;
  return function () {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, arguments), delay);
  };
}
function validateUserProductCode(codeId, barcodeId) {
  document.getElementById(codeId).addEventListener(
    "input",
    debounce(function () {
      validateCode(barcodeId, this.value, codeId);
    }, 500)
  );
}
