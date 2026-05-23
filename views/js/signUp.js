const BASE_URL = "/Ecommerce";
$(document).ready(function () {
  setup();
  verifyForm();
});

function setup() {
  $("#eyepassword").click(function () {
    toggleIcon(this.id);
    toggleInput("password");
  });
  $("#eyerepassword").click(function () {
    toggleIcon(this.id);
    toggleInput("repassword");
  });
}

function verifyForm() {
  $.validator.addMethod("usernameValidator", function (value, element) {
    return usernameValidator(value);
  });

  $.validator.addMethod("nameValidator", function (value, element) {
    return nameValidator(value);
  });

  $.validator.addMethod("passwordValidator", function (value, element) {
    return passwordValidator(value);
  });

  $.validator.addMethod("checkPasswords", function (value, element) {
    return value === $("#password").val();
  });

  $("#form").validate({
    rules: {
      name: {
        required: true,
        minlength: 3,
        nameValidator: true,
      },
      username: {
        required: true,
        minlength: 4,
        usernameValidator: true,
      },
      email: {
        required: true,
        email: true,
      },
      password: {
        required: true,
        passwordValidator: true,
      },
      repassword: {
        required: true,
        checkPasswords: true,
      },
      terms: {
        required: true,
      },
    },
    messages: {
      name: {
        required: "Please enter a name",
        minlength: "Please enter a valid name, 3 characters at least",
        nameValidator: "Please enter a valid name",
      },
      email: {
        required: "Please enter a email address",
        email: "Please enter a valid email address",
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
        checkPasswords: "The password doesn't match",
      },
      terms: "Please accept our terms",
    },
    errorElement: "span",
    errorPlacement: function (error, element) {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass("is-invalid");
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass("is-invalid");
    },
    submitHandler: function (form) {
      let formData = new FormData(form);
      $.ajax({
        url: `${BASE_URL}/sign-up/`,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (xhr) {
          if (xhr.success) {
            notifySuccessResponse(xhr.message);
          } else {
            notifyErrorResponse(xhr);
          }
        },
        error: function (xhr) {
          notifyErrorResponse(xhr);
        },
      });
    },
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
function nameValidator(name) {
  return /^[a-zA-Z]{3,}$/.test(name);
}

function passwordValidator(pass) {
  // 8 characters minimum, 1 uppercase, 1 lowercase, 1 number and 1 symbol
  return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(pass);
}
function usernameValidator(user) {
  // 4 chracters minimum
  return /^[a-zA-Z0-9](?!.*[_.]{2})[a-zA-Z0-9._]{2,18}[a-zA-Z0-9]$/.test(user);
}
function checkPasswords(pass, pass2) {
  return pass === pass2;
}
