$(document).ready(function () {
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

  getCategories();
  showFullText();
  setupCounter("description", "counter");
  setupCounter("edit-description", "edit-counter");
  loadDeleteButton();
  loadEditForm();
  loadModalByURL();
});

function loadModalByURL() {
  let params = new URLSearchParams(window.location.search);
  if (params.get("openModal") === "true") {
    $("#modal-default").modal("show");
  }
}

async function insert() {
  const form = getById("form");
  const category = getJSONForm(form);
  const { data, error } = await apiRequest("api/categories", {
    method: "POST",
    body: category,
  });
  if (data) {
    notifySuccessResponse(API_MSGS.Created);
    getDatatable("tableCategories").ajax.reload(null, false);
  }
  if (error) {
    notifyErrorResponse(error);
  }
}

async function deleteItem(id) {
  const msg =
    "<p style='color: red'>Please note that this will also remove associated products.</p>";
  const response = await getDeleteMsg(msg);
  if (response.isConfirmed) {
    const { data, error } = await apiRequest(`api/categories/${id}`, {
      method: "DELETE",
    });
    if (data) {
      notifySuccessResponse(API_MSGS.Deleted);
      getDatatable("tableCategories").ajax.reload(null, false);
    }
    if (error) {
      notifyErrorResponse(error);
    }
  }
}

async function edit() {
  const editForm = getById("form-edit");
  let categoryForm = getForm(editForm);
  categoryForm.active === "on"
    ? (categoryForm.active = 1)
    : (categoryForm.active = 0);

  const category = JSON.stringify(categoryForm);
  const { data, error } = await apiRequest(
    `api/categories/${categoryForm.id}`,
    {
      method: "PUT",
      body: category,
    }
  );
  if (data) {
    notifySuccessResponse(API_MSGS.Updated);
    getDatatable("tableCategories").ajax.reload(null, false);
  }
  if (error) {
    console.log(error);
    notifyErrorResponse(error);
  }
}

function getCategories() {
  let loaderTimeout;
  let loaderShownAt = null;
  const MIN_VISIBLE_TIME = 400; // ms
  const DEBOUNCE_DELAY = 200; // ms

  $("#tableCategories").DataTable({
    serverSide: true,
    processing: false,
    ajax: function (data, callback, settings) {
      loaderTimeout = setTimeout(() => {
        $(".loader").show();
        loaderShownAt = Date.now();
      }, DEBOUNCE_DELAY);

      $.ajax({
        url: "api/categories",
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
        data: "name",
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
        data: "active",
        render: function (data, type, row) {
          return data === 1 ? "Yes" : "No";
        },
      },
      getActionsColumnDataTable(),
    ],
  });
}

function loadEditForm() {
  $("#tableCategories").on("click", ".edit-button", function () {
    const table = $("#tableCategories").DataTable();
    let row = $(this).closest("tr");

    if (row.hasClass("child")) {
      row = row.prev(); // for responsive rows
    }

    const data = table.row(row).data();
    const descriptionLimit = 255;

    $("#edit-name").val(data.name);
    $("#edit-idcategory").val(data.id);
    $("#edit-description").val(data.description);
    $("#customSwitch1").prop("checked", data.active === 1);

    const $description = $("#edit-description");
    const $counter = $("#edit-counter");
    updateCounter($description, $counter, descriptionLimit);
  });
}

function showFullText() {
  $("#tableCategories").on("click", ".view-full-text", function () {
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
