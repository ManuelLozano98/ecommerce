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
    getCategories();
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
      getCategories();
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
    getCategories();
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

