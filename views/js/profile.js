const BASE_URL = "/Ecommerce";

$(document).ready(function () {
  bsCustomFileInput.init();
  setupPasswordButtons();
  setupSecurityForm();
  setupSettingsForm();

  function setupSecurityForm() {
    const form = document.forms["form-security"];
    const messageBox = document.getElementById("securityMessage");
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      messageBox.innerHTML = "";

      const email = form.email.value;
      const password = form.password.value;
      const newPassword = form["new-password"].value;
      const confirmPassword = form["re-password"].value;
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
      const errors = [];

      if (!password) {
        errors.push("Current password is required");
      }

      if (newPassword || confirmPassword) {
        if (!passwordRegex.test(password)) {
          errors.push(
            "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character",
          );
        }

        if (newPassword !== confirmPassword) {
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

      try {
        submitButton.disabled = true;
        submitButton.innerText = "Updating...";

        const response = await fetch(`${BASE_URL}/user/security`, {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            email,
            password,
            "new-password": newPassword,
            "re-password": confirmPassword,
          }),
        });

        const result = await response.json();

        if (!result.success) {
          notifyErrorResponse(result);
          form.reset();
          return;
        }

        notifySuccessResponse(result.message);
      } catch (error) {
        notifyErrorResponse(error);
      } finally {
        submitButton.disabled = false;
        submitButton.innerText = "Update Security";
      }
    });
  }

  function setupSettingsForm() {
    const form = document.forms["form-settings"];
    const messageBox = document.getElementById("settingsMessage");
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      messageBox.innerHTML = "";

      const name = form["name"].value;
      const address = form["address"].value;
      const phone = form["phone"].value;
      const phoneRegex = /^[6-9]\d{8}$/;
      const image = form.image.files[0] || null;
      const formData = new FormData();
      formData.append("image", image);
      const errors = [];

      if (name.length > 100) {
        errors.push("The name is too long");
      }

      if (phone) {
        if (!phoneRegex.test(phone)) {
          errors.push("Phone must be at least 8 numbers");
        }

        if (address.length > 255) {
          errors.push("The address is too long");
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

      try {
        submitButton.disabled = true;
        submitButton.innerText = "Updating...";

        const response = await fetch(`${BASE_URL}/user/settings/`, {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            name,
            phone,
            address,
          }),
        });

        const responseImage = await fetch(`${BASE_URL}/user/settings/photo/`, {
          method: "POST",
          body: formData,
        });

        const result = await response.json();
        const resultImage = await responseImage.json();

        if (!result.success) {
          notifyErrorResponse(result);
        }

        if (!resultImage.success) {
          notifyErrorResponse(resultImage);
        }

        form.reset();
        notifySuccessResponse(result.message);
        notifySuccessResponse(resultImage.message);
      } catch (error) {
        notifyErrorResponse(error);
      } finally {
        submitButton.disabled = false;
        submitButton.innerText = "Update Settings";
      }
    });
  }
});

function setupPasswordButtons() {
  $("#eyepassword").click(function () {
    toggleIcon(this.id);
    toggleInput("currentPassword");
  });

  $("#eyenewpassword").click(function () {
    toggleIcon(this.id);
    toggleInput("newPassword");
  });

  $("#eyerepassword").click(function () {
    toggleIcon(this.id);
    toggleInput("confirmPassword");
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
