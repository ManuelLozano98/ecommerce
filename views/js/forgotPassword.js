$(document).ready(function () {
  const params = new URLSearchParams(window.location.search);
  if (params.get("error")) {
    notifyErrorResponse({
      message: "The password reset link is invalid or has expired",
    });
  }

  const form = document.forms["form"];
  const messageBox = document.getElementById("message");
  const submitButton = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    params.delete("error");
    const newUrl = window.location.pathname;
    window.history.replaceState({}, "", newUrl);
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

    const response = await fetch(`${BASE_URL}/forgot-password/`, {
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
