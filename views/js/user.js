$(document).ready(function () {
  getUsers();
  loadEditForm();
  $("#form-edit").on("submit", function (e) {
    e.preventDefault();
    const form = getById("form-edit");
    if (isFormValid(form)) {
      edit();
    } else {
      redirectTab("#form-edit");
    }
  });
  $("#form").on("submit", function (e) {
    e.preventDefault();
    const form = getById("form");
    if (isFormValid(form)) {
      insert();
    } else {
      redirectTab("#form");
    }
  });
  loadDeleteButton();
  showFullText();
  setupPasswordButtons();
  verify();
});

function getUsers() {
  let loaderTimeout;
  let loaderShownAt = null;
  const MIN_VISIBLE_TIME = 400; // ms
  const DEBOUNCE_DELAY = 200; // ms

  $("#tableUsers").DataTable({
    serverSide: true,
    processing: false,
    ajax: function (data, callback, settings) {
      loaderTimeout = setTimeout(() => {
        $(".loader").show();
        loaderShownAt = Date.now();
      }, DEBOUNCE_DELAY);

      $.ajax({
        url: "api/users/detailed",
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
        data: "email",
        render: function (data) {
          return truncateText(data);
        },
      },
      {
        data: "username",
        render: function (data) {
          return truncateText(data);
        },
      },
      {
        data: "phone",
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
      {
        data: "address",
        render: function (data) {
          return truncateText(data);
        },
      },
      {
        data: "document",
      },
      {
        data: "document_name",
      },
      {
        data: "verification_token",
        render: function (data) {
          return truncateText(data);
        },
      },
      { data: "token_expires_at" },
      { data: "registration_date" },
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
  $("#tableUsers").on("click", ".edit-button", function () {
    let row = $(this).closest("tr");
    let table = $("#tableUsers").DataTable();
    if (row.hasClass("child")) {
      row = row.prev(); // needed for responsive tables
    }
    let data = table.row(row).data();
    $("#user_id").val(data.id);
    $("#edit-name").val(data.name);
    $("#edit-username").val(data.username);
    $("#edit-phone").val(data.phone);
    $("#edit-address").val(data.address);
    $("#edit-email").val(data.email);
    $("#edit-document").val(data.document);
    const $documentId = data.document_type_id;
    loadDocumentTypes("edit-document_type", $documentId);
    for (let element of document.getElementById("edit-document_type")
      .children) {
      // Find the document
      element.removeAttribute("selected");
      if (data.document_type === element.value) {
        element.setAttribute("selected", true);
      }
    }
    if (data.image !== "") {
      $("#edit-img").show();
      $("#edit-img").attr("src", "uploads/images/" + data.image);
    } else {
      $("#edit-img").hide();
    }
    $("#customSwitch1").prop("checked", data.active === 1);
  });
}

async function loadDocumentTypes(selectHtml, id = "") {
  if (document.getElementById(selectHtml).children.length <= 0) {
    const { data, error } = await apiRequest(`api/users/documentType/`, {
      method: "GET",
    });
    if (data) {
      let select = document.getElementById(selectHtml);
      for (let index = 0; index < data.data.length; index++) {
        let option = document.createElement("option");
        option.text = data.data[index].name;
        option.value = data.data[index].id;
        if (id && id === data.data.id) {
          option.selected = true;
        }
        select.appendChild(option);
      }
    }
    if (error) {
      notifyErrorResponse(error);
    }
  }
}
async function insert() {
  const form = new FormData(getById("form"));
  let active = form.get("active");
  if (active === "on") {
    form.set("active", "1");
  } else {
    form.set("active", "0");
  }
  form.delete("image");
  const user = Object.fromEntries(form.entries());

  const userBody = JSON.stringify(user);
  const { data, error } = await apiRequest(`api/users/`, {
    method: "POST",
    body: userBody,
  });
  if (data) {
    const imageFile = getById("form").image.files[0];
    if (imageFile) {
      const imageForm = new FormData();
      imageForm.append("image", imageFile);
      const { dataImage, errorImage } = await fetch(
        `api/users/${data.data.id}/image`,
        {
          method: "POST",
          body: imageForm,
        }
      );
      if (dataImage) {
        notifySuccessResponse(API_MSGS.Updated);
        getUsers();
        return;
      }
      if (errorImage) {
        notifyErrorResponse(errorImage);
        return;
      }
    }
    if (error) {
      notifyErrorResponse(error);
    }
    notifySuccessResponse(API_MSGS.Updated);
    getUsers();
  }
  if (error) {
    notifyErrorResponse(error);
  }
}
async function edit() {
  const $id = $("#user_id").val();
  const form = new FormData(getById("form-edit"));
  let active = form.get("active");
  if (active === "on") {
    form.set("active", "1");
  } else {
    form.set("active", "0");
  }
  form.delete("image");
  const user = Object.fromEntries(form.entries());

  const userBody = JSON.stringify(user);
  const { data, error } = await apiRequest(`api/users/${$id}/`, {
    method: "PUT",
    body: userBody,
  });
  if (data) {
    const imageFile = getById("form-edit").image.files[0];
    if (imageFile) {
      const imageForm = new FormData();
      imageForm.append("image", imageFile);
      const { dataImage, errorImage } = await fetch(
        `api/users/${data.data.id}/image`,
        {
          method: "POST",
          body: imageForm,
        }
      );
      if (dataImage) {
        notifySuccessResponse(API_MSGS.Updated);
        getUsers();
        return;
      }
      if (errorImage) {
        notifyErrorResponse(errorImage);
        return;
      }
    }
    if (error) {
      notifyErrorResponse(error);
    }
    notifySuccessResponse(API_MSGS.Updated);
    getUsers();
  }
  if (error) {
    notifyErrorResponse(error);
  }
}
async function deleteItem(id) {
  const response = await getDeleteMsg();
  if (response.isConfirmed) {
    const { data, error } = await apiRequest(`api/users/${id}`, {
      method: "DELETE",
    });
    if (data) {
      notifySuccessResponse(API_MSGS.Deleted);
      getUsers();
    }
    if (error) {
      notifyErrorResponse(error);
    }
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

function showFullText() {
  $("#tableUsers").on("click", ".view-full-text", function () {
    $("#modal-body").empty();
    const fullText = decodeURIComponent($(this).data("full"));
    $("#modal-body").append(`<p>${fullText}</p>`);
    $("#viewModalText").modal("show");
  });
}

function setupPasswordButtons() {
  $("#eyepassword").click(function () {
    toggleIcon(this.id);
    toggleInput("password");
  });
  $("#eyerepassword").click(function () {
    toggleIcon(this.id);
    toggleInput("repassword");
  });

  $("#edit-eyepassword").click(function () {
    toggleIcon(this.id);
    toggleInput("edit-password");
  });
  $("#edit-eyerepassword").click(function () {
    toggleIcon(this.id);
    toggleInput("edit-repassword");
  });
}

function toggleIcon(elementId) {
  if ($("#" + elementId).attr("class") === "fas fa-eye") {
    $("#" + elementId).attr("class", "fas fa-eye-slash");
  } else {
    $("#" + elementId).attr("class", "fas fa-eye");
  }
}
function toggleInput(elementId) {
  if ($("#" + elementId).attr("type") === "password") {
    $("#" + elementId).attr("type", "text");
  } else {
    $("#" + elementId).attr("type", "password");
  }
}

function redirectTab(formSelector) {
  let tabError = $(formSelector)
    .find(".is-invalid")
    .first()
    .closest(".tab-pane");
  let userTab = $(formSelector + " .tab-pane.fade.active.show");
  if (!tabError.is(userTab)) {
    let tabId = tabError.attr("id");
    let formTabs =
      formSelector.substr(-4) === "edit" ? "#edit-formTabs" : "#formTabs";
    let linkUserTab = $(formTabs).find(".nav-link.active");
    linkUserTab.removeClass("active");
    linkUserTab.removeAttr("aria-selected");
    let linkErrorTab = $("#" + tabId + "-tab");
    linkErrorTab.addClass("active");
    linkUserTab.attr("aria-selected", "true");
    userTab.removeClass("active show");
    tabError.addClass("active show");
  }
}

function verify() {
  $.validator.addMethod("usernameValidator", function (value, element) {
    return /^[a-zA-Z0-9](?!.*[_.]{2})[a-zA-Z0-9._]{2,18}[a-zA-Z0-9]$/.test(
      value
    );
  });

  $.validator.addMethod("nameValidator", function (value, element) {
    return /^[a-zA-Z]{3,}$/.test(value);
  });

  $.validator.addMethod("emailValidator", function (value, element) {
    return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
  });

  $.validator.addMethod("passwordValidator", function (value, element) {
    return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(value);
  });

  $.validator.addMethod(
    "checkPasswordsValidator",
    function (value, element, param) {
      let passwordValue = $(param).val();
      return value === passwordValue;
    }
  );
  $.validator.addMethod("phoneValidator", function (value, element) {
    return this.optional(element) || /^[6-9]\d{8}$/.test(value);
  });
  $.validator.addMethod("dniValidator", function (value, element) {
    let array = [
      "T",
      "R",
      "W",
      "A",
      "G",
      "M",
      "Y",
      "F",
      "P",
      "D",
      "X",
      "B",
      "N",
      "J",
      "Z",
      "S",
      "Q",
      "V",
      "H",
      "L",
      "C",
      "K",
      "E",
    ];
    return (
      this.optional(element) ||
      (/^\d{8}[A-HJ-NP-TV-Z]$/i.test(value) &&
        value.toUpperCase() ==
          value.substring(0, value.length - 1) +
            array[value.substring(0, value.length - 1) % 23])
    );
  });

  $.validator.addMethod("editEmailValidator", function (value, element) {
    return (
      this.optional(element) ||
      /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value)
    );
  });

  $.validator.addMethod(
    "editPasswordValidator",
    function (value, element, params) {
      let param = $(params).val();
      let flag = 0;
      if (value === "" && param === "") flag = 1;
      if (value !== "" && param !== "") flag = 2;
      if (flag === 1) return true;
      if (flag === 2)
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(value);
      return false;
    }
  );

  let commonRules = {
    name: {
      required: true,
      minlength: 3,
      nameValidator: true,
    },
    phone: {
      phoneValidator: true,
    },
    document: {
      dniValidator: true,
    },
  };

  let commonMessages = {
    name: {
      required: "Please enter a name",
      minlength: "Please enter a valid name, 3 characters at least",
      nameValidator: "Please enter a valid name",
    },
    phone: {
      phoneValidator: "Please enter a valid phone",
    },
    document: {
      dniValidator: "Please enter a valid DNI",
    },
  };

  let validationConfig = {
    ignore: [],
    errorElement: "span",
    errorPlacement: (error, element) => {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },
    highlight: (element) => {
      $(element).addClass("is-invalid");
    },
    unhighlight: (element) => {
      $(element).removeClass("is-invalid");
    },
  };

  $("#form").validate({
    ...validationConfig,
    rules: {
      ...commonRules,
      username: {
        required: true,
        minlength: 4,
        usernameValidator: true,
      },
      email: {
        required: true,
        emailValidator: true,
      },
      password: {
        required: true,
        passwordValidator: true,
      },
      repassword: {
        required: true,
        passwordValidator: true,
        checkPasswordsValidator: "#password",
      },
    },
    messages: {
      ...commonMessages,
      email: {
        required: "Please enter a email address",
        emailValidator: "Please enter a valid email address",
      },
      username: {
        required: "Please enter a username",
        usernameValidator:
          "The username must be between 4 and 20 characters, with no special characters or spaces, and not begin or end with . or _",
      },
      password: {
        required: "Please provide a password",
        passwordValidator:
          "Your password must be at least 8 characters long, 1 uppercase, 1 lowercase, 1 number and 1 symbol",
      },
      repassword: {
        required: "Please provide a password",
        passwordValidator:
          "Your password must be at least 8 characters long, 1 uppercase, 1 lowercase, 1 number and 1 symbol",
        checkPasswordsValidator: "The password doesn't match",
      },
    },
  });

  $("#form-edit").validate({
    ...validationConfig,
    rules: {
      ...commonRules,

      email: {
        editEmailValidator: true,
      },
      password: {
        editPasswordValidator: "#edit-repassword",
      },
      repassword: {
        editPasswordValidator: "#edit-password",
        checkPasswordsValidator: "#edit-password",
      },
    },
    messages: {
      ...commonMessages,
      email: {
        editEmailValidator: "Please enter a valid email address",
      },
      password: {
        editPasswordValidator:
          "Your password must be at least 8 characters long, 1 uppercase, 1 lowercase, 1 number and 1 symbol",
      },
      repassword: {
        editPasswordValidator: "",
        checkPasswordsValidator: "The password doesn't match",
      },
    },
  });
}

function isFormValid(formSelector) {
  return $(formSelector).valid();
}
