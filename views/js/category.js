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

