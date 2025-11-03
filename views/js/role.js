$(document).ready(function () {
  getRoles();
  setupCounter("description", "counter");
  setupCounter("edit-description", "edit-counter");
  showFullText();
  loadEditForm();
  setupSelect2();
  loadUsernames("users");
  loadDeleteButton();
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
});

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

function setupCounter(inputId, counterId, limit = 255) {
  getById(inputId).addEventListener("input", function () {
    updateCounter(this, getById(counterId), limit);
  });
}

async function getRoles() {
  $("#tableRoles").DataTable({
    serverSide: true,
    processing: false,
    ajax: function (data, callback, settings) {
      $.ajax({
        url: "api/roles",
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

function showFullText() {
  $("#tableRoles").on("click", ".view-full-text", function () {
    $("#modal-body").empty();
    const fullText = decodeURIComponent($(this).data("full"));
    $("#modal-body").append(`<p>${fullText}</p>`);
    $("#viewModalText").modal("show");
  });
}

async function insert() {
  const formElement = getById("form");
  const formData = new FormData(formElement);
  const role = getJSONForm(formElement);
  const { data, error } = await apiRequest("api/roles", {
    method: "POST",
    body: role,
  });
  if (data) {
    let dataUserRole = null;
    let errorUserRole = null;
    const users = formData.getAll("user_id[]");
    console.log(users);
    for (let i = 0; i < users.length; i++) {
      let userId = users[i];
      const role = data.data.id;
      const roleBody = JSON.stringify({ role_id: role });
      ({ dataUserRole, errorUserRole } = await apiRequest(
        `api/users/${userId}/roles/`,
        {
          method: "POST",
          body: roleBody,
        }
      ));
    }
    if (dataUserRole) {
      notifySuccessResponse(API_MSGS.Created);
      getDatatable().ajax.reload(null, false);
      return;
    }
    if (errorUserRole) {
      notifyErrorResponse(errorUserRole);
      return;
    }
    notifySuccessResponse(API_MSGS.Created);
    getDatatable().ajax.reload(null, false);
  }
  if (error) {
    notifyErrorResponse(error);
  }
}

async function deleteItem(id) {
  const msg =
    "<p style='color: red'>Please note that this will also remove associated users.</p>";
  const response = await getDeleteMsg(msg);
  if (response.isConfirmed) {
    const { data, error } = await apiRequest(`api/roles/${id}`, {
      method: "DELETE",
    });
    if (data) {
      notifySuccessResponse(API_MSGS.Deleted);
      getDatatable().ajax.reload(null, false);
    }
    if (error) {
      notifyErrorResponse(error);
    }
  }
}

async function edit() {
  const editForm = getById("form-edit");
  const id = getById("edit-idrole").value;
  let roleForm = getForm(editForm);
  roleForm.active === "on" ? (roleForm.active = 1) : (roleForm.active = 0);

  const role = JSON.stringify(roleForm);
  const { data, error } = await apiRequest(`api/roles/${id}`, {
    method: "PUT",
    body: role,
  });
  if (data) {
    notifySuccessResponse(API_MSGS.Updated);
    getDatatable().ajax.reload(null, false);
  }
  if (error) {
    console.log(error);
    notifyErrorResponse(error);
  }
}

function loadEditForm() {
  $("#tableRoles").on("click", ".edit-button", function () {
    const table = $("#tableRoles").DataTable();
    let row = $(this).closest("tr");

    if (row.hasClass("child")) {
      row = row.prev(); // for responsive rows
    }

    const data = table.row(row).data();
    const descriptionLimit = 255;
    $("#edit-name").val(data.name);
    $("#edit-idrole").val(data.id);
    $("#edit-description").val(data.description);
    $("#customSwitch1").prop("checked", data.active === 1);
    const $description = $("#edit-description");
    const $counter = $("#edit-counter");
    updateCounter($description, $counter, descriptionLimit);
  });
}

function getDatatable() {
  return $("#tableRoles").DataTable();
}

async function loadUsernames(selectHtml) {
  const { data, error } = await getUsers();
  if (data) {
    $(`#${selectHtml}`).empty();
    let select = getById(selectHtml);
    data.data.forEach((user) => {
      let option = new Option(user.username, user.id);
      select.append(option);
    });
  }
  if (error) {
    notifyErrorResponse(error);
  }
}

async function getUsers() {
  return await apiRequest(`api/users/username`, {
    method: "GET",
  });
}
