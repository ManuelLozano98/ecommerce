$(document).ready(function () {
  getById("form").addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    checkoutAddress(data);
  });

  async function checkoutAddress(form) {
    const { data, error } = await apiRequest(`${BASE_URL}/checkout/address`, {
      method: "POST",
      body: JSON.stringify(form),
    });
    if (!data.success) {
      notifyErrorResponse(data);
      return;
    }
    if (error) {
      notifyErrorResponse(error);
      return;
    }

    const response = await fetch(`${BASE_URL}/checkout`, {
      method: "POST",
    });

    const checkout = await response.json();
    if (checkout.clientSecret) {
      location.href = `${BASE_URL}/checkout?client=${checkout.clientSecret}`;
    }

    if (checkout.message) {
      notifyErrorResponse(checkout);
    }
  }
});
