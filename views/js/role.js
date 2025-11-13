$(document).ready(function () {
  getRoles();
  getUsersRoles();
  setupCounter("description", "counter");
  setupCounter("edit-description", "edit-counter");
  showFullText();
  loadEditForm();
  setupSelect2();
  $("#addBtn").on("click", function (e) {
    loadUsernames("users");
  });
  $("#addUserRolesBtn").on("click", function (e) {
    loadUsernames("users-roles");
    loadRoles("roles-users");
  });
  $(document).on("click", "[id^='btn_add_']", function (e) {
    loadRoles("roles-users2");
  });

  loadRolesFormEdit();
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
$("#form-roles").on("submit", function (e) {
  e.preventDefault();
  const form = getById("form-roles");
  if (checkForm(form)) {
    const userId = getById("thisUser").value;
    insertRolesToUser(userId);
  }
});
$("#form-users-roles").on("submit", function (e) {
  e.preventDefault();
  const form = getById("form-users-roles");
  if (checkForm(form)) {
    insertUsersRoles();
  }
});

function loadDeleteButton() {
  document.addEventListener("click", function (e) {
    let deleteBtn = e.target.closest('[id^="btn-delete_"]');
    if (deleteBtn) {
      let id = deleteBtn.id.split("_")[1];
      deleteItem(id);
    }
  });
  document.addEventListener("click", function (e) {
    let deleteBtn = e.target.closest('[id^="btn-deleteAll_"]');
    if (deleteBtn) {
      let id = deleteBtn.id.split("_")[1];
      deleteAllRolesByUserId(id);
    }
  });
  document.addEventListener("click", function (e) {
    let deleteBtn = e.target.closest('[id^="btn_delete_"]');
    if (deleteBtn) {
      let roleId = deleteBtn.getAttribute("data-role_id");
      let userId = deleteBtn
        .closest("tr")
        .cells[0].firstChild.getAttribute("data-user_id");
      deleteRoleByUser(roleId, userId);
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

async function getUsersRoles() {
  let loaderTimeout;
  let loaderShownAt = null;
  const MIN_VISIBLE_TIME = 400; // ms
  const DEBOUNCE_DELAY = 200; // ms

  $("#tableUsersRoles").DataTable({
    serverSide: true,
    processing: false,
    ajax: function (data, callback, settings) {
      loaderTimeout = setTimeout(() => {
        $(".loader").show();
        loaderShownAt = Date.now();
      }, DEBOUNCE_DELAY);

      $.ajax({
        url: "api/users/roles/detailed",
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
      {
        data: "username",
        render: function (data, type, row) {
          return `<span data-user_id="${row.user_id}">${data}</span>`;
        },
      },
      {
        data: "roles",
        orderable: false, // This column does not allow sorting
        render: function (data) {
          return data.map((role) => {
            return `
            <span class="badge bg-primary roles" data-role_id="${role.role_id}" title="Remove ${role.name}" id="btn_delete_${role.id}">
              ${role.name}
              <input type="hidden" value="${role.id}"/>
            </span>
          `;
          });
        },
      },
      {
        data: null, // Column generated manually
        orderable: false, // This column does not allow sorting
        searchable: false, // This column does not allow searching
        render: function (data, type, row) {
          let id = Object.values(data)[0];
          return `
            <button id="btn_add_${id}" class="btn btn-success btn-sm rounded-0 add-button" type="button" data-toggle="modal" data-target="#modal-add-default" data-placement="top" title="Add"><i class="fa fa-plus-circle"></i></button>
            <button id="btn-deleteAll_${id}" class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete all roles"><i class="fa fa-trash"></i></button>
          `;
        },
      },
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
  $("#tableUsersRoles").on("click", ".view-full-text", function () {
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
      ({ data: dataUserRole, error: errorUserRole } = await apiRequest(
        `api/users/${userId}/roles/`,
        {
          method: "POST",
          body: roleBody,
        }
      ));
    }
    if (dataUserRole) {
      notifySuccessResponse(API_MSGS.Created);
      getDatatable("tableRoles").ajax.reload(null, false);
      getDatatable("tableUsersRoles").ajax.reload(null, false);
      return;
    }
    if (errorUserRole) {
      notifyErrorResponse(errorUserRole);
      return;
    }
    notifySuccessResponse(API_MSGS.Created);
    getDatatable("tableRoles").ajax.reload(null, false);
  }
  if (error) {
    notifyErrorResponse(error);
  }
}

async function insertRolesToUser(userId) {
  const formElement = getById("form-roles");
  const formData = new FormData(formElement);
  const roles = formData.getAll("role_id[]");
  let data = null;
  let error = null;
  for (let i = 0; i < roles.length; i++) {
    const roleBody = JSON.stringify({ role_id: roles[i] });
    ({ data, error } = await apiRequest(`api/users/${userId}/roles`, {
      method: "POST",
      body: roleBody,
    }));
  }
  if (data) {
    notifySuccessResponse(API_MSGS.Created);
  }
  if (error) {
    notifyErrorResponse(error);
  }
  getDatatable("tableUsersRoles").ajax.reload(null, false);
}

async function insertUsersRoles() {
  const user_ids = $("#users-roles").val() || [];
  const role_ids = $("#roles-users").val() || [];
  let data = null;
  let error = null;
  for (const user_id of user_ids) {
    for (const role_id of role_ids) {
      const roleBody = JSON.stringify({ role_id: role_id });
      ({ data, error } = await apiRequest(`api/users/${user_id}/roles`, {
        method: "POST",
        body: roleBody,
      }));
    }
  }
  if (data) {
    notifySuccessResponse(API_MSGS.Created);
  }
  if (error) {
    notifyErrorResponse(error);
  }
  getDatatable("tableUsersRoles").ajax.reload(null, false);
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
      getDatatable("tableRoles").ajax.reload(null, false);
      getDatatable("tableUsersRoles").ajax.reload(null, false);
    }
    if (error) {
      notifyErrorResponse(error);
    }
  }
}

async function deleteAllRolesByUserId(id) {
  const response = await getDeleteMsg();
  if (response.isConfirmed) {
    const { data, error } = await apiRequest(`api/users/${id}/roles`, {
      method: "DELETE",
    });
    if (data) {
      notifySuccessResponse(API_MSGS.Deleted);
      getDatatable("tableUsersRoles").ajax.reload(null, false);
    }
    if (error) {
      notifyErrorResponse(error);
    }
  }
}

async function deleteRoleByUser(roleId, userId) {
  const response = await getDeleteMsg();
  if (response.isConfirmed) {
    const { data, error } = await apiRequest(
      `api/users/${userId}/roles/${roleId}`,
      {
        method: "DELETE",
      }
    );
    if (data) {
      notifySuccessResponse(API_MSGS.Deleted);
      getDatatable("tableUsersRoles").ajax.reload(null, false);
    }
    if (error) {
      notifyErrorResponse(error);
    }
  }
}

function loadRolesFormEdit() {
  $("#tableUsersRoles").on("click", ".add-button", function () {
    const table = $("#tableUsersRoles").DataTable();
    let row = $(this).closest("tr");

    if (row.hasClass("child")) {
      row = row.prev(); // for responsive rows
    }

    const data = table.row(row).data();
    $("#thisUser").val(data.user_id);
  });
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
    getDatatable("tableRoles").ajax.reload(null, false);
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

async function loadRoles(selectHtml) {
  const { data, error } = await getRolesApi();
  if (data) {
    $(`#${selectHtml}`).empty();
    let select = getById(selectHtml);
    data.data.forEach((role) => {
      let option = new Option(role.name, role.id);
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

async function getRolesApi() {
  return await apiRequest(`api/roles`, {
    method: "GET",
  });
}
