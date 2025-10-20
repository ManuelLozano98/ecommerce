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
