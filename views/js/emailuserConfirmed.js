const BASE_URL = "/Ecommerce";

$(document).ready(function () {
  if (!hasError) {
    const redirect = document.getElementById("redirect");

    if (redirect) {
      redirect.classList.remove("hide-loader");
    }

    setTimeout(() => {
      location.href = BASE_URL + "/login";
    }, 3000);
  }

  const form = document.forms["form"];
  const messageBox = document.getElementById("message");
  const submitButton = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    messageBox.innerHTML = "";

    const email = form.email.value;
    const errors = [];

    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!email) {
      errors.push("Email is required");
    }

    if (!regex.test(email)) {
      notifyErrorResponse({
        message: "Validation error. Invalid email",
      });
      return;
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

    const response = await fetch(`${BASE_URL}/resend-email/`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ email }),
    });

    const result = await response.json();

    if (!result.success) {
      notifyErrorResponse(result);
      return;
    }

    notifySuccessResponse(result.message);
  });
});
