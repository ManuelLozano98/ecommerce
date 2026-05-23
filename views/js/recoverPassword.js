const BASE_URL = "/Ecommerce";

$(document).ready(function () {
  setupPasswordButtons();
  const form = document.forms["form"];
  const messageBox = document.getElementById("message");
  const submitButton = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    messageBox.innerHTML = "";
    const password = form.password.value;
    const confirmPassword = form["confirmPassword"].value;
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    const errors = [];

    if (!password || !confirmPassword) {
      errors.push("Password is required");
    }

    if (password && confirmPassword) {
      if (!passwordRegex.test(password)) {
        errors.push(
          "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character",
        );
      }

      if (password !== confirmPassword) {
        errors.push("Passwords do not match");
      }
    }

    if (errors.length > 0) {
      notifyErrorResponse({
        message: "Validation error",
        details: {
          form: errors,
        },
      });
      return;
    }

    const response = await fetch(window.location.pathname, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ password, confirmPassword }),
    });

    const result = await response.json();

    if (!result.success) {
      notifyErrorResponse(result);
      return;
    }

    notifySuccessResponse(result.message);
  });
});

function setupPasswordButtons() {
  $("#passwordlock").click(function () {
    toggleIcon(this.id);
    toggleInput("password");
  });

  $("#confirmpasswordlock").click(function () {
    toggleIcon(this.id);
    toggleInput("confirmpassword");
  });
}

function toggleIcon(elementId) {
  if ($("#" + elementId).attr("class") === "fas fa-lock") {
    $("#" + elementId).attr("class", "fas fa-unlock");
  } else {
    $("#" + elementId).attr("class", "fas fa-lock");
  }
}
function toggleInput(elementId) {
  if ($("#" + elementId).attr("type") === "password") {
    $("#" + elementId).attr("type", "text");
  } else {
    $("#" + elementId).attr("type", "password");
  }
}
